<?php
class Benevole
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }


    public static function valider(array $d, bool $avecMotDePasse = true): array
    {
        $erreurs = [];
        if (trim($d['nom'] ?? '') === '')    $erreurs['nom'] = 'Le nom est obligatoire.';
        if (trim($d['prenom'] ?? '') === '') $erreurs['prenom'] = 'Le prénom est obligatoire.';

        if (!filter_var(trim($d['email'] ?? ''), FILTER_VALIDATE_EMAIL)) {
            $erreurs['email'] = "L'adresse e-mail n'est pas valide.";
        }

        $tel = preg_replace('/\s+/', '', $d['telephone'] ?? '');
        if (!preg_match('/^\d{8}$/', $tel)) {
            $erreurs['telephone'] = 'Le téléphone doit contenir 8 chiffres.';
        }

        if ($avecMotDePasse) {
            $mdp = $d['mot_de_passe'] ?? '';
            if (strlen($mdp) < 6) {
                $erreurs['mot_de_passe'] = 'Le mot de passe doit contenir au moins 6 caractères.';
            } elseif ($mdp !== ($d['confirmation'] ?? '')) {
                $erreurs['confirmation'] = 'Les deux mots de passe ne correspondent pas.';
            }
        }
        return $erreurs;
    }

    private function normaliser(array $d): array
    {
        return [
            'nom'       => trim($d['nom']),
            'prenom'    => trim($d['prenom']),
            'email'     => strtolower(trim($d['email'])),
            'telephone' => preg_replace('/\s+/', '', $d['telephone']),
        ];
    }

    public function creer(array $d): int
    {
        $v = $this->normaliser($d);
        $stmt = $this->db->prepare(
            'INSERT INTO benevole (nom, prenom, email, telephone, mot_de_passe)
             VALUES (:nom, :prenom, :email, :telephone, :mdp)'
        );
        $stmt->execute($v + ['mdp' => password_hash($d['mot_de_passe'], PASSWORD_DEFAULT)]);
        return (int) $this->db->lastInsertId();
    }

    public function trouverParId(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM benevole WHERE id_benevole = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function trouverParEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM benevole WHERE email = :email');
        $stmt->execute(['email' => strtolower(trim($email))]);
        return $stmt->fetch() ?: null;
    }

    public function emailExiste(string $email, ?int $exclureId = null): bool
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM benevole WHERE email = :email AND id_benevole <> :id'
        );
        $stmt->execute(['email' => strtolower(trim($email)), 'id' => $exclureId ?? 0]);
        return (int) $stmt->fetchColumn() > 0;
    }

    /** Liste avec le nombre de participations, recherche facultative. */
    public function tous(string $recherche = ''): array
    {
        $sql = 'SELECT b.id_benevole, b.nom, b.prenom, b.email, b.telephone,
                       COUNT(p.id_participation) AS nb_participations
                FROM benevole b
                LEFT JOIN participation p ON p.id_benevole = b.id_benevole';
        $params = [];
        if ($recherche !== '') {
            $sql .= ' WHERE b.nom LIKE :q1 OR b.prenom LIKE :q2 OR b.email LIKE :q3';
            $params = ['q1' => "%$recherche%", 'q2' => "%$recherche%", 'q3' => "%$recherche%"];
        }
        $sql .= ' GROUP BY b.id_benevole ORDER BY b.nom, b.prenom';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Met à jour un bénévole. Le mot de passe n'est changé que s'il est fourni. */
    public function modifier(int $id, array $d): void
    {
        $v = $this->normaliser($d);
        $sql = 'UPDATE benevole SET nom = :nom, prenom = :prenom, email = :email, telephone = :telephone';
        $params = $v + ['id' => $id];
        if (!empty($d['mot_de_passe'])) {
            $sql .= ', mot_de_passe = :mdp';
            $params['mdp'] = password_hash($d['mot_de_passe'], PASSWORD_DEFAULT);
        }
        $sql .= ' WHERE id_benevole = :id';
        $this->db->prepare($sql)->execute($params);
    }

    public function supprimer(int $id): void
    {
        $this->db->prepare('DELETE FROM benevole WHERE id_benevole = :id')->execute(['id' => $id]);
    }
}
