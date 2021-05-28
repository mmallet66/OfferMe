<?php

namespace App\Entity;

use App\Repository\WishListRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=WishListRepository::class)
 * @ORM\Table(name="`wishlist`")
 */
class WishList
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length="255")
     * @Assert\NotBlank(message="Veuillez choisir un titre pour votre liste.")
     * @Assert\Length(max=255,maxMessage="Veuillez choisir un titre moins long.")
     */
    private $title;

    /**
     * @ORM\Column(type="string", length="255", nullable=true)
     * @Assert\Length(max=255,maxMessage="Veuillez choisir une description moins long.")
     */
    private $description;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle()
    {
        return (string) $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription()
    {
        return (string) $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
