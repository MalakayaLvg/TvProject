<?php

namespace App\Entity;

use App\Repository\WatchListRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WatchListRepository::class)]
class WatchList
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;



    #[ORM\OneToOne(inversedBy: 'watchList', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    /**
     * @var Collection<int, Film>
     */
    #[ORM\ManyToMany(targetEntity: Film::class, inversedBy: 'watchLists')]
    private Collection $films;

    /**
     * @var Collection<int, Series>
     */
    #[ORM\ManyToMany(targetEntity: Series::class, inversedBy: 'watchLists')]
    private Collection $series;

    public function __construct()
    {
        $this->films = new ArrayCollection();
        $this->series = new ArrayCollection();
    }





    public function getId(): ?int
    {
        return $this->id;
    }



    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, Film>
     */
    public function getFilms(): Collection
    {
        return $this->films;
    }

    public function addFilm(Film $film): static
    {
        if (!$this->films->contains($film)) {
            $this->films->add($film);
        }

        return $this;
    }

    public function removeFilm(Film $film): static
    {
        $this->films->removeElement($film);

        return $this;
    }

    /**
     * @return Collection<int, Series>
     */
    public function getSeries(): Collection
    {
        return $this->series;
    }

    public function addSeries(Series $series): static
    {
        if (!$this->series->contains($series)) {
            $this->series->add($series);
        }

        return $this;
    }

    public function removeSeries(Series $series): static
    {
        $this->series->removeElement($series);

        return $this;
    }


}
