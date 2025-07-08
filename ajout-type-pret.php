<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajout type de prêt - E-BANK</title>
    <link rel="stylesheet" href="../style/style.css">
    <style>
        .error-message {
            color: #dc3545;
            display: block;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <?php include('Header.html'); ?>

    <div class="main-content">
        <div class="container">
            <div class="form-container">
                <div class="form-header">
                    <h3>Ajout d'un type de prêt</h3>
                    <p>Remplissez les informations pour créer un nouveau type de prêt</p>
                </div>

                <form id="typePretForm">
                    <span id="nomError" class="error-message"></span>
                    <input type="hidden" id="id">

                    <div class="form-row">
                        <div class="form-group">
                            <label for="nom">Nom du prêt *</label>
                            <input type="text" class="form-control-custom" name="nomTypePret" id="nom" placeholder="ex. Prêt immobilier" required>
                        </div>
                        <div class="form-group">
                            <label for="tauxInteret">Taux d'intérêt annuel *</label>
                            <div style="position: relative;">
                                <input type="number" class="form-control-custom" name="tauxInteret" id="tauxInteret" step="0.01" min="0" max="100" placeholder="ex. 5.86" required>
                                <span style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: var(--text-secondary);">%</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="dureeMax">Durée maximum (mois) *</label>
                            <input type="number" class="form-control-custom" name="dureeMax" id="dureeMax" placeholder="ex. 96" min="1" required>
                        </div>
                        <div class="form-group">
                            <label for="montantMax">Montant maximum (Ar) *</label>
                            <input type="number" class="form-control-custom" name="montantMax" id="montantMax" step="0.01" placeholder="ex. 1000000" min="0" required>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">Annuler</button>
                        <button type="button" class="btn-primary-custom" onclick="ajouter()">Ajouter</button>
                    </div>
                </form>
            </div>
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
      function ajouter() {
        let isValid = true;
        const nom = document.getElementById("nom").value;
        const tauxInteret = document.getElementById("tauxInteret").value;
        const dureeMax = document.getElementById("dureeMax").value;
        const montantMax = document.getElementById("montantMax").value;
        if (!nom || !tauxInteret || !dureeMax || !montantMax) {
            document.getElementById("nomError").textContent = "Les champs doivent être tous remplis.";
            isValid = false;
        } 
        const tauxNum = parseInt(tauxInteret);
        const dureeNum = parseFloat(dureeMax);
        const montantNum = parseFloat(montantMax);
        if (tauxNum < 0 || dureeNum < 0 || montantNum < 0) {
            document.getElementById("nomError").textContent = "Les champs numériques ne doivent pas être négatifs.";
            isValid = false;
        }
        if (tauxNum > 100) {
            document.getElementById("nomError").textContent = "Le taux d'intérêt ne doit pas dépasser 100%.";
            isValid = false;
        }
        if (!/^\d+(\.\d{1,2})?$/.test(montantMax) || !/^\d+(\.\d{1,2})?$/.test(tauxInteret)) {
            document.getElementById("nomError").textContent = "Taux d'intérêt et montant: Maximum 2 chiffres après la virgule.";
            isValid = false;
        }
        const data = new URLSearchParams();
        data.append('nom', nom);
        data.append('tauxInteret', tauxInteret);
        data.append('dureeMax', dureeMax);
        data.append('montantMax', montantMax);
        if(isValid) {
            ajax("POST", "/type-pret", data.toString(), (response) => {
                alert(response.message);
                resetForm();
            });
        }
    }
    function resetForm() {
        document.getElementById("id").value = "";
        document.getElementById("nom").value = "";
        document.getElementById("tauxInteret").value = "";
        document.getElementById("dureeMax").value = "";
        document.getElementById("montantMax").value = "";
      }
    </script>
  </body>
</html>
