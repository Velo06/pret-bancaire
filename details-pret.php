<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du Prêt - E-BANK</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Styles spécifiques pour cette page */
        .pret-details-container {
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
        
        .montant {
            font-weight: 700;
            color: var(--secondary-color);
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: var(--white);
            margin: 10% auto;
            padding: 2rem;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        
        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 1.5rem;
        }
    </style>
</head>
<body>
    <?php include('Header.html'); ?>

    <div class="main-content">
        <div class="container">
            <div class="table-container">
                <div class="table-header">
                    <h3>Détails du Prêt</h3>
                    <button onclick="window.history.back()" class="btn-primary-custom">
                        <i class="fas fa-arrow-left"></i> Retour
                    </button>
                </div>
                
                <div class="pret-details-container">
                    <div id="pret-details">
                        <div class="detail-grid">
                            <!-- Les détails seront chargés dynamiquement ici -->
                            <div class="detail-item">
                                <span class="detail-label">Chargement en cours...</span>
                            </div>
                        </div>
                    </div>
                    
                    <div id="actions-section" style="display: none;">
                        <div class="action-buttons">
                            <button id="validate-btn" class="btn-primary-custom">
                                <i class="fas fa-check"></i> Valider
                            </button>
                            <button id="reject-btn" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Rejeter
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="table-container mt-4">
                    <div class="table-header">
                        <h3>Historique des Remboursements</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Montant</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody id="remboursements-table">
                                <tr>
                                    <td colspan="3">Chargement en cours...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmation -->
    <div id="confirm-modal" class="modal">
        <div class="modal-content">
            <h3 id="modal-title">Confirmation</h3>
            <p id="modal-message">Voulez-vous vraiment valider ce prêt ?</p>
            <div class="modal-actions">
                <button id="modal-cancel" class="btn btn-outline-secondary">Annuler</button>
                <button id="modal-confirm" class="btn-primary-custom">Confirmer</button>
            </div>
        </div>
    </div>

    <script>
        const apiBase = "http://localhost:8000/ws";
        const pretId = new URLSearchParams(window.location.search).get('id');
        let currentPret = null;

        // Fonction principale de chargement
        async function chargerDetailsPret() {
            if (!pretId) {
                showError('Prêt non trouvé');
                return;
            }

            try {
                const response = await fetch(`${apiBase}/prets/${pretId}`);
                if (!response.ok) throw new Error('Erreur de chargement');
                
                currentPret = await response.json();
                renderPretDetails(currentPret);
                renderRemboursements(currentPret.historique_remboursements);
                setupActions();
            } catch (error) {
                console.error('Erreur:', error);
                showError('Une erreur est survenue lors du chargement des détails du prêt');
            }
        }

        // Afficher les détails du prêt
        function renderPretDetails(pret) {
            const detailsDiv = document.getElementById('pret-details');
            
            detailsDiv.innerHTML = `
                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="detail-label">ID Prêt</span>
                        <span class="detail-value">${pret.id}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Client</span>
                        <span class="detail-value">${pret.client_nom}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Type de prêt</span>
                        <span class="detail-value">${pret.type_pret} (${(pret.taux_interet_annuel * 100).toFixed(2)}%)</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Montant</span>
                        <span class="detail-value montant">${formatMoney(pret.montant_emprunt)} Ar</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Date début</span>
                        <span class="detail-value">${formatDate(pret.date_debut)}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Date fin</span>
                        <span class="detail-value">${pret.date_fin ? formatDate(pret.date_fin) : 'N/A'}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Statut</span>
                        <span class="detail-value">
                            <span class="status-badge ${getStatusClass(pret.statut)}">${pret.statut}</span>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Date création</span>
                        <span class="detail-value">${formatDate(pret.date_creation)}</span>
                    </div>
                    <div class="detail-item">
                        <button onclick='exportPdf(${pret.id})'>Exporter en PDF</button>
                    </div>
                </div>
            `;
        }

        // Afficher l'historique des remboursements
        function renderRemboursements(remboursements) {
            const tbody = document.getElementById('remboursements-table');
            
            if (!remboursements || remboursements.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3">Aucun remboursement enregistré</td></tr>';
                return;
            }

            tbody.innerHTML = remboursements.map(remb => `
                <tr>
                    <td>${formatDate(remb.date_remboursement)}</td>
                    <td class="montant">${formatMoney(remb.montant_rembourse)} Ar</td>
                    <td>
                        <span class="status-badge ${remb.etat_remboursement === 1 ? 'status-approved' : 'status-rejected'}">
                            ${remb.etat_remboursement === 1 ? 'Payé' : 'Non payé'}
                        </span>
                    </td>
                </tr>
            `).join('');
        }

        // Configurer les boutons d'action
        function setupActions() {
            const actionsSection = document.getElementById('actions-section');
            
            // Afficher seulement si le prêt est en attente
            if (currentPret.id_etat_validation === 1) { // 1 = En attente
                actionsSection.style.display = 'block';
                
                document.getElementById('validate-btn').addEventListener('click', () => {
                    showConfirmModal(
                        'Valider le prêt',
                        'Voulez-vous vraiment valider ce prêt ?',
                        validerPret
                    );
                });
                
                document.getElementById('reject-btn').addEventListener('click', () => {
                    showConfirmModal(
                        'Rejeter le prêt',
                        'Voulez-vous vraiment rejeter ce prêt ?',
                        rejeterPret
                    );
                });
            }
        }

        // Valider le prêt
        async function validerPret() {
            try {
                const response = await fetch(`${apiBase}/prets/${pretId}/valider`, {
                    method: 'PUT'
                });
                
                if (!response.ok) throw new Error('Échec de la validation');
                
                const result = await response.json();
                alert(result.message);
                chargerDetailsPret(); // Recharger les données
            } catch (error) {
                console.error('Erreur:', error);
                alert('Une erreur est survenue lors de la validation');
            }
        }

        // Rejeter le prêt
        async function rejeterPret() {
            try {
                const response = await fetch(`${apiBase}/prets/${pretId}/rejeter`, {
                    method: 'PUT'
                });
                
                if (!response.ok) throw new Error('Échec du rejet');
                
                const result = await response.json();
                alert(result.message);
                chargerDetailsPret(); // Recharger les données
            } catch (error) {
                console.error('Erreur:', error);
                alert('Une erreur est survenue lors du rejet');
            }
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
                'payé': 'status-approved',
                'non payé': 'status-rejected'
            };
            return statusMap[status.toLowerCase()] || 'status-pending';
        }

        function showError(message) {
            document.getElementById('pret-details').innerHTML = `
                <div class="error">${message}</div>
            `;
        }

        // Gestion de la modal de confirmation
        function showConfirmModal(title, message, confirmCallback) {
            const modal = document.getElementById('confirm-modal');
            document.getElementById('modal-title').textContent = title;
            document.getElementById('modal-message').textContent = message;
            
            const confirmBtn = document.getElementById('modal-confirm');
            confirmBtn.onclick = function() {
                confirmCallback();
                modal.style.display = 'none';
            };
            
            document.getElementById('modal-cancel').onclick = function() {
                modal.style.display = 'none';
            };
            
            modal.style.display = 'block';
        }

        // Fermer la modal si on clique en dehors
        window.onclick = function(event) {
            const modal = document.getElementById('confirm-modal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        };

        function exportPdf(pretId) {
            window.location.href = `http://localhost:8000/ws/export/${pretId}`;
        }

        // Lancement au chargement
        document.addEventListener('DOMContentLoaded', chargerDetailsPret);
    </script>
</body>
</html>