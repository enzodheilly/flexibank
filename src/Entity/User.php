<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\Column(length: 100)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(type: "json")]
    private array $roles = [];

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $paysDeResidence = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $profession = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adressePostale = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $codePostal = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $ville = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $numeroTelephone = null;

    #[ORM\Column(type: 'string', length: 20, unique: true)]
    private ?string $codeClient = null;

    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $createdAt = null;

    private ?string $plainPassword = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: BankAccount::class, orphanRemoval: true)]
    private Collection $accounts;
    
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Newsletter::class)]
    private Collection $newsletters;

    #[ORM\Column(type: "boolean")]
    private bool $isActive = true;  // Par défaut, on suppose que l'utilisateur est actif

    #[ORM\Column(length: 20)]
    private ?string $status = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: LoanRequest::class, orphanRemoval: true)]
    private Collection $loanRequests; 
    
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Ticket::class)]
    private $tickets;

    #[ORM\Column(type:"integer")]
    private $failedAttempts = 0; // Nombre de tentatives échouées

   
    #[ORM\Column(type:"datetime", nullable:true)]
    private $lockUntil; // Date de verrouillage

    public function __construct()
    {
        $this->roles[] = 'ROLE_USER';
        $this->createdAt = new \DateTime();
        $this->accounts = new ArrayCollection();
        $this->loanRequests = new ArrayCollection();
        $this->codeClient = strtoupper(substr(uniqid('CL-', true), 0, 20));
        $this->tickets = new ArrayCollection();
        $this->status = 'Actif';
        $this->newsletters = new ArrayCollection();
    }

    public function getAccounts(): Collection
    {
        return $this->accounts;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }
    
    public function getNewsletters(): Collection
    {
        return $this->newsletters;
    }
    
public function getIsActive(): bool
{
    return $this->isActive;
}

public function setIsActive(bool $isActive): self
{
    $this->isActive = $isActive;
    return $this;
}

public function getStatus(): ?string
{
    return $this->status;
}

public function setStatus(?string $status): self
{
    $this->status = $status;
    return $this;
}

public function getTotalBalance(): float
{
    $totalBalance = 0;
    
    // Parcourez les comptes bancaires associés à cet utilisateur
    foreach ($this->accounts as $account) {
        // Ajoutez le solde de chaque compte bancaire à la somme totale
        $totalBalance += $account->getBalance();
    }

    return $totalBalance;
}

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getCodeClient(): ?string
    {
        return $this->codeClient;
    }

    public function setCodeClient(string $codeClient): self
    {
        $this->codeClient = $codeClient;
        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
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

    public function getNumeroTelephone(): ?string
    {
        return $this->numeroTelephone;
    }

    public function setNumeroTelephone(?string $numeroTelephone): self
    {
        $this->numeroTelephone = $numeroTelephone;
        return $this;
    }

    public function getPaysDeResidence(): ?string
    {
        return $this->paysDeResidence;
    }

    public function setPaysDeResidence(?string $paysDeResidence): self
    {
        $this->paysDeResidence = $paysDeResidence;
        return $this;
    }

    public function getProfession(): ?string
    {
        return $this->profession;
    }

    public function setProfession(?string $profession): self
    {
        $this->profession = $profession;
        return $this;
    }

    public function isClient(): bool
    {
        return in_array('ROLE_CLIENT', $this->roles);
    }

    public function isClientComplete(): bool
    {
        return !empty($this->paysDeResidence) && !empty($this->profession) &&
               !empty($this->adressePostale) && !empty($this->codePostal) &&
               !empty($this->ville) && !empty($this->numeroTelephone);
    }


    public function getAdressePostale(): ?string
    {
        return $this->adressePostale;
    }

    public function setAdressePostale(?string $adressePostale): self
    {
        $this->adressePostale = $adressePostale;
        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(?string $codePostal): self
    {
        $this->codePostal = $codePostal;
        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): self
    {
        $this->ville = $ville;
        return $this;
    }

    public function getTickets(): ArrayCollection
    {
        return $this->tickets;
    }

    public function getLoanRequests(): Collection
    {
        return $this->loanRequests;
    }

    public function addLoanRequest(LoanRequest $loanRequest): self
    {
        if (!$this->loanRequests->contains($loanRequest)) {
            $this->loanRequests[] = $loanRequest;
            $loanRequest->setUser($this);  // Associe cette LoanRequest à l'utilisateur
        }

        return $this;
    }

    public function removeLoanRequest(LoanRequest $loanRequest): self
    {
        if ($this->loanRequests->contains($loanRequest)) {
            $this->loanRequests->removeElement($loanRequest);
            if ($loanRequest->getUser() === $this) {
                $loanRequest->setUser(null);  // Déassocie la LoanRequest de cet utilisateur
            }
        }

        return $this;
    }

    public function getFailedAttempts(): ?int
    {
        return $this->failedAttempts;
    }

    public function setFailedAttempts(int $failedAttempts): self
    {
        $this->failedAttempts = $failedAttempts;

        return $this;
    }

    // Getter et Setter pour lockUntil
    public function getLockUntil(): ?\DateTimeInterface
    {
        return $this->lockUntil;
    }

    public function setLockUntil(?\DateTimeInterface $lockUntil): self
    {
        $this->lockUntil = $lockUntil;

        return $this;
    }

}
