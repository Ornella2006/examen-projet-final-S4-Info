<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un prêt</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h1 class="text-2xl font-bold mb-6 text-center">Nouveau Prêt</h1>
        <?php if (!empty($error)): ?>
            <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="bg-green-100 text-green-700 p-4 rounded mb-4">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>
        <form action="/examen-projet-final-S4-Info/ws/pret/new" method="POST" class="space-y-4">
            <div>
                <label for="idClient" class="block text-sm font-medium text-gray-700">ID Client</label>
                <input type="number" name="idClient" id="idClient" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
            </div>
            <div>
                <label for="idTypePret" class="block text-sm font-medium text-gray-700">ID Type de Prêt</label>
                <input type="number" name="idTypePret" id="idTypePret" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
            </div>
            <div>
                <label for="montant" class="block text-sm font-medium text-gray-700">Montant</label>
                <input type="number" step="0.01" name="montant" id="montant" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
            </div>
            <div>
                <label for="dureeMois" class="block text-sm font-medium text-gray-700">Durée (mois)</label>
                <input type="number" name="dureeMois" id="dureeMois" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
            </div>
            <div>
                <label for="dateDemande" class="block text-sm font-medium text-gray-700">Date de demande</label>
                <input type="date" name="dateDemande" id="dateDemande" class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
            </div>
            <div>
                <label for="dateAccord" class="block text-sm font-medium text-gray-700">Date d'accord</label>
                <input type="date" name="dateAccord" id="dateAccord" class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
            </div>
            <div>
                <label for="datePremiereEcheance" class="block text-sm font-medium text-gray-700">Date 1ère échéance</label>
                <input type="date" name="datePremiereEcheance" id="datePremiereEcheance" class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded-md hover:bg-blue-600">Créer le prêt</button>
        </form>
    </div>
</body>
</html>
