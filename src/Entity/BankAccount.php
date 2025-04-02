<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: \App\Repository\BankAccountRepository::class)]
class BankAccount
{
    const ACCOUNT_TYPE_COURANT = 'Courant';
    const ACCOUNT_TYPE_EPARGNE = 'Epargne';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 20, unique: true)]
    #[Assert\Length(min: 20, max: 20, exactMessage: "L'IBAN doit contenir exactement 20 caractères.")]
    #[Assert\Regex(pattern: "/^FR\\d{18}$/", message: "Le format de l'IBAN est invalide.")]
    private $iban;
    
    // Type de compte : Courant ou Epargne
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Choice(choices: [self::ACCOUNT_TYPE_COURANT, self::ACCOUNT_TYPE_EPARGNE], message: "Le type de compte doit être 'Courant' ou 'Epargne'.")]
    private $accountType;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'accounts')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private $user;    
    
    // Balance avec précision décimale
    #[ORM\Column(type: 'decimal', precision: 15, scale: 2)]
    private $balance = 0.0;

    // Relation OneToMany pour les virements reçus (virements où ce compte est le destinataire)
    #[ORM\OneToMany(mappedBy: 'toAccount', targetEntity: Transfer::class, orphanRemoval: true)]
    private $incomingTransfers;

    // Relation OneToMany pour les virements envoyés (virements où ce compte est le compte source)
    #[ORM\OneToMany(mappedBy: 'fromAccount', targetEntity: Transfer::class, orphanRemoval: true)]
    private $outgoingTransfers;

    public function __construct()
    {
        $this->iban = $this->generateIban();
        $this->balance = 0.0;
        $this->incomingTransfers = new ArrayCollection();
        $this->outgoingTransfers = new ArrayCollection();
    }

    // Générer un IBAN valide
    public function generateIban(): string
    {
        $countryCode = 'FR';
        $bankCode = '30003'; // Exemple de code banque
        $branchCode = '00459'; // Exemple de code agence
        $accountNumber = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT); // 6 chiffres pour le numéro de compte
    
        // La clé de contrôle initiale est 00
        $ibanWithoutKey = $countryCode . '00' . $bankCode . $branchCode . $accountNumber;
    
        // Pour calculer la clé de contrôle, tu dois manipuler l'IBAN sans la clé (ici "FR00")
        $ibanRearranged = substr($ibanWithoutKey, 4) . 'FR00'; // Le code pays et la clé de contrôle seront déplacés
        $ibanNumeric = '';
    
        // Remplacer les lettres par des chiffres (A=10, B=11,..., Z=35)
        $ibanRearranged = strtoupper($ibanRearranged);
        foreach (str_split($ibanRearranged) as $char) {
            if (ctype_alpha($char)) {
                $ibanNumeric .= ord($char) - 55; // Convertit chaque lettre en son équivalent numérique
            } else {
                $ibanNumeric .= $char;
            }
        }
    
        // Calcul de la clé de contrôle en modulo 97
        $ibanKey = 98 - (intval($ibanNumeric) % 97);
        
        // On retourne l'IBAN complet avec la clé de contrôle
        return $countryCode . str_pad($ibanKey, 2, '0', STR_PAD_LEFT) . substr($ibanWithoutKey, 4);
    }

    // Getters et Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIban(): ?string
    {
        return $this->iban;
    }

    // Setter pour l'IBAN
    public function setIban(string $iban): self
    {
        $this->iban = $iban;

        return $this; // Permet le chaînage des appels de méthode
    }

    public function getAccountType(): ?string
    {
        return $this->accountType;
    }

    public function setAccountType(string $accountType): self
    {
        $this->accountType = $accountType;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getBalance(): float
    {
        return (float)$this->balance;
    }

    public function setBalance(float $balance): self
    {
        $this->balance = $balance;
        return $this;
    }

    // Ajouter une méthode pour calculer les intérêts
    public function applyInterest(float $rate): void
    {
        if ($this->accountType === self::ACCOUNT_TYPE_EPARGNE) {
            $this->balance += $this->balance * ($rate / 100);
        }
    }
}
