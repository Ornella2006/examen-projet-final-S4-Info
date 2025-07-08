<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Gestion des remboursements</title>
  
</head>
<body>

  <h1>Gestion des remboursements</h1>

  <div>
    <h2>Rembourser un prêt</h2>
    <select id="idPretRemboursement" required>
      <option value="">Sélectionner un prêt</option>
    </select>
    <input type="number" id="montantRembourse" placeholder="Montant remboursé (€)" step="0.01" min="0" readonly>
    <input type="date" id="dateRemboursement" required>
    <button onclick="rembourserPret()">Rembourser</button>
    <p id="error-remboursement-message" class="error"></p>
    <p id="success-remboursement-message" class="success"></p>
    <div id="suivi-remboursement"></div>
  </div>

  <h2>Remboursements effectués</h2>
  <table id="table-remboursements">
    <thead>
      <tr>
        <th>ID Remboursement</th>
        <th>ID Prêt</th>
        <th>Client</th>
        <th>Montant remboursé (€)</th>
        <th>Date de remboursement</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>

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

    function chargerPretsRemboursement() {
      ajax("GET", "/prets", null, (data) => {
        const select = document.getElementById("idPretRemboursement");
        select.innerHTML = '<option value="">Sélectionner un prêt</option>';
        data.filter(p => p.statut === 'accorde').forEach(p => {
          const option = document.createElement("option");
          option.value = p.idPret;
          option.textContent = `Prêt #${p.idPret} - ${p.nom} ${p.prenom} (${p.montant} €, Annuité: ${p.annuiteMensuelle} €)`;
          option.dataset.annuite = p.annuiteMensuelle;
          option.dataset.dateAccord = p.dateAccord || ''; // Gérer dateAccord null
          option.dataset.delaiPremierRemboursementMois = p.delaiPremierRemboursementMois;
          option.dataset.soldeRestant = p.soldeRestant;
          option.dataset.totalADeduire = p.sommeTotaleRembourser;
          select.appendChild(option);
        });

        select.onchange = () => {
          const selectedOption = select.options[select.selectedIndex];
          const montantRembourseInput = document.getElementById("montantRembourse");
          const dateRemboursementInput = document.getElementById("dateRemboursement");
          const suiviDiv = document.getElementById("suivi-remboursement");
          dateRemboursementInput.value = "";
          dateRemboursementInput.removeAttribute("min");
          suiviDiv.innerHTML = "";

          if (selectedOption.value) {
            montantRembourseInput.value = selectedOption.dataset.annuite;
            const totalADeduire = parseFloat(selectedOption.dataset.totalADeduire);
            const soldeRestant = parseFloat(selectedOption.dataset.soldeRestant);
            const totalPaye = totalADeduire - soldeRestant;
            suiviDiv.innerHTML = `Montant total payé : ${totalPaye.toFixed(2)} € | Solde restant : ${soldeRestant.toFixed(2)} €`;

            // Calculer la date minimale pour le premier remboursement
            let minDatePremierRemboursement = null;
            if (selectedOption.dataset.dateAccord) {
              const dateAccord = new Date(selectedOption.dataset.dateAccord);
              const delaiMois = parseInt(selectedOption.dataset.delaiPremierRemboursementMois);
              minDatePremierRemboursement = new Date(dateAccord);
              minDatePremierRemboursement.setMonth(dateAccord.getMonth() + delaiMois);
              minDatePremierRemboursement = minDatePremierRemboursement.toISOString().split('T')[0];
            } else {
              minDatePremierRemboursement = new Date().toISOString().split('T')[0]; // Fallback à aujourd'hui
            }

            // Récupérer la dernière date de remboursement
            ajax("GET", `/prets/${selectedOption.value}/remboursements`, null, (remboursements) => {
              const lastRemboursement = remboursements.length > 0
                ? remboursements.sort((a, b) => new Date(b.dateRemboursement) - new Date(a.dateRemboursement))[0]
                : null;
              const minDate = lastRemboursement
                ? new Date(new Date(lastRemboursement.dateRemboursement).getTime() + 24 * 60 * 60 * 1000).toISOString().split('T')[0]
                : minDatePremierRemboursement;
              dateRemboursementInput.setAttribute("min", minDate);
            }, (status, error) => {
              document.getElementById("error-remboursement-message").textContent = `Erreur de chargement des remboursements: ${error}`;
            });
          } else {
            montantRembourseInput.value = "";
          }
        };
      }, (status, error) => {
        document.getElementById("error-remboursement-message").textContent = `Erreur de chargement des prêts: ${error}`;
      });
    }

    function rembourserPret() {
      const idPret = document.getElementById("idPretRemboursement").value;
      const montantRembourse = document.getElementById("montantRembourse").value;
      const dateRemboursement = document.getElementById("dateRemboursement").value;

      if (!idPret) {
        document.getElementById("error-remboursement-message").textContent = "Sélectionnez un prêt.";
        return;
      }
      const parsedMontantRembourse = parseFloat(montantRembourse);
      if (montantRembourse === "" || isNaN(parsedMontantRembourse) || parsedMontantRembourse <= 0) {
        document.getElementById("error-remboursement-message").textContent = "Le montant remboursé doit être un nombre positif.";
        return;
      }
      if (!dateRemboursement) {
        document.getElementById("error-remboursement-message").textContent = "La date de remboursement est requise.";
        return;
      }

      const data = `idPret=${idPret}&montantRembourse=${parsedMontantRembourse}&dateRemboursement=${dateRemboursement}`;
      console.log("Valeurs avant envoi:", { idPret, montantRembourse: parsedMontantRembourse, dateRemboursement });

      ajax("POST", "/remboursements", data, (response) => {
        document.getElementById("success-remboursement-message").textContent = `Remboursement effectué avec l'ID ${response.id}.`;
        chargerPretsRemboursement();
        chargerRemboursements();
        document.getElementById("idPretRemboursement").value = "";
        document.getElementById("montantRembourse").value = "";
        document.getElementById("dateRemboursement").value = "";
        document.getElementById("suivi-remboursement").innerHTML = "";
        document.getElementById("error-remboursement-message").textContent = "";
      }, (status, error) => {
        document.getElementById("error-remboursement-message").textContent = `Erreur lors du remboursement: ${error}`;
      });
    }

    function chargerRemboursements() {
      ajax("GET", "/remboursements", null, (data) => {
        const tbody = document.querySelector("#table-remboursements tbody");
        tbody.innerHTML = "";
        data.forEach(r => {
          const tr = document.createElement("tr");
          tr.innerHTML = `
            <td>${r.idRemboursement}</td>
            <td>${r.idPret}</td>
            <td>${r.nom} ${r.prenom}</td>
            <td>${r.montantRembourse}</td>
            <td>${r.dateRemboursement}</td>
          `;
          tbody.appendChild(tr);
        });
      }, (status, error) => {
        document.getElementById("error-remboursement-message").textContent = `Erreur de chargement des remboursements: ${error}`;
      });
    }

    // Charger les données au démarrage
    chargerPretsRemboursement();
    chargerRemboursements();
  </script>

</body>
</html>