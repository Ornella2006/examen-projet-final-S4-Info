<div class="header">
    <div class="page-title">
        <h1>Gestion des Prêts</h1>
    </div>
</div>

<div class="content">
    <div style="margin-bottom: 20px;">
        <button onclick="openAddPretModal()">Ajouter un prêt</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Client</th>
                <th>Type de prêt</th>
                <th>Montant (€)</th>
                <th>Durée (mois)</th>
                <th>Date d'accord</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="prets-tbody"></tbody>
    </table>
</div>

<!-- Modal pour ajouter un prêt -->
<div id="addPretModal" style="display: none;">
    <div style="background: white; padding: 20px; border-radius: 5px;">
        <h2>Ajouter un prêt</h2>
        <form id="addPretForm">
            <label for="idClient">Client</label>
            <select id="idClient" required></select>
            <label for="idTypePret">Type de prêt</label>
            <select id="idTypePret" required></select>
            <label for="montant">Montant (€)</label>
            <input type="number" id="montant" step="0.01" required>
            <label for="dureeMois">Durée (mois)</label>
            <input type="number" id="dureeMois" required>
            <button type="submit">Ajouter</button>
            <button type="button" onclick="closeAddPretModal()">Annuler</button>
        </form>
    </div>
</div>

<script>
    const apiBase = "http://localhost/examen-projet-final-S4-Info/ws";

    function fetchPrets() {
        fetch(`${apiBase}/prets`)
            .then(r => r.json())
            .then(data => {
                const tbody = document.getElementById('prets-tbody');
                tbody.innerHTML = '';
                data.forEach(pret => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${pret.idPret}</td>
                        <td>${pret.nom} ${pret.prenom}</td>
                        <td>${pret.libelle}</td>
                        <td>${parseFloat(pret.montant).toFixed(2)}</td>
                        <td>${pret.dureeMois}</td>
                        <td>${pret.dateAccord}</td>
                        <td>${pret.statut}</td>
                        <td>
                            <a href="${apiBase}/prets/${pret.idPret}/pdf" download="pret_${pret.idPret}.pdf">Télécharger PDF</a>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            })
            .catch(error => console.error('Erreur:', error));
    }

    function openAddPretModal() {
        document.getElementById('addPretModal').style.display = 'block';
        // Charger les clients et types de prêts
        fetch(`${apiBase}/clients`)
            .then(r => r.json())
            .then(clients => {
                const select = document.getElementById('idClient');
                select.innerHTML = clients.map(c => `<option value="${c.idClient}">${c.nom} ${c.prenom}</option>`).join('');
            });
        fetch(`${apiBase}/types-prets`)
            .then(r => r.json())
            .then(types => {
                const select = document.getElementById('idTypePret');
                select.innerHTML = types.map(t => `<option value="${t.idTypePret}">${t.libelle}</option>`).join('');
            });
    }

    function closeAddPretModal() {
        document.getElementById('addPretModal').style.display = 'none';
    }

    document.getElementById('addPretForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const data = {
            idClient: document.getElementById('idClient').value,
            idTypePret: document.getElementById('idTypePret').value,
            montant: document.getElementById('montant').value,
            dureeMois: document.getElementById('dureeMois').value
        };
        fetch(`${apiBase}/prets`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
            .then(r => r.json())
            .then(() => {
                closeAddPretModal();
                fetchPrets();
            })
            .catch(error => console.error('Erreur:', error));
    });

    fetchPrets();
</script>