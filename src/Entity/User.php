<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups; 

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getAllNotices", "getAllGames"])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(["getAllNotices", "getAllGames"])]
    private ?string $username = null;

    #[ORM\Column]
    private array $roles = [];
    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\ManyToOne(inversedBy: 'user')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Persona $persona = null;


    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Notice::class)]
    private Collection $notice;

    #[ORM\OneToOne(inversedBy: 'user', cascade: ['persist', 'remove'])]
    #[Groups(["getAllNotices", "getAllGames"])]
    private ?Picture $picture = null;

    public function __construct()
    {
        $this->notice = new ArrayCollection();
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
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

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPersona(): ?Persona
    {
        return $this->persona;
    }

    public function setPersona(?Persona $persona): static
    {
        $this->persona = $persona;

        return $this;
    }

    /**
     * @return Collection<int, Notice>
     */
    public function getNotice(): Collection
    {
        return $this->notice;
    }

    public function addNotice(Notice $notice): static
    {
        if (!$this->notice->contains($notice)) {
            $this->notice->add($notice);
            $notice->setUser($this);
        }

        return $this;
    }

    public function removeNotice(Notice $notice): static
    {
        if ($this->notice->removeElement($notice)) {
            // set the owning side to null (unless already changed)
            if ($notice->getUser() === $this) {
                $notice->setUser(null);
            }
        }

        return $this;
    }

    public function getPicture(): ?Picture
    {
        return $this->picture;
    }

    public function setPicture(?Picture $picture): static
    {
        $this->picture = $picture;

        return $this;
    }
}
