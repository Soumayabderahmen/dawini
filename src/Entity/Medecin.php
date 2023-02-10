<?php

namespace App\Entity;

use App\Repository\MedecinRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MedecinRepository::class)]
class Medecin extends User
{
   
    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse_cabinet = null;

    #[ORM\Column(length: 255)]
    private ?string $fixe = null;

    #[ORM\Column(length: 255)]
    private ?string $diplome_formation = null;

    #[ORM\Column]
    private ?float $tarif = null;

    #[ORM\Column]
    private ?bool $cnam = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $horraire_ouverture = null;

    #[ORM\ManyToOne(inversedBy: 'medecins')]
    private ?Specialites $specialites = null;

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getAdresseCabinet(): ?string
    {
        return $this->adresse_cabinet;
    }

    public function setAdresseCabinet(string $adresse_cabinet): self
    {
        $this->adresse_cabinet = $adresse_cabinet;

        return $this;
    }

    public function getFixe(): ?string
    {
        return $this->fixe;
    }

    public function setFixe(string $fixe): self
    {
        $this->fixe = $fixe;

        return $this;
    }

    public function getDiplomeFormation(): ?string
    {
        return $this->diplome_formation;
    }

    public function setDiplomeFormation(string $diplome_formation): self
    {
        $this->diplome_formation = $diplome_formation;

        return $this;
    }

    public function getTarif(): ?float
    {
        return $this->tarif;
    }

    public function setTarif(float $tarif): self
    {
        $this->tarif = $tarif;

        return $this;
    }

    public function isCnam(): ?bool
    {
        return $this->cnam;
    }

    public function setCnam(bool $cnam): self
    {
        $this->cnam = $cnam;

        return $this;
    }

    public function getHorraireOuverture(): ?\DateTimeInterface
    {
        return $this->horraire_ouverture;
    }

    public function setHorraireOuverture(\DateTimeInterface $horraire_ouverture): self
    {
        $this->horraire_ouverture = $horraire_ouverture;

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
}
