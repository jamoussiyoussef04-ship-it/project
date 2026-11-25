# GREEN TUNISIA - Administration et statistiques

## Structure

- `config/` : connexion PDO commune
- `models/` : accès à MySQL
- `controllers/` : logique applicative
- `views/` : vues PHP et composants ThemeWagon
- `public/` : point d'entrée web et initialisation
- `template/` : source du thème Spark Admin

## Installation XAMPP

1. Importer `db.txt` dans phpMyAdmin pour créer `green_tunisia` et son schéma.
2. Copier le dossier `green-tunisia` dans `C:\xampp\htdocs\`.
3. Placer Apache sur le port configuré et autoriser les fichiers `.htaccess`, ou démarrer avec PHP :
   `php -S localhost:8080 -t public`
4. Ouvrir `http://localhost:8080/setup.php` pour créer le premier administrateur.
5. Se connecter sur `http://localhost:8080/?page=login`.

La configuration par défaut utilise `127.0.0.1`, base `green_tunisia`, utilisateur `root` sans mot de passe. Pour modifier ces valeurs sans changer les fichiers, définir `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER` et `DB_PASSWORD`.

Le mot de passe initial est choisi uniquement sur la page d'installation et est enregistré avec `password_hash()`. Aucun mot de passe en clair n'est écrit dans la base.
