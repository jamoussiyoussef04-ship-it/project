<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../Model/Participation.php';
require_once __DIR__ . '/../Model/ParticipationModel.php';
require_once __DIR__ . '/../Model/CampagneModel.php';
require_once __DIR__ . '/../Model/Benevole.php';
require_once __DIR__ . '/../Model/BenevoleModel.php';

/**
 * Contrôleur ParticipationController (Architecture MVC / POO)
 * Enregistre et gère les inscriptions des bénévoles aux campagnes,
 * vérifie les dates, prévient les doublons et gère les conflits de capacité/date.
 */
class ParticipationController
{
    private ParticipationModel $participationModel;
    private CampagneModel $campagneModel;
    private BenevoleModel $benevoleModel;

    public function __construct(
        ?ParticipationModel $participationModel = null,
        ?CampagneModel $campagneModel = null,
        ?BenevoleModel $benevoleModel = null
    ) {
        $this->participationModel = $participationModel ?? new ParticipationModel();
        $this->campagneModel = $campagneModel ?? new CampagneModel();
        $this->benevoleModel = $benevoleModel ?? new BenevoleModel();
    }

    /**
     * Exécute l'ensemble des vérifications métiers et enregistre une participation.
     * Utilisé à la fois par le Front Office, le Back Office et les tests unitaires.
     *
     * @param array<string, mixed> $input
     * @return array{ok: bool, errors: array<string, string>, message: string, id_participation?: int}
     */
    public function processInscription(array $input, bool $isBackOffice = false): array
    {
        $errors = [];

        $idCampagne = isset($input['id_campagne']) && ctype_digit((string) $input['id_campagne'])
            ? (int) $input['id_campagne']
            : 0;
        $mode = trim((string) ($input['mode_benevole'] ?? 'existing'));
        $commentaire = trim((string) ($input['commentaire'] ?? ''));
        $statut = trim((string) ($input['statut'] ?? 'Confirmee'));

        if (!$isBackOffice || !in_array($statut, ['En attente', 'Confirmee'], true)) {
            $statut = 'Confirmee';
        }

        if ($idCampagne <= 0) {
            $errors['id_campagne'] = 'Veuillez sélectionner une campagne valide.';
        }

        if (mb_strlen($commentaire) > 255) {
            $errors['commentaire'] = 'Le commentaire ne doit pas dépasser 255 caractères.';
        }

        $campagne = $idCampagne > 0 ? $this->campagneModel->getById($idCampagne) : null;
        if ($idCampagne > 0 && $campagne === null) {
            $errors['id_campagne'] = 'La campagne sélectionnée n\'existe pas.';
        }

        // Validation et résolution du bénévole (existant ou création rapide)
        $idBenevole = 0;
        if ($mode === 'new') {
            $nom = trim((string) ($input['nom'] ?? ''));
            $prenom = trim((string) ($input['prenom'] ?? ''));
            $email = strtolower(trim((string) ($input['email'] ?? '')));
            $telephone = trim((string) ($input['telephone'] ?? ''));

            if ($nom === '' || mb_strlen($nom) < 2 || mb_strlen($nom) > 100) {
                $errors['nom'] = 'Le nom doit contenir entre 2 et 100 caractères.';
            }
            if ($prenom === '' || mb_strlen($prenom) < 2 || mb_strlen($prenom) > 100) {
                $errors['prenom'] = 'Le prénom doit contenir entre 2 et 100 caractères.';
            }
            if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                $errors['email'] = 'Veuillez saisir une adresse email valide.';
            }
            if ($telephone !== '' && !preg_match('/^[0-9+\s\-]{8,20}$/', $telephone)) {
                $errors['telephone'] = 'Numéro de téléphone invalide (8 à 20 chiffres).';
            }

            if (empty($errors)) {
                $benevole = new Benevole(null, $nom, $prenom, $email, $telephone !== '' ? $telephone : null);
                $idBenevole = $this->benevoleModel->findOrCreate($benevole);
            }
        } else {
            $idBenevole = isset($input['id_benevole']) && ctype_digit((string) $input['id_benevole'])
                ? (int) $input['id_benevole']
                : 0;
            if ($idBenevole <= 0) {
                $errors['id_benevole'] = 'Veuillez sélectionner un bénévole.';
            } else {
                $benevoleRow = $this->benevoleModel->getById($idBenevole);
                if ($benevoleRow === null) {
                    $errors['id_benevole'] = 'Le bénévole sélectionné est introuvable.';
                }
            }
        }

        if (!empty($errors) || $campagne === null) {
            return [
                'ok'      => false,
                'errors'  => $errors,
                'message' => 'Veuillez vérifier les informations saisies.',
            ];
        }

        // RÈGLE MÉTIER 1 : Vérification de la date et du statut de la campagne
        $today = date('Y-m-d');
        if ($campagne['date'] < $today) {
            return [
                'ok'      => false,
                'errors'  => ['id_campagne' => 'Inscription impossible : la date de cette campagne (' . formatDateFr($campagne['date']) . ') est déjà dépassée.'],
                'message' => 'Inscription refusée : la date de la campagne est déjà passée.',
            ];
        }

        if (in_array($campagne['statut'], ['Terminee', 'Annulee'], true)) {
            return [
                'ok'      => false,
                'errors'  => ['id_campagne' => "Inscription impossible : cette campagne est {$campagne['statut']}."],
                'message' => "Inscription refusée : la campagne est {$campagne['statut']}.",
            ];
        }

        // RÈGLE MÉTIER 2 : Éviter les inscriptions en double
        $existing = $this->participationModel->findByCampagneAndBenevole($idCampagne, $idBenevole);
        if ($existing !== null && $existing['statut'] !== 'Annulee') {
            return [
                'ok'      => false,
                'errors'  => ['id_benevole' => 'Ce bénévole est déjà inscrit à cette campagne (inscription #' . $existing['id_participation'] . ').'],
                'message' => 'Inscription en double détectée : ce bénévole est déjà inscrit à cette campagne.',
            ];
        }

        // RÈGLE MÉTIER 3 (Avancée) : Vérification de la capacité maximale
        $nbActifs = $this->participationModel->countActiveByCampagne($idCampagne);
        if ($nbActifs >= (int) $campagne['capacite_max']) {
            return [
                'ok'      => false,
                'errors'  => ['id_campagne' => "Capacité maximale atteinte ({$campagne['capacite_max']} places)."],
                'message' => "Campagne complète : toutes les {$campagne['capacite_max']} places sont réservées.",
            ];
        }

        // RÈGLE MÉTIER 4 (Avancée) : Conflit de date (même bénévole le même jour)
        $conflit = $this->participationModel->findSameDateConflict($idBenevole, $campagne['date'], $idCampagne);
        if ($conflit !== null) {
            return [
                'ok'      => false,
                'errors'  => ['id_benevole' => "Conflit de planning : ce bénévole participe déjà à « {$conflit['titre']} » le " . formatDateFr($conflit['date']) . '.'],
                'message' => "Conflit de date : ce bénévole est déjà inscrit à une autre campagne le " . formatDateFr($campagne['date']) . '.',
            ];
        }

        $participation = new Participation(
            null,
            $idCampagne,
            $idBenevole,
            date('Y-m-d H:i:s'),
            $statut,
            $commentaire !== '' ? $commentaire : null
        );

        $newId = $this->participationModel->create($participation);

        return [
            'ok'               => true,
            'errors'           => [],
            'message'          => 'Inscription enregistrée avec succès (participation #' . $newId . ').',
            'id_participation' => $newId,
        ];
    }

    // =========================================================================
    // FRONT OFFICE
    // =========================================================================

    /**
     * Front Office : Enregistrer la participation depuis la fiche campagne.
     */
    public function inscrireFront(): void
    {
        $idCampagne = isset($_POST['id_campagne']) ? (int) $_POST['id_campagne'] : 0;
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?space=front&controller=campagne&action=index');
        }

        $result = $this->processInscription($_POST, false);
        if (!$result['ok']) {
            $_SESSION['errors_participation'] = $result['errors'];
            $_SESSION['old_participation'] = $_POST;
            setFlash('danger', $result['message']);
        } else {
            setFlash('success', $result['message']);
        }

        if ($idCampagne > 0) {
            redirect("index.php?space=front&controller=campagne&action=show&id={$idCampagne}");
        }
        redirect('index.php?space=front&controller=campagne&action=index');
    }

    /**
     * Front Office : Consulter les inscriptions d'un bénévole.
     */
    public function mesParticipationsFront(): void
    {
        $benevoles = $this->benevoleModel->getAll();
        $selectedId = isset($_GET['id_benevole']) && ctype_digit((string) $_GET['id_benevole'])
            ? (int) $_GET['id_benevole']
            : (!empty($benevoles) ? (int) $benevoles[0]['id_benevole'] : 0);

        $benevole = $selectedId > 0 ? $this->benevoleModel->getById($selectedId) : null;
        $participations = $selectedId > 0 ? $this->participationModel->getByBenevole($selectedId) : [];
        $flashes = consumeFlashes();

        require __DIR__ . '/../View/FrontOffice/participations/mes_participations.php';
    }

    /**
     * Front Office : Annuler / se désinscrire d'une campagne avant sa date.
     */
    public function desinscrireFront(int $idParticipation): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?space=front&controller=participation&action=mesParticipations');
        }

        $participation = $this->participationModel->getById($idParticipation);
        if ($participation === null) {
            setFlash('danger', 'Participation introuvable.');
            redirect('index.php?space=front&controller=participation&action=mesParticipations');
        }

        $idBenevole = (int) $participation['id_benevole'];
        if ($participation['campagne_date'] < date('Y-m-d')) {
            setFlash('danger', 'Désinscription impossible : cette campagne a déjà eu lieu.');
            redirect("index.php?space=front&controller=participation&action=mesParticipations&id_benevole={$idBenevole}");
        }

        $this->participationModel->updateStatut($idParticipation, 'Annulee');
        setFlash('success', "Votre désinscription de « {$participation['campagne_titre']} » a été prise en compte.");
        redirect("index.php?space=front&controller=participation&action=mesParticipations&id_benevole={$idBenevole}");
    }

    // =========================================================================
    // BACK OFFICE
    // =========================================================================

    /**
     * Back Office : Consulter toutes les inscriptions (jointures campagne + benevole).
     */
    public function indexBack(): void
    {
        $idCampagne = isset($_GET['id_campagne']) && ctype_digit((string) $_GET['id_campagne'])
            ? (int) $_GET['id_campagne']
            : null;
        $statut = isset($_GET['statut']) ? trim((string) $_GET['statut']) : null;
        $search = isset($_GET['search']) ? trim((string) $_GET['search']) : null;

        $participations = $this->participationModel->getAll($idCampagne, $statut, $search);
        $campagnes = $this->campagneModel->getAll();
        $benevoles = $this->benevoleModel->getAll();
        $flashes = consumeFlashes();

        $errors = $_SESSION['errors_participation_back'] ?? [];
        $old = $_SESSION['old_participation_back'] ?? [];
        unset($_SESSION['errors_participation_back'], $_SESSION['old_participation_back']);

        require __DIR__ . '/../View/BackOffice/participations/index.php';
    }

    /**
     * Back Office : Enregistrer une inscription manuelle avec contrôles complets.
     */
    public function storeBack(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?space=back&controller=participation&action=index');
        }

        $result = $this->processInscription($_POST, true);
        if (!$result['ok']) {
            $_SESSION['errors_participation_back'] = $result['errors'];
            $_SESSION['old_participation_back'] = $_POST;
            setFlash('danger', $result['message']);
        } else {
            setFlash('success', $result['message']);
        }

        $redirectTo = trim((string) ($_POST['redirect_to'] ?? ''));
        if ($redirectTo === 'campagne_show' && !empty($_POST['id_campagne'])) {
            redirect('index.php?space=back&controller=campagne&action=show&id=' . (int) $_POST['id_campagne']);
        }

        redirect('index.php?space=back&controller=participation&action=index');
    }

    /**
     * Back Office : Modifier le statut d'une participation (Valider / Annuler / En attente).
     */
    public function updateStatutBack(int $idParticipation): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?space=back&controller=participation&action=index');
        }

        $nouveauStatut = trim((string) ($_POST['statut'] ?? ''));
        $participation = $this->participationModel->getById($idParticipation);
        if ($participation === null) {
            setFlash('danger', 'Participation introuvable.');
            redirect('index.php?space=back&controller=participation&action=index');
        }

        if (!in_array($nouveauStatut, Participation::STATUTS_VALIDES, true)) {
            setFlash('danger', 'Statut de participation invalide.');
            redirect('index.php?space=back&controller=participation&action=index');
        }

        // Si on réactive ou confirme une inscription annulée, vérifier date + capacité + conflit
        if ($participation['statut'] === 'Annulee' && $nouveauStatut !== 'Annulee') {
            if ($participation['campagne_date'] < date('Y-m-d')) {
                setFlash('danger', 'Impossible de réactiver une inscription pour une campagne passée.');
                redirect('index.php?space=back&controller=participation&action=index');
            }
            $nbActifs = $this->participationModel->countActiveByCampagne((int) $participation['id_campagne']);
            if ($nbActifs >= (int) $participation['capacite_max']) {
                setFlash('danger', 'Impossible de confirmer : capacité maximale de la campagne atteinte.');
                redirect('index.php?space=back&controller=participation&action=index');
            }
            $conflit = $this->participationModel->findSameDateConflict(
                (int) $participation['id_benevole'],
                (string) $participation['campagne_date'],
                (int) $participation['id_campagne']
            );
            if ($conflit !== null) {
                setFlash('danger', "Conflit de date : ce bénévole participe déjà à « {$conflit['titre']} » à cette date.");
                redirect('index.php?space=back&controller=participation&action=index');
            }
        }

        $this->participationModel->updateStatut($idParticipation, $nouveauStatut);
        setFlash('success', "Le statut de l'inscription #{$idParticipation} a été mis à jour : {$nouveauStatut}.");

        $redirectTo = trim((string) ($_POST['redirect_to'] ?? ''));
        if ($redirectTo === 'campagne_show') {
            redirect('index.php?space=back&controller=campagne&action=show&id=' . (int) $participation['id_campagne']);
        }
        redirect('index.php?space=back&controller=participation&action=index');
    }

    /**
     * Back Office : Supprimer une participation.
     */
    public function deleteBack(int $idParticipation): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?space=back&controller=participation&action=index');
        }

        $participation = $this->participationModel->getById($idParticipation);
        if ($participation === null) {
            setFlash('danger', 'Participation introuvable.');
            redirect('index.php?space=back&controller=participation&action=index');
        }

        $this->participationModel->delete($idParticipation);
        setFlash('success', "L'inscription #{$idParticipation} a été supprimée.");

        $redirectTo = trim((string) ($_POST['redirect_to'] ?? ''));
        if ($redirectTo === 'campagne_show') {
            redirect('index.php?space=back&controller=campagne&action=show&id=' . (int) $participation['id_campagne']);
        }
        redirect('index.php?space=back&controller=participation&action=index');
    }

    /**
     * Back Office : Exporter la liste des inscriptions en CSV (fonctionnalité métier avancée).
     */
    public function exportCsvBack(): void
    {
        $idCampagne = isset($_GET['id_campagne']) && ctype_digit((string) $_GET['id_campagne'])
            ? (int) $_GET['id_campagne']
            : null;
        $statut = isset($_GET['statut']) ? trim((string) $_GET['statut']) : null;
        $search = isset($_GET['search']) ? trim((string) $_GET['search']) : null;

        $rows = $this->participationModel->getAll($idCampagne, $statut, $search);

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="participations_green_tunisia_' . date('Ymd_His') . '.csv"');

        $out = fopen('php://output', 'w');
        if ($out === false) {
            exit;
        }

        // BOM UTF-8 pour ouverture correcte des accents dans Excel
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, [
            'ID Participation',
            'Campagne',
            'Type',
            'Date Campagne',
            'Lieu',
            'Bénévole',
            'Email',
            'Téléphone',
            'Date Inscription',
            'Statut',
            'Commentaire',
        ], ';');

        foreach ($rows as $row) {
            fputcsv($out, [
                $row['id_participation'],
                $row['campagne_titre'],
                $row['type_campagne'],
                $row['campagne_date'],
                $row['campagne_lieu'],
                $row['benevole_prenom'] . ' ' . $row['benevole_nom'],
                $row['benevole_email'],
                $row['benevole_telephone'] ?? '',
                $row['date_inscription'],
                $row['statut'],
                $row['commentaire'] ?? '',
            ], ';');
        }

        fclose($out);
        exit;
    }
}
