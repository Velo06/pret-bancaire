<?php 
  $activePage = 'accueil';
  include 'Header.php'; 
?>
<main class="main-content">
  <div class="container">
    <!-- formulaire_fond_template.html -->
    <div class="form-container">
      <div class="form-header">
        <h3>Mettre à jour le fond</h3>
        <p>Ajouter un montant pour alimenter le fond actuel</p>
      </div>

      <!-- État actuel du fond -->
      <div class="mb-4 text-center">
        <strong>Solde actuel : </strong><span id="etat_fond_actuelle">...</span>
      </div>

      <!-- Formulaire -->
      <form onsubmit="event.preventDefault(); ajoutFond();">
        <div class="form-row">
          <div class="form-group">
            <label for="montant">Montant à ajouter *</label>
            <input type="number" id="montant" class="form-control-custom" placeholder="Ex: 50000 Ar" required />
          </div>
        </div>

        <div class="form-actions">
          <button type="reset" class="btn btn-outline-secondary">Annuler</button>
          <button type="submit" class="btn-primary-custom">Mettre à jour</button>
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

      function ajoutFond() {
        const montant = document.getElementById("montant").value;
        const data = `montant=${encodeURIComponent(montant)}`;

        ajax("POST", "/creation_fond", data, (response) => {
          alert(response.message);
          resetForm();
          etat_fond_actuelle();
        });
      }

      function etat_fond_actuelle() {
        ajax("GET", "/etat_fond", null, (data) => {
          const soldeElement = document.querySelector("#etat_fond_actuelle");
          soldeElement.innerHTML = data.solde_actuelle + " Ar";
        });
      }

      function resetForm() {
        document.getElementById("montant").value = "";
      }

      window.onload = () => {
        etat_fond_actuelle();
      };
    </script>

  </div>
</main>