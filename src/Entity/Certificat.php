<?php

namespace App\Entity;

use App\Repository\CertificatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CertificatRepository::class)]
class Certificat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'certificat', targetEntity: Consulation::class)]
    private Collection $consulation;

    public function __construct()
    {
        $this->consulation = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * @return Collection<int, Consulation>
     */
    public function getConsulation(): Collection
    {
        return $this->consulation;
    }

    public function addConsulation(Consulation $consulation): self
    {
        if (!$this->consulation->contains($consulation)) {
            $this->consulation->add($consulation);
            $consulation->setCertificat($this);
        }

        return $this;
    }

    public function removeConsulation(Consulation $consulation): self
    {
        if ($this->consulation->removeElement($consulation)) {
            // set the owning side to null (unless already changed)
            if ($consulation->getCertificat() === $this) {
                $consulation->setCertificat(null);
            }
        }

        return $this;
    }
}
