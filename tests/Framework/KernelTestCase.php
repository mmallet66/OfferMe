<?php

namespace App\Tests\Framework;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase as BaseKernelTestCase;

class KernelTestCase extends BaseKernelTestCase
{
    /** @var ValidatorInterface */
    protected $validator;

    /** @var EntityManagerInterface */
    protected $em;

    protected function setUp(): void
    {
        self::bootKernel();
        DatabasePrimer::prime(self::$kernel);

        $this->em = self::$container->get('doctrine')->getManager();
        $this->validator = self::$container->get('validator');
    }

    /**
     * Assert that an user entity has a validation error and return the error message if exists
     * 
     * @param User $user
     * @param integer $numberOfExpectedErrors
     * @return string|null Constraint violation message
     */
    protected function assertHasError(User $user, int $numberOfExpectedErrors = 1): ?string
    {
        $errors = $this->validator->validate($user);

        $this->assertCount($numberOfExpectedErrors, $errors);

        return $errors->count() ? $errors->get(0)->getMessage() : null;
    }

    protected function getValidUserEntity()
    {
        return (new User)
            ->setEmail('johndoe@example.com')
            ->setFirstname('john')
            ->setLastname('DOE')
            ->setPassword('1!Abcdef');
    }

    protected function teardown(): void
    {
        parent::teardown();

        $this->em->close();
        $this->em = null;

        $this->validator = null;
    }
}