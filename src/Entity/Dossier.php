<?php

namespace App\Entity;

use App\Repository\DossierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
#[ORM\Entity(repositoryClass: DossierRepository::class)]
class Dossier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("Dossiers")]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups("Dossiers")]
    #[Assert\Numero(message:"le numero de dossier utilisé")]
    #[Assert\Positive(message:"le numero de dossier doit etre positive")]
    #[Assert\NotBlank(message : "le numero de dossier ne doit pas etre vide")]
    private ?int $numero = null;

    #[ORM\Column(length: 255)]
    #[Groups("Dossiers")]
    #[Assert\Code_apci(message:"le code apci est utilisé")]
    #[Assert\NotBlank(message : "le code apci ne doit pas etre vide")]
    #[Assert\Length(
        min : 9,
        max : 9,
        minMessage : "Le code apci doit avoir au minimun {{ limit }} caracteres ",
        maxMessage : "La code apci doit avoir au maximum {{ limit }} characters")]
    private ?string $code_APCI = null;

    #[ORM\Column(length: 255)]
    #[Groups("Dossiers")]
    #[Assert\NotBlank(message: "la description de doit pas etre vide ")]
    #[Assert\Length(
           min : 10,
           max : 500,
           minMessage : "La description doit avoir au minimun {{ limit }} caracteres ",
           maxMessage : "La description doit avoir au maximum {{ limit }} characters")]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'dossiers')]
    private ?Patient $patient = null;

    #[ORM\ManyToOne(inversedBy: 'dossiers')]
    private ?Medecin $medecin = null;

    #[ORM\OneToMany(mappedBy: 'dossier', targetEntity: Images::class,orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $images;

    #[ORM\OneToMany(mappedBy: 'dossiers', targetEntity: Diagnostique::class)]
    private Collection $diagnostiques;

  

    public function __toString(): string{
        return (string)$this->numero;
       }
    

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->diagnostiques = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getCodeAPCI(): ?string
    {
        return $this->code_APCI;
    }

    public function setCodeAPCI(string $code_APCI): self
    {
        $this->code_APCI = $code_APCI;

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

    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(?Patient $patient): self
    {
        $this->patient = $patient;

        return $this;
    }

    public function getMedecin(): ?Medecin
    {
        return $this->medecin;
    }

    public function setMedecin(?Medecin $medecin): self
    {
        $this->medecin = $medecin;

        return $this;
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
            $image->setDossier($this);
        }

        return $this;
    }

    public function removeImage(Images $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getDossier() === $this) {
                $image->setDossier(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Diagnostique>
     */
    public function getDiagnostiques(): Collection
    {
        return $this->diagnostiques;
    }

    public function addDiagnostique(Diagnostique $diagnostique): self
    {
        if (!$this->diagnostiques->contains($diagnostique)) {
            $this->diagnostiques->add($diagnostique);
            $diagnostique->setDossiers($this);
        }

        return $this;
    }

    public function removeDiagnostique(Diagnostique $diagnostique): self
    {
        if ($this->diagnostiques->removeElement($diagnostique)) {
            // set the owning side to null (unless already changed)
            if ($diagnostique->getDossiers() === $this) {
                $diagnostique->setDossiers(null);
            }
        }

        return $this;
    }

   
}