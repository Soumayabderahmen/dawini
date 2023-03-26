<?php

namespace App\Entity;

use App\Repository\ConsulationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
#[ORM\Entity(repositoryClass: ConsulationRepository::class)]
class Consulation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getConsul"])]
    private ?int $id = null;


    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(["getConsul"])]
    private ?\DateTimeInterface $date = null;

   

    #[ORM\ManyToOne(inversedBy: 'consulation')]
    private ?Certificat $certificat = null;

    #[ORM\ManyToOne(inversedBy: 'consulations')]
    private ?Patient $patients = null;

    #[ORM\ManyToOne(inversedBy: 'consulations')]
    private ?Medecin $medecin = null;

    #[ORM\OneToMany(mappedBy: 'consultation', targetEntity: Diagnostique::class)]
    private Collection $diagnostiques;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups("getConsul")]
    private ?\DateTimeInterface $heuredebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups("getConsul")]
    private ?\DateTimeInterface $heurefin = null;

    #[ORM\OneToMany(mappedBy: 'consulation', targetEntity: Ordonnance::class)]
    #[Groups("getConsul")]
    private Collection $ordonnance;

    #[ORM\Column(length: 255)]
    private ?string $urlConsultation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $estTermine = null;

    public function __construct()
    { 
        $this->date= new \DateTime();
        $this->heuredebut= new \DateTime();
        $this->heurefin= new \DateTime();

        $this->diagnostiques = new ArrayCollection();
        $this->ordonnance = new ArrayCollection();
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

   

    public function getCertificat(): ?Certificat
    {
        return $this->certificat;
    }

    public function setCertificat(?Certificat $certificat): self
    {
        $this->certificat = $certificat;

        return $this;
    }

    public function getPatients(): ?Patient
    {
        return $this->patients;
    }

    public function setPatients(?Patient $patients): self
    {
        $this->patients = $patients;

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
            $diagnostique->setConsultation($this);
        }

        return $this;
    }

    public function removeDiagnostique(Diagnostique $diagnostique): self
    {
        if ($this->diagnostiques->removeElement($diagnostique)) {
            // set the owning side to null (unless already changed)
            if ($diagnostique->getConsultation() === $this) {
                $diagnostique->setConsultation(null);
            }
        }

        return $this;
    }
    public function __toString() {
        return $this->getHeuredebut() ? : 'Consulation';

    }

    public function getHeuredebut(): ?\DateTimeInterface
    {
        return $this->heuredebut;
    }

    public function setHeuredebut(\DateTimeInterface $heuredebut): self
    {
        $this->heuredebut = $heuredebut;

        return $this;
    }

    public function getHeurefin(): ?\DateTimeInterface
    {
        return $this->heurefin;
    }

    public function setHeurefin(\DateTimeInterface $heurefin): self
    {
        $this->heurefin = $heurefin;

        return $this;
    }

    /**
     * @return Collection<int, Ordonnance>
     */
    public function getOrdonnance(): Collection
    {
        return $this->ordonnance;
    }

    public function addOrdonnance(Ordonnance $ordonnance): self
    {
        if (!$this->ordonnance->contains($ordonnance)) {
            $this->ordonnance->add($ordonnance);
            $ordonnance->setConsulation($this);
        }

        return $this;
    }

    public function removeOrdonnance(Ordonnance $ordonnance): self
    {
        if ($this->ordonnance->removeElement($ordonnance)) {
            // set the owning side to null (unless already changed)
            if ($ordonnance->getConsulation() === $this) {
                $ordonnance->setConsulation(null);
            }
        }

        return $this;
    }

    public function getUrlConsultation(): ?string
    {
        return $this->urlConsultation;
    }

    public function setUrlConsultation(string $urlConsultation): self
    {
        $this->urlConsultation = $urlConsultation;

        return $this;
    }

    public function getEstTermine(): ?string
    {
        return $this->estTermine;
    }

    public function setEstTermine(?string $estTermine): self
    {
        $this->estTermine = $estTermine;

        return $this;
    }
}