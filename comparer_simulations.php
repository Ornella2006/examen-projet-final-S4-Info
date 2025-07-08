<?php
// Pas de <!DOCTYPE html>, <html>, <head>, <body>, ou </html> car inclus dans template.php
?>

<style>
    /* Styles spécifiques à la page de comparaison */
    .simulation-list {
        margin-bottom: 20px;
    }

    .simulation-item {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
        padding: 10px;
        background-color: white;
        border: 1px solid #66b3ff;
        border-radius: 4px;
    }

    .simulation-item input[type="checkbox"] {
        margin-right: 10px;
    }

    .compare-button {
        margin-bottom: 20px;
    }

    .comparison-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .comparison-table th,
    .comparison-table td {
        border: 1px solid #66b3ff;
        padding: 10px;
        text-align: left;
    }

    .comparison-table th {
        background-color: #003366;
        color: white;
    }

    .comparison-table td {
        background-color: white;
    }

    .hidden {
        display: none;
    }
</style>

<div class="header">
    <div class="page-title">
        <h1>Comparer les simulations</h1>
    </div>
</div>

<div class="content">
    <div class="simulation-list">
        <h2>Liste des simulations</h2>
        <div id="simulation-list-items"></div>
        <button class="compare-button" onclick="compareSimulations()">Comparer</button>
        <p id="error-message" class="error"></p>
    </div>
    <div id="comparison-result" class="hidden">
        <h2>Résultat de la comparaison</h2>
        <table class="comparison-table">
            <thead>
                <tr>
                    <th>Critère</th>
                </tr>
            </thead>
            <tbody id="comparison-table-body"></tbody>
        </table>
    </div>
</div>

<script>
    const apiBase = "http://localhost/examen-projet-final-S4-Info/ws";

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

    function loadSimulations() {
        ajax("GET", "/simulations", null, (data) => {
            const listDiv = document.getElementById("simulation-list-items");
            listDiv.innerHTML = "";
            data.forEach(sim => {
                const div = document.createElement("div");
                div.className = "simulation-item";
                div.innerHTML = `
                    <input type="checkbox" name="simulation" value="${sim.idSimulation}">
                    ${sim.clientNom} ${sim.clientPrenom} - ${sim.typePret} (${sim.nomEtablissementFinancier}) - ${sim.montant} €, ${sim.dureeMois} mois, Intérêts: ${sim.interets} € (${sim.dateSimulation})
                `;
                listDiv.appendChild(div);
            });
        }, (status, error) => {
            document.getElementById("error-message").textContent = `Erreur de chargement des simulations: ${error}`;
        });
    }

    function compareSimulations() {
        const checkboxes = document.querySelectorAll('input[name="simulation"]:checked');
        if (checkboxes.length === 0) {
            document.getElementById("error-message").textContent = "Veuillez sélectionner au moins une simulation.";
            return;
        }

        const simIds = Array.from(checkboxes).map(cb => cb.value);
        ajax("GET", `/simulations?ids=${simIds.join(',')}`, null, (data) => {
            if (data.length === 0) {
                document.getElementById("error-message").textContent = "Aucune simulation trouvée pour les identifiants sélectionnés.";
                return;
            }

            // Clear and rebuild table headers dynamically
            const thead = document.querySelector('.comparison-table thead tr');
            thead.innerHTML = '<th>Critère</th>'; // Reset headers
            data.forEach((sim, index) => {
                const th = document.createElement('th');
                th.textContent = `Simulation ${index + 1} (ID: ${sim.idSimulation})`;
                thead.appendChild(th);
            });

            // Populate comparison table
            const tbody = document.getElementById("comparison-table-body");
            tbody.innerHTML = `
                <tr><td>Montant</td>${data.map(sim => `<td>${sim.montant} €</td>`).join('')}</tr>
                <tr><td>Durée (mois)</td>${data.map(sim => `<td>${sim.dureeMois}</td>`).join('')}</tr>
                <tr><td>Délai 1er Remboursement (mois)</td>${data.map(sim => `<td>${sim.delaiPremierRemboursementMois}</td>`).join('')}</tr>
                <tr><td>Intérêts</td>${data.map(sim => `<td>${sim.interets} €</td>`).join('')}</tr>
                <tr><td>Taux d'Assurance</td>${data.map(sim => `<td>${sim.tauxAssurance || 0}%</td>`).join('')}</tr>
                <tr><td>Date de Simulation</td>${data.map(sim => `<td>${sim.dateSimulation}</td>`).join('')}</tr>
            `;
            document.getElementById("comparison-result").classList.remove("hidden");
            document.getElementById("error-message").textContent = "";
        }, (status, error) => {
            document.getElementById("error-message").textContent = `Erreur lors de la comparaison: ${error}`;
        });
    }

    // Charger les simulations au démarrage
    loadSimulations();
</script>