<?php 
    $activePage = 'clients';
    include 'Header.php'; 
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Clients - E-BANK</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .client-form {
            background: var(--white);
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin: 2rem 0;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .form-actions {
            margin-top: 1.5rem;
        }
        
        .action-buttons button {
            margin-right: 0.5rem;
        }
        
        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>

    <div class="main-content">
        <div class="container">
            <div class="table-container">
                <div class="table-header">
                    <h3>Gestion des Clients</h3>
                    <button class="btn-primary-custom" onclick="resetFormClient()">
                        <i class="fas fa-plus"></i> Nouveau Client
                    </button>
                </div>
                
                <div class="client-form">
                    <input type="hidden" id="id">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nom">Nom complet</label>
                            <input type="text" id="nom" class="form-control-custom" placeholder="Nom complet">
                        </div>
                        <div class="form-group">
                            <label for="username">Nom d'utilisateur</label>
                            <input type="text" id="username" class="form-control-custom" placeholder="Username">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" class="form-control-custom" placeholder="Email">
                        </div>
                        <div class="form-group">
                            <label for="telephone">Téléphone</label>
                            <input type="text" id="telephone" class="form-control-custom" placeholder="Téléphone">
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button onclick="ajouterOuModifierClient()" class="btn-primary-custom">
                            <i class="fas fa-save"></i> Enregistrer
                        </button>
                        <button onclick="resetFormClient()" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </button>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>Rôle</th>
                                <th>Statut</th>
                                <th>Date Inscription</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="table-clients">
                            <!-- Les clients seront chargés ici -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
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

        function chargerClients() {
            ajax("GET", "/clients", null, (data) => {
                const tbody = document.querySelector("#table-clients");
                tbody.innerHTML = "";
                
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="9">Aucun client trouvé</td></tr>';
                    return;
                }
                
                data.forEach((client) => {
                    const tr = document.createElement("tr");
                    tr.innerHTML = `
                        <td>${client.id}</td>
                        <td>${client.nom}</td>
                        <td>${client.username}</td>
                        <td>${client.email}</td>
                        <td>${client.telephone}</td>
                        <td>${client.role_nom}</td>
                        <td><span class="status-badge ${getStatusClass(client.statut_nom)}">${client.statut_nom}</span></td>
                        <td>${formatDate(client.date_inscription)}</td>
                        <td class="action-buttons">
                            <button onclick='remplirFormulaireClient(${JSON.stringify(client)})' class="btn btn-sm btn-outline-primary" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick='supprimerClient(${client.id})' class="btn btn-sm btn-outline-danger" title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                            <button onclick='afficherDetailsClient(${client.id})' class="btn btn-sm btn-outline-secondary" title="Détails">
                                <i class="fas fa-eye"></i>
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

            if (!nom || !username || !email || !telephone) {
                alert("Veuillez remplir tous les champs obligatoires");
                return;
            }

            const data = `nom=${encodeURIComponent(nom)}&username=${encodeURIComponent(username)}&email=${encodeURIComponent(email)}&telephone=${encodeURIComponent(telephone)}`;

            if (id) {
                if (!confirm("Modifier ce client ?")) return;
                ajax("PUT", `/clients/${id}`, data, (response) => {
                    alert(response.message);
                    resetFormClient();
                    chargerClients();
                });
            } else {
                ajax("POST", "/clients", data, (response) => {
                    alert(response.message);
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
            
            // Scroll vers le formulaire
            document.querySelector('.client-form').scrollIntoView({ behavior: 'smooth' });
        }

        function supprimerClient(id) {
            if (confirm("Êtes-vous sûr de vouloir supprimer ce client ?")) {
                ajax("DELETE", `/clients/${id}`, null, (response) => {
                    alert(response.message);
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
            window.location.href = `fiche-client.php?id=${id}`;
        }
        
        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString('fr-FR');
        }
        
        function getStatusClass(status) {
            const statusMap = {
                'actif': 'status-approved',
                'inactif': 'status-rejected',
                'suspendu': 'status-pending'
            };
            return statusMap[status.toLowerCase()] || '';
        }

        document.addEventListener('DOMContentLoaded', chargerClients);
    </script>
</body>
</html>