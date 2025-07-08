<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche Client - E-BANK</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .client-details-container {
            background: var(--white);
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin: 2rem 0;
        }
        
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }
        
        .detail-item {
            margin-bottom: 1rem;
        }
        
        .detail-label {
            font-weight: 700;
            color: var(--text-secondary);
            display: block;
            margin-bottom: 0.25rem;
        }
        
        .detail-value {
            color: var(--text-primary);
            font-size: 1.1rem;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--border-color);
        }
        
        .action-btn {
            padding: 0.5rem 0.8rem;
            font-size: 0.875rem;
            margin-right: 0.5rem;
        }
        
        .no-data {
            text-align: center;
            color: var(--text-secondary);
            padding: 2rem;
            font-style: italic;
        }
    </style>
</head>
<body>
    <?php include('Header.html'); ?>

    <div class="main-content">
        <div class="container">
            <div class="table-container">
                <div class="table-header">
                    <h3>Fiche Client</h3>
                    <button onclick="window.location.href='liste-client.html'" class="btn-primary-custom">
                        <i class="fas fa-arrow-left"></i> Retour
                    </button>
                </div>
                
                <div class="client-details-container">
                    <div id="client-details">
                        <div class="section-header">
                            <h3>Informations personnelles</h3>
                        </div>
                        <div class="detail-grid">
                            <!-- Les détails seront chargés dynamiquement ici -->
                            <div class="detail-item">
                                <span class="detail-label">Chargement en cours...</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="table-container mt-4">
                    <div class="table-header">
                        <h3>Liste des Prêts</h3>
                        <button onclick="nouveauPret()" class="btn-primary-custom">
                            <i class="fas fa-plus"></i> Nouveau Prêt
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Type de prêt</th>
                                    <th>Montant</th>
                                    <th>Taux d'intérêt</th>
                                    <th>Date début</th>
                                    <th>Date fin</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="prets-table">
                                <tr>
                                    <td colspan="8" class="no-data">Chargement en cours...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const apiBase = "http://localhost:8000/ws";
        const clientId = new URLSearchParams(window.location.search).get('id');

        // Fonction principale de chargement
        async function chargerDetailsClient() {
            if (!clientId) {
                showError('Client non trouvé');
                return;
            }

            try {
                await Promise.all([
                    chargerInfosClient(),
                    chargerPretsClient()
                ]);
            } catch (error) {
                console.error('Erreur:', error);
                showError('Une erreur est survenue lors du chargement des données');
            }
        }

        // Charger les informations du client
        async function chargerInfosClient() {
            const response = await fetch(`${apiBase}/clients/${clientId}`);
            if (!response.ok) throw new Error('Erreur client');
            
            const client = await response.json();
            renderClientDetails(client);
        }

        // Charger les prêts du client
        async function chargerPretsClient() {
            const response = await fetch(`${apiBase}/clients/${clientId}/prets`);
            if (!response.ok) throw new Error('Erreur prêts');
            
            const prets = await response.json();
            renderPretsTable(prets);
        }

        // Afficher les détails du client
        function renderClientDetails(client) {
            const detailsDiv = document.getElementById('client-details');
            detailsDiv.innerHTML = `
                <div class="section-header">
                    <h3>Informations personnelles</h3>
                </div>
                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="detail-label">ID Client</span>
                        <span class="detail-value">${client.id}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Nom complet</span>
                        <span class="detail-value">${client.nom}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Nom d'utilisateur</span>
                        <span class="detail-value">${client.username}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Email</span>
                        <span class="detail-value">${client.email}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Téléphone</span>
                        <span class="detail-value">${client.telephone}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Rôle</span>
                        <span class="detail-value">${client.role_nom}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Statut</span>
                        <span class="detail-value">
                            <span class="status-badge ${getStatusClass(client.statut_nom)}">${client.statut_nom}</span>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Date inscription</span>
                        <span class="detail-value">${formatDate(client.date_inscription)}</span>
                    </div>
                </div>
            `;
        }

        // Afficher la table des prêts
        function renderPretsTable(prets) {
            const tbody = document.getElementById('prets-table');
            
            if (!prets || prets.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="no-data">Aucun prêt enregistré pour ce client</td></tr>';
                return;
            }

            tbody.innerHTML = prets.map(pret => `
                <tr>
                    <td>${pret.id}</td>
                    <td>${pret.type_pret_nom}</td>
                    <td class="montant">${formatMoney(pret.montant_emprunt)} Ar</td>
                    <td>${(pret.taux_interet_annuel * 100).toFixed(2)}%</td>
                    <td>${formatDate(pret.date_debut)}</td>
                    <td>${pret.date_fin ? formatDate(pret.date_fin) : 'N/A'}</td>
                    <td>
                        <span class="status-badge ${getStatusClass(pret.etat_validation)}">
                            ${pret.etat_validation}
                        </span>
                    </td>
                    <td>
                        <button onclick="voirDetailsPret(${pret.id})" class="btn btn-sm btn-outline-primary" title="Détails">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${pret.id_etat_validation === 1 ? `
                        <button onclick="modifierPret(${pret.id})" class="btn btn-sm btn-outline-secondary" title="Modifier">
                            <i class="fas fa-edit"></i>
                        </button>
                        ` : ''}
                    </td>
                </tr>
            `).join('');
        }

        // Fonctions utilitaires
        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString('fr-FR');
        }

        function formatMoney(amount) {
            return new Intl.NumberFormat('fr-FR').format(amount);
        }

        function getStatusClass(status) {
            const statusMap = {
                'en attente': 'status-pending',
                'validé': 'status-approved',
                'rejeté': 'status-rejected',
                'actif': 'status-approved',
                'inactif': 'status-rejected'
            };
            return statusMap[status.toLowerCase()] || '';
        }

        function showError(message) {
            document.getElementById('client-details').innerHTML = `
                <div class="error">${message}</div>
            `;
        }

        function nouveauPret() {
            window.location.href = `nouveau-pret.html?clientId=${clientId}`;
        }

        function voirDetailsPret(pretId) {
            window.location.href = `details-pret.php?id=${pretId}`;
        }

        function modifierPret(pretId) {
            window.location.href = `modifier-pret.html?id=${pretId}`;
        }

        document.addEventListener('DOMContentLoaded', chargerDetailsClient);
    </script>
</body>
</html>