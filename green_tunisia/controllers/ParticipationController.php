<?php
class ParticipationController extends Controller
{
    private Participation $model;

    public function __construct()
    {
        $this->model = new Participation();
    }



    public function mesParticipations(): void
    {
        $id = $this->exigerConnexion();
        $participations = $this->model->parBenevole($id);
        $disponibles = $this->model->campagnesDisponibles($id);
        $this->render('front/benevole/mes_participations', compact('participations', 'disponibles'));
    }

    public function inscrire(): void
    {
        $id = $this->exigerConnexion();
        if ($this->estPost()) {
            $erreur = $this->model->inscrire($id, (int) ($_POST['id_campagne'] ?? 0));
            $erreur ? $this->flash($erreur, 'erreur') : $this->flash('Votre inscription est enregistrée.');
        }
        $this->redirect('participation', 'mesParticipations');
    }

    public function annuler(): void
    {
        $id = $this->exigerConnexion();
        if ($this->estPost()) {
            $this->model->annuler((int) ($_POST['id_participation'] ?? 0), $id);
            $this->flash('Votre participation est annulée.');
        }
        $this->redirect('participation', 'mesParticipations');
    }



    public function historique(): void
    {
        $idBenevole = (int) ($_GET['id_benevole'] ?? 0) ?: null;
        $participations = $this->model->historique($idBenevole);
        $benevoles = (new Benevole())->tous();
        $this->render('back/participation/historique', compact('participations', 'benevoles', 'idBenevole'), 'back');
    }
}
