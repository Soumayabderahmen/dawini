<?php

namespace App\Entity;

use App\Repository\AssistantRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AssistantRepository::class)]
class Assistant extends User
{
    #[ORM\ManyToOne(inversedBy: 'assistant')]
    private ?Medecin $medecin = null;

    public function getMedecin(): ?Medecin
    {
        return $this->medecin;
    }

    public function setMedecin(?Medecin $medecin): self
    {
        $this->medecin = $medecin;

        return $this;
    }
}
