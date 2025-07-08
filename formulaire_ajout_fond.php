<?php 
  $activePage = 'accueil';
  include 'Header.php'; 
?>
<main class="main-content">
  <div class="container" style="max-width: 800px; margin: 0 auto;">
    <!-- formulaire_fond_template.html -->
    <div class="form-container" style="padding: 2rem;">
      <div class="form-header" style="text-align: center; margin-bottom: 1.5rem;">
        <h3 style="margin-bottom: 0.5rem;">Mettre à jour le fond</h3>
        <p style="color: var(--text-secondary);">Ajouter un montant pour alimenter le fond actuel</p>
      </div>

      <!-- État actuel du fond -->
      <div class="mb-4 text-center" style="margin-bottom: 2rem; padding: 1rem; background: var(--bg-light); border-radius: 6px;">
        <strong style="font-size: 1.1rem;">Solde actuel : </strong>
        <span id="etat_fond_actuelle" style="font-size: 1.2rem; font-weight: bold; color: var(--secondary-color);">...</span>
      </div>

      <!-- Formulaire -->
      <form onsubmit="event.preventDefault(); ajoutFond();">
        <div class="form-row" style="margin-bottom: 1.5rem;">
          <div class="form-group">
            <label for="montant" style="display: block; margin-bottom: 0.5rem; font-weight: 700;">Montant à ajouter *</label>
            <input type="number" id="montant" class="form-control-custom" 
                   placeholder="Ex: 50000 Ar" required 
                   style="width: 100%; padding: 10px 15px; border: 2px solid var(--border-color); border-radius: 4px;" />
          </div>
        </div>

        <div class="form-actions" style="display: flex; justify-content: center; gap: 1rem; margin-top: 2rem;">
          <button type="reset" class="btn btn-outline-secondary" style="padding: 10px 20px;">Annuler</button>
          <button type="submit" class="btn-primary-custom" style="padding: 10px 20px;">Mettre à jour</button>
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