<?php include 'header.html'; ?>
<link rel="stylesheet" href="about.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<main class="main-content">
  <div class="container mt-5">
    <h3 class="text-center mb-4">Liste des prêts simulés</h3>

    <div class="row" id="liste-prets">
      <!-- Les cartes seront injectées ici -->
    </div>
  </div>

  <!-- 🔘 Bouton de comparaison FIXE -->
  <button id="btn-compare" class="btn btn-warning"
    style="position: fixed; bottom: 30px; right: 30px; display: none; z-index: 999;"
    onclick="comparerPrets()">
    Comparer les prêts sélectionnés
  </button>

  <script>
    const listeContainer = document.getElementById("liste-prets");
    let pretsSelectionnes = [];

    // 🔁 Récupération des prêts via API
    fetch("http://localhost:8000/ws/list_pret_simuler")
      .then(res => res.json())
      .then(prets => {
        if (!Array.isArray(prets)) {
          throw new Error("Format inattendu");
        }

        listeContainer.innerHTML = prets.map(pret => `
          <div class="col-lg-4 col-md-4 col-sm-6 mb-4">
            <div class="services__item border shadow-sm p-3">
              <input type="checkbox" class="form-check-input mb-2" 
                onchange="toggleSelection(${pret.id})" id="check-${pret.id}">
              <div class="services__item__text">
                <h4><span>Prêt #${pret.id}</span></h4>
                <p><strong>Montant :</strong> ${parseInt(pret.montant_emprunt).toLocaleString()} Ar</p>
                <p><strong>Mensualité :</strong> ${parseInt(pret.mensualite).toLocaleString()} Ar</p>
                <p><strong>Total remboursé :</strong> ${parseInt(pret.montant_total_rembourse).toLocaleString()} Ar</p>
              </div>
            </div>
          </div>
        `).join('');
      })
      .catch(err => {
        console.error("Erreur de chargement :", err);
        listeContainer.innerHTML = `<div class="col-12 text-danger">Impossible de charger les prêts.</div>`;
      });

    function toggleSelection(id) {
      const index = pretsSelectionnes.indexOf(id);
      if (index >= 0) {
        pretsSelectionnes.splice(index, 1);
      } else {
        if (pretsSelectionnes.length >= 2) {
          alert("Vous ne pouvez comparer que deux prêts à la fois.");
          document.getElementById(`check-${id}`).checked = false;
          return;
        }
        pretsSelectionnes.push(id);
      }

      document.getElementById("btn-compare").style.display = pretsSelectionnes.length === 2 ? "block" : "none";
    }

    function comparerPrets() {
      if (pretsSelectionnes.length !== 2) {
        alert("Veuillez sélectionner 2 prêts.");
        return;
      }

      const [id1, id2] = pretsSelectionnes;
      window.location.href = `Comparaison.php?id1=${id1}&id2=${id2}`;
    }
  </script>
</main>