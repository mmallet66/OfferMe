<?php

namespace App\Tests\FunctionnalTests;

use Generator;
use App\Entity\User;
use App\Tests\Framework\DatabasePrimer;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityTest extends WebTestCase
{
    /** @var KernelBrowser */
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        static::bootKernel();
        DatabasePrimer::prime(static::$kernel);
    }

    public function registerNewUser()
    {
        $crawler = $this->client->request('GET', '/inscription');
        $registrationForm = $crawler->selectButton('S\'inscrire')->form();

        $this->client->submit($registrationForm, [
                'registration_form[firstname]' => 'John',
                'registration_form[lastname]' => 'Doe',
                'registration_form[email]' => 'johndoe@example.com',
                'registration_form[password][first]' => '1!Secret',
                'registration_form[password][second]' => '1!Secret',
            ]
        );
    }

    /**
     * Check if all the registration form fields are present in the register view
     * 
     * @dataProvider registration_form_field_provider
     * @test
     */
    public function registration_form_is_good($formField): void
    {
        $crawler = $this->client->request('GET', '/inscription');
        $registrationForm = $crawler->selectButton('S\'inscrire')->form();

        $this->assertTrue(
            $registrationForm->has("registration_form[$formField]"),
            sprintf('The %s field is missing in the registration form.', str_replace('][', ' ', $formField))
        );
    }

    public function registration_form_field_provider(): Generator
    {
        yield 'firstname field' => ['firstname'];
        yield 'lastname'  => ['lastname'];
        yield 'email field' => ['email'];
        yield 'password field' => ['password][first'];
        yield 'confirm password field' => ['password][second'];
        yield 'csrf token field' => ['_token'];
    }

    /**
     * Check if all the login form fields are present in the login view
     * 
     * @dataProvider login_form_field_provider
     * @test
     */
    public function login_form_is_good($formField): void
    {
        $crawler = $this->client->request('GET', '/connexion');
        $loginForm = $crawler->selectButton('Se connecter')->form();

        $this->assertTrue(
            $loginForm->has($formField),
            sprintf('The %s field is missing in the login form.', str_replace('][', ' ', $formField))
        );
    }

    public function login_form_field_provider(): Generator
    {
        yield 'email field' => ['email'];
        yield 'password field' => ['password'];
        yield 'csrf token field' => ['_csrf_token'];
    }

    /** @test */
    public function we_can_sign_up(): void
    {
        $this->registerNewUser();

        $em = static::$container->get('doctrine.orm.entity_manager');
        $repo = $em->getRepository(User::class);
        $user = $repo->findOneBy(['email' => 'johndoe@example.com']);

        $this->assertSame('John', $user->getFirstname(), 'The user firstname does not match.');
        $this->assertSame('Doe', $user->getLastname(), 'The user lastname does not match.');
        $this->assertSame('johndoe@example.com', $user->getEmail(), 'The user email does not match.');

        $userPasswordEncoder = static::$container->get('security.user_password_encoder.generic');
        $this->assertTrue($userPasswordEncoder->isPasswordValid($user, '1!Secret'), 'The user password does not match.');

        $this->assertResponseRedirects('/connexion', 302);
    }

    /** @test */
    public function we_can_sign_in_with_good_credentials(): void
    {
        $this->registerNewUser();
        
        $crawler = $this->client->request('GET', '/connexion');
        $loginForm = $crawler->selectButton('Se connecter')->form([
            'email' => 'johndoe@example.com',
            'password' => '1!Secret'
        ]);

        $this->client->submit($loginForm);
        $this->client->followRedirects();

        echo("
\e[43;30m⚠ TODO ⚠  \e[39;49m
\e[43;30mUpdate redirection route when it will be created in :\e[39;49m
\e[43;30m|-> App\Tests\FunctionnalTests\SecurityTest::we_can_sign_in_with_good_credentials\e[39;49m
\e[43;30m|-> App\Security\UserAuthenticator::onAuthenticationSuccess\e[39;49m
");
        /** @todo Update redirection route when it will be created */
        // $this->assertResponseRedirects('/blabla', 302);
        $this->assertResponseRedirects();
    }

    /** @test */
    public function we_cannot_sign_in_with_bad_credentials(): void
    {
        $this->registerNewUser();
        
        $crawler = $this->client->request('GET', '/connexion');
        $loginForm = $crawler->selectButton('Se connecter')->form([
            'email' => 'johndoe@example.com',
            'password' => 'Bad Password'
        ]);

        $this->client->submit($loginForm);
        $this->client->followRedirects();
        $this->assertResponseRedirects('/connexion', 302);
    }
}
