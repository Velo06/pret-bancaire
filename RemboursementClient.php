<!-- formulaire_remboursement_template.html -->
<?php include 'header.html'; ?>
<main class="main-content">
  <div class="container">
    <div class="form-container">
      <div class="form-header">
        <h3>Remboursement d'un prêt</h3>
        <p>Saisissez les informations de remboursement ci-dessous</p>
      </div>

      <!-- (Optionnel) Affichage du fond actuel -->
      <div class="text-center mb-4">
        <strong>Solde actuel :</strong> <span id="etat_fond_actuelle">Non chargé</span>
      </div>

      <form onsubmit="event.preventDefault(); rembourser();">
        <div class="form-row">
          <div class="form-group">
            <label for="pret_id">ID du prêt *</label>
            <input type="number" id="pret_id" class="form-control-custom" placeholder="Ex: 5" required />
          </div>
          <div class="form-group">
            <label for="montant">Montant remboursé *</label>
            <input type="number" id="montant" class="form-control-custom" placeholder="Ex: 15000" required />
          </div>
        </div>

        <div class="form-actions">
          <button type="reset" class="btn btn-outline-secondary">Annuler</button>
          <button type="submit" class="btn-primary-custom">Rembourser</button>
        </div>
      </form>
    </div>

    <script>
      const apiBase = "http://localhost:8000/ws";

      function ajax(method, url, data, callback) {
        const xhr = new XMLHttpRequest();
        xhr.open(method, apiBase + url, true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = () => {
          if (xhr.readyState === 4 && xhr.status === 200) {
            callback(JSON.parse(xhr.responseText));
          }
        };
        xhr.send(data);
      }

      function rembourser() {
        const pretId = document.getElementById("pret_id").value;
        const montant = document.getElementById("montant").value;
        const data = `montant=${encodeURIComponent(montant)}&pret_id=${encodeURIComponent(pretId)}`;

        ajax("POST", "/remboursement", data, (response) => {
          alert(response.message);
          console.log(response.message);
          resetForm();
          etat_fond_actuelle(); // si souhaité
        });
      }

      function resetForm() {
        document.getElementById("pret_id").value = "";
        document.getElementById("montant").value = "";
      }

      function etat_fond_actuelle() {
        ajax("GET", "/etat_fond", null, (data) => {
          const soldeElement = document.querySelector("#etat_fond_actuelle");
          soldeElement.innerHTML = data.solde_actuelle + " Ar";
        });
      }

      window.onload = () => {
        if (document.getElementById("etat_fond_actuelle")) {
          etat_fond_actuelle();
        }
      };
    </script>
  </div>
</main>