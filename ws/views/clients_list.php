<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des clients</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-8">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Liste des clients</h1>
        <a href="../ws/export_clients_pdf.php" target="_blank" class="mb-4 inline-block bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Exporter en PDF (FPDF)</a>
        <div class="overflow-x-auto">
        <table class="min-w-full border">
            <thead class="bg-gray-200">
                <tr>
                    <th class="p-2">ID</th>
                    <th class="p-2">Nom</th>
                    <th class="p-2">Prénom</th>
                    <th class="p-2">Adresse</th>
                    <th class="p-2">Téléphone</th>
                    <th class="p-2">Email</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($clients as $client): ?>
                <tr>
                    <td class="p-2 text-center"><?= htmlspecialchars($client['idClient']) ?></td>
                    <td class="p-2 text-center"><?= htmlspecialchars($client['nom']) ?></td>
                    <td class="p-2 text-center"><?= htmlspecialchars($client['prenom']) ?></td>
                    <td class="p-2 text-center"><?= htmlspecialchars($client['adresse']) ?></td>
                    <td class="p-2 text-center"><?= htmlspecialchars($client['telephone']) ?></td>
                    <td class="p-2 text-center"><?= htmlspecialchars($client['email']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>
</body>
</html>
