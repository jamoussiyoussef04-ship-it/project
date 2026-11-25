<?php

declare(strict_types=1);

/**
 * Entité Campagne - Modèle objet (POO)
 * Attributs principaux : id_campagne, titre, description, date, lieu, objectif
 * Attributs métiers : type_campagne, capacite_max, statut
 */
class Campagne
{
    public const TYPES_VALIDES = ['Nettoyage', 'Plantation', 'Sensibilisation'];
    public const STATUTS_VALIDES = ['Planifiee', 'En cours', 'Terminee', 'Annulee'];

    private ?int $id_campagne;
    private string $titre;
    private string $description;
    private string $date;
    private string $lieu;
    private string $objectif;
    private string $type_campagne;
    private int $capacite_max;
    private string $statut;

    public function __construct(
        ?int $id_campagne = null,
        string $titre = '',
        string $description = '',
        string $date = '',
        string $lieu = '',
        string $objectif = '',
        string $type_campagne = 'Nettoyage',
        int $capacite_max = 30,
        string $statut = 'Planifiee'
    ) {
        $this->id_campagne = $id_campagne;
        $this->titre = $titre;
        $this->description = $description;
        $this->date = $date;
        $this->lieu = $lieu;
        $this->objectif = $objectif;
        $this->type_campagne = $type_campagne;
        $this->capacite_max = $capacite_max;
        $this->statut = $statut;
    }

    public function getIdCampagne(): ?int
    {
        return $this->id_campagne;
    }

    public function setIdCampagne(?int $id_campagne): void
    {
        $this->id_campagne = $id_campagne;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): void
    {
        $this->titre = trim($titre);
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = trim($description);
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function setDate(string $date): void
    {
        $this->date = trim($date);
    }

    public function getLieu(): string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): void
    {
        $this->lieu = trim($lieu);
    }

    public function getObjectif(): string
    {
        return $this->objectif;
    }

    public function setObjectif(string $objectif): void
    {
        $this->objectif = trim($objectif);
    }

    public function getTypeCampagne(): string
    {
        return $this->type_campagne;
    }

    public function setTypeCampagne(string $type_campagne): void
    {
        $this->type_campagne = $type_campagne;
    }

    public function getCapaciteMax(): int
    {
        return $this->capacite_max;
    }

    public function setCapaciteMax(int $capacite_max): void
    {
        $this->capacite_max = $capacite_max;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): void
    {
        $this->statut = $statut;
    }

    public function isPast(?string $today = null): bool
    {
        $reference = $today ?? date('Y-m-d');
        return $this->date < $reference;
    }
}
