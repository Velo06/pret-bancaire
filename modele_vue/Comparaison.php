<?php include 'header.html'; ?>
<style>
  /* colle ici le CSS complet ci-dessus */
  /* Base */
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f8f9fa;
    margin: 0;
    padding: 0;
  }

  .main-content {
    padding-bottom: 60px;
  }

  /* En-tête */
  h3.text-center {
    font-weight: 700;
    color: #333;
  }

  /* Cartes de prêt */
  .card {
    border-radius: 10px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
  }

  .card-header {
    font-weight: bold;
    font-size: 18px;
    border-radius: 10px 10px 0 0;
  }

  .card-body p {
    margin-bottom: 10px;
    font-size: 15px;
    color: #444;
  }

  .card-body p strong {
    color: #222;
  }

  /* Disposition responsive */
  #comparaison-contenu {
    row-gap: 30px;
  }

  @media (max-width: 767.98px) {
    .card {
      margin-bottom: 20px;
    }

    .card-header {
      text-align: center;
    }
  }
</style>
<main class="main-content">
  <div class="container mt-5">
    <h3 class="text-center mb-4">Comparaison de deux prêts</h3>

    <div class="row" id="comparaison-contenu">
      <!-- Cartes des prêts ici -->
    </div>
  </div>

  <script>
    function getParam(name) {
      const url = new URL(window.location.href);
      return url.searchParams.get(name);
    }

    const id1 = getParam("id1");
    const id2 = getParam("id2");

    if (!id1 || !id2) {
      alert("Veuillez sélectionner deux prêts à comparer.");
      window.location.href = "pret_list.html";
    }

    Promise.all([
        fetch(`http://localhost:8000/ws/prets_comparaison/${id1}`).then(res => res.json()),
        fetch(`http://localhost:8000/ws/prets_comparaison/${id2}`).then(res => res.json())
      ])
      .then(([pret1, pret2]) => {
        const container = document.getElementById("comparaison-contenu");

        container.innerHTML = `
          <div class="col-md-6">
            <div class="card border shadow-sm">
              <div class="card-header bg-primary text-white">Prêt ID #${pret1.id}</div>
              <div class="card-body">
                ${buildDetailPret(pret1)}
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="card border shadow-sm">
              <div class="card-header bg-success text-white">Prêt ID #${pret2.id}</div>
              <div class="card-body">
                ${buildDetailPret(pret2)}
              </div>
            </div>
          </div>
        `;
      })
      .catch(err => {
        console.error("Erreur chargement comparaison :", err);
        alert("Erreur lors de la récupération des prêts.");
      });

    function buildDetailPret(pret) {
      return `
        <p><strong>Montant emprunté :</strong> ${parseInt(pret.montant_emprunt).toLocaleString()} Ar</p>
        <p><strong>Mensualité :</strong> ${parseInt(pret.mensualite).toLocaleString()} Ar</p>
        <p><strong>Assurance mensuelle :</strong> ${parseInt(pret.assurance_mensuelle).toLocaleString()} Ar</p>
        <p><strong>Total intérêts :</strong> ${parseInt(pret.total_interets).toLocaleString()} Ar</p>
        <p><strong>Total assurances :</strong> ${parseInt(pret.total_assurances).toLocaleString()} Ar</p>
        <p><strong>Total remboursé :</strong> ${parseInt(pret.montant_total_rembourse).toLocaleString()} Ar</p>
        <p><strong>Date de début :</strong> ${new Date(pret.date_debut).toLocaleDateString()}</p>
        <p><strong>Date de fin :</strong> ${new Date(pret.date_fin).toLocaleDateString()}</p>
        <p><strong>État :</strong> ${etatPret(pret.id_etat_validation)}</p>
      `;
    }

    function etatPret(id) {
      switch (id) {
        case 1:
          return "🟡 En attente";
        case 2:
          return "🟢 Validé";
        case 3:
          return "🔴 Refusé";
        default:
          return "❔ Inconnu";
      }
    }
  </script>
</main>