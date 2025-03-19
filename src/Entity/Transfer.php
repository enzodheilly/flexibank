<?php

namespace App\Entity;

use App\Repository\TransferRepository;
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
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    private ?BankAccount $fromAccount = null;

    // Relation ManyToOne avec BankAccount pour le compte destinataire
    #[ORM\ManyToOne(targetEntity: BankAccount::class)]
    #[ORM\JoinColumn(nullable: false)]
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

    // Date du virement (remplaçant createdAt)
    #[ORM\Column(type: "datetime_immutable")]
    private ?\DateTimeImmutable $date = null;

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
