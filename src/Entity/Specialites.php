<?php

namespace App\Entity;

use App\Repository\SpecialitesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SpecialitesRepository::class)]
class Specialites
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'specialites', targetEntity: Medecin::class)]
    private Collection $medecin;

    #[ORM\OneToMany(mappedBy: 'specialite', targetEntity: Images::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $images;

    #[ORM\OneToMany(mappedBy: 'specialites', targetEntity: Sujet::class)]
    private Collection $sujets;

    #[ORM\OneToMany(mappedBy: 'specialites', targetEntity: Article::class)]
    private Collection $articles;

    public function __construct()
    {
        $this->medecin = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->sujets = new ArrayCollection();
        $this->articles = new ArrayCollection();
    }


   

   

  

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

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
     * @return Collection<int, Medecin>
     */
    public function getMedecin(): Collection
    {
        return $this->medecin;
    }

    public function addMedecin(Medecin $medecin): self
    {
        if (!$this->medecin->contains($medecin)) {
            $this->medecin->add($medecin);
            $medecin->setSpecialites($this);
        }

        return $this;
    }

    public function removeMedecin(Medecin $medecin): self
    {
        if ($this->medecin->removeElement($medecin)) {
            // set the owning side to null (unless already changed)
            if ($medecin->getSpecialites() === $this) {
                $medecin->setSpecialites(null);
            }
        }

        return $this;
    }

    

   public function __toString(): string{
    return (string)$this->nom;
   }

   /**
    * @return Collection<int, Images>
    */
   public function getImages(): Collection
   {
       return $this->images;
   }

   public function addImage(Images $image): self
   {
       if (!$this->images->contains($image)) {
           $this->images->add($image);
           $image->setSpecialite($this);
       }

       return $this;
   }

   public function removeImage(Images $image): self
   {
       if ($this->images->removeElement($image)) {
           // set the owning side to null (unless already changed)
           if ($image->getSpecialite() === $this) {
               $image->setSpecialite(null);
           }
       }

       return $this;
   }

   /**
    * @return Collection<int, Sujet>
    */
   public function getSujets(): Collection
   {
       return $this->sujets;
   }

   public function addSujet(Sujet $sujet): self
   {
       if (!$this->sujets->contains($sujet)) {
           $this->sujets->add($sujet);
           $sujet->setSpecialites($this);
       }

       return $this;
   }

   public function removeSujet(Sujet $sujet): self
   {
       if ($this->sujets->removeElement($sujet)) {
           // set the owning side to null (unless already changed)
           if ($sujet->getSpecialites() === $this) {
               $sujet->setSpecialites(null);
           }
       }

       return $this;
   }

   /**
    * @return Collection<int, Article>
    */
   public function getArticles(): Collection
   {
       return $this->articles;
   }

   public function addArticle(Article $article): self
   {
       if (!$this->articles->contains($article)) {
           $this->articles->add($article);
           $article->setSpecialites($this);
       }

       return $this;
   }

   public function removeArticle(Article $article): self
   {
       if ($this->articles->removeElement($article)) {
           // set the owning side to null (unless already changed)
           if ($article->getSpecialites() === $this) {
               $article->setSpecialites(null);
           }
       }

       return $this;
   }
}
