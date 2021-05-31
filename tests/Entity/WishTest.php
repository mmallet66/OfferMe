<?php

namespace App\Tests\Entity;

use App\Tests\Framework\KernelTestCase;
use Generator;

class WishTest extends KernelTestCase
{
    private const NAME_NOT_BLANK_CONSTRAINT_MESSAGE = "Veuillez choisir un titre pour votre souhait.";

    private const NAME_LENGTH_CONSTRAINT_MESSAGE = "Veuillez choisir un titre moins long.";

    private const DESCRIPTION_LENGTH_CONSTRAINT_MESSAGE = "Veuillez choisir une description moins long.";

    private const PRICE_REGEX_CONSTRAINT_MESSAGE = "Veuillez saisir un prix valide (utiliser le point pour les decimales).";

    private const INVALID_URL_CONSTRAINT_MESSAGE = "Veuillez saisir une url valide.";

    /** @test */
    public function wish_is_valid(): void
    {
        $wish = $this->getValidWishEntity();
        $this->assertHasError($wish, 0);
        
        $this->assertSame('Name test', $wish->getName());
        $this->assertSame('Just a simple description to test the entity.', $wish->getDescription());
        $this->assertSame('1000.23', $wish->getPrice());
        $this->assertSame('https://www.domain.com/img/1.jpg', $wish->getUrl());
        $this->assertSame('image_name.jpg', $wish->getImage());
        $this->assertSame(false, $wish->getIsOffered());
        
        $wish->setIsOffered(true);
        $this->assertSame(true, $wish->getIsOffered());
    }

    /** @test */
    public function wish_is_invalid_because_no_name_entered(): void
    {
        $wish = $this->getValidWishEntity()->setName('');

        $errorMessage = $this->assertHasError($wish);
        $this->assertSame(self::NAME_NOT_BLANK_CONSTRAINT_MESSAGE, $errorMessage);
    }

    /** @test */
    public function wish_is_invalid_because_name_is_longer_than_255_characters(): void
    {
        $stringOver255 = bin2hex(random_bytes(128));
        $wish = $this->getValidWishEntity()->setName($stringOver255);

        $errorMessage = $this->assertHasError($wish);
        $this->assertSame(self::NAME_LENGTH_CONSTRAINT_MESSAGE, $errorMessage);
    }

    /** @test */
    public function wish_is_invalid_because_description_is_longer_than_255_characters(): void
    {
        $stringOver255 = bin2hex(random_bytes(128));
        $wish = $this->getValidWishEntity()->setDescription($stringOver255);

        $errorMessage = $this->assertHasError($wish);
        $this->assertSame(self::DESCRIPTION_LENGTH_CONSTRAINT_MESSAGE, $errorMessage);
    }

    /**
     * @dataProvider invalid_prices_provider
     * @test
     * */
    public function wish_price_is_invalid($price): void
    {
        $wish = $this->getValidWishEntity()->setPrice($price);

        $errorMessage = $this->assertHasError($wish);
        $this->assertSame(self::PRICE_REGEX_CONSTRAINT_MESSAGE, $errorMessage);
    }
    
    public function invalid_prices_provider(): Generator
    {
        yield 'a letter' => ['price'];
        yield 'a negative number' => [-12.12];
        yield 'an integer greater than 9999999999' => [10000000000];
        yield 'a float greater than 99999999.99' => [100000000.01];
    }
    
    /** @test */
    public function wish_is_invalid_because_url_is_not_a_valid_url()
    {
        $wish = $this->getValidWishEntity()->setUrl('invalid-url');

        $errorMessage = $this->assertHasError($wish);
        $this->assertSame(self::INVALID_URL_CONSTRAINT_MESSAGE, $errorMessage);
    }
}
