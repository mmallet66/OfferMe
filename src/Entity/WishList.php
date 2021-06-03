<?php

namespace App\Entity;

use App\Repository\WishListRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @ORM\OneToMany(targetEntity=Wish::class, mappedBy="wishlist", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $wishes;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="wishLists")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct()
    {
        $this->wishes = new ArrayCollection();
    }

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

    /**
     * @return Collection|Wish[]
     */
    public function getWishes(): Collection
    {
        return $this->wishes;
    }

    public function addWish(Wish $wish): self
    {
        if (!$this->wishes->contains($wish)) {
            $this->wishes[] = $wish;
            $wish->setWishlist($this);
        }

        return $this;
    }

    public function removeWish(Wish $wish): self
    {
        if ($this->wishes->removeElement($wish)) {
            // set the owning side to null (unless already changed)
            if ($wish->getWishlist() === $this) {
                $wish->setWishlist(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
