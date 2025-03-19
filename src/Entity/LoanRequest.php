<?php

// src/Entity/LoanRequest.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LoanRequestRepository::class)]
class LoanRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'float')]
    #[Assert\NotBlank(message: 'Le montant est obligatoire')]
    #[Assert\Positive(message: 'Le montant doit être positif')]
    private $amount;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank(message: 'La durée est obligatoire')]
    #[Assert\Range(min: 1, max: 360, notInRangeMessage: 'La durée doit être comprise entre 1 et 360 mois')]
    private $duration;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: 'Le type de prêt est obligatoire')]
    private $loanType;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: 'La profession est obligatoire')]
    private $profession;

    // Getters and Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;
        return $this;
    }

    public function getLoanType(): ?string
    {
        return $this->loanType;
    }

    public function setLoanType(string $loanType): self
    {
        $this->loanType = $loanType;
        return $this;
    }

    public function getProfession(): ?string
    {
        return $this->profession;
    }

    public function setProfession(string $profession): self
    {
        $this->profession = $profession;
        return $this;
    }
}
