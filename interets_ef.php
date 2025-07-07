<div class="header">
    <div class="page-title">
        <h1>Intérêts Gagnés par Mois</h1>
    </div>
    <!-- <div class="user-profile">
        <img src="template/logo.png" alt="Admin">
        <span>Admin</span>
        <i class="fas fa-chevron-down"></i>
    </div> -->
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
        fetch(`${apiBase}/interets_ef?debut=${debut}&fin=${fin}`)
            .then(r => r.json())
            .then(data => {
                const tbody = document.getElementById('interets-tbody');
                tbody.innerHTML = '';
                const labels = [];
                const values = [];
                data.forEach(row => {
                    labels.push(row.periode);
                    values.push(parseFloat(row.total_interets));
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${row.periode}</td>
                        <td>${row.total_interets} €</td>
                    `;
                    tbody.appendChild(tr);
                });

                const ctx = document.getElementById('chart').getContext('2d');
                if (window.myChart) window.myChart.destroy();
                window.myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Intérêts gagnés (€)',
                            data: values,
                            backgroundColor: 'rgba(52, 152, 219, 0.5)',
                            borderColor: 'rgba(52, 152, 219, 1)',
                            borderWidth: 1
                        }]
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
                alert("Erreur lors du chargement des données.");
            });
    }
</script>