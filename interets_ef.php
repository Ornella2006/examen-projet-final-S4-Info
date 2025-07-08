<style>


.page-title h1 {
    margin: 0;
    font-size: 22px;
    font-weight: 500;
}

.content {
    background-color: white;
    padding: 25px;
    border-radius: 5px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

/* Filtres */
.content > div:first-child {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 25px;
    padding: 15px;
    background-color: #f5f9ff; /* Fond bleu très clair */
    border-radius: 5px;
}

.content > div:first-child label {
    font-weight: 500;
    color: #003366; /* Bleu marine */
    margin-right: 5px;
}

.content > div:first-child input[type="month"] {
    padding: 8px 12px;
    border: 1px solid #66b3ff; /* Bleu clair */
    border-radius: 4px;
    font-family: 'Poppins', sans-serif;
}

.content > div:first-child button {
    background-color: #003366; /* Bleu marine */
    color: white;
    border: none;
    padding: 8px 20px;
    border-radius: 4px;
    cursor: pointer;
    font-family: 'Poppins', sans-serif;
    transition: background-color 0.3s;
}

.content > div:first-child button:hover {
    background-color: #004080; /* Bleu marine plus clair */
}

/* Tableau */
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 30px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

table thead {
    background-color: #003366; /* Bleu marine */
    color: white;
}

table th {
    padding: 12px 15px;
    text-align: left;
    font-weight: 500;
}

table tbody tr {
    border-bottom: 1px solid #e0e0e0;
}

table tbody tr:nth-child(even) {
    background-color: #f8fafc;
}

table tbody tr:hover {
    background-color: #f0f7ff; /* Bleu très clair */
}

table td {
    padding: 12px 15px;
    color: #333;
}

table td:last-child {
    font-weight: 500;
    color: #003366; /* Bleu marine */
}

/* Graphique */
#chart {
    margin-top: 30px;
    max-height: 500px;
}

/* Message quand pas de données */
.no-data {
    text-align: center;
    padding: 20px;
    color: #666;
    font-style: italic;
}

/* Responsive */
@media (max-width: 768px) {
    .content > div:first-child {
        flex-direction: column;
        align-items: flex-start;
    }
    
    table {
        display: block;
        overflow-x: auto;
    }
    
    #chart {
        max-width: 100%;
    }
}
</style>
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
    const apiBase = "http://localhost/examen-projet-final-S4-Info/ws";

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