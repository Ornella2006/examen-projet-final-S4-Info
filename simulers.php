<?php
// Pas de <!DOCTYPE html>, <html>, <head>, <body>, ou </html> car inclus dans template.php
?>
<div class="header">
    <div class="page-title">
        <h1>Simulation de prêts</h1>
    </div>
</div>

<div class="content">
    <div>
        <h2>Simulation de prêt</h2>
        <fieldset>
            <legend>Paramètres du prêt</legend>
            <div class="input-group">
                <label for="sim-idTypePret">Type de prêt :</label>
                <select id="sim-idTypePret" required>
                    <option value="">Sélectionner un type de prêt</option>
                </select>
            </div>
            <div class="input-group">
                <label for="sim-montant">Montant du prêt (€) :</label>
                <input type="number" id="sim-montant" placeholder="Ex: 10000 €" step="0.01" min="1000" required>
            </div>
            <div class="input-group">
                <label for="sim-dureeMois">Durée du prêt (mois) :</label>
                <input type="number" id="sim-dureeMois" placeholder="Ex: 12 mois" min="1" required>
            </div>
            <div class="input-group">
                <label for="sim-delaiPremierRemboursementMois">Délai avant 1er remboursement (mois) :</label>
                <input type="number" id="sim-delaiPremierRemboursementMois" placeholder="Ex: 3 mois" min="0" max="24" value="0" title="Nombre de mois avant le premier remboursement">
            </div>
            <div class="input-group">
                <label for="sim-dateDemande">Date de demande :</label>
                <input type="date" id="sim-dateDemande" required>
            </div>
            <div class="input-group">
                <label for="sim-tauxAssurance">Taux d'assurance (% par an) :</label>
                <input type="number" id="sim-tauxAssurance" placeholder="Ex: 0.5 %" step="0.01" min="0" max="5" value="0" title="Taux d'assurance annuel en pourcentage">
            </div>
        </fieldset>
        <button onclick="simulerPret()">Simuler</button>
        <p id="error-message" class="error"></p>
        <div id="simulation-result"></div>
    </div>
</div>

<script>
    const apiBase = "/examen-projet-final-S4-Info/ws";

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

    function chargerTypesPrets() {
        ajax("GET", "/types-prets", null, (data) => {
            const simSelect = document.getElementById("sim-idTypePret");
            simSelect.innerHTML = '<option value="">Sélectionner un type de prêt</option>';
            data.forEach(t => {
                const option = document.createElement("option");
                option.value = t.idTypePret;
                option.textContent = `${t.libelle} (Taux: ${t.tauxInteret}%, Durée max: ${t.dureeMaxMois} mois)`;
                simSelect.appendChild(option);
            });
        }, (status, error) => {
            document.getElementById("error-message").textContent = `Erreur de chargement des types de prêts: ${error}`;
        });
    }

    function simulerPret() {
        try {
            const idTypePret = document.getElementById("sim-idTypePret").value;
            const montant = document.getElementById("sim-montant").value;
            const dureeMois = document.getElementById("sim-dureeMois").value;
            const delaiPremierRemboursementMois = document.getElementById("sim-delaiPremierRemboursementMois").value;
            const dateDemande = document.getElementById("sim-dateDemande").value;
            const tauxAssurance = document.getElementById("sim-tauxAssurance").value;

            if (!idTypePret) {
                document.getElementById("error-message").textContent = "Sélectionnez un type de prêt pour la simulation.";
                return;
            }
            const parsedMontant = parseFloat(montant);
            if (montant === "" || isNaN(parsedMontant) || parsedMontant < 1000) {
                document.getElementById("error-message").textContent = "Le montant doit être supérieur ou égal à 1000 €.";
                return;
            }
            const parsedDureeMois = parseInt(dureeMois);
            if (dureeMois === "" || isNaN(parsedDureeMois) || parsedDureeMois <= 0) {
                document.getElementById("error-message").textContent = "La durée doit être un entier positif.";
                return;
            }
            const parsedDelai = parseInt(delaiPremierRemboursementMois) || 0;
            if (parsedDelai < 0 || parsedDelai > 24) {
                document.getElementById("error-message").textContent = "Le délai de premier remboursement doit être compris entre 0 et 24 mois.";
                return;
            }
            if (!dateDemande) {
                document.getElementById("error-message").textContent = "La date de demande est requise.";
                return;
            }
            const parsedTauxAssurance = parseFloat(tauxAssurance) || 0;
            if (tauxAssurance !== "" && (parsedTauxAssurance < 0 || parsedTauxAssurance > 5)) {
                document.getElementById("error-message").textContent = "Le taux d'assurance doit être compris entre 0 et 5%.";
                return;
            }

            const data = `idTypePret=${idTypePret}&montant=${parsedMontant}&dureeMois=${parsedDureeMois}&delaiPremierRemboursementMois=${parsedDelai}&dateDemande=${dateDemande}&tauxAssurance=${parsedTauxAssurance}`;
            console.log("Valeurs simulation:", { idTypePret, montant: parsedMontant, dureeMois: parsedDureeMois, delaiPremierRemboursementMois: parsedDelai, dateDemande, tauxAssurance: parsedTauxAssurance });

            ajax("POST", "/prets/simuler", data, (response) => {
                document.getElementById("simulation-result").innerHTML = `
                    <h3>Résultat de la simulation</h3>
                    <p>Montant: ${response.montant} €</p>
                    <p>Durée: ${response.dureeMois} mois</p>
                    <p>Taux d'intérêt: ${response.tauxInteret}%</p>
                    <p>Taux d'assurance: ${response.tauxAssurance}%</p>
                    <p>Annuité mensuelle: ${response.annuite} €</p>
                    <p>Intérêts totaux: ${response.interetsTotaux} €</p>
                    <p>Coût total du prêt: ${response.coutTotal} €</p>
                    <p>Date de retour estimée: ${response.dateRetourEstimee}</p>
                `;
                document.getElementById("error-message").textContent = "";
            }, (status, error) => {
                document.getElementById("error-message").textContent = `Erreur lors de la simulation: ${error}`;
            });
        } catch (error) {
            console.error("Erreur dans simulerPret:", error);
            document.getElementById("error-message").textContent = `Erreur JavaScript: ${error.message}`;
        }
    }

    // Charger les données au démarrage
    chargerTypesPrets();
    document.getElementById("sim-dateDemande").value = new Date().toISOString().split('T')[0];
</script>