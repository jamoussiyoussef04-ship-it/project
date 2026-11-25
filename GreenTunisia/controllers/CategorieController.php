<?php
require_once __DIR__ . '/../models/Categorie.php';

/**
 * Controller Categorie : reçoit les données, applique la validation
 * et la règle métier 6, puis appelle le Model.
 *
 * Chaque méthode d'écriture retourne un tableau :
 *   ['success' => bool, 'errors' => string[], 'old' => array]
 */
class CategorieController
{
    private Categorie $model;

    public function __construct()
    {
        $this->model = new Categorie();
    }

    /** Afficher les catégories. */
    public function index(): array
    {
        return $this->model->getAll();
    }

    public function show(int $id): ?array
    {
        return $this->model->getById($id);
    }

    /** Ajouter une catégorie (données du formulaire : $_POST). */
    public function store(array $post): array
    {
        $data   = $this->clean($post);
        $errors = $this->validate($data);

        if ($errors) {
            return ['success' => false, 'errors' => $errors, 'old' => $data];
        }

        $this->model->add($data['nom'], $data['description']);
        return ['success' => true, 'errors' => [], 'old' => $data];
    }

    /** Modifier une catégorie. */
    public function update(int $id, array $post): array
    {
        $data   = $this->clean($post);
        $errors = $this->validate($data);

        if ($this->model->getById($id) === null) {
            $errors[] = "Cette catégorie n'existe pas.";
        }

        if ($errors) {
            return ['success' => false, 'errors' => $errors, 'old' => $data];
        }

        $this->model->update($id, $data['nom'], $data['description']);
        return ['success' => true, 'errors' => [], 'old' => $data];
    }

    /** Supprimer une catégorie (refusé si elle contient encore des articles). */
    public function delete(int $id): array
    {
        if ($this->model->getById($id) === null) {
            return ['success' => false, 'errors' => ["Cette catégorie n'existe pas."], 'old' => []];
        }

        $nb = $this->model->countArticles($id);
        if ($nb > 0) {
            return [
                'success' => false,
                'errors'  => ["Suppression impossible : cette catégorie contient encore $nb article(s). "
                            . "Déplacez ou supprimez-les d'abord."],
                'old'     => [],
            ];
        }

        $this->model->delete($id);
        return ['success' => true, 'errors' => [], 'old' => []];
    }

    /** Nettoie les données reçues (trim). L'échappement HTML se fait dans les vues. */
    private function clean(array $post): array
    {
        $description = trim($post['description'] ?? '');
        return [
            'nom'         => trim($post['nom'] ?? ''),
            'description' => $description === '' ? null : $description,
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];
        if ($data['nom'] === '') {
            $errors[] = 'Le nom de la catégorie est obligatoire.';
        } elseif (mb_strlen($data['nom']) > 100) {
            $errors[] = 'Le nom ne doit pas dépasser 100 caractères.';
        }
        return $errors;
    }
}
