<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $date;

    #[ORM\Column(type: 'decimal', scale: 2)]
    private float $montant;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $user;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $beneficiaire;

    // Getter et Setter pour id
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter et Setter pour date
    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    // Getter et Setter pour montant
    public function getMontant(): float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;
        return $this;
    }

    // Getter et Setter pour user
    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    // Getter et Setter pour beneficiaire
    public function getBeneficiaire(): User
    {
        return $this->beneficiaire;
    }

    public function setBeneficiaire(User $beneficiaire): self
    {
        $this->beneficiaire = $beneficiaire;
        return $this;
    }
}
