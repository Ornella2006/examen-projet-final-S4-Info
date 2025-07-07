<h2>Enregistrer un nouveau type de prêt</h2>
<?php if (!empty($error)): ?>
    <div style="color:red;">Erreur : <?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<form method="post" action="">
    <label>Nom / Libellé du type de prêt *</label><br>
    <input type="text" name="libelle" value="<?= htmlspecialchars($old['libelle'] ?? '') ?>" required><br><br>
    <label>Taux d’intérêt (%) *</label><br>
    <input type="number" name="tauxInteret" min="0" step="0.01" value="<?= htmlspecialchars($old['tauxInteret'] ?? '') ?>" required><br><br>
    <label>Durée par défaut (mois) *</label><br>
    <input type="number" name="dureeMaxMois" min="1" value="<?= htmlspecialchars($old['dureeMaxMois'] ?? '') ?>" required><br><br>
    <button type="submit">Valider</button>
</form>
