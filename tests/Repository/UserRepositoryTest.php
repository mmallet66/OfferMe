<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Entity\WishList;
use App\Tests\Framework\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    /** @test */
    public function get_user_with_his_wishlists()
    {
        $user = $this->getValidUserEntity()
            ->addWishList($this->getValidWishListEntity())
            ->addWishList($this->getValidWishListEntity()->setTitle('hello'))
            ->addWishList($this->getValidWishListEntity()->setTitle('Bonjour'));

        $this->em->persist($user);
        $this->em->flush();

        $userRepo = $this->em->getRepository(User::class);
        $wishLists = $userRepo->find(1)->getWishLists();

        foreach ($wishLists as $wishList) {
            $this->assertInstanceOf(WishList::class, $wishList);
        }
    }
}