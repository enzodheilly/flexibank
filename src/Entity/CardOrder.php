<?php

// src/Entity/CardOrder.php
namespace App\Entity;

use App\Repository\CardOrderRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CardOrderRepository::class)]
class CardOrder
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $fullName = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $birthDate = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $email = null;

    #[ORM\Column(type: 'string', length: 20)]
    private ?string $phone = null;

    #[ORM\Column(type: 'string', length: 20)]
    private ?string $cardType = 'basic'; // Le type de carte

    #[ORM\Column(type: 'string', length: 19)] // Si tu veux une longueur de 19 caractères pour le numéro de carte
    private ?string $cardNumber = null;    

    #[ORM\Column(type: 'string', length: 3)]
    private ?string $ccv = null; // Le code de sécurité CCV

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $expirationDate = null; // La date d'expiration

    #[ORM\Column(type: 'string', length: 20)]
    private ?string $status = 'En attente'; // Modifier la valeur par défaut à 'En attente'    

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt;

    #[ORM\Column(type: "string", nullable: true)]  // Ajouter 'nullable' pour permettre à ce champ d'être nul
    private ?string $type = null; // Type nullable, avec valeur par défaut à null    

    // Relation avec l'utilisateur
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'cardOrders')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')] // Ajout de 'onDelete' pour suppression en cascade
    private ?User $user = null;
    
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    // Getters & Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;
        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeInterface $birthDate): self
    {
        $this->birthDate = $birthDate;
        return $this;
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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getCardType(): ?string
    {
        return $this->cardType;
    }

    public function setCardType(string $cardType): self
    {
        $this->cardType = $cardType;
        return $this;
    }

    public function getCardNumber(): ?string
    {
        return $this->cardNumber;
    }

    public function setCardNumber(string $cardNumber): self
    {
        $this->cardNumber = $cardNumber;
        return $this;
    }

    public function getCcv(): ?string
    {
        return $this->ccv;
    }

    public function setCcv(string $ccv): self
    {
        $this->ccv = $ccv;
        return $this;
    }

    public function getExpirationDate(): ?\DateTimeInterface
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(\DateTimeInterface $expirationDate): self
    {
        $this->expirationDate = $expirationDate;
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    // Getter et setter pour l'utilisateur
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }    

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }
}
