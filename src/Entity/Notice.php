<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
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

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(["getAllNotices","getAllGames"])]
    private ?\DateTimeInterface $createAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updateAt = null;

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

    public function getCreateAt(): ?\DateTimeInterface
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeInterface $createAt): static
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeInterface
    {
        return $this->updateAt;
    }

    public function setUpdateAt(\DateTimeInterface $updateAt): static
    {
        $this->updateAt = $updateAt;

        return $this;
    }
}
