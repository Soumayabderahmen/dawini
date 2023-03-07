<?php

namespace App\Entity;

use App\Repository\SujetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SujetRepository::class)]
class Sujet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $message = null;

   

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'sujet', targetEntity: ReplaySujet::class)]
    private Collection $replaySujets;

    #[ORM\ManyToOne(inversedBy: 'sujets')]
    private ?Specialites $specialites = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $statut = null;

    #[ORM\OneToMany(mappedBy: 'sujet', targetEntity: SujetLike::class)]
    private Collection $sujetLikes;

    #[ORM\ManyToOne(inversedBy: 'sujets')]
    private ?User $utilisateur = null;

    public function __construct()
    {
        $this->replaySujets = new ArrayCollection();
        $this->sujetLikes = new ArrayCollection();
        $this->date = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

   

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, ReplaySujet>
     */
    public function getReplaySujets(): Collection
    {
        return $this->replaySujets;
    }

    public function addReplaySujet(ReplaySujet $replaySujet): self
    {
        if (!$this->replaySujets->contains($replaySujet)) {
            $this->replaySujets->add($replaySujet);
            $replaySujet->setSujet($this);
        }

        return $this;
    }

    public function removeReplaySujet(ReplaySujet $replaySujet): self
    {
        if ($this->replaySujets->removeElement($replaySujet)) {
            // set the owning side to null (unless already changed)
            if ($replaySujet->getSujet() === $this) {
                $replaySujet->setSujet(null);
            }
        }

        return $this;
    }

    public function getSpecialites(): ?Specialites
    {
        return $this->specialites;
    }

    public function setSpecialites(?Specialites $specialites): self
    {
        $this->specialites = $specialites;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * @return Collection<int, SujetLike>
     */
    public function getSujetLikes(): Collection
    {
        return $this->sujetLikes;
    }

    public function addSujetLike(SujetLike $sujetLike): self
    {
        if (!$this->sujetLikes->contains($sujetLike)) {
            $this->sujetLikes->add($sujetLike);
            $sujetLike->setSujet($this);
        }

        return $this;
    }

    public function removeSujetLike(SujetLike $sujetLike): self
    {
        if ($this->sujetLikes->removeElement($sujetLike)) {
            // set the owning side to null (unless already changed)
            if ($sujetLike->getSujet() === $this) {
                $sujetLike->setSujet(null);
            }
        }

        return $this;
    }

    public function getUtilisateur(): ?User
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?User $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }
    public function __toString(): string{
        return (string)$this->title;
       }

       public function getLikesCount(): int
    {
        return $this->sujetLikes->count();
    }
}
