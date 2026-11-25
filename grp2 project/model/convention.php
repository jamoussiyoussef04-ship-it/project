<?php
class Convention
{
    private ?int $id_convention;
    private string $date_debut;
    private string $date_fin;
    private string $description;
    private int $id_partenaire;

    public function __construct(
        ?int $id_convention,
        string $date_debut,
        string $date_fin,
        string $description,
        int $id_partenaire
    ) {
        $this->id_convention = $id_convention;
        $this->date_debut = $date_debut;
        $this->date_fin = $date_fin;
        $this->description = $description;
        $this->id_partenaire = $id_partenaire;
    }

    // Getters
    public function getIdConvention(): ?int
    {
        return $this->id_convention;
    }

    public function getDateDebut(): string
    {
        return $this->date_debut;
    }

    public function getDateFin(): string
    {
        return $this->date_fin;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getIdPartenaire(): int
    {
        return $this->id_partenaire;
    }

    // Setters
    public function setDateDebut(string $date_debut): void
    {
        $this->date_debut = $date_debut;
    }

    public function setDateFin(string $date_fin): void
    {
        $this->date_fin = $date_fin;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setIdPartenaire(int $id_partenaire): void
    {
        $this->id_partenaire = $id_partenaire;
    }
}