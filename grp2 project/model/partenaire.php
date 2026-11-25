<?php
class Partenaire
{
    private ?int $id_partenaire;
    private string $nom;
    private string $email;
    private string $telephone;

    public function __construct(
        ?int $id_partenaire,
        string $nom,
        string $email,
        string $telephone
    ) {
        $this->id_partenaire = $id_partenaire;
        $this->nom = $nom;
        $this->email = $email;
        $this->telephone = $telephone;
    }

    // Getters
    public function getIdPartenaire(): ?int
    {
        return $this->id_partenaire;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getTelephone(): string
    {
        return $this->telephone;
    }

    // Setters
    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setTelephone(string $telephone): void
    {
        $this->telephone = $telephone;
    }
}