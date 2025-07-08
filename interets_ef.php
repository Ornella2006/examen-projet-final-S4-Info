<div class="header">
    <div class="page-title">
        <h1>Intérêts Gagnés par Mois et par Établissement Financier</h1>
    </div>
</div>

<div class="content">
    <div style="margin-bottom: 20px;">
        <label for="debut" style="margin-right: 10px;">Mois/Année début</label>
        <input type="month" id="debut" required>
        <label for="fin" style="margin-left: 20px; margin-right: 10px;">Mois/Année fin</label>
        <input type="month" id="fin" required>
        <button onclick="fetchInterets()">Filtrer</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>Période</th>
                <th>ID EF</th>
                <th>Nom EF</th>
                <th>Total intérêts (€)</th>
            </tr>
        </thead>
        <tbody id="interets-tbody"></tbody>
    </table>

    <canvas id="chart"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const apiBase = "/examen-projet-final-S4-Info/ws";

    function fetchInterets() {
        const debut = document.getElementById('debut').value;
        const fin = document.getElementById('fin').value;
        if (!debut || !fin) {
            alert("Veuillez sélectionner les deux dates.");
            return;
        }
        console.log(`Requête API: ${apiBase}/interets-ef?debut=${debut}&fin=${fin}`);
        fetch(`${apiBase}/interets-ef?debut=${debut}&fin=${fin}`)
            .then(r => {
                if (!r.ok) throw new Error(`Erreur HTTP ${r.status}`);
                return r.json();
            })
            .then(data => {
                console.log("Données reçues:", data);
                const tbody = document.getElementById('interets-tbody');
                tbody.innerHTML = '';

                // Organiser les données par période et EF
                const periods = [...new Set(data.map(row => row.periode))];
                const efMap = {};
                data.forEach(row => {
                    if (!efMap[row.idEtablissementFinancier]) {
                        efMap[row.idEtablissementFinancier] = {
                            nom: row.nomEtablissementFinancier,
                            data: {}
                        };
                    }
                    efMap[row.idEtablissementFinancier].data[row.periode] = parseFloat(row.total_interets);
                });

                // Remplir le tableau
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4">Aucune donnée disponible pour cette période.</td></tr>';
                } else {
                    data.forEach(row => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${row.periode}</td>
                            <td>${row.idEtablissementFinancier}</td>
                            <td>${row.nomEtablissementFinancier}</td>
                            <td>${parseFloat(row.total_interets).toFixed(2)} €</td>
                        `;
                        tbody.appendChild(tr);
                    });
                }

                // Créer le graphique
                const ctx = document.getElementById('chart').getContext('2d');
                if (window.myChart) window.myChart.destroy();
                window.myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: periods,
                        datasets: Object.keys(efMap).map(efId => ({
                            label: `EF ${efId} - ${efMap[efId].nom}`,
                            data: periods.map(p => efMap[efId].data[p] || 0),
                            backgroundColor: `rgba(${Math.random() * 255}, ${Math.random() * 255}, ${Math.random() * 255}, 0.5)`,
                            borderColor: `rgba(${Math.random() * 255}, ${Math.random() * 255}, ${Math.random() * 255}, 1)`,
                            borderWidth: 1
                        }))
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Intérêts (€)',
                                    font: { size: 14 }
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Période',
                                    font: { size: 14 }
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error("Erreur lors du chargement des données:", error);
                alert("Erreur lors du chargement des données: " + error.message);
            });
    }
</script>