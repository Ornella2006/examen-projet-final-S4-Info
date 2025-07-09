<?php
// Pas de <!DOCTYPE html>, <html>, <head>, <body>, ou </html> car inclus dans template.php
?>

<div class="header">
    <div class="page-title">
        <h1>Gestion des prêts clients</h1>
    </div>
</div>

<div class="content">
    <div class="form-container">
        <div>
            <h2>Nouveau prêt</h2>
            <select id="idClient" required>
                <option value="">Sélectionner un client</option>
            </select>
            <select id="idTypePret" required>
                <option value="">Sélectionner un type de prêt</option>
            </select>
            <select id="idEtablissementFinancier" required>
                <option value="">Sélectionner un établissement</option>
            </select>
            <input type="number" id="montant" placeholder="Montant (€)" step="100" min="1000" required>
            <input type="number" id="dureeMois" placeholder="Durée (mois)" min="1" required>
            <div class="input-group">
                <label for="delaiPremierRemboursementMois">Délai 1er remboursement (mois) :</label>
                <input type="number" id="delaiPremierRemboursementMois" placeholder="Ex: 3 mois" min="0" max="24" value="0" title="Nombre de mois avant le premier remboursement">
            </div>
            <input type="date" id="dateDemande" required>
            <input type="number" id="tauxAssurance" placeholder="Taux d'assurance (%)" step="0.01" min="0" max="5" value="0">
            <button onclick="creerPret()">Créer le prêt</button>
            <p id="error-pret-message" class="error"></p>
            <p id="success-pret-message" class="success"></p>
        </div>

        <h2>Prêts enregistrés</h2>
        <table id="table-prets">
            <thead id="table-prets-thead">
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Type de prêt</th>
                    <th>Montant (€)</th>
                    <th>Durée (mois)</th>
                    <th>Intérêts (€)</th>
                    <th>Taux d’intérêt (%)</th>
                    <th>Taux d’assurance (%)</th>
                    <th>Annuité mensuelle (€)</th>
                    <th>Assurance mensuelle (€)</th>
                    <th>Paiement mensuel (€)</th>
                    <th>Somme totale à rembourser (€)</th>
                    <th>Date de demande</th>
                    <th>Date de retour estimée</th>
                    <th>Statut</th>
                    <th class="actions-column">Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>   d:\ETU003273_ETU003255_ETU003243
            const montant = document.getElementById("montant").value;
            const dureeMois = document.getElementById("dureeMois").value;
            const delaiPremierRemboursementMois = document.getElementById("delaiPremierRemboursementMois").value;
            const dateDemande = document.getElementById("dateDemande").value;
            const tauxAssurance = document.getElementById("tauxAssurance").value;

            if (!idClient) {
                document.getElementById("error-pret-message").textContent = "Sélectionnez un client.";
                return;
            }
            if (!idTypePret) {
                document.getElementById("error-pret-message").textContent = "Sélectionnez un type de prêt.";
                return;
            }
            if (!idEtablissementFinancier) {
                document.getElementById("error-pret-message").textContent = "Sélectionnez un établissement.";
                return;
            }
            const parsedMontant = parseFloat(montant);
            if (montant === "" || isNaN(parsedMontant) || parsedMontant < 1000) {
                document.getElementById("error-pret-message").textContent = "Le montant doit être supérieur ou égal à 1000 €.";
                return;
            }
            const parsedDureeMois = parseInt(dureeMois);
            if (dureeMois === "" || isNaN(parsedDureeMois) || parsedDureeMois <= 0) {
                document.getElementById("error-pret-message").textContent = "La durée doit être un entier positif.";
                return;
            }
            const parsedDelai = parseInt(delaiPremierRemboursementMois) || 0;
            if (parsedDelai < 0 || parsedDelai > 24) {
                document.getElementById("error-pret-message").textContent = "Le délai de premier remboursement doit être compris entre 0 et 24 mois.";
                return;
            }
            if (!dateDemande) {
                document.getElementById("error-pret-message").textContent = "La date de demande est requise.";
                return;
            }
            const parsedTauxAssurance = parseFloat(tauxAssurance) || 0;
            if (tauxAssurance !== "" && (parsedTauxAssurance < 0 || parsedTauxAssurance > 5)) {
                document.getElementById("error-pret-message").textContent = "Le taux d'assurance doit être compris entre 0 et 5%.";
                return;
            }

            const data = `idClient=${idClient}&idTypePret=${idTypePret}&idEtablissementFinancier=${idEtablissementFinancier}&montant=${parsedMontant}&dureeMois=${parsedDureeMois}&delaiPremierRemboursementMois=${parsedDelai}&dateDemande=${dateDemande}&tauxAssurance=${parsedTauxAssurance}`;
            console.log("Valeurs avant envoi:", { idClient, idTypePret, idEtablissementFinancier, montant: parsedMontant, dureeMois: parsedDureeMois, delaiPremierRemboursementMois: parsedDelai, dateDemande, tauxAssurance: parsedTauxAssurance });

            ajax("POST", "/prets", data, (response) => {
                document.getElementById("success-pret-message").textContent = `Prêt créé avec l'ID ${response.id}. Cliquez sur Valider pour confirmer.`;
                chargerPrets();
                document.getElementById("error-pret-message").textContent = "";
                resetForm();
                document.getElementById("simulation-result").innerHTML = "";
            }, (status, error) => {
                document.getElementById("error-pret-message").textContent = `Erreur lors de la création du prêt: ${error}`;
            });
        } catch (error) {
            console.error("Erreur dans creerPret:", error);
            document.getElementById("error-pret-message").textContent = `Erreur JavaScript: ${error.message}`;
        }
    }

    function validerPret(id) {
        try {
            ajax("POST", `/prets/${id}/valider`, null, () => {
                document.getElementById("success-pret-message").textContent = `Prêt ${id} validé avec succès.`;
                chargerPrets();
                document.getElementById("error-pret-message").textContent = "";
            }, (status, error) => {
                document.getElementById("error-pret-message").textContent = `Erreur lors de la validation du prêt ${id}: ${error}`;
            });
        } catch (error) {
            console.error("Erreur dans validerPret:", error);
            document.getElementById("error-pret-message").textContent = `Erreur JavaScript: ${error.message}`;
        }
    }

   function exporterPDF(id) {
    console.log("Exportation PDF pour idPret:", id);
    window.location.href = `/ETU003273/export_pret_pdf.php?idPret=${id}`;
}

    function chargerPrets() {
        ajax("GET", "/prets", null, (data) => {
            const tbody = document.querySelector("#table-prets tbody");
            const thead = document.querySelector("#table-prets-thead");
            tbody.innerHTML = "";

            const hasPendingLoans = data.some(p => p.statut === 'en_attente');

            thead.innerHTML = `
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Type de prêt</th>
                    <th>Montant (€)</th>
                    <th>Durée (mois)</th>
                    <th>Intérêts (€)</th>
                    <th>Taux d’intérêt (%)</th>
                    <th>Taux d’assurance (%)</th>
                    <th>Annuité mensuelle (€)</th>
                    <th>Assurance mensuelle (€)</th>
                    <th>Paiement mensuel (€)</th>
                    <th>Somme totale à rembourser (€)</th>
                    <th>Date de demande</th>
                    <th>Date de retour estimée</th>
                    <th>Statut</th>
                    <th class="actions-column">Actions</th>
                </tr>
            `;

            data.forEach(p => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td>${p.idPret}</td>
                    <td>${p.nom} ${p.prenom}</td>
                    <td>${p.libelle}</td>
                    <td>${p.montant}</td>
                    <td>${p.dureeMois}</td>
                    <td>${p.interets}</td>
                    <td>${p.tauxInteretAnnuel}</td>
                    <td>${p.tauxAssurance}</td>
                    <td>${p.annuiteMensuelle}</td>
                    <td>${p.assuranceMensuelle}</td>
                    <td>${p.paiementMensuel}</td>
                    <td>${p.sommeTotaleRembourser}</td>
                    <td>${p.dateDemande}</td>
                    <td>${p.dateRetourEstimee}</td>
                    <td>${p.statut}</td>
                    <td>
                        ${p.statut === 'en_attente' ? `<button onclick="validerPret(${p.idPret})">Valider</button>` : ''}
                        ${p.statut === 'accorde' ? `<button onclick="exporterPDF(${p.idPret})">PDF</button>` : ''}
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }, (status, error) => {
            document.getElementById("error-pret-message").textContent = `Erreur de chargement des prêts: ${error}`;
        });
    }

    function resetForm() {
        document.getElementById("idClient").value = "";
        document.getElementById("idTypePret").value = "";
        document.getElementById("idEtablissementFinancier").value = "";
        document.getElementById("montant").value = "";
        document.getElementById("dureeMois").value = "";
        document.getElementById("delaiPremierRemboursementMois").value = "0";
        document.getElementById("dateDemande").value = "";
        document.getElementById("tauxAssurance").value = "0";
        document.getElementById("error-pret-message").textContent = "";
        document.getElementById("success-pret-message").textContent = "";
        document.getElementById("simulation-result").innerHTML = "";
    }

    chargerClients();
    chargerTypesPrets();
    chargerEtablissements();
    chargerPrets();
    document.getElementById("dateDemande").value = new Date().toISOString().split('T')[0];
</script>