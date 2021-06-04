<?php

namespace App\Tests\UnitTests\Entity;

use App\Tests\Framework\KernelTestCase;

class WishListTest extends KernelTestCase
{
    private const TITLE_NOT_BLANK_CONSTRAINT_MESSAGE = "Veuillez choisir un titre pour votre liste.";

    private const TITLE_LENGTH_CONSTRAINT_MESSAGE = "Veuillez choisir un titre moins long.";

    private const DESCRIPTION_LENGTH_CONSTRAINT_MESSAGE = "Veuillez choisir une description moins long.";

    /** @test */
    public function wishlist_is_valid()
    {
        $wishList = $this->getValidWishListEntity();
        $this->assertHasError($wishList, 0);
        
        $this->assertSame('Title test', $wishList->getTitle());
        $this->assertSame('Just a simple description to test the entity.', $wishList->getDescription());
    }
    
    /** @test */
    public function wishlist_is_invalid_because_no_title_entered()
    {
        $wishList = $this->getValidWishListEntity()->setTitle('');

        $errorMessage = $this->assertHasError($wishList);
        $this->assertSame(self::TITLE_NOT_BLANK_CONSTRAINT_MESSAGE, $errorMessage);
    }

    /** @test */
    public function wishlist_is_invalid_because_title_is_longer_than_255_characters()
    {
        $stringOver255 = bin2hex(random_bytes(128));
        $wishList = $this->getValidWishListEntity()->setTitle($stringOver255);

        $errorMessage = $this->assertHasError($wishList);
        $this->assertSame(self::TITLE_LENGTH_CONSTRAINT_MESSAGE, $errorMessage);
    }

    /** @test */
    public function wishlist_is_invalid_because_description_is_longer_than_255_characters()
    {
        $stringOver255 = bin2hex(random_bytes(128));
        $wishList = $this->getValidWishListEntity()->setDescription($stringOver255);

        $errorMessage = $this->assertHasError($wishList);
        $this->assertSame(self::DESCRIPTION_LENGTH_CONSTRAINT_MESSAGE, $errorMessage);
    }
}
