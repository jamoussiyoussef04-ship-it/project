<?php
class Don
{
    private ?int $id_don;
    private float $montant;
    private string $date_don;
    private string $type_don;
    private int $id_donateur;

    public function __construct(
        ?int $id_don,
        float $montant,
        string $date_don,
        string $type_don,
        int $id_donateur
    ) {
        $this->id_don = $id_don;
        $this->montant = $montant;
        $this->date_don = $date_don;
        $this->type_don = $type_don;
        $this->id_donateur = $id_donateur;
    }

    public function getIdDon(): ?int
    {
        return $this->id_don;
    }

    public function getMontant(): float
    {
        return $this->montant;
    }

    public function getDateDon(): string
    {
        return $this->date_don;
    }

    public function getTypeDon(): string
    {
        return $this->type_don;
    }

    public function getIdDonateur(): int
    {
        return $this->id_donateur;
    }

    public function setMontant(float $montant): void
    {
        $this->montant = $montant;
    }

    public function setDateDon(string $date_don): void
    {
        $this->date_don = $date_don;
    }

    public function setTypeDon(string $type_don): void
    {
        $this->type_don = $type_don;
    }

    public function setIdDonateur(int $id_donateur): void
    {
        $this->id_donateur = $id_donateur;
    }
}