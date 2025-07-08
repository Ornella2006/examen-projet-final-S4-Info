<?php
// No <!DOCTYPE html>, <html>, <head>, or <body> tags as included in template.php
?>

<style>
    .funds-container {
        margin-bottom: 20px;
    }

    .filter-section {
        margin-bottom: 20px;
        padding: 20px;
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
    }

    .filter-section label {
        margin-right: 10px;
        font-weight: 500;
        color: #1e293b;
    }

    .filter-section input[type="month"] {
        padding: 8px;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        font-size: 14px;
        margin-right: 10px;
    }

    .filter-section button {
        padding: 8px 16px;
        background-color: #003366;
        color: #ffffff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .filter-section button:hover {
        background-color: #004080;
    }

    .funds-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
    }

    .funds-table th,
    .funds-table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #e2e8f0;
    }

    .funds-table th {
        background-color: #003366;
        color: #ffffff;
        font-weight: 600;
    }

    .funds-table td {
        color: #1e293b;
    }

    .no-data {
        text-align: center;
        color: #64748b;
        padding: 20px;
    }
</style>

<div class="header">
    <div class="page-title">
        <h1>Fonds disponibles par mois</h1>
    </div>
</div>

<div class="content">
    <div class="funds-container">
        <div class="filter-section">
            <label for="start-month">Mois de début :</label>
            <input type="month" id="start-month" name="start-month" value="<?php echo date('Y-m'); ?>">
            <label for="end-month">Mois de fin :</label>
            <input type="month" id="end-month" name="end-month" value="<?php echo date('Y-m'); ?>">
            <button onclick="loadFunds()">Filtrer</button>
        </div>
        <div id="funds-table-container">
            <table class="funds-table" id="funds-table">
                <thead>
                    <tr>
                        <th>Mois</th>
                        <th>Montant disponible (€)</th>
                    </tr>
                </thead>
                <tbody id="funds-table-body"></tbody>
            </table>
            <div id="no-data" class="no-data" style="display: none;">Aucune donnée disponible</div>
        </div>
    </div>
</div>

<script>
    const apiBase = "http://localhost/examen-projet-final-S4-Info/ws";
    const idEtablissementFinancier = 1; // Replace with dynamic ID if needed

    function ajax(method, url, data, callback, errorCallback) {
        const xhr = new XMLHttpRequest();
        xhr.open(method, apiBase + url, true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    callback(JSON.parse(xhr.responseText));
                } else {
                    errorCallback(xhr.status, xhr.responseText);
                }
            }
        };
        console.log(`Envoi ${method} ${url} avec données : ${data}`);
        xhr.send(data);
    }

    function loadFunds() {
        const startMonth = document.getElementById('start-month').value + '-01';
        const endMonth = document.getElementById('end-month').value + '-01';
        const url = `/etablissements/${idEtablissementFinancier}/funds?start_month=${startMonth}&end_month=${endMonth}`;

        ajax("GET", url, null, (data) => {
            const tbody = document.getElementById('funds-table-body');
            const noData = document.getElementById('no-data');
            tbody.innerHTML = '';
            noData.style.display = 'none';

            if (Object.keys(data).length === 0) {
                noData.style.display = 'block';
                return;
            }

            Object.entries(data).forEach(([month, amount]) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${month}</td>
                    <td>${amount.toFixed(2)} €</td>
                `;
                tbody.appendChild(row);
            });
        }, (status, error) => {
            document.getElementById('no-data').style.display = 'block';
            document.getElementById('no-data').textContent = `Erreur: ${error}`;
        });
    }

    // Load funds on page load with default dates
    window.onload = loadFunds;
</script>