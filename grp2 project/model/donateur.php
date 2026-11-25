<?php
class Donateur
{
    private ?int $id_donateur;
    private string $nom;
    private string $email;
    private string $telephone;

    public function __construct(
        ?int $id_donateur,
        string $nom,
        string $email,
        string $telephone
    ) {
        $this->id_donateur = $id_donateur;
        $this->nom = $nom;
        $this->email = $email;
        $this->telephone = $telephone;
    }

    public function getIdDonateur(): ?int
    {
        return $this->id_donateur;
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