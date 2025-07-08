<?php include 'Header.html'; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Montants disponibles - Établissement Financier</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 20px;
    }
    .filters {
      margin-bottom: 20px;
    }
    .filters label {
      margin-right: 10px;
    }
    table {
      border-collapse: collapse;
      width: 100%;
    }
    th, td {
      padding: 8px;
      text-align: center;
      border: 1px solid #ccc;
    }
    th {
      background-color: #f2f2f2;
    }
    .error {
      color: red;
    }
  </style>
</head>
<body>
  <h2>Montants disponibles par mois</h2>

  <div class="filters">
    <label for="debut">Début :</label>
    <input type="month" id="debut" />
    <label for="fin">Fin :</label>
    <input type="month" id="fin" />
    <button onclick="chargerDisponibilite()">Afficher</button>
    <span id="error" class="error"></span>
  </div>

  <table id="table-disponibilite">
    <thead>
      <tr>
        <th>Mois</th>
        <th>Montant prêté</th>
        <th>Montant remboursé</th>
        <th>Montant disponible</th>
      </tr>
    </thead>
    <tbody></tbody>
    <tfoot>
      <tr>
        <th colspan="3">Solde initial</th>
        <th id="solde-initial"></th>
      </tr>
    </tfoot>
  </table>

  <script>
    const apiBase = "http://localhost:8000/ws";

    function chargerDisponibilite() {
      const debut = document.getElementById("debut").value;
      const fin = document.getElementById("fin").value;
      const errorSpan = document.getElementById("error");
      const tbody = document.querySelector("#table-disponibilite tbody");
      const soldeInitialCell = document.getElementById("solde-initial");

      errorSpan.textContent = "";
      tbody.innerHTML = "";
      soldeInitialCell.textContent = "";

      if (!debut || !fin) {
        errorSpan.textContent = "Veuillez sélectionner une date de début et de fin.";
        return;
      }

      fetch(`${apiBase}/montant_disponible?debut=${debut}&fin=${fin}`)
        .then(response => response.json())
        .then(data => {
          if (!data.success) {
            errorSpan.textContent = data.message || "Erreur lors du chargement.";
            return;
          }

          data.data.forEach(row => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
              <td>${row.mois}</td>
              <td>${parseFloat(row.pret_mensuel).toLocaleString('fr-FR', {minimumFractionDigits: 2})} Ar</td>
              <td>${parseFloat(row.remboursement_mensuel).toLocaleString('fr-FR', {minimumFractionDigits: 2})} Ar</td>
              <td>${parseFloat(row.solde_disponible).toLocaleString('fr-FR', {minimumFractionDigits: 2})} Ar</td>
            `;
            tbody.appendChild(tr);
          });

          soldeInitialCell.textContent = parseFloat(data.solde_initial).toLocaleString('fr-FR', {minimumFractionDigits: 2}) + " Ar";
        })
        .catch(err => {
          errorSpan.textContent = "Erreur : " + err.message;
        });
    }
  </script>
</body>
</html>
