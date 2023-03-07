<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Console\Color;
use Doctrine\ORM\Mapping\InheritanceType;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\DiscriminatorColumn;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[InheritanceType('JOINED')]
#[DiscriminatorColumn(name: 'Type', type: 'string')]
#[DiscriminatorMap(['user' => User::class, 'admin' => Admin::class,'patient'=>Patient::class,'assistant'=>Assistant::class ,'medecin'=>Medecin::class])]
#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé. Veuillez en choisir un autre.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('medecin')]
    private ?int $id = null;
    
    #[ORM\Column(length: 180, unique: true)]
   // #[Assert\NotBlank(message: 'Veuillez renseigner ce champ')]
    #[Assert\Regex(
            pattern:'/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            message:'L\'email {{ value }} n\'est pas un email valide.',
         )]
         #[Groups('medecin')]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups('medecin')]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    // #[Assert\NotBlank(message: 'Veuillez renseigner ce champ')]
    // #[Assert\Length(
    //     min: 6,
    //     minMessage: 'Votre mot de passe doit comporter au minimum {{ limit }} caractères',
       
    // )]
    #[Groups('medecin')]
    private ?string $password = null;


    
    #[ORM\Column(length: 255)]
    #[Groups('medecin')]
    private ?string $nom = null;
    
    #[ORM\Column(length: 255)]
    #[Groups('medecin')]
    private ?string $prenom = null;
   
    #[ORM\Column]
    //#[Assert\NotBlank(message: 'Veuillez renseigner ce champ')]
    #[Assert\Length(
        
        min: 8,
        max: 8,
        exactMessage: 'Cette champ doit comporter exactement 8 caractères',
       
    )]
    #[Groups('medecin')]
    private ?int $cin = null;

    #[ORM\Column(length: 255)]
    #[Groups('medecin')]
    private ?string $sexe = null;

    #[ORM\Column]
   // #[Assert\NotBlank(message: 'Veuillez renseigner ce champ')]
    #[Assert\Length(
        
        min: 8,
        max: 8,
        exactMessage: 'Cette champ doit comporter exactement 8 caractères',
       
    )]
    #[Groups('medecin')]
    private ?String $telephone = null;

    #[ORM\Column(length: 255)]
    #[Groups('medecin')]
    private ?string $gouvernorat = null;

    #[ORM\Column(length: 255)]
   // #[Assert\NotBlank(message: 'Veuillez renseigner ce champ')]
    #[Assert\Length(
        min: 8,
        minMessage: 'Cette champ doit comporter au moins 8 caractères',
       
    )]
    #[Groups('medecin')]
    private ?string $adresse = null;
   
    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    // #[Assert\NotBlank(message: 'Veuillez renseigner ce champ')]
    // #[Assert\EqualTo(
    //     propertyPath : 'password',
    //     message :'Vous n\'avez pas saisi le même mot de passe !',
    //     )]
    #[Groups('medecin')]
    private $confirm_password=null;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Commentaire::class)]
    private Collection $commentaires;

  

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('medecin')]
    private ?string $image = null;

   
    #[ORM\Column(length: 180, nullable: true)]
    #[Groups('medecin')]
    private ?string $reset_token;

    #[ORM\Column(nullable: true)]
    private ?bool $enabled = null;
    
    #[ORM\Column(length: 255, nullable: true)]
    private  ?string $token;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: SujetLike::class)]
    private Collection $sujetLikes;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Sujet::class)]
    private Collection $sujets;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: ReplaySujet::class)]
    private Collection $replaySujets;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ArticleFavorie::class)]
    private Collection $articleFavories;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ArticleLike::class)]
    private Collection $articleLikes;
    

   
   

    // #[ORM\OneToMany(mappedBy: 'users', targetEntity: Images::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    // private Collection $images;

    // #[ORM\OneToMany(mappedBy: 'images', targetEntity: Images::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    // private Collection $images;

   
  
   
  

    public function __construct()
    {
       
       
        $this->commentaires = new ArrayCollection();
       
        $this->enabled = false;
        $this->sujetLikes = new ArrayCollection();
        $this->sujets = new ArrayCollection();
        $this->replaySujets = new ArrayCollection();
        $this->articleFavories = new ArrayCollection();
        $this->articleLikes = new ArrayCollection();
     
       
       
       
       
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    
    public function __toString(): string{
        return (string)$this->nom.' '.(string)$this->prenom;
       }
    
    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
         return $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
       
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getConfirmPassword()
    {
      return $this->confirm_password;
    }
    public function setConfirmPassword($confirm_password)
    {
    $this->confirm_password = $confirm_password;
    return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getCin(): ?int
    {
        return $this->cin;
    }

    public function setCin(int $cin): self
    {
        $this->cin = $cin;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): self
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getTelephone(): ?String
    {
        return $this->telephone;
    }

    public function setTelephone(String $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getGouvernorat(): ?string
    {
        return $this->gouvernorat;
    }

    public function setGouvernorat(string $gouvernorat): self
    {
        $this->gouvernorat = $gouvernorat;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setUtilisateur($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getUtilisateur() === $this) {
                $commentaire->setUtilisateur(null);
            }
        }

        return $this;
    }

   
    

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }
      /**
     * @return mixed
     */
    public function getResetToken()
    {
        return $this->reset_token;
    }

    /**
     * @param mixed $reset_token
     */
    public function setResetToken($reset_token): void
    {
        $this->reset_token = $reset_token;
    }

   

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(?bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    
    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

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
            $sujetLike->setUser($this);
        }

        return $this;
    }

    public function removeSujetLike(SujetLike $sujetLike): self
    {
        if ($this->sujetLikes->removeElement($sujetLike)) {
            // set the owning side to null (unless already changed)
            if ($sujetLike->getUser() === $this) {
                $sujetLike->setUser(null);
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
            $sujet->setUtilisateur($this);
        }

        return $this;
    }

    public function removeSujet(Sujet $sujet): self
    {
        if ($this->sujets->removeElement($sujet)) {
            // set the owning side to null (unless already changed)
            if ($sujet->getUtilisateur() === $this) {
                $sujet->setUtilisateur(null);
            }
        }

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
            $replaySujet->setUtilisateur($this);
        }

        return $this;
    }

    public function removeReplaySujet(ReplaySujet $replaySujet): self
    {
        if ($this->replaySujets->removeElement($replaySujet)) {
            // set the owning side to null (unless already changed)
            if ($replaySujet->getUtilisateur() === $this) {
                $replaySujet->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ArticleFavorie>
     */
    public function getArticleFavories(): Collection
    {
        return $this->articleFavories;
    }

    public function addArticleFavory(ArticleFavorie $articleFavory): self
    {
        if (!$this->articleFavories->contains($articleFavory)) {
            $this->articleFavories->add($articleFavory);
            $articleFavory->setUser($this);
        }

        return $this;
    }

    public function removeArticleFavory(ArticleFavorie $articleFavory): self
    {
        if ($this->articleFavories->removeElement($articleFavory)) {
            // set the owning side to null (unless already changed)
            if ($articleFavory->getUser() === $this) {
                $articleFavory->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ArticleLike>
     */
    public function getArticleLikes(): Collection
    {
        return $this->articleLikes;
    }

    public function addArticleLike(ArticleLike $articleLike): self
    {
        if (!$this->articleLikes->contains($articleLike)) {
            $this->articleLikes->add($articleLike);
            $articleLike->setUser($this);
        }

        return $this;
    }

    public function removeArticleLike(ArticleLike $articleLike): self
    {
        if ($this->articleLikes->removeElement($articleLike)) {
            // set the owning side to null (unless already changed)
            if ($articleLike->getUser() === $this) {
                $articleLike->setUser(null);
            }
        }

        return $this;
    }
}

   
   


    

   

 
  
  

    

   
 
    



  

       
    

 

