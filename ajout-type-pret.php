<?php 
  $activePage = 'typepret';
  include 'Header.php'; 
?>

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
        
        /* Styles améliorés pour le formulaire */
        .form-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 1.5rem;
        }
        
        .form-control-custom {
            width: 100%;
            padding: 10px 15px;
            font-size: 14px;
        }
        
        /* Style pour les champs numériques */
        input[type="number"] {
            -moz-appearance: textfield;
        }
        
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        
        /* Style pour les sélecteurs */
        select.form-control-custom {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 16px;
            padding-right: 30px;
        }
        
        /* Positionnement des symboles dans les champs */
        .input-with-symbol {
            position: relative;
        }
        
        .input-symbol {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            pointer-events: none;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .form-container {
                padding: 1.5rem;
            }
            
            .form-row {
                grid-template-columns: 1fr;
                gap: 15px;
            }
        }
        
        @media (max-width: 480px) {
            .form-container {
                padding: 1rem;
            }
            
            .form-control-custom {
                padding: 8px 12px;
            }
        }
    </style>
</head>
<body>

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
                            <div class="input-with-symbol">
                                <input type="number" class="form-control-custom" name="tauxInteret" id="tauxInteret" step="0.01" min="0" max="100" placeholder="ex. 5.86" required>
                                <span class="input-symbol">%</span>
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
                            <div class="input-with-symbol">
                                <input type="number" class="form-control-custom" name="montantMax" id="montantMax" step="0.01" placeholder="ex. 1000000" min="0" required>
                                <span class="input-symbol">Ar</span>
                            </div>
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
          if (xhr.readyState === 4) {
            if (xhr.status === 200) {
              try {
                callback(JSON.parse(xhr.responseText));
              } catch (e) {
                console.error("Erreur de parsing JSON:", e);
                document.getElementById("nomError").textContent = "Erreur de traitement des données";
              }
            } else {
              document.getElementById("nomError").textContent = `Erreur ${xhr.status}: ${xhr.statusText}`;
            }
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
        
        // Réinitialiser les messages d'erreur
        document.getElementById("nomError").textContent = "";
        
        // Validation des champs
        if (!nom || !tauxInteret || !dureeMax || !montantMax) {
            document.getElementById("nomError").textContent = "Les champs doivent être tous remplis.";
            isValid = false;
        } 
        
        const tauxNum = parseFloat(tauxInteret);
        const dureeNum = parseInt(dureeMax);
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
        
        if(isValid) {
            const data = new URLSearchParams();
            data.append('nom', nom);
            data.append('tauxInteret', tauxInteret);
            data.append('dureeMax', dureeMax);
            data.append('montantMax', montantMax);
            
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
        document.getElementById("nomError").textContent = "";
      }
      
      // Permettre la soumission avec la touche Entrée
      document.querySelectorAll('.form-control-custom').forEach(input => {
        input.addEventListener('keypress', function(e) {
          if (e.key === 'Enter') {
            e.preventDefault();
            ajouter();
          }
        });
      });
    </script>
  </body>
</html>