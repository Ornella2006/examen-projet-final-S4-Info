<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Intérêts gagnés par mois</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css">
</head>
<body class="bg-gray-100 min-h-screen p-8">
    <div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Intérêts gagnés par mois</h1>
        <form method="get" class="flex gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium">Mois/Année début</label>
                <input type="month" name="debut" required class="border rounded p-1">
            </div>
            <div>
                <label class="block text-sm font-medium">Mois/Année fin</label>
                <input type="month" name="fin" required class="border rounded p-1">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Filtrer</button>
            </div>
        </form>
        <?php if (!empty($data)): ?>
            <table class="w-full mb-6 border">
                <tr class="bg-gray-200"><th class="p-2">Période</th><th class="p-2">Total intérêts</th></tr>
                <?php foreach ($data as $row): ?>
                    <tr>
                        <td class="p-2 text-center"><?= htmlspecialchars($row['periode']) ?></td>
                        <td class="p-2 text-right"><?= number_format($row['total_interets'], 2, ',', ' ') ?> €</td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <canvas id="chart" width="600" height="300"></canvas>
            <script>
                const labels = <?= json_encode(array_column($data, 'periode')) ?>;
                const values = <?= json_encode(array_map('floatval', array_column($data, 'total_interets'))) ?>;
                new Chart(document.getElementById('chart'), {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Intérêts gagnés (€)',
                            data: values,
                            backgroundColor: 'rgba(54, 162, 235, 0.5)'
                        }]
                    }
                });
            </script>
        <?php elseif(isset($data)): ?>
            <div class="text-center text-gray-500">Aucune donnée pour la période sélectionnée.</div>
        <?php endif; ?>
    </div>
</body>
</html>
