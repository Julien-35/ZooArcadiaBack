<?php

namespace App\Entity;

use App\Repository\AnimalRepository;
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

    #[ORM\ManyToOne]
    private ?Race $Race = null;

    #[ORM\ManyToOne]
    private ?Habitat $Habitat = null;

    #[ORM\ManyToOne]
    private ?Nourriture $Nourriture = null;


    public function __construct()
    {
        $this->races = new ArrayCollection();
        $this->habitats = new ArrayCollection();
        $this->nourritures = new ArrayCollection();

    }

    public function getId(): ?int
    {
        return $this->id;
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


    public function getHabitat(): ?Habitat
    {
        return $this->Habitat;
    }

    public function setHabitat(?Habitat $Habitat): static
    {
        $this->Habitat = $Habitat;

        return $this;
    }

    public function getRace(): ?Race
    {
        return $this->Race;
    }

    public function setRace(?Race $Race): static
    {
        $this->Race = $Race;

        return $this;
    }

    
    public function getNourriture(): ?Nourriture
    {
        return $this->Nourriture;
    }

    public function setNourriture(?Nourriture $Nourriture): static
    {
        $this->Nourriture = $Nourriture;

        return $this;
    }

}