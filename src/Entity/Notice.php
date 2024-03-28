<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\NoticeRepository;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: NoticeRepository::class)]
class Notice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getAllNotices"])]
    private ?int $id = null;

    #[ORM\Column(length: 500)]
    #[Groups(["getAllNotices","getAllGames"])]
    private ?string $comment = null;

    #[ORM\Column]
    #[Groups(["getAllNotices","getAllGames"])]
    private ?int $note = null;

    #[ORM\ManyToOne(inversedBy: 'notices')]
    #[Groups(["getAllNotices"])]
    private ?Game $game = null;

    #[ORM\ManyToOne(inversedBy: 'notice')]
    #[Groups(["getAllNotices","getAllGames"])]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): static
    {
        $this->game = $game;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
