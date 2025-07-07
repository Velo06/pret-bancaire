<?php include 'header.html'; ?>
<main class="main-content">
  <div class="container">
    <div class="form-container">
      <div class="form-header">
        <h3>Détails du prêt</h3>
        <p>Informations détaillées du prêt sélectionné</p>
      </div>

      <div id="pret-details" class="mb-4">
        <!-- Contenu injecté par JS -->
        <p>Chargement en cours...</p>
      </div>

      <div id="actions-section" class="form-actions" style="display: none;">
        <button id="validate-btn" class="btn-primary-custom">Valider le prêt</button>
        <button id="reject-btn" class="btn btn-outline-danger">Rejeter le prêt</button>
        <button onclick="window.history.back()" class="btn btn-outline-secondary">Retour</button>
      </div>
    </div>

    <div class="table-container">
      <div class="table-header">
        <h3>Historique des remboursements</h3>
      </div>
      <div class="table-responsive">
        <table class="custom-table" id="remboursements-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Montant</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="2">Chargement en cours...</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal de confirmation -->
    <div id="confirm-modal" class="modal" style="display: none;">
      <div class="modal-content p-4" style="max-width: 500px; margin: auto;">
        <h4 id="modal-title" class="mb-3">Confirmation</h4>
        <p id="modal-message"></p>
        <div class="form-actions mt-4">
          <button id="modal-cancel" class="btn btn-outline-secondary">Annuler</button>
          <button id="modal-confirm" class="btn-primary-custom">Confirmer</button>
        </div>
      </div>
    </div>

    <script>
      const apiBase = "http://localhost:8000/ws";
      const pretId = new URLSearchParams(window.location.search).get('id');
      let currentPret = null;

      async function chargerDetailsPret() {
        if (!pretId) return showError('ID de prêt introuvable');
        try {
          const response = await fetch(`${apiBase}/prets/${pretId}`);
          if (!response.ok) throw new Error('Erreur chargement');
          currentPret = await response.json();
          renderPretDetails(currentPret);
          renderRemboursements(currentPret.historique_remboursements || []);
          setupActions();
        } catch (e) {
          showError('Erreur lors du chargement du prêt');
        }
      }

      function renderPretDetails(pret) {
        const html = `
      <div class="info-field"><strong>ID :</strong> ${pret.id}</div>
      <div class="info-field"><strong>Client :</strong> ${pret.client_nom}</div>
      <div class="info-field"><strong>Type :</strong> ${pret.type_pret} (${(pret.taux_interet_annuel * 100).toFixed(2)}%)</div>
      <div class="info-field"><strong>Montant :</strong> <span class="montant">${formatMoney(pret.montant_emprunt)} Ar</span></div>
      <div class="info-field"><strong>Date début :</strong> ${formatDate(pret.date_debut)}</div>
      <div class="info-field"><strong>Date fin :</strong> ${pret.date_fin ? formatDate(pret.date_fin) : 'N/A'}</div>
      <div class="info-field"><strong>Statut :</strong> <span class="status-badge ${getStatusClass(pret.statut)}">${pret.statut}</span></div>
      <div class="info-field"><strong>Créé le :</strong> ${formatDate(pret.date_creation)}</div>
    `;
        document.getElementById('pret-details').innerHTML = html;
      }

      function renderRemboursements(list) {
        const tbody = document.querySelector("#remboursements-table tbody");
        if (!list.length) {
          tbody.innerHTML = `<tr><td colspan="2">Aucun remboursement</td></tr>`;
          return;
        }
        tbody.innerHTML = list.map(remb => `
      <tr>
        <td>${formatDateTime(remb.date_remboursement)}</td>
        <td class="montant">${formatMoney(remb.montant_rembourse)} Ar</td>
      </tr>
    `).join('');
      }

      function setupActions() {
        const section = document.getElementById('actions-section');
        if (currentPret.id_etat_validation === 1) {
          section.style.display = 'flex';
          document.getElementById('validate-btn').onclick = () => showConfirmModal("Valider le prêt", "Confirmer la validation ?", validerPret);
          document.getElementById('reject-btn').onclick = () => showConfirmModal("Rejeter le prêt", "Confirmer le rejet ?", rejeterPret);
        }
      }

      async function validerPret() {
        try {
          const res = await fetch(`${apiBase}/prets/${pretId}/valider`, {
            method: "PUT"
          });
          const result = await res.json();
          alert(result.message);
          chargerDetailsPret();
        } catch {
          alert("Erreur de validation");
        }
      }

      async function rejeterPret() {
        try {
          const res = await fetch(`${apiBase}/prets/${pretId}/rejeter`, {
            method: "PUT"
          });
          const result = await res.json();
          alert(result.message);
          chargerDetailsPret();
        } catch {
          alert("Erreur de rejet");
        }
      }

      function formatDate(d) {
        return new Date(d).toLocaleDateString('fr-FR');
      }

      function formatDateTime(d) {
        return new Date(d).toLocaleString('fr-FR');
      }

      function formatMoney(val) {
        return new Intl.NumberFormat('fr-FR').format(val);
      }

      function getStatusClass(status) {
        return {
          "en attente": "status-pending",
          "validé": "status-approved",
          "rejeté": "status-rejected"
        } [status.toLowerCase()] || "";
      }

      function showError(msg) {
        document.getElementById('pret-details').innerHTML = `<div class="alert alert-danger">${msg}</div>`;
      }

      function showConfirmModal(title, message, onConfirm) {
        document.getElementById('modal-title').textContent = title;
        document.getElementById('modal-message').textContent = message;
        document.getElementById('confirm-modal').style.display = "block";
        document.getElementById('modal-confirm').onclick = () => {
          onConfirm();
          document.getElementById('confirm-modal').style.display = "none";
        };
        document.getElementById('modal-cancel').onclick = () => {
          document.getElementById('confirm-modal').style.display = "none";
        };
      }

      window.onclick = function(event) {
        const modal = document.getElementById('confirm-modal');
        if (event.target === modal) {
          modal.style.display = "none";
        }
      };

      window.onload = chargerDetailsPret;
    </script>
  </div>
</main>