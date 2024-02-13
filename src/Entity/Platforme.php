<?php

namespace App\Entity;

use App\Repository\PlatformeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlatformeRepository::class)]
class Platforme
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'platformes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Game $name = null;

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
}
