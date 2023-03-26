<?php

namespace App\Entity;

use App\Repository\DiagnostiqueRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: DiagnostiqueRepository::class)]
class Diagnostique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message : "le champs date de diagnostique ne doit pas etre vide")]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message : "le champs symptome ne doit pas etre vide")]
    #[Assert\Length(
           min : 3,
           max : 500,
           minMessage : "Symptome doit avoir au minimun {{ limit }} caracteres ",
           maxMessage : "Symptome doit avoir au maximum {{ limit }} caracteres"
      )
     ]
    private ?string $symptome = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
           min : 3,
           max : 500,
           minMessage : "le resultat de test doit avoir au minimun {{ limit }} caracteres ",
           maxMessage : "le resultat de test doit avoir au maximum {{ limit }} caracteres"
      )
    ]
    private ?string $resultat_test = null;

    #[ORM\Column(length: 255)] 
    #[Assert\NotBlank(message : "le diagnostique final ne doit pas etre vide")]
    #[Assert\Length(
           min : 3,
           max : 500,
           minMessage : "le diagnostique finale doit avoir au minimun {{ limit }} caracteres ",
           maxMessage : "le diagnostique finale doit avoir au maximum {{ limit }} caracteres"
      )
    ]
    private ?string $diag_final = null;

    #[ORM\ManyToOne(inversedBy: 'diagnostiques')]
    private ?Patient $patient = null;

    #[ORM\ManyToOne(inversedBy: 'diagnostiques')]
    private ?Consulation $consultation = null;

    #[ORM\ManyToOne(inversedBy: 'diagnostiques')]
    private ?Dossier $dossiers = null;

    

 

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getSymptome(): ?string
    {
        return $this->symptome;
    }

    public function setSymptome(string $symptome): self
    {
        $this->symptome = $symptome;

        return $this;
    }

    public function getResultatTest(): ?string
    {
        return $this->resultat_test;
    }

    public function setResultatTest(string $resultat_test): self
    {
        $this->resultat_test = $resultat_test;

        return $this;
    }

    public function getDiagFinal(): ?string
    {
        return $this->diag_final;
    }

    public function setDiagFinal(string $diag_final): self
    {
        $this->diag_final = $diag_final;

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

    public function getConsultation(): ?Consulation
    {
        return $this->consultation;
    }

    public function setConsultation(?Consulation $consultation): self
    {
        $this->consultation = $consultation;

        return $this;
    }

    public function getDossiers(): ?Dossier
    {
        return $this->dossiers;
    }

    public function setDossiers(?Dossier $dossiers): self
    {
        $this->dossiers = $dossiers;

        return $this;
    }

    
}
