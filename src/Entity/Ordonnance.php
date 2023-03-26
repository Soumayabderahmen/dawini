<?php

namespace App\Entity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\OrdonnanceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrdonnanceRepository::class)]
class Ordonnance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("getConsul")]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(
       message: 'la description ne doit pas etre vide',
    )]
    #[Assert\Length(
        min: 5,
        max: 500,
        minMessage: 'La description doit être au moins  {{ limit }} caracteres',
        maxMessage: 'La description ne doit pas depasser {{ limit }} caracteres',
    )]
    #[Groups("getConsul")]
    private ?string $description = null;

   

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(inversedBy: 'ordonnance')]
    private ?Consulation $consulation = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;
    private ?string $imageFile = null;


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



    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getConsulation(): ?Consulation
    {
        return $this->consulation;
    }

    public function setConsulation(?Consulation $consulation): self
    {
        $this->consulation = $consulation;

        return $this;
    }

    public function __toString()
    {
        return $this->id;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    

    /**
     * Get the value of imageFile
     *
     * @return ?string
     */
    public function getImageFile(): ?string
    {
        return $this->imageFile;
    }
}