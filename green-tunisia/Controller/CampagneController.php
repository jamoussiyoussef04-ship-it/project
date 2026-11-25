<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../Model/Campagne.php';
require_once __DIR__ . '/../Model/CampagneModel.php';
require_once __DIR__ . '/../Model/ParticipationModel.php';
require_once __DIR__ . '/../Model/BenevoleModel.php';

/**
 * Contrôleur CampagneController (Architecture MVC / POO)
 * Gère les campagnes écologiques côté Front Office et Back Office.
 */
class CampagneController
{
    private CampagneModel $campagneModel;
    private ParticipationModel $participationModel;
    private BenevoleModel $benevoleModel;

    public function __construct(
        ?CampagneModel $campagneModel = null,
        ?ParticipationModel $participationModel = null,
        ?BenevoleModel $benevoleModel = null
    ) {
        $this->campagneModel = $campagneModel ?? new CampagneModel();
        $this->participationModel = $participationModel ?? new ParticipationModel();
        $this->benevoleModel = $benevoleModel ?? new BenevoleModel();
    }

    /**
     * Valide les données de formulaire d'une campagne (règles simples + avancées).
     *
     * @param array<string, mixed> $input
     * @return array{errors: array<string, string>, campagne: Campagne}
     */
    public function validateCampagneData(array $input, bool $isUpdate = false, int $currentParticipants = 0): array
    {
        $errors = [];

        $titre = trim((string) ($input['titre'] ?? ''));
        $description = trim((string) ($input['description'] ?? ''));
        $date = trim((string) ($input['date'] ?? ''));
        $lieu = trim((string) ($input['lieu'] ?? ''));
        $objectif = trim((string) ($input['objectif'] ?? ''));
        $typeCampagne = trim((string) ($input['type_campagne'] ?? 'Nettoyage'));
        $capaciteRaw = trim((string) ($input['capacite_max'] ?? '30'));
        $statut = trim((string) ($input['statut'] ?? 'Planifiee'));

        if ($titre === '' || mb_strlen($titre) < 3 || mb_strlen($titre) > 150) {
            $errors['titre'] = 'Le titre doit contenir entre 3 et 150 caractères.';
        }

        if ($description === '' || mb_strlen($description) < 10) {
            $errors['description'] = 'La description doit contenir au moins 10 caractères.';
        }

        if ($lieu === '' || mb_strlen($lieu) < 3 || mb_strlen($lieu) > 150) {
            $errors['lieu'] = 'Le lieu doit contenir entre 3 et 150 caractères.';
        }

        if ($objectif === '' || mb_strlen($objectif) < 5 || mb_strlen($objectif) > 255) {
            $errors['objectif'] = 'L\'objectif doit contenir entre 5 et 255 caractères.';
        }

        if (!in_array($typeCampagne, Campagne::TYPES_VALIDES, true)) {
            $errors['type_campagne'] = 'Type de campagne invalide (Nettoyage, Plantation ou Sensibilisation).';
        }

        if (!in_array($statut, Campagne::STATUTS_VALIDES, true)) {
            $errors['statut'] = 'Statut de campagne invalide.';
        }

        // Validation stricte de la date (format Y-m-d + règle métier de date)
        $dt = DateTime::createFromFormat('Y-m-d', $date);
        $isValidDate = $dt !== false && $dt->format('Y-m-d') === $date;
        if (!$isValidDate) {
            $errors['date'] = 'Veuillez saisir une date valide (format AAAA-MM-JJ).';
        } else {
            $today = date('Y-m-d');
            if (!$isUpdate && $date < $today) {
                $errors['date'] = 'Impossible de créer une campagne avec une date déjà passée.';
            } elseif ($isUpdate && in_array($statut, ['Planifiee', 'En cours'], true) && $date < $today) {
                $errors['date'] = 'Une campagne planifiée ou en cours doit avoir une date supérieure ou égale à aujourd\'hui.';
            }
        }

        // Validation capacité maximale + règle métier avancée
        if (!ctype_digit($capaciteRaw) || (int) $capaciteRaw < 1 || (int) $capaciteRaw > 5000) {
            $errors['capacite_max'] = 'La capacité maximale doit être un entier entre 1 et 5000.';
            $capaciteMax = 30;
        } else {
            $capaciteMax = (int) $capaciteRaw;
            if ($isUpdate && $capaciteMax < $currentParticipants) {
                $errors['capacite_max'] = "Impossible de réduire la capacité en dessous des {$currentParticipants} participants déjà inscrits.";
            }
        }

        $campagne = new Campagne(
            isset($input['id_campagne']) ? (int) $input['id_campagne'] : null,
            $titre,
            $description,
            $date,
            $lieu,
            $objectif,
            in_array($typeCampagne, Campagne::TYPES_VALIDES, true) ? $typeCampagne : 'Nettoyage',
            $capaciteMax,
            in_array($statut, Campagne::STATUTS_VALIDES, true) ? $statut : 'Planifiee'
        );

        return [
            'errors'   => $errors,
            'campagne' => $campagne,
        ];
    }

    // =========================================================================
    // FRONT OFFICE
    // =========================================================================

    /**
     * Front Office : Consulter les campagnes disponibles avec filtres & compteur de participants.
     */
    public function indexFront(): void
    {
        $search = isset($_GET['search']) ? trim((string) $_GET['search']) : null;
        $type = isset($_GET['type']) ? trim((string) $_GET['type']) : null;
        $onlyOpen = isset($_GET['disponibles']) && $_GET['disponibles'] === '1';

        $campagnes = $this->campagneModel->getAll($search, $type, null, $onlyOpen);
        $stats = $this->campagneModel->getStats();
        $flashes = consumeFlashes();

        require __DIR__ . '/../View/FrontOffice/campagnes/index.php';
    }

    /**
     * Front Office : Voir les détails d'une campagne, son nombre de participants et s'inscrire.
     */
    public function showFront(int $idCampagne): void
    {
        $campagne = $this->campagneModel->getById($idCampagne);
        if ($campagne === null) {
            setFlash('danger', 'La campagne demandée est introuvable.');
            redirect('index.php?space=front&controller=campagne&action=index');
        }

        $participations = $this->participationModel->getByCampagne($idCampagne);
        $benevoles = $this->benevoleModel->getAll();
        $flashes = consumeFlashes();

        $old = $_SESSION['old_participation'] ?? [];
        $errors = $_SESSION['errors_participation'] ?? [];
        unset($_SESSION['old_participation'], $_SESSION['errors_participation']);

        require __DIR__ . '/../View/FrontOffice/campagnes/show.php';
    }

    // =========================================================================
    // BACK OFFICE
    // =========================================================================

    /**
     * Back Office : Liste CRUD des campagnes + statistiques du tableau de bord.
     */
    public function indexBack(): void
    {
        $search = isset($_GET['search']) ? trim((string) $_GET['search']) : null;
        $type = isset($_GET['type']) ? trim((string) $_GET['type']) : null;
        $statut = isset($_GET['statut']) ? trim((string) $_GET['statut']) : null;

        $campagnes = $this->campagneModel->getAll($search, $type, $statut, false);
        $stats = $this->campagneModel->getStats();
        $flashes = consumeFlashes();

        require __DIR__ . '/../View/BackOffice/campagnes/index.php';
    }

    /**
     * Back Office : Afficher le détail d'une campagne et ses inscriptions.
     */
    public function showBack(int $idCampagne): void
    {
        $campagne = $this->campagneModel->getById($idCampagne);
        if ($campagne === null) {
            setFlash('danger', 'Campagne introuvable.');
            redirect('index.php?space=back&controller=campagne&action=index');
        }

        $participations = $this->participationModel->getByCampagne($idCampagne);
        $benevoles = $this->benevoleModel->getAll();
        $flashes = consumeFlashes();

        $errors = $_SESSION['errors_participation_back'] ?? [];
        $old = $_SESSION['old_participation_back'] ?? [];
        unset($_SESSION['errors_participation_back'], $_SESSION['old_participation_back']);

        require __DIR__ . '/../View/BackOffice/campagnes/show.php';
    }

    /**
     * Back Office : Formulaire d'ajout d'une campagne.
     */
    public function createBack(): void
    {
        $isEdit = false;
        $errors = $_SESSION['errors_campagne'] ?? [];
        $old = $_SESSION['old_campagne'] ?? [
            'titre'         => '',
            'description'   => '',
            'date'          => date('Y-m-d', strtotime('+7 days')),
            'lieu'          => '',
            'objectif'      => '',
            'type_campagne' => 'Nettoyage',
            'capacite_max'  => 30,
            'statut'        => 'Planifiee',
        ];
        unset($_SESSION['errors_campagne'], $_SESSION['old_campagne']);

        $flashes = consumeFlashes();
        require __DIR__ . '/../View/BackOffice/campagnes/form.php';
    }

    /**
     * Back Office : Traitement POST de création d'une campagne.
     */
    public function storeBack(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?space=back&controller=campagne&action=create');
        }

        $validation = $this->validateCampagneData($_POST, false, 0);
        if (!empty($validation['errors'])) {
            $_SESSION['errors_campagne'] = $validation['errors'];
            $_SESSION['old_campagne'] = $_POST;
            setFlash('danger', 'Veuillez corriger les erreurs du formulaire avant d\'enregistrer.');
            redirect('index.php?space=back&controller=campagne&action=create');
        }

        $newId = $this->campagneModel->create($validation['campagne']);
        setFlash('success', "La campagne « {$validation['campagne']->getTitre()} » (#{$newId}) a été ajoutée avec succès.");
        redirect('index.php?space=back&controller=campagne&action=index');
    }

    /**
     * Back Office : Formulaire de modification d'une campagne.
     */
    public function editBack(int $idCampagne): void
    {
        $campagne = $this->campagneModel->getById($idCampagne);
        if ($campagne === null) {
            setFlash('danger', 'Campagne introuvable.');
            redirect('index.php?space=back&controller=campagne&action=index');
        }

        $isEdit = true;
        $errors = $_SESSION['errors_campagne'] ?? [];
        $old = $_SESSION['old_campagne'] ?? $campagne;
        unset($_SESSION['errors_campagne'], $_SESSION['old_campagne']);

        $flashes = consumeFlashes();
        require __DIR__ . '/../View/BackOffice/campagnes/form.php';
    }

    /**
     * Back Office : Traitement POST de mise à jour d'une campagne.
     */
    public function updateBack(int $idCampagne): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect("index.php?space=back&controller=campagne&action=edit&id={$idCampagne}");
        }

        $existing = $this->campagneModel->getById($idCampagne);
        if ($existing === null) {
            setFlash('danger', 'Campagne introuvable.');
            redirect('index.php?space=back&controller=campagne&action=index');
        }

        $payload = $_POST;
        $payload['id_campagne'] = $idCampagne;

        $validation = $this->validateCampagneData($payload, true, (int) $existing['nb_participants']);
        if (!empty($validation['errors'])) {
            $_SESSION['errors_campagne'] = $validation['errors'];
            $_SESSION['old_campagne'] = array_merge($existing, $_POST);
            setFlash('danger', 'Veuillez corriger les erreurs du formulaire.');
            redirect("index.php?space=back&controller=campagne&action=edit&id={$idCampagne}");
        }

        $this->campagneModel->update($validation['campagne']);
        setFlash('success', "La campagne « {$validation['campagne']->getTitre()} » a été mise à jour.");
        redirect('index.php?space=back&controller=campagne&action=index');
    }

    /**
     * Back Office : Suppression d'une campagne.
     */
    public function deleteBack(int $idCampagne): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?space=back&controller=campagne&action=index');
        }

        $existing = $this->campagneModel->getById($idCampagne);
        if ($existing === null) {
            setFlash('danger', 'Campagne introuvable.');
            redirect('index.php?space=back&controller=campagne&action=index');
        }

        $this->campagneModel->delete($idCampagne);
        setFlash('success', "La campagne « {$existing['titre']} » et ses inscriptions liées ont été supprimées.");
        redirect('index.php?space=back&controller=campagne&action=index');
    }
}
