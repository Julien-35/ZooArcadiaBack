<?php

namespace App\Entity;

use Symfony\Component\HttpFoundation\File;
use App\Repository\AnimalRepository;
use Doctrine\DBAL\Types\Types;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnimalRepository::class)]
class Animal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $prenom = null;

    #[ORM\Column(length: 50)]
    private ?string $etat = null;

    #[ORM\Column(length: 50)]
    private ?string $nourriture = null;

    #[ORM\Column(length: 50)]
    private ?string $grammage = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $feeding_time = null;

    #[ORM\ManyToOne(targetEntity: Habitat::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Habitat $habitat = null;

    #[ORM\ManyToOne(targetEntity: Race::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Race $race = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, length: 500000)]
    private ?string $image_data = null;

    #[ORM\OneToMany(mappedBy: 'animal', targetEntity: RapportVeterinaire::class)]
    private Collection $rapportsVeterinaires;


    public function __construct()
    {
        $this->rapportsVeterinaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImageData(): ?string
    {
        return $this->image_data;
    }
    
    public function setImageData(?string $image_data): self
    {
        $this->image_data = $image_data;

        return $this;
    }
    
    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getNourriture(): ?string
    {
        return $this->nourriture;
    }

    public function setNourriture(string $nourriture): static
    {
        $this->nourriture = $nourriture;

        return $this;
    }

    public function getGrammage(): ?string
    {
        return $this->grammage;
    }

    public function setGrammage(string $grammage): static
    {
        $this->grammage = $grammage;

        return $this;
    }


    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }

    public function getFeedingTime(): ?\DateTimeInterface
    {
        return $this->feeding_time;
    }

    public function setFeedingTime(\DateTimeInterface $feeding_time): self
    {
        $this->feeding_time = $feeding_time;
        return $this;
    }

    public function getHabitat(): ?Habitat
    {
        return $this->habitat;
    }

    public function setHabitat(?Habitat $habitat): self
    {
        $this->habitat = $habitat;

        return $this;
    }

    public function getRace(): ?Race
    {
        return $this->race;
    }
    
    public function setRace(?Race $race): self
    {
        $this->race = $race;
    
        return $this;
    }

    public function getRapportsVeterinaires(): Collection
    {
        return $this->rapportsVeterinaires;
    }

    // public function addRapportVeterinaire(RapportVeterinaire $rapportVeterinaire): self
    // {
    //     if (!$this->rapportsVeterinaires->contains($rapportVeterinaire)) {
    //         $this->rapportsVeterinaires[] = $rapportVeterinaire;
    //         $rapportVeterinaire->setAnimal($this);
    //     }

    //     return $this;
    // }

    // public function removeRapportVeterinaire(RapportVeterinaire $rapportVeterinaire): self
    // {
    //     if ($this->rapportsVeterinaires->removeElement($rapportVeterinaire)) {
    //         if ($rapportVeterinaire->getAnimal() === $this) {
    //             $rapportVeterinaire->setAnimal(null);
    //         }
    //     }

    //     return $this;
    // }
 
}