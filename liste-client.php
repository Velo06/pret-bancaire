<?php include 'header.html'; ?>
<main class="main-content">
  <!-- formulaire_pret_template.html -->
  <div class="container">
    <div class="form-container">
      <div class="form-header">
        <h3>Ajouter ou modifier un client</h3>
        <p>Remplissez les champs ci-dessous pour enregistrer un client</p>
      </div>

      <form onsubmit="event.preventDefault(); ajouterOuModifierClient();">
        <input type="hidden" id="id" />

        <div class="form-row">
          <div class="form-group">
            <label for="nom">Nom *</label>
            <input type="text" id="nom" class="form-control-custom" required />
          </div>
          <div class="form-group">
            <label for="username">Username *</label>
            <input
              type="text"
              id="username"
              class="form-control-custom"
              required
            />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="email">Email *</label>
            <input
              type="email"
              id="email"
              class="form-control-custom"
              required
            />
          </div>
          <div class="form-group">
            <label for="telephone">Téléphone *</label>
            <input
              type="text"
              id="telephone"
              class="form-control-custom"
              required
            />
          </div>
        </div>

        <div class="form-actions">
          <button
            type="reset"
            class="btn btn-outline-secondary"
            onclick="resetFormClient()"
          >
            Annuler
          </button>
          <button type="submit" class="btn-primary-custom">Enregistrer</button>
        </div>
      </form>
    </div>

    <div class="table-container">
      <div class="table-header">
        <h3>Liste des clients</h3>
      </div>

      <div class="table-responsive">
        <table class="custom-table" id="table-clients">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nom</th>
              <th>Username</th>
              <th>Email</th>
              <th>Téléphone</th>
              <th>Rôle</th>
              <th>Statut</th>
              <th>Inscription</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>

    <script>
      const apiBase = "http://localhost:8000/ws";

      function ajax(method, url, data, callback) {
        const xhr = new XMLHttpRequest();
        xhr.open(method, apiBase + url, true);
        xhr.setRequestHeader(
          "Content-Type",
          "application/x-www-form-urlencoded"
        );
        xhr.onreadystatechange = () => {
          if (xhr.readyState === 4 && xhr.status === 200) {
            callback(JSON.parse(xhr.responseText));
          }
        };
        xhr.send(data);
      }

      function chargerClients() {
        ajax("GET", "/clients", null, (data) => {
          const tbody = document.querySelector("#table-clients tbody");
          tbody.innerHTML = "";
          data.forEach((client) => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
          <td>${client.id}</td>
          <td>${client.nom}</td>
          <td>${client.username}</td>
          <td>${client.email}</td>
          <td>${client.telephone}</td>
          <td>${client.role_nom}</td>
          <td>${client.statut_nom}</td>
          <td>${new Date(client.date_inscription).toLocaleDateString()}</td>
          <td>
            <button class="btn btn-sm btn-outline-success me-1" title="Modifier" onclick='remplirFormulaireClient(${JSON.stringify(
              client
            )})'>
              <i class="fa fa-edit"></i>
            </button>
            <button class="btn btn-sm btn-outline-danger me-1" title="Supprimer" onclick='supprimerClient(${
              client.id
            })'>
              <i class="fa fa-trash"></i>
            </button>
            <button class="btn btn-sm btn-outline-primary" title="Voir" onclick='afficherDetailsClient(${
              client.id
            })'>
              <i class="fa fa-eye"></i>
            </button>
          </td>
        `;
            tbody.appendChild(tr);
          });
        });
      }

      function ajouterOuModifierClient() {
        const id = document.getElementById("id").value;
        const nom = document.getElementById("nom").value;
        const username = document.getElementById("username").value;
        const email = document.getElementById("email").value;
        const telephone = document.getElementById("telephone").value;

        const data = `nom=${encodeURIComponent(
          nom
        )}&username=${encodeURIComponent(username)}&email=${encodeURIComponent(
          email
        )}&telephone=${encodeURIComponent(telephone)}`;

        if (id) {
          ajax("PUT", `/clients/${id}`, data, () => {
            resetFormClient();
            chargerClients();
          });
        } else {
          ajax("POST", "/clients", data, () => {
            resetFormClient();
            chargerClients();
          });
        }
      }

      function remplirFormulaireClient(client) {
        document.getElementById("id").value = client.id;
        document.getElementById("nom").value = client.nom;
        document.getElementById("username").value = client.username;
        document.getElementById("email").value = client.email;
        document.getElementById("telephone").value = client.telephone;
        window.scrollTo({ top: 0, behavior: "smooth" });
      }

      function supprimerClient(id) {
        if (confirm("Supprimer ce client ?")) {
          ajax("DELETE", `/clients/${id}`, null, () => {
            chargerClients();
          });
        }
      }

      function resetFormClient() {
        document.getElementById("id").value = "";
        document.getElementById("nom").value = "";
        document.getElementById("username").value = "";
        document.getElementById("email").value = "";
        document.getElementById("telephone").value = "";
      }

      function afficherDetailsClient(id) {
        window.location.href = `fiche-client.html?id=${id}`;
      }

      window.onload = chargerClients;
    </script>
  </div>
</main>
