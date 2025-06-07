<?php

// src/Entity/LoanRequest.php

namespace App\Entity;

use App\Repository\LoanRequestRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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

    #[ORM\Column(type: 'string', options: ["default" => "En Attente"])] // Définition de la valeur par défaut
    #[Assert\NotBlank(message: 'Le statut est obligatoire')]
    private $status = 'En Attente';  // Valeur par défaut dans le code

    #[ORM\Column(type: 'float', nullable: true)] // Optionnel : si tu veux stocker la mensualité
    private $monthlyPayment;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private $user;

    #[ORM\Column(type:"datetime")]
    private $requestDate;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getMonthlyPayment(): ?float
    {
        return $this->monthlyPayment;
    }

    public function setMonthlyPayment(float $monthlyPayment): self
    {
        $this->monthlyPayment = $monthlyPayment;
        return $this;
    }

    // Ajoutez les getters et setters pour user
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getRequestDate(): ?\DateTimeInterface
    {
        return $this->requestDate;
    }

    // Setter pour requestDate
    public function setRequestDate(\DateTimeInterface $requestDate): self
    {
        $this->requestDate = $requestDate;
        return $this;
    }
}
