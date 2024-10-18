<?php

namespace App\Entity;

use App\Repository\SeriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SeriesRepository::class)]
class Series
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $publish_date = null;

    #[ORM\Column(nullable: true)]
    private ?int $critical_rate = null;



    /**
     * @var Collection<int, Season>
     */
    #[ORM\OneToMany(targetEntity: Season::class, mappedBy: 'series', cascade: ['remove'])]
    private Collection $seasons;

    /**
     * @var Collection<int, WatchList>
     */
    #[ORM\ManyToMany(targetEntity: WatchList::class, mappedBy: 'series')]
    private Collection $watchLists;


    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'series')]
    private Collection $comments;

    /**
     * @var Collection<int, Image>
     */
    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'series')]
    private Collection $horizontalImages;

    public function __construct()
    {
        $this->seasons = new ArrayCollection(); 
        $this->watchLists = new ArrayCollection(); 
        $this->comments = new ArrayCollection();
        $this->horizontalImages = new ArrayCollection(); 
    }


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

    public function setDescription(string $description): static
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

    public function getCriticalRate(): ?int
    {
        return $this->critical_rate;
    }

    public function setCriticalRate(?int $critical_rate): static
    {
        $this->critical_rate = $critical_rate;

        return $this;
    }



    /**
     * @return Collection<int, Season>
     */
    public function getSeasons(): Collection
    {
        return $this->seasons;
    }

    public function addSeason(Season $season): static
    {
        if (!$this->seasons->contains($season)) {
            $this->seasons->add($season);
            $season->setSeries($this);
        }

        return $this;
    }

    public function removeSeason(Season $season): static
    {
        if ($this->seasons->removeElement($season)) {
            // set the owning side to null (unless already changed)
            if ($season->getSeries() === $this) {
                $season->setSeries(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, WatchList>
     */
    public function getWatchLists(): Collection
    {
        return $this->watchLists;
    }

    public function addWatchList(WatchList $watchList): static
    {
        if (!$this->watchLists->contains($watchList)) {
            $this->watchLists->add($watchList);
            $watchList->addSeries($this);
        }

        return $this;
    } 
    public function removeWatchList(WatchList $watchList): static
    {
        if ($this->watchLists->removeElement($watchList)) {
            $watchList->removeSeries($this);
        }
        return $this;
    }
    /**
     *
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setSeries($this); 
        }

        return $this;
    }
 
 
    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getSeries() === $this) {
                $comment->setSeries(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getHorizontalImages(): Collection
    {
        return $this->horizontalImages;
    }

    public function addHorizontalImage(Image $horizontalImage): static
    {
        if (!$this->horizontalImages->contains($horizontalImage)) {
            $this->horizontalImages->add($horizontalImage);
            $horizontalImage->setSeries($this);
        }

        return $this;
    }

    public function removeHorizontalImage(Image $horizontalImage): static
    {
        if ($this->horizontalImages->removeElement($horizontalImage)) {
            // set the owning side to null (unless already changed)
            if ($horizontalImage->getSeries() === $this) {
                $horizontalImage->setSeries(null);
            }
        }

        return $this;
    } 
}
