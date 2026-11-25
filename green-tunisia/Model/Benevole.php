<?php

declare(strict_types=1);

/**
 * Entité Benevole - Modèle objet (POO)
 * Représente les bénévoles liés aux participations (entité partagée avec Étudiant 3).
 */
class Benevole
{
    private ?int $id_benevole;
    private string $nom;
    private string $prenom;
    private string $email;
    private ?string $telephone;

    public function __construct(
        ?int $id_benevole = null,
        string $nom = '',
        string $prenom = '',
        string $email = '',
        ?string $telephone = null
    ) {
        $this->id_benevole = $id_benevole;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->telephone = $telephone;
    }

    public function getIdBenevole(): ?int
    {
        return $this->id_benevole;
    }

    public function setIdBenevole(?int $id_benevole): void
    {
        $this->id_benevole = $id_benevole;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): void
    {
        $this->nom = trim($nom);
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): void
    {
        $this->prenom = trim($prenom);
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = strtolower(trim($email));
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): void
    {
        $this->telephone = $telephone !== null ? trim($telephone) : null;
    }

    public function getNomComplet(): string
    {
        return trim($this->prenom . ' ' . $this->nom);
    }
}
