<?php

namespace App\Tests\FunctionnalTests;

use Generator;
use PHPUnit\Framework\ExpectationFailedException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RouterTest extends WebTestCase
{
    /**
     * @dataProvider provideRoute
     *
     * @test
     */
    public function available_route(string $route): void
    {
        $client = static::createClient();
        $client->request('GET', $route);

        try {
            $this->assertResponseIsSuccessful();
        } catch (ExpectationFailedException $e) {
            throw new \Exception("Failed asserting that route path \"$route\" is available.");
            
        }
    }

    public function provideRoute(): Generator
    {
        yield '"/register"' => ["/register"];
    }
}
