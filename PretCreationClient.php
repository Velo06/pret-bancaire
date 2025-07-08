<?php 
  $activePage = 'clients';
  include 'Header.php'; 
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Création de Prêt - E-BANK</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .info-client {
            background-color: var(--bg-light);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .montant-info {
            font-weight: 700;
            color: var(--secondary-color);
        }
        
        .date-picker {
            position: relative;
        }
        
        .date-picker i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            pointer-events: none;
        }
    </style>
</head>
<body>

    <div class="main-content">
        <div class="container">
            <div class="form-container">
                <div class="form-header">
                    <h3>Création de Prêt</h3>
                    <p>Remplissez les informations nécessaires pour créer un nouveau prêt</p>
                </div>

                <form id="pretForm" onsubmit="event.preventDefault(); ajouterPret();">
                    <input type="hidden" id="id">
                    
                    <!-- Info client -->
                    <div class="info-client" id="client-info">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="clientId">ID Client *</label>
                                <input type="number" id="clientId" class="form-control-custom" placeholder="Ex: 101" required>
                            </div>
                            <div class="form-group">
                                <label for="clientName">Nom du Client</label>
                                <input type="text" id="clientName" class="form-control-custom" placeholder="Chargement..." readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Type de prêt et montant -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="type_pret_id">Type de prêt *</label>
                            <select id="type_pret_id" class="form-control-custom" required>
                                <option value="">-- Choisir un type de prêt --</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="montant_emprunt">Montant emprunté (Ar) *</label>
                            <input type="number" id="montant_emprunt" class="form-control-custom" placeholder="Ex: 200000" required>
                        </div>
                    </div>

                    <!-- Dates -->
                    <div class="form-row">
                        <div class="form-group date-picker">
                            <label for="date_debut">Date de début *</label>
                            <input type="date" id="date_debut" class="form-control-custom" required>
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="form-group date-picker">
                            <label for="date_fin">Date de fin *</label>
                            <input type="date" id="date_fin" class="form-control-custom" required>
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>

                    <!-- Info type de prêt -->
                    <div id="type-pret-info" class="info-client" style="display: none;">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Taux d'intérêt annuel</label>
                                <span id="taux-interet" class="montant-info">-</span>
                            </div>
                            <div class="form-group">
                                <label>Montant maximum</label>
                                <span id="montant-max" class="montant-info">-</span>
                            </div>
                            <div class="form-group">
                                <label>Durée maximum</label>
                                <span id="duree-max">- mois</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="reset" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </button>
                        <button type="submit" class="btn-primary-custom">
                            <i class="fas fa-save"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const apiBase = "http://localhost:8000/ws";
        let typesPret = [];

        function ajax(method, url, data, callback) {
            const xhr = new XMLHttpRequest();
            xhr.open(method, apiBase + url, true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = () => {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        callback(JSON.parse(xhr.responseText));
                    } else {
                        console.error('Erreur:', xhr.statusText);
                        alert("Une erreur est survenue");
                    }
                }
            };
            xhr.send(data);
        }

        function chargerSelects() {
            ajax("GET", "/type_pret", null, (types) => {
                typesPret = types;
                const select = document.getElementById("type_pret_id");
                select.innerHTML = '<option value="">-- Choisir un type de prêt --</option>';
                
                types.forEach((t) => {
                    const option = document.createElement("option");
                    option.value = t.id;
                    option.textContent = `${t.nom} (max ${formatMoney(t.montant_max_pres)} Ar)`;
                    select.appendChild(option);
                });
            });
        }

        function chargerClientInfo(clientId) {
            if (!clientId) return;
            
            ajax("GET", `/clients/${clientId}`, null, (client) => {
                document.getElementById("clientName").value = client.nom;
            });
        }

        function afficherInfosTypePret(typePretId) {
            const infoDiv = document.getElementById("type-pret-info");
            if (!typePretId) {
                infoDiv.style.display = 'none';
                return;
            }
            
            const typePret = typesPret.find(t => t.id == typePretId);
            if (typePret) {
                document.getElementById("taux-interet").textContent = `${(typePret.taux_interet_annuel * 100).toFixed(2)}%`;
                document.getElementById("montant-max").textContent = formatMoney(typePret.montant_max_pres) + ' Ar';
                document.getElementById("duree-max").textContent = typePret.duree_max_mois + ' mois';
                infoDiv.style.display = 'block';
            } else {
                infoDiv.style.display = 'none';
            }
        }

        function ajouterPret() {
            const clientId = document.getElementById("clientId").value;
            const typePretId = document.getElementById("type_pret_id").value;
            const montant = document.getElementById("montant_emprunt").value;
            const dateDebut = document.getElementById("date_debut").value;
            const dateFin = document.getElementById("date_fin").value;

            if (!clientId || !typePretId || !montant || !dateDebut || !dateFin) {
                alert("Veuillez remplir tous les champs obligatoires");
                return;
            }

            const data = new URLSearchParams();
            data.append('client', clientId);
            data.append('type_pret_id', typePretId);
            data.append('montant_emprunt', montant);
            data.append('date_debut', dateDebut);
            data.append('date_fin', dateFin);
            data.append('is_pret_simulation', '0');

            ajax("POST", "/creation_pret", data.toString(), (response) => {
                alert(response.message);
                if (response.success) {
                    resetForm();
                }
            });
        }

        function resetForm() {
            document.getElementById("pretForm").reset();
            document.getElementById("type-pret-info").style.display = 'none';
        }

        function formatMoney(amount) {
            return new Intl.NumberFormat('fr-FR').format(amount);
        }
        
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const clientId = urlParams.get("clientId");
            if (clientId) {
                document.getElementById("clientId").value = clientId;
                chargerClientInfo(clientId);
            }
            
            chargerSelects();
            
            document.getElementById("type_pret_id").addEventListener('change', function() {
                afficherInfosTypePret(this.value);
            });
            
            document.getElementById("clientId").addEventListener('change', function() {
                chargerClientInfo(this.value);
            });
        });
    </script>
</body>
</html>