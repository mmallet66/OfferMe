<?php

namespace App\Tests\UnitTests\Entity;

use App\Tests\Framework\KernelTestCase;

class UserTest extends KernelTestCase
{
    private const EMAIL_NOT_BLANK_CONSTRAINT_MESSAGE = "Veuillez saisir une adresse email.";
    
    private const INVALID_EMAIL_CONSTRAINT_MESSAGE = "Veuillez saisir une adresse email valide.";
    
    private const FIRSTNAME_NOT_BLANK_CONSTRAINT_MESSAGE = "Veuillez saisir votre prénom.";
    
    private const FIRSTNAME_LENGTH_CONSTRAINT_MESSAGE = "Votre prénom doit contenir moins de 50 caractères.";
    
    private const FIRSTNAME_REGEX_CONSTRAINT_MESSAGE = "Votre prénom ne peut contenir que des lettres, espaces ou trait d'union (-).";
    
    private const LASTNAME_NOT_BLANK_CONSTRAINT_MESSAGE = "Veuillez saisir votre nom.";
    
    private const LASTNAME_LENGTH_CONSTRAINT_MESSAGE = "Votre nom doit contenir moins de 50 caractères.";
    
    private const LASTNAME_REGEX_CONSTRAINT_MESSAGE = "Votre nom ne peut contenir que des lettres, espaces ou trait d'union (-).";

    private const UNIQUE_ENTITY_CONSTRAINT_MESSAGE = "Cette adresse email est déjà utilisée.";

    /** @test */
    public function user_is_valid()
    {
        $user = $this->getValidUserEntity();
        $this->assertHasError($user, 0);

        $this->assertSame('johndoe@example.com', $user->getEmail());
        $this->assertSame('johndoe@example.com', $user->getUsername());
        $this->assertSame('john', $user->getFirstname());
        $this->assertSame('DOE', $user->getLastname());
        $this->assertSame('1!Abcdef', $user->getPassword());
        $this->assertFalse($user->getIsVerified());
        $this->assertSame('ROLE_USER', $user->getRoles()[0]);
        
        $user->setIsVerified(true);
        $this->assertTrue($user->getIsVerified());
        
        $user->setRoles(['ROLE_ADMIN']);
        $this->assertSame('ROLE_ADMIN', $user->getRoles()[0]);
    }

    /** @test */
    public function user_is_invalid_because_no_email_entered()
    {
        $user = $this->getValidUserEntity()->setEmail('');
        $error = $this->assertHasError($user);
        $this->assertSame(self::EMAIL_NOT_BLANK_CONSTRAINT_MESSAGE, $error);
    }

    /** @test */
    public function user_is_invalid_because_email_entered_is_invalid()
    {
        $user = $this->getValidUserEntity()->setEmail('johnDoe@example');
        $error = $this->assertHasError($user);
        $this->assertSame(self::INVALID_EMAIL_CONSTRAINT_MESSAGE, $error);
    }

    /** @test */
    public function user_is_invalid_because_no_firstname_entered()
    {
        $user = $this->getValidUserEntity()->setFirstname('');
        $error = $this->assertHasError($user);
        $this->assertSame(self::FIRSTNAME_NOT_BLANK_CONSTRAINT_MESSAGE, $error);
    }

    /** @test */
    public function user_is_invalid_because_firstname_must_be_less_than_50_characters()
    {
        $moreThan50Chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $user = $this->getValidUserEntity()->setFirstname($moreThan50Chars);
        $error = $this->assertHasError($user);
        $this->assertSame(self::FIRSTNAME_LENGTH_CONSTRAINT_MESSAGE, $error);
    }

    /** @test */
    public function user_is_invalid_because_firstname_does_not_contain_only_letters_or_hyphen_or_space()
    {
        $user1 = $this->getValidUserEntity()->setFirstname('Jean-Pierre');
        $this->assertHasError($user1, 0);

        $user2 = $this->getValidUserEntity()->setFirstname('Jean_Pierre');
        $error = $this->assertHasError($user2);
        $this->assertSame(self::FIRSTNAME_REGEX_CONSTRAINT_MESSAGE, $error);

        $user3 = $this->getValidUserEntity()->setFirstname('Jean1');
        $error = $this->assertHasError($user3);
        $this->assertSame(self::FIRSTNAME_REGEX_CONSTRAINT_MESSAGE, $error);

    }

    /** @test */
    public function user_is_invalid_because_no_lastname_entered()
    {
        $user = $this->getValidUserEntity()->setLastname('');
        $error = $this->assertHasError($user);
        $this->assertSame(self::LASTNAME_NOT_BLANK_CONSTRAINT_MESSAGE, $error);
    }

    /** @test */
    public function user_is_invalid_because_lastname_must_be_less_than_50_characters()
    {
        $moreThan50Chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $user = $this->getValidUserEntity()->setLastname($moreThan50Chars);
        $error = $this->assertHasError($user);
        $this->assertSame(self::LASTNAME_LENGTH_CONSTRAINT_MESSAGE, $error);
    }

    /** @test */
    public function user_is_invalid_because_lastname_does_not_contain_only_letters_or_hyphen_or_space()
    {
        $user1 = $this->getValidUserEntity()->setLastname('Jean-Pierre');
        $this->assertHasError($user1, 0);

        $user2 = $this->getValidUserEntity()->setLastname('Jean_Pierre');
        $error = $this->assertHasError($user2);
        $this->assertSame(self::LASTNAME_REGEX_CONSTRAINT_MESSAGE, $error);

        $user3 = $this->getValidUserEntity()->setLastname('Jean1');
        $error = $this->assertHasError($user3);
        $this->assertSame(self::LASTNAME_REGEX_CONSTRAINT_MESSAGE, $error);

    }

    /** @test */
    public function user_must_be_unique()
    {
        $user1 = $this->getValidUserEntity();
        $this->em->persist($user1);
        $this->em->flush();

        // We check if the UniqueEntity constraint works
        $error = $this->assertHasError($this->getValidUserEntity());
        // We check that the constraint violation message is the one we want
        $this->assertSame(self::UNIQUE_ENTITY_CONSTRAINT_MESSAGE, $error);
    }
}
