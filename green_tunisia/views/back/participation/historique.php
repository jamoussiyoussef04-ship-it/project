<section class="panneau">
  <h1>Historique des participations</h1>
  <form class="recherche" method="get" action="index.php">
    <input type="hidden" name="controller" value="participation">
    <input type="hidden" name="action" value="historique">
    <label for="id_benevole" class="sr">Bénévole</label>
    <select id="id_benevole" name="id_benevole">
      <option value="">Tous les bénévoles</option>
      <?php foreach ($benevoles as $b): ?>
        <option value="<?= (int) $b['id_benevole'] ?>" <?= $idBenevole == $b['id_benevole'] ? 'selected' : '' ?>>
          <?= e($b['nom'] . ' ' . $b['prenom']) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <button class="btn" type="submit">Filtrer</button>
  </form>

  <?php if (!$participations): ?>
    <p class="vide">Aucune participation enregistrée.</p>
  <?php else: ?>
  <div class="tableau-scroll">
  <table>
    <thead><tr><th>Bénévole</th><th>Campagne</th><th>Date de la campagne</th><th>Date d'inscription</th></tr></thead>
    <tbody>
    <?php foreach ($participations as $p): ?>
      <tr>
        <td><?= e($p['nom'] . ' ' . $p['prenom']) ?></td>
        <td><?= e($p['titre']) ?></td>
        <td><?= e(date('d/m/Y', strtotime($p['date_campagne']))) ?></td>
        <td><?= e(date('d/m/Y', strtotime($p['date_inscription']))) ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  </div>
  <?php endif; ?>
</section>
