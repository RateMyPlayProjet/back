<?php

namespace App\Entity;

use App\Repository\PlateformeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
//Serializer groups
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PlateformeRepository::class)]
class Plateforme
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getAllFromGame"])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'Plateformes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Game $name = null;

    #[ORM\ManyToMany(targetEntity: Game::class, mappedBy: 'plateformes')]
    private Collection $namePlateforme;

    public function __construct()
    {
        $this->namePlateforme = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getName(): ?Game
    {
        return $this->name;
    }

    public function setName(?Game $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getNamePlateforme(): Collection
    {
        return $this->namePlateforme;
    }

    public function addNamePlateforme(Game $namePlateforme): static
    {
        if (!$this->namePlateforme->contains($namePlateforme)) {
            $this->namePlateforme->add($namePlateforme);
            $namePlateforme->addPlateforme($this);
        }

        return $this;
    }

    public function removeNamePlateforme(Game $namePlateforme): static
    {
        if ($this->namePlateforme->removeElement($namePlateforme)) {
            $namePlateforme->removePlateforme($this);
        }

        return $this;
    }
}
