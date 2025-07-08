<div class="header">
    <div class="page-title">
      <h1>Gestion des types de prêts</h1>
    </div>
  </div>

  <div class="content">
    <div class="form-container">
      <div>
        <input type="hidden" id="idTypePret">
        <input type="text" id="libelle" placeholder="Libellé du prêt" required>
        <input type="number" id="tauxInteret" placeholder="Taux d'intérêt (%)" step="0.01" min="0" required>
        <input type="number" id="dureeMaxMois" placeholder="Durée max (mois)" min="1" required>
        <button onclick="ajouterOuModifier()">Ajouter / Modifier</button>
        <p id="error-message" class="error"></p>
      </div>

      <table id="table-types-prets">
        <thead>
          <tr>
            <th>ID</th>
            <th>Libellé</th>
            <th>Taux d'intérêt (%)</th>
            <th>Durée max (mois)</th>
            <th>Date de création</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
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
        const tbody = document.querySelector("#table-types-prets tbody");
        tbody.innerHTML = "";
        data.forEach(t => {
          const tr = document.createElement("tr");
          tr.innerHTML = `
            <td>${t.idTypePret}</td>
            <td>${t.libelle}</td>
            <td>${parseFloat(t.tauxInteret).toFixed(2)}</td>
            <td>${t.dureeMaxMois}</td>
            <td>${t.dateCreation}</td>
            <td>
              <button onclick='remplirFormulaire(${JSON.stringify(t)})'>✏️</button>
              <button onclick='supprimerTypePret(${t.idTypePret})'>🗑️</button>
            </td>
          `;
          tbody.appendChild(tr);
        });
      }, (status, error) => {
        document.getElementById("error-message").textContent = `Erreur de chargement: ${error}`;
      });
    }

    function ajouterOuModifier() {
      const id = document.getElementById("idTypePret").value;
      const libelle = document.getElementById("libelle").value.trim();
      const tauxInteret = document.getElementById("tauxInteret").value;
      const dureeMaxMois = document.getElementById("dureeMaxMois").value;

      if (!libelle) {
        document.getElementById("error-message").textContent = "Le libellé du prêt est requis.";
        return;
      }
      const parsedTauxInteret = parseFloat(tauxInteret);
      if (tauxInteret === "" || isNaN(parsedTauxInteret) || parsedTauxInteret < 0) {
        document.getElementById("error-message").textContent = "Le taux d'intérêt doit être un nombre positif.";
        return;
      }
      const parsedDureeMaxMois = parseInt(dureeMaxMois);
      if (dureeMaxMois === "" || isNaN(parsedDureeMaxMois) || parsedDureeMaxMois <= 0) {
        document.getElementById("error-message").textContent = "La durée maximale doit être un entier positif.";
        return;
      }

      const data = `libelle=${encodeURIComponent(libelle)}&tauxInteret=${parsedTauxInteret}&dureeMaxMois=${parsedDureeMaxMois}`;
      console.log("Valeurs avant envoi:", { libelle, tauxInteret: parsedTauxInteret, dureeMaxMois: parsedDureeMaxMois });

      if (id) {
        ajax("PUT", `/types-prets/${id}`, data, () => {
          resetForm();
          chargerTypesPrets();
          document.getElementById("error-message").textContent = "";
        }, (status, error) => {
          document.getElementById("error-message").textContent = `Erreur lors de la modification: ${error}`;
        });
      } else {
        ajax("POST", "/types-prets", data, () => {
          resetForm();
          chargerTypesPrets();
          document.getElementById("error-message").textContent = "";
        }, (status, error) => {
          document.getElementById("error-message").textContent = `Erreur lors de l'ajout: ${error}`;
        });
      }
    }

    function remplirFormulaire(t) {
      document.getElementById("idTypePret").value = t.idTypePret;
      document.getElementById("libelle").value = t.libelle;
      document.getElementById("tauxInteret").value = parseFloat(t.tauxInteret).toFixed(2);
      document.getElementById("dureeMaxMois").value = t.dureeMaxMois;
    }

    function supprimerTypePret(id) {
      if (confirm("Supprimer ce type de prêt ?")) {
        ajax("DELETE", `/types-prets/${id}`, null, () => {
          chargerTypesPrets();
          document.getElementById("error-message").textContent = "";
        }, (status, error) => {
          document.getElementById("error-message").textContent = `Erreur lors de la suppression: ${error}`;
        });
      }
    }

    function resetForm() {
      document.getElementById("idTypePret").value = "";
      document.getElementById("libelle").value = "";
      document.getElementById("tauxInteret").value = "";
      document.getElementById("dureeMaxMois").value = "";
      document.getElementById("error-message").textContent = "";
    }

    chargerTypesPrets();
  </script>

</body>
</html>