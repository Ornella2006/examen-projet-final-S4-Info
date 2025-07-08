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
    <p id="error-message" class="error"></p>
    <p id="success-message" class="success"></p>
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
          <th>Taux d’intérêt (%)</th>
        <th>Taux d’assurance (%)</th>
        <th>Annuité mensuelle (€)</th>
        <th>Somme totale à rembourser (€)<
        <th>Date de demande</th>
        <th>Date de retour estimée</th>
        <th>Statut</th>
        <th class="actions-column">Actions</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>
   </div>   
</div>

  <script>
    const apiBase = "/ETU003273/ws";

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

    function chargerClients() {
      ajax("GET", "/clients", null, (data) => {
        const select = document.getElementById("idClient");
        select.innerHTML = '<option value="">Sélectionner un client</option>';
        data.forEach(c => {
          const option = document.createElement("option");
          option.value = c.idClient;
          option.textContent = `${c.nom} ${c.prenom} (${c.email})`;
          select.appendChild(option);
        });
      }, (status, error) => {
        document.getElementById("error-pret-message").textContent = `Erreur de chargement des clients: ${error}`;
      });
    }

    function chargerTypesPrets() {
      ajax("GET", "/types-prets", null, (data) => {
        const select = document.getElementById("idTypePret");
        select.innerHTML = '<option value="">Sélectionner un type de prêt</option>';
        data.forEach(t => {
          const option = document.createElement("option");
          option.value = t.idTypePret;
          option.textContent = `${t.libelle} (Taux: ${t.tauxInteret}%, Durée max: ${t.dureeMaxMois} mois)`;
          select.appendChild(option);
        });
      }, (status, error) => {
        document.getElementById("error-pret-message").textContent = `Erreur de chargement des types de prêts: ${error}`;
      });
    }

    function chargerEtablissements() {
      ajax("GET", "/etablissements", null, (data) => {
        const select = document.getElementById("idEtablissementFinancier");
        select.innerHTML = '<option value="">Sélectionner un établissement</option>';
        data.forEach(e => {
          const option = document.createElement("option");
          option.value = e.idEtablissementFinancier;
          option.textContent = `${e.nomEtablissementFinancier} (Solde: ${e.fondTotal} €)`;
          select.appendChild(option);
        });
      }, (status, error) => {
        document.getElementById("error-pret-message").textContent = `Erreur de chargement des établissements: ${error}`;
      });
    }

    function simulerPret() {
      try {
        const idTypePret = document.getElementById("idTypePret").value;
        const montant = document.getElementById("montant").value;
        const dureeMois = document.getElementById("dureeMois").value;
        const delaiPremierRemboursementMois = document.getElementById("delaiPremierRemboursementMois").value;
        const dateDemande = document.getElementById("dateDemande").value;
        const tauxAssurance = document.getElementById("tauxAssurance").value;

        if (!idTypePret) {
          document.getElementById("error-pret-message").textContent = "Sélectionnez un type de prêt.";
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
        const parsedTauxAssurance = parseFloat(tauxAssurance) || 0;
        if (tauxAssurance !== "" && (parsedTauxAssurance < 0 || parsedTauxAssurance > 5)) {
          document.getElementById("error-pret-message").textContent = "Le taux d'assurance doit être compris entre 0 et 5%.";
          return;
        }
        if (!dateDemande) {
          document.getElementById("error-pret-message").textContent = "La date de demande est requise.";
          return;
        }

        const data = `idTypePret=${idTypePret}&montant=${parsedMontant}&dureeMois=${parsedDureeMois}&delaiPremierRemboursementMois=${parsedDelai}&dateDemande=${dateDemande}&tauxAssurance=${parsedTauxAssurance}`;
        ajax("POST", "/prets/simuler", data, (response) => {
          document.getElementById("simulation-result").innerHTML = `
            <h3>Résultat de la simulation</h3>
            <p>Annuité mensuelle : ${response.annuite} €</p>
            <p>Intérêts totaux : ${response.interetsTotaux} €</p>
            <p>Coût total : ${response.coutTotal} €</p>
            <p>Date de retour estimée : ${response.dateRetourEstimee}</p>
          `;
          document.getElementById("error-pret-message").textContent = "";
        }, (status, error) => {
          document.getElementById("error-pret-message").textContent = `Erreur lors de la simulation: ${error}`;
        });
      } catch (error) {
        console.error("Erreur dans simulerPret:", error);
        document.getElementById("error-pret-message").textContent = `Erreur JavaScript: ${error.message}`;
      }
    }

    function creerPret() {
      try {
        const idClient = document.getElementById("idClient").value;
        const idTypePret = document.getElementById("idTypePret").value;
        const idEtablissementFinancier = document.getElementById("idEtablissementFinancier").value;
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
            <th>Somme totale à rembourser (€)</th>
            <th>Date de demande</th>
            <th>Date de retour estimée</th>
            <th>Statut</th>
            ${hasPendingLoans ? '<th class="actions-column">Actions</th>' : ''}
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
            <td>${p.sommeTotaleRembourser}</td>
            <td>${p.dateDemande}</td>
            <td>${p.dateRetourEstimee}</td>
            <td>${p.statut}</td>
            ${hasPendingLoans && p.statut === 'en_attente' ? `<td><button onclick="validerPret(${p.idPret})">Valider</button></td>` : (hasPendingLoans ? '<td></td>' : '')}
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
</body>
</html>