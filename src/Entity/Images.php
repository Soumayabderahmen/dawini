<?php

namespace App\Entity;

use App\Repository\ImagesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImagesRepository::class)]
class Images
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    private ?Article $aticles = null;

   

  

    #[ORM\ManyToOne(inversedBy: 'images')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Specialites $specialite = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    private ?Dossier $dossier = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    private ?ReplaySujet $replaySujet = null;

  

    // #[ORM\ManyToOne(inversedBy: 'images')]
    // #[ORM\JoinColumn(nullable: false,referencedColumnName:"id",name:"user_id")]
    // private ?User $users = null;

   

   
  

  

    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getAticles(): ?Article
    {
        return $this->aticles;
    }

    public function setAticles(?Article $aticles): self
    {
        $this->aticles = $aticles;

        return $this;
    }

 

  

 

   

    public function getSpecialite(): ?Specialites
    {
        return $this->specialite;
    }

    public function setSpecialite(?Specialites $specialite): self
    {
        $this->specialite = $specialite;

        return $this;
    }

    public function getDossier(): ?Dossier
    {
        return $this->dossier;
    }

    public function setDossier(?Dossier $dossier): self
    {
        $this->dossier = $dossier;

        return $this;
    }

    public function getReplaySujet(): ?ReplaySujet
    {
        return $this->replaySujet;
    }

    public function setReplaySujet(?ReplaySujet $replaySujet): self
    {
        $this->replaySujet = $replaySujet;

        return $this;
    }

   
   

   
   
    

    

    
    

  

   

   
}
