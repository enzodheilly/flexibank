<?php

// src/Entity/Transfer.php

namespace App\Entity;

use App\Repository\TransferRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\BankAccount; // Assurez-vous d'importer l'entité BankAccount

#[ORM\Entity(repositoryClass: TransferRepository::class)]
#[ORM\HasLifecycleCallbacks] // Permet d'utiliser les callbacks comme PrePersist
class Transfer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Relation ManyToOne avec BankAccount pour le compte source
    #[ORM\ManyToOne(targetEntity: BankAccount::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')] // Ajout de onDelete="CASCADE"
    #[Assert\NotBlank]
    private ?BankAccount $fromAccount = null;

    // Relation ManyToOne avec BankAccount pour le compte destinataire
    #[ORM\ManyToOne(targetEntity: BankAccount::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')] // Ajout de onDelete="CASCADE"
    #[Assert\NotBlank]
    private ?BankAccount $toAccount = null;

    // Montant du virement
    #[ORM\Column(type: "decimal", precision: 10, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    private ?float $amount = null;

    // Description du virement, obligatoire
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'La description ne peut pas être vide.')]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)] // Permet à "status" d'être nul
    private ?string $status = null;    

    // Date du virement (remplaçant createdAt)
    #[ORM\Column(type: "datetime_immutable")]
    private ?\DateTimeImmutable $date = null;

    #[ORM\Column(type:"string", length:255, nullable:true)]
    private ?string $type = null;

    // Constructeur initialisant la date du virement
    public function __construct()
    {
        $this->date = new \DateTimeImmutable();
    }

    // Callback PrePersist pour gérer la date avant l'enregistrement
    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        if ($this->date === null) {
            $this->date = new \DateTimeImmutable();
        }
    
        // Si le statut n'est pas défini, on le met par défaut à "en attente"
        if ($this->status === null) {
            $this->status = 'En Attente';
        }
    }

    // Getter et setter pour l'ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter et setter pour fromAccount
    public function getFromAccount(): ?BankAccount
    {
        return $this->fromAccount;
    }

    public function setFromAccount(BankAccount $fromAccount): static
    {
        $this->fromAccount = $fromAccount;
        return $this;
    }

    // Getter et setter pour toAccount
    public function getToAccount(): ?BankAccount
    {
        return $this->toAccount;
    }
    
    public function setToAccount(BankAccount $toAccount): static
    {
        $this->toAccount = $toAccount;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;
        return $this;
    }

    // Getter et setter pour le montant
    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;
        return $this;
    }

    // Getter et setter pour la description
    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    // Getter et setter pour la date
    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): static
    {
        $this->date = $date;
        return $this;
    }

    // Méthode __toString() pour afficher de manière conviviale le virement
    public function __toString(): string
    {
        return "Virement de " . number_format($this->amount, 2) . "€ de " . $this->fromAccount->getAccountNumber() . " à " . $this->toAccount->getAccountNumber();
    }
}
