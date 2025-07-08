<div class="header">
    <div class="page-title">
        <h1>Ajouter des Fonds</h1>
    </div>
    <!-- <div class="user-profile">
        <img src="template/logo.png" alt="Admin">
        <span>Admin</span>
        <i class="fas fa-chevron-down"></i>
    </div> -->
</div>

<div class="content">
    <div class="form-container">
        <select id="etablissementId">
            <option value="">Sélectionner un établissement</option>
        </select>
        <input type="number" id="montant" placeholder="Montant à ajouter" step="0.01" min="0.01">
        <input type="date" id="dateAjout" placeholder="Date d’ajout (optionnel)">
        <button onclick="ajouterFonds()">Ajouter Fonds</button>
        <div id="message"></div>
    </div>
</div>

<script>
    const apiBase = "/ETU003273/ws";

    function ajax(method, url, data, callback) {
        const xhr = new XMLHttpRequest();
        xhr.open(method, apiBase + url, true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    callback(JSON.parse(xhr.responseText));
                } else {
                    callback({ error: `Erreur ${xhr.status}: ${xhr.statusText}` });
                }
            }
        };
        xhr.send(data);
    }

    function chargerEtablissements() {
        console.log("Début du chargement des établissements...");
        ajax("GET", "/etablissements", null, (data) => {
            console.log("Données reçues de l’API :", data);
            const select = document.getElementById("etablissementId");
            select.innerHTML = '<option value="">Sélectionner un établissement</option>';
            if (data.error) {
                console.log("Erreur reçue :", data.error);
                document.getElementById("message").innerHTML = data.error;
                return;
            }
            console.log("Nombre d’établissements :", data.length);
            data.forEach(e => {
                console.log("Ajout de l’établissement :", e.nomEtablissementFinancier);
                const option = document.createElement("option");
                option.value = e.idEtablissementFinancier;
                option.textContent = e.nomEtablissementFinancier;
                select.appendChild(option);
            });
            console.log("Liste déroulante mise à jour");
        });
    }

    function ajouterFonds() {
        const etablissementId = document.getElementById("etablissementId").value;
        const montant = document.getElementById("montant").value;
        const dateAjout = document.getElementById("dateAjout").value;
        const messageDiv = document.getElementById("message");

        if (!etablissementId) {
            messageDiv.innerHTML = "Veuillez sélectionner un établissement";
            return;
        }
        if (!montant || montant <= 0) {
            messageDiv.innerHTML = "Veuillez entrer un montant valide";
            return;
        }

        const data = `montant=${encodeURIComponent(montant)}&dateAjout=${encodeURIComponent(dateAjout)}`;
        ajax("POST", `/etablissements/${etablissementId}/fonds`, data, (response) => {
            if (response.error) {
                messageDiv.innerHTML = `Erreur : ${response.error}`;
            } else {
                messageDiv.innerHTML = response.message;
                document.getElementById("montant").value = "";
                document.getElementById("dateAjout").value = "";
            }
        });
    }

    chargerEtablissements();
</script>