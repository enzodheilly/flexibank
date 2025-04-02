<?php

// src/Entity/Ticket.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\TicketRepository;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
class Ticket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column(type: "string", length: 255)]
    private string $email;

    #[ORM\Column(type: "string", length: 255)]
    private string $subject;

    #[ORM\Column(type: "string", length: 255)]
    private string $priority;

    #[ORM\Column(type: "text")]
    private string $message;

    #[ORM\Column(type: "datetime_immutable")]
    private ?\DateTimeImmutable $submissionDate = null;

    // Nouveau champ status
    #[ORM\Column(type: "string", length: 50)]
    private string $status = 'En Attente';  // Définir "Pending" par défaut

    public function __construct($user = '', $email = '', $subject = '', $priority = '', $status = 'En Attente')
    {
        $this->user = $user;
        $this->email = $email;
        $this->subject = $subject;
        $this->priority = $priority;
        $this->date = new \DateTime();
        $this->status = $status;  // Initialiser le statut par défaut
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    public function getPriority(): string
    {
        return $this->priority;
    }

    public function setPriority(string $priority): self
    {
        $this->priority = $priority;
        return $this;
    }
    public function getSubmissionDate(): ?\DateTimeImmutable
    {
        return $this->submissionDate;
    }

    // Setter pour la date de soumission
    public function setSubmissionDate(\DateTimeImmutable $submissionDate): self
    {
        $this->submissionDate = $submissionDate;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    // Getter et Setter pour le champ status
    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }
}
