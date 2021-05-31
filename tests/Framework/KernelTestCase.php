<?php

namespace App\Tests\Framework;

use App\Entity\User;
use App\Entity\Wish;
use App\Entity\WishList;
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
     * Assert that an entity has a validation error and return the error message if exists
     * 
     * @param Object $object
     * @param integer $numberOfExpectedErrors
     * @return string|null Constraint violation message
     */
    protected function assertHasError(Object $object, int $numberOfExpectedErrors = 1): ?string
    {
        $errors = $this->validator->validate($object);

        $this->assertCount($numberOfExpectedErrors, $errors);

        return $errors->count() ? $errors->get(0)->getMessage() : null;
    }

    /**
     * Create a new User entity with valid values
     *
     * @return User
     */
    protected function getValidUserEntity(): User
    {
        return (new User)
            ->setEmail('johndoe@example.com')
            ->setFirstname('john')
            ->setLastname('DOE')
            ->setPassword('1!Abcdef');
    }

    /**
     * Create a new Wish entity with valid values
     *
     * @return WishList
     */
    protected function getValidWishListEntity(): WishList
    {
        return (new WishList)
            ->setTitle('Title test')
            ->setDescription('Just a simple description to test the entity.');
    }

    /**
     * Create a new WishList entity with valid values
     *
     * @return Wish
     */
    protected function getValidWishEntity(): Wish
    {
        return (new Wish)
            ->setName('Name test')
            ->setDescription('Just a simple description to test the entity.')
            ->setPrice(1000.23)
            ->setUrl('https://www.domain.com/img/1.jpg')
            ->setImage('image_name.jpg');
    }

    protected function teardown(): void
    {
        parent::teardown();

        $this->em->close();
        $this->em = null;

        $this->validator = null;
    }
}