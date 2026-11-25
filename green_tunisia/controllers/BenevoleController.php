<?php
class BenevoleController extends Controller
{
    private Benevole $model;

    public function __construct()
    {
        $this->model = new Benevole();
    }



    public function inscription(): void
    {
        $erreurs = [];
        $old = [];
        if ($this->estPost()) {
            $old = $_POST;
            $erreurs = Benevole::valider($_POST);
            if (!$erreurs && $this->model->emailExiste($_POST['email'])) {
                $erreurs['email'] = 'Cette adresse e-mail est déjà utilisée.';
            }
            if (!$erreurs) {
                $_SESSION['benevole_id'] = $this->model->creer($_POST);
                session_regenerate_id(true);
                $this->flash('Votre compte est créé. Bienvenue !');
                $this->redirect('participation', 'mesParticipations');
            }
        }
        $this->render('front/benevole/inscription', compact('erreurs', 'old'));
    }

    public function connexion(): void
    {
        $erreur = '';
        if ($this->estPost()) {
            $benevole = $this->model->trouverParEmail($_POST['email'] ?? '');
            if ($benevole && password_verify($_POST['mot_de_passe'] ?? '', $benevole['mot_de_passe'])) {
                session_regenerate_id(true);
                $_SESSION['benevole_id'] = (int) $benevole['id_benevole'];
                $this->redirect('participation', 'mesParticipations');
            }
            $erreur = 'E-mail ou mot de passe incorrect.';
        }
        $this->render('front/benevole/connexion', compact('erreur'));
    }

    public function deconnexion(): void
    {
        unset($_SESSION['benevole_id']);
        session_regenerate_id(true);
        $this->flash('Vous êtes déconnecté.');
        $this->redirect('benevole', 'connexion');
    }

    public function profil(): void
    {
        $id = $this->exigerConnexion();
        $benevole = $this->model->trouverParId($id);
        if (!$benevole) {
            unset($_SESSION['benevole_id']);
            $this->redirect('benevole', 'connexion');
        }

        $erreurs = [];
        $old = $benevole;
        if ($this->estPost()) {
            $old = $_POST;
            $changeMdp = ($_POST['mot_de_passe'] ?? '') !== '';
            $erreurs = Benevole::valider($_POST, $changeMdp);
            if (!$erreurs && $this->model->emailExiste($_POST['email'], $id)) {
                $erreurs['email'] = 'Cette adresse e-mail est déjà utilisée.';
            }
            if (!$erreurs) {
                $this->model->modifier($id, $_POST);
                $this->flash('Votre profil a été mis à jour.');
                $this->redirect('benevole', 'profil');
            }
        }
        $this->render('front/benevole/profil', compact('erreurs', 'old'));
    }

   

    public function liste(): void
    {
        $recherche = trim($_GET['q'] ?? '');
        $benevoles = $this->model->tous($recherche);
        $this->render('back/benevole/liste', compact('benevoles', 'recherche'), 'back');
    }

    public function modifier(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $benevole = $this->model->trouverParId($id);
        if (!$benevole) {
            $this->flash('Bénévole introuvable.', 'erreur');
            $this->redirect('benevole', 'liste');
        }

        $erreurs = [];
        $old = $benevole;
        if ($this->estPost()) {
            $old = $_POST;
            $changeMdp = ($_POST['mot_de_passe'] ?? '') !== '';
            $erreurs = Benevole::valider($_POST, $changeMdp);
            if (!$erreurs && $this->model->emailExiste($_POST['email'], $id)) {
                $erreurs['email'] = 'Cette adresse e-mail est déjà utilisée.';
            }
            if (!$erreurs) {
                $this->model->modifier($id, $_POST);
                $this->flash('Le bénévole a été modifié.');
                $this->redirect('benevole', 'liste');
            }
        }
        $this->render('back/benevole/edit', compact('erreurs', 'old', 'id'), 'back');
    }

    public function supprimer(): void
    {
        if ($this->estPost()) {
            $this->model->supprimer((int) ($_POST['id'] ?? 0));
            $this->flash('Le bénévole et ses participations ont été supprimés.');
        }
        $this->redirect('benevole', 'liste');
    }
}
