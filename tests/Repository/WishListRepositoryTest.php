<?php

namespace App\Tests\Repository;

use App\Entity\Wish;
use App\Entity\WishList;
use App\Tests\Framework\KernelTestCase;

class WishListRepositoryTest extends KernelTestCase
{
    /** @test */
    public function get_wishlist_with_his_wishes()
    {
        $user = $this->getValidUserEntity();

        $wishList = $this->getValidWishListEntity()
            ->setUser($user)
            ->addWish($this->getValidWishEntity())
            ->addWish($this->getValidWishEntity()->setName('Fake Name'))
            ->addWish($this->getValidWishEntity()->setName('Fake Second Name'));

        $this->em->persist($user);
        $this->em->persist($wishList);
        $this->em->flush();

        $wishListRepo = $this->em->getRepository(WishList::class);
        $wishes = $wishListRepo->find(1)->getWishes();

        foreach ($wishes as $wish) {
            $this->assertInstanceOf(Wish::class, $wish);
        }
    }
}