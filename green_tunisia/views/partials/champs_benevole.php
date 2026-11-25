<?php
// Variables attendues : $old, $erreurs, $motDePasseObligatoire (bool)
$champ = function (string $nom, string $label, string $type = 'text', string $aide = '') use ($old, $erreurs) {
    $valeur = $type === 'password' ? '' : ($old[$nom] ?? '');
    $id = 'champ_' . $nom;
    $invalide = isset($erreurs[$nom]);
    echo '<div class="mb-3">';
    echo '<label for="' . $id . '" class="form-label fw-medium">' . e($label) . '</label>';
    echo '<input id="' . $id . '" name="' . e($nom) . '" type="' . $type . '" class="form-control' . ($invalide ? ' is-invalid' : '') . '" value="' . e($valeur) . '">';
    if ($aide) {
        echo '<div class="form-text">' . e($aide) . '</div>';
    }
    if ($invalide) {
        echo '<div class="invalid-feedback d-block">' . e($erreurs[$nom]) . '</div>';
    }
    echo '</div>';
};
$champ('prenom', 'Prénom');
$champ('nom', 'Nom');
$champ('email', 'Adresse e-mail', 'email');
$champ('telephone', 'Téléphone', 'tel', '8 chiffres, par exemple 20123456');
$aideMdp = $motDePasseObligatoire ? 'Au moins 6 caractères.' : 'Laissez vide pour garder le mot de passe actuel.';
$champ('mot_de_passe', $motDePasseObligatoire ? 'Mot de passe' : 'Nouveau mot de passe', 'password', $aideMdp);
$champ('confirmation', 'Confirmer le mot de passe', 'password');