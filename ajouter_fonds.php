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