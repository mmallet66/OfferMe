<?php

namespace App\Tests\FunctionnalTests;

use App\Entity\User;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    use FixturesTrait;

    /** @var KernelBrowser */
    private $client;

    private $em;

    protected function setUp(): void
    {
        $this->client = $this->createClient();
        $this->em = self::$container->get('doctrine')->getManager();
    }

    /** @test */
    public function can_not_access_to_profile_page_if_no_user_is_connected(): void
    {
        $this->client->request("GET", "/profil");

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    /** @test */
    public function can_access_to_profile_page_if_a_user_is_connected(): void
    {
        $this->loadFixtureFiles([__DIR__ . '/Fixtures/user.yaml']);

        $user = $this->em->getRepository(User::class)->findOneBy([]);
        $this->client->loginUser($user);

        $this->client->request("GET", "/profil");

        $this->assertResponseIsSuccessful();
    }
}