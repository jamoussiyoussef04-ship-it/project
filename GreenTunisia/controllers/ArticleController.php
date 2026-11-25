<?php
require_once __DIR__ . '/../models/Article.php';
require_once __DIR__ . '/../models/Categorie.php';

/**
 * Controller Article : reçoit les données, applique la validation
 * et les règles métiers, puis appelle le Model.
 *
 * Chaque méthode d'écriture retourne un tableau :
 *   ['success' => bool, 'errors' => string[], 'old' => array]
 */
class ArticleController
{
    private const IMAGE_DIR      = __DIR__ . '/../public/images/';
    private const IMAGE_MAX_SIZE = 2 * 1024 * 1024; // 2 Mo
    private const IMAGE_TYPES    = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    ];

    private Article $articleModel;
    private Categorie $categorieModel;

    public function __construct()
    {
        $this->articleModel   = new Article();
        $this->categorieModel = new Categorie();
    }

    /**
     * Afficher les articles, avec filtre optionnel par catégorie
     * (ex : index.php?categorie=2).
     */
    public function index(?int $idCategorie = null): array
    {
        $articles = $idCategorie
            ? $this->articleModel->getByCategorie($idCategorie)
            : $this->articleModel->getAll();

        return [
            'articles'   => $articles,
            'categories' => $this->categorieModel->getAll(),
            'selected'   => $idCategorie,
        ];
    }

    public function show(int $id): ?array
    {
        return $this->articleModel->getById($id);
    }

    /** Ajouter un article ($_POST + $_FILES['image']). */
    public function store(array $post, ?array $file = null): array
    {
        $data   = $this->clean($post);
        $errors = $this->validate($data);

        $image = null;
        if ($file !== null && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            [$image, $imageError] = $this->uploadImage($file);
            if ($imageError) {
                $errors[] = $imageError;
            }
        }

        if ($errors) {
            return ['success' => false, 'errors' => $errors, 'old' => $data];
        }

        $this->articleModel->add(
            $data['titre'],
            $data['contenu'],
            $data['date_publication'],
            $image,
            $data['id_categorie']
        );
        return ['success' => true, 'errors' => [], 'old' => $data];
    }

    /** Modifier un article. Sans nouvelle image, l'ancienne est conservée. */
    public function update(int $id, array $post, ?array $file = null): array
    {
        $data     = $this->clean($post);
        $errors   = $this->validate($data);
        $existing = $this->articleModel->getById($id);

        if ($existing === null) {
            $errors[] = "Cet article n'existe pas.";
        }

        $image = $existing['image'] ?? null;
        if ($file !== null && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            [$newImage, $imageError] = $this->uploadImage($file);
            if ($imageError) {
                $errors[] = $imageError;
            } else {
                $image = $newImage;
            }
        }

        if ($errors) {
            return ['success' => false, 'errors' => $errors, 'old' => $data];
        }

        $this->articleModel->update(
            $id,
            $data['titre'],
            $data['contenu'],
            $data['date_publication'],
            $image,
            $data['id_categorie']
        );
        return ['success' => true, 'errors' => [], 'old' => $data];
    }

    /** Supprimer un article. */
    public function delete(int $id): array
    {
        if ($this->articleModel->getById($id) === null) {
            return ['success' => false, 'errors' => ["Cet article n'existe pas."], 'old' => []];
        }

        $this->articleModel->delete($id);
        return ['success' => true, 'errors' => [], 'old' => []];
    }

    /** Nettoie les données reçues (trim). L'échappement HTML se fait dans les vues. */
    private function clean(array $post): array
    {
        return [
            'titre'            => trim($post['titre'] ?? ''),
            'contenu'          => trim($post['contenu'] ?? ''),
            'date_publication' => trim($post['date_publication'] ?? ''),
            'id_categorie'     => (int) ($post['id_categorie'] ?? 0),
        ];
    }

    /** Règles métiers 1 à 5. */
    private function validate(array $data): array
    {
        $errors = [];

        // Règles 1 et 5 : titre obligatoire, ni vide ni seulement des espaces (trim déjà fait)
        if ($data['titre'] === '') {
            $errors[] = "Le titre de l'article est obligatoire.";
        } elseif (mb_strlen($data['titre']) > 255) {
            $errors[] = 'Le titre ne doit pas dépasser 255 caractères.';
        }

        // Règle 2 : contenu obligatoire
        if ($data['contenu'] === '') {
            $errors[] = "Le contenu de l'article est obligatoire.";
        }

        // Règle 4 : date de publication valide (format AAAA-MM-JJ, date réelle)
        $d = DateTime::createFromFormat('Y-m-d', $data['date_publication']);
        if (!$d || $d->format('Y-m-d') !== $data['date_publication']) {
            $errors[] = 'La date de publication est invalide.';
        }

        // Règle 3 : la catégorie doit exister
        if ($data['id_categorie'] <= 0 || $this->categorieModel->getById($data['id_categorie']) === null) {
            $errors[] = 'Veuillez choisir une catégorie existante.';
        }

        return $errors;
    }

    /**
     * Valide et enregistre l'image envoyée.
     * Retourne [nomDuFichier|null, messageErreur|null].
     */
    private function uploadImage(array $file): array
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return [null, "Erreur lors de l'envoi de l'image."];
        }
        if ($file['size'] > self::IMAGE_MAX_SIZE) {
            return [null, "L'image ne doit pas dépasser 2 Mo."];
        }

        // On vérifie le vrai type du fichier, pas seulement son extension
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        if (!isset(self::IMAGE_TYPES[$mime])) {
            return [null, "Format d'image non autorisé (JPG, PNG, WEBP ou GIF)."];
        }

        $name = bin2hex(random_bytes(8)) . '.' . self::IMAGE_TYPES[$mime];
        if (!move_uploaded_file($file['tmp_name'], self::IMAGE_DIR . $name)) {
            return [null, "Impossible d'enregistrer l'image."];
        }

        return [$name, null];
    }
}
