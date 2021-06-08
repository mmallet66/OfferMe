<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @UniqueEntity(fields = {"email"},message ="Cette adresse email est déjà utilisée.")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(message="Veuillez saisir une adresse email.")
     * @Assert\Email(message = "Veuillez saisir une adresse email valide.")
     */
    private $email;
    
    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];
    
    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Veuillez définir votre mot de passe.")
     * @Assert\Regex(
     *      pattern="/^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[^\w\d\s:])([^\s]){8,16}$/",
     *      message="Veuillez saisir un mot de passe valide."
     * )
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="Veuillez saisir votre prénom.")
     * @Assert\Length(max=50, maxMessage="Votre prénom doit contenir moins de {{ limit }} caractères.")
     * @Assert\Regex(
     *      pattern="/^[a-zéèçà]{2,50}(-| )?([a-zéèçà]{2,50})?$/i",
     *      message="Votre prénom ne peut contenir que des lettres, espaces ou trait d'union (-)."
     * )
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="Veuillez saisir votre nom.")
     * @Assert\Length(max=50, maxMessage="Votre nom doit contenir moins de {{ limit }} caractères.")
     * @Assert\Regex(
     *      pattern="/^[a-zéèçà]{2,50}(-| )?([a-zéèçà]{2,50})?$/i",
     *      message="Votre nom ne peut contenir que des lettres, espaces ou trait d'union (-)."
     * )
     */
    private $lastname;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\OneToMany(targetEntity=WishList::class, mappedBy="user", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $wishLists;

    public function __construct()
    {
        $this->wishLists = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection|WishList[]
     */
    public function getWishLists(): Collection
    {
        return $this->wishLists;
    }

    public function addWishList(WishList $wishList): self
    {
        if (!$this->wishLists->contains($wishList)) {
            $this->wishLists[] = $wishList;
            $wishList->setUser($this);
        }

        return $this;
    }

    public function removeWishList(WishList $wishList): self
    {
        if ($this->wishLists->removeElement($wishList)) {
            // set the owning side to null (unless already changed)
            if ($wishList->getUser() === $this) {
                $wishList->setUser(null);
            }
        }

        return $this;
    }
}
