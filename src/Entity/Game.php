<?php

namespace App\Entity;

use App\Repository\GameRepository;
use App\Repository\GamesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getAllGames", "getAllNotices"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getAllGames"])]
    private ?string $name = null;

    #[ORM\Column(type: Types::ARRAY)]
    #[Groups(["getAllGames"])]
    private array $genre = [];

    #[ORM\Column(length: 255)]
    #[Groups(["getAllGames"])]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(["getAllGames"])]
    private ?\DateTimeInterface $dateSortie = null;

    #[ORM\Column(length: 24)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToMany(targetEntity: Plateforme::class, inversedBy: 'games')]
    #[Groups(["getAllGames"])]
    private Collection $plateformes;


    #[ORM\Column(length: 255)]
    #[Groups(["getAllGames"])]
    private ?string $nbJoueurs = null;

    #[ORM\OneToMany(mappedBy: 'game', targetEntity: Notice::class)]
    #[Groups(["getAllGames"])]
    private Collection $notices;

    public function __construct()
    {
        $this->plateformes = new ArrayCollection();
        $this->notices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getGenre(): array
    {
        return $this->genre;
    }

    public function setGenre(array $genre): static
    {
        $this->genre = $genre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDateSortie(): ?\DateTimeInterface
    {
        return $this->dateSortie;
    }

    public function setDateSortie(\DateTimeInterface $dateSortie): static
    {
        $this->dateSortie = $dateSortie;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Plateforme>
     */
    public function getPlateformes(): Collection
    {
        return $this->plateformes;
    }

    public function addPlateforme(Plateforme $plateforme): static
    {
        if (!$this->plateformes->contains($plateforme)) {
            $this->plateformes->add($plateforme);
        }

        return $this;
    }

    public function removePlateforme(Plateforme $plateforme): static
    {
        $this->plateformes->removeElement($plateforme);

        return $this;
    }

    public function getNbJoueurs(): ?string
    {
        return $this->nbJoueurs;
    }

    public function setNbJoueurs(string $nbJoueurs): static
    {
        $this->nbJoueurs = $nbJoueurs;

        return $this;
    }

    /**
     * @return Collection<int, Notice>
     */
    public function getNotices(): Collection
    {
        return $this->notices;
    }

    public function addNotice(Notice $notice): static
    {
        if (!$this->notices->contains($notice)) {
            $this->notices->add($notice);
            $notice->setGame($this);
        }

        return $this;
    }

    public function removeNotice(Notice $notice): static
    {
        if ($this->notices->removeElement($notice)) {
            // set the owning side to null (unless already changed)
            if ($notice->getGame() === $this) {
                $notice->setGame(null);
            }
        }

        return $this;
    }
}
