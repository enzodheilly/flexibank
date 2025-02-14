<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class CardOrder
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $userEmail = null;

    #[ORM\Column(type: 'string', length: 50)]
    private ?string $cardType = null;

    #[ORM\Column(type: 'string', length: 19)]
    private ?string $cardNumber = null;

    #[ORM\Column(type: 'string', length: 3)]
    private ?string $ccv = null;

    #[ORM\Column(type: 'date')]  // Ou 'datetime' si vous avez besoin d'un timestamp complet
    private ?\DateTimeInterface $expirationDate = null;

    #[ORM\Column(type: 'string', length: 20)]
    private ?string $status = 'pending';

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $orderDate = null;

    public function __construct()
    {
        // Initialisation de la date de commande à la date actuelle
        $this->orderDate = new \DateTime();  
        // Valeur par défaut pour status
        $this->status = 'pending';
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserEmail(): ?string
    {
        return $this->userEmail;
    }

    public function setUserEmail(string $userEmail): self
    {
        $this->userEmail = $userEmail;
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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getOrderDate(): ?\DateTimeInterface
    {
        return $this->orderDate;
    }

    public function setOrderDate(\DateTimeInterface $orderDate): self
    {
        $this->orderDate = $orderDate;
        return $this;
    }
}
