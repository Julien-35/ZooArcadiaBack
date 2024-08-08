<?php

namespace App\Entity;

use App\Repository\AvisRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AvisRepository::class)]
class Avis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(length: 50)]
    public ?string $pseudo = null;

    #[ORM\Column(length: 500)]
    public ?string $commentaire = null;

    #[ORM\Column(type: 'boolean')]  // Changez le type à boolean pour correspondre à tinyint(1)
    public ?bool $is_visible = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;
        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): static
    {
        $this->commentaire = $commentaire;
        return $this;
    }

    public function isIsVisible(): ?bool
    {
        return $this->is_visible; 
    }

    public function setIsVisible(bool $isVisible): static
    {
        $this->is_visible = $isVisible; 
        return $this;
    }
}
