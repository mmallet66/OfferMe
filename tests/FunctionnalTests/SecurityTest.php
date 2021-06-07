<?php

namespace App\Tests\FunctionnalTests;

use Generator;
use App\Entity\User;
use App\Tests\Framework\DatabasePrimer;
use Symfony\Component\DomCrawler\Form;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityTest extends WebTestCase
{
    /** @var KernelBrowser */
    private $client;

    /** @var Crawler */
    private $crawler;

    /** @var Form */
    private $registrationForm;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->crawler = $this->client->request("GET", "/inscription");
        $this->registrationForm = $this->crawler->selectButton("S'inscrire")->form();
    }

    /**
     * Check if all the registration form fields are present in the register view
     * 
     * @dataProvider form_field_provider
     * @test
     */
    public function registration_form_is_good($formField): void
    {
        $this->assertTrue(
            $this->registrationForm->has("registration_form[$formField]"),
            sprintf("The %s field is missing in the registration form.", str_replace('][', ' ', $formField))
        );
    }

    public function form_field_provider(): Generator
    {
        yield 'firstname field' => ['firstname'];
        yield 'lastname'  => ['lastname'];
        yield 'email field' => ['email'];
        yield 'password field' => ['password][first'];
        yield 'confirm password field' => ['password][second'];
        yield 'csrf token field' => ['_token'];
    }

    /** @test */
    public function we_can_sign_up(): void
    {
        static::bootKernel();
        DatabasePrimer::prime(static::$kernel);

        $this->client->submit($this->registrationForm, [
                "registration_form[firstname]" => "John",
                "registration_form[lastname]" => "Doe",
                "registration_form[email]" => "johndoe@example.com",
                "registration_form[password][first]" => "1!Secret",
                "registration_form[password][second]" => "1!Secret",
            ]
        );

        $em = static::$container->get("doctrine.orm.entity_manager");
        $repo = $em->getRepository(User::class);
        $user = $repo->findOneBy(['email' => 'johndoe@example.com']);

        $this->assertSame("John", $user->getFirstname(), "The user firstname does not match.");
        $this->assertSame("Doe", $user->getLastname(), "The user lastname does not match.");
        $this->assertSame("johndoe@example.com", $user->getEmail(), "The user email does not match.");

        $userPasswordEncoder = static::$container->get("security.user_password_encoder.generic");
        $this->assertTrue($userPasswordEncoder->isPasswordValid($user, "1!Secret"), "The user password does not match.");

        $this->assertResponseRedirects();
    }
}
