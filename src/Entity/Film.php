<?php

namespace App\Entity;

use App\Repository\FilmRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FilmRepository::class)]
class Film
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $publish_date = null;

    #[ORM\Column(nullable: true)]
    private ?int $runtime = null;

    #[ORM\Column(nullable: true)]
    private ?int $critical_rate = null;

    #[ORM\Column(nullable: true)]
    private ?bool $seen = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPublishDate(): ?\DateTimeInterface
    {
        return $this->publish_date;
    }

    public function setPublishDate(\DateTimeInterface $publish_date): static
    {
        $this->publish_date = $publish_date;

        return $this;
    }

    public function getRuntime(): ?int
    {
        return $this->runtime;
    }

    public function setRuntime(?int $runtime): static
    {
        $this->runtime = $runtime;

        return $this;
    }

    public function getCriticalRate(): ?int
    {
        return $this->critical_rate;
    }

    public function setCriticalRate(?int $critical_rate): static
    {
        $this->critical_rate = $critical_rate;

        return $this;
    }

    public function isSeen(): ?bool
    {
        return $this->seen;
    }

    public function setSeen(?bool $seen): static
    {
        $this->seen = $seen;

        return $this;
    }
}
