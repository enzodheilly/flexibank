<?php

// src/Entity/User.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
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

    // Champs spécifiques au client, mais optionnels au début
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

    #[ORM\Column(type: 'string', length: 10, unique: true)]
    private ?string $codeClient = null;

    // Propriété de la date de création
    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $profilePicture = null; // Propriété pour la photo de profil

    private ?string $plainPassword = null;

    public function __construct()
    {
        $this->roles[] = 'ROLE_USER';
        $this->createdAt = new \DateTime();  // Initialise la date de création au moment de la création de l'utilisateur
    }

    // Getters et setters pour la propriété createdAt
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    // Implémentation des méthodes de l'interface UserInterface
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function eraseCredentials(): void
    {
        // Effacer les données sensibles comme le mot de passe en clair
        $this->plainPassword = null;
    }

    // Implémentation de la méthode de l'interface PasswordAuthenticatedUserInterface
    public function getPassword(): ?string
    {
        return $this->password;
    }

    // Getters et setters pour les autres propriétés...
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
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

    public function getNumeroTelephone(): ?string
    {
        return $this->numeroTelephone;
    }

    public function setNumeroTelephone(?string $numeroTelephone): self
    {
        $this->numeroTelephone = $numeroTelephone;
        return $this;
    }

    public function isClient(): bool
    {
        // Vérifie si l'utilisateur a toutes les informations nécessaires pour être un client
        return !empty($this->paysDeResidence) && !empty($this->profession) && 
               !empty($this->adressePostale) && !empty($this->codePostal) && 
               !empty($this->ville) && !empty($this->numeroTelephone);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    // Setter
    public function setProfilePicture(?string $profilePicture): self
    {
        $this->profilePicture = $profilePicture;
        return $this;
    }

    // Méthode pour obtenir l'URL de la photo de profil
    public function getProfilePictureUrl(): ?string
{
    if ($this->profilePicture) {
        return '/uploads/profile_pictures/' . $this->profilePicture;
    }

    return null; // ou un chemin par défaut si l'image n'existe pas
}

public function getCodeClient(): ?string
{
    return $this->codeClient;
}

public function setCodeClient(?string $codeClient): self
{
    $this->codeClient = $codeClient;
    return $this;
}

}
