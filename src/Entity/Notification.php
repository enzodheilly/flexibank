<?php

// src/Entity/Notification.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;

#[ORM\Entity(repositoryClass: "App\Repository\NotificationRepository")]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string")]
    private $description;  // Remplacer message par description

    #[ORM\Column(type: "datetime")]
    private $createdAt;

    #[ORM\Column(type: "boolean")]
    private $isRead = false;

    #[ORM\Column(type: "boolean")]
    private $isNotRead = true; 

    // Relation avec User avec suppression en cascade
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'notifications')]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]  // Suppression en cascade
    private $user;

    // Constructeur pour définir la date par défaut
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string  // Renommer message en description
    {
        return $this->description;
    }

    public function setDescription(string $description): self  // Renommer setMessage en setDescription
    {
        $this->description = $description;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getIsRead(): ?bool
    {
        return $this->isRead;
    }

    public function setIsRead(bool $isRead): self
    {
        $this->isRead = $isRead;
        $this->isNotRead = !$isRead;  // Quand on marque comme lu, isNotRead devient false
        return $this;
    }

    public function getIsNotRead(): ?bool
    {
        return $this->isNotRead;
    }

    public function setIsNotRead(bool $isNotRead): self
    {
        $this->isNotRead = $isNotRead;
        // Si on marque la notification comme non lue, on marque isRead à false
        if ($isNotRead) {
            $this->isRead = false;
        }
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
}
