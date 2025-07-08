<?php include 'Header.html'; ?>
<main class="main-content">
  <!-- formulaire_pret_template.html -->
  <div class="form-container">
    <div class="form-header">
      <h3>Créer ou modifier un prêt</h3>
      <p>Veuillez remplir les informations nécessaires ci-dessous</p>
    </div>

    <form onsubmit="event.preventDefault(); ajouterPret();">
      <!-- Champ caché pour modification -->
      <input type="hidden" id="id" />

      <!-- Informations du prêt -->
      <div class="form-row">
        <div class="form-group">
          <label for="clientId">ID Client *</label>
          <input type="number" id="clientId" class="form-control-custom" placeholder="Ex: 101" required />
        </div>
        <div class="form-group">
          <label for="type_pret_id">Type de prêt *</label>
          <select id="type_pret_id" class="form-control-custom" required>
            <option value="">-- Choisir un type de prêt --</option>
          </select>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="montant_emprunt">Montant emprunté *</label>
          <input type="number" id="montant_emprunt" class="form-control-custom" placeholder="Ex: 200000" required />
        </div>
        <div class="form-group">
          <label for="date_debut">Date de début *</label>
          <input type="date" id="date_debut" class="form-control-custom" required />
        </div>
      </div>

      <div class="form-actions">
        <button type="reset" class="btn btn-outline-secondary">Annuler</button>
        <button type="submit" class="btn-primary-custom">Ajouter / Modifier</button>
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

    function chargerSelects() {
      ajax("GET", "/type_pret", null, (types) => {
        const select = document.getElementById("type_pret_id");
        select.innerHTML =
          '<option value="">-- Choisir un type de prêt --</option>';
        types.forEach((t) => {
          const option = document.createElement("option");
          option.value = t.id;
          option.text = `${t.nom} - Max ${t.montant_max_pres} Ar`;
          select.appendChild(option);
        });
      });
    }

    function ajouterPret() {
      const clientId = document.getElementById("clientId").value;
      const typePretId = document.getElementById("type_pret_id").value;
      const montant = document.getElementById("montant_emprunt").value;
      const dateDebut = document.getElementById("date_debut").value;

      const data = `client=${encodeURIComponent(clientId)}&type_pret_id=${encodeURIComponent(typePretId)}&montant_emprunt=${encodeURIComponent(montant)}&date_debut=${encodeURIComponent(dateDebut)}`;

      ajax("POST", "/creation_pret", data, (response) => {
        alert(response.message);
        resetForm();
      });
    }

    function resetForm() {
      document.getElementById("clientId").value = "";
      document.getElementById("type_pret_id").value = "";
      document.getElementById("montant_emprunt").value = "";
      document.getElementById("date_debut").value = "";
    }

    window.onload = () => {
      chargerSelects();
    };
  </script>

  </div>
</main>