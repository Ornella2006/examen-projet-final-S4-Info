<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enregistrer un Type de Prêt</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h1 class="text-2xl font-bold mb-6 text-center">Nouveau Type de Prêt</h1>
        
        <?php if (!empty($error)): ?>
            <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="bg-green-100 text-green-700 p-4 rounded mb-4">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
       <form action="/examen-projet-final-S4-Info/ws/type_pret/new" method="POST" class="space-y-4">
            <div>
                <label for="libelle" class="block text-sm font-medium text-gray-700">Nom du type de prêt</label>
                <input type="text" name="libelle" id="libelle" required
                       class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label for="tauxInteret" class="block text-sm font-medium text-gray-700">Taux d'intérêt (%)</label>
                <input type="number" step="0.01" min="0" name="tauxInteret" id="tauxInteret" required
                       class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label for="dureeMaxMois" class="block text-sm font-medium text-gray-700">Durée maximale (mois)</label>
                <input type="number" min="1" name="dureeMaxMois" id="dureeMaxMois" required
                       class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit"
                    class="w-full bg-blue-500 text-white p-2 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                Enregistrer
            </button>
        </form>
    </div>
</body>
</html>