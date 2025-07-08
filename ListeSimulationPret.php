<?php include 'Header.html'; ?>

<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet" />


<main class="main-content">
    <div class="container mt-5">
        <h3 class="mb-4">Liste des prêts simulés</h3>

        <div class="row" id="pret-container">
            <!-- 📦 Les cartes seront injectées ici -->
        </div>

        <div class="text-center mt-4">
            <button id="btn-compare" class="btn btn-primary" disabled onclick="comparerPrets()"> Comparer les prêts sélectionnés</button>
        </div>
    </div>

    <script>
        const selectedPrets = [];

        function loadPrets() {
            fetch("http://localhost:8000/ws/list_pret_simuler")
                .then(res => res.json())
                .then(data => {
                    const container = document.getElementById("pret-container");
                    container.innerHTML = "";

                    data.forEach(pret => {
                        const card = document.createElement("div");
                        card.className = "col-md-4 mb-4";

                        card.innerHTML = `
                            <div class="card shadow-sm border">
                                <div class="card-body">
                                    <h5 class="card-title">Prêt ID #${pret.id}</h5>
                                    <p><strong>Montant :</strong> ${parseInt(pret.montant_emprunt).toLocaleString()} Ar</p>
                                    <p><strong>Mensualité :</strong> ${parseInt(pret.mensualite).toLocaleString()} Ar</p>
                                    <p><strong>Total intérêts :</strong> ${parseInt(pret.total_interets).toLocaleString()} Ar</p>
                                    <p><strong>Total remboursé :</strong> ${parseInt(pret.montant_total_rembourse).toLocaleString()} Ar</p>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" onchange="togglePret(${pret.id}, this)">
                                        <label class="form-check-label">Sélectionner pour comparaison</label>
                                    </div>
                                </div>
                            </div>
                        `;

                        container.appendChild(card);
                    });
                })
                .catch(err => {
                    console.error("Erreur lors du chargement :", err);
                    alert("Erreur de chargement des prêts.");
                });
        }

        function togglePret(id, checkbox) {
            if (checkbox.checked) {
                if (selectedPrets.length >= 2) {
                    checkbox.checked = false;
                    alert("Vous ne pouvez comparer que deux prêts à la fois.");
                    return;
                }
                selectedPrets.push(id);
            } else {
                const index = selectedPrets.indexOf(id);
                if (index > -1) selectedPrets.splice(index, 1);
            }

            document.getElementById("btn-compare").disabled = selectedPrets.length !== 2;
        }

        function comparerPrets() {
            if (selectedPrets.length !== 2) {
                alert("Veuillez sélectionner exactement deux prêts à comparer.");
                return;
            }

            const [id1, id2] = selectedPrets;
            window.location.href = `Comparaison.php?id1=${id1}&id2=${id2}`;
        }

        // Au chargement
        window.onload = loadPrets;
    </script>
</main>