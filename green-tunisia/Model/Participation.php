<?php

declare(strict_types=1);

/**
 * Entité Participation - Modèle objet (POO)
 * Matérialise la relation (1,N) entre Campagne et Participation,
 * et la liaison avec Benevole.
 */
class Participation
{
    public const STATUTS_VALIDES = ['En attente', 'Confirmee', 'Annulee'];

    private ?int $id_participation;
    private int $id_campagne;
    private int $id_benevole;
    private string $date_inscription;
    private string $statut;
    private ?string $commentaire;

    public function __construct(
        ?int $id_participation = null,
        int $id_campagne = 0,
        int $id_benevole = 0,
        string $date_inscription = '',
        string $statut = 'Confirmee',
        ?string $commentaire = null
    ) {
        $this->id_participation = $id_participation;
        $this->id_campagne = $id_campagne;
        $this->id_benevole = $id_benevole;
        $this->date_inscription = $date_inscription !== '' ? $date_inscription : date('Y-m-d H:i:s');
        $this->statut = $statut;
        $this->commentaire = $commentaire;
    }

    public function getIdParticipation(): ?int
    {
        return $this->id_participation;
    }

    public function setIdParticipation(?int $id_participation): void
    {
        $this->id_participation = $id_participation;
    }

    public function getIdCampagne(): int
    {
        return $this->id_campagne;
    }

    public function setIdCampagne(int $id_campagne): void
    {
        $this->id_campagne = $id_campagne;
    }

    public function getIdBenevole(): int
    {
        return $this->id_benevole;
    }

    public function setIdBenevole(int $id_benevole): void
    {
        $this->id_benevole = $id_benevole;
    }

    public function getDateInscription(): string
    {
        return $this->date_inscription;
    }

    public function setDateInscription(string $date_inscription): void
    {
        $this->date_inscription = $date_inscription;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): void
    {
        $this->statut = $statut;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): void
    {
        $this->commentaire = $commentaire !== null ? trim($commentaire) : null;
    }
}
