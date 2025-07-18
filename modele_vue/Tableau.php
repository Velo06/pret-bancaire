<!-- tableau.html -->
<?php include 'Header.php'; ?>
<main class="main-content">
    <div class="container">

        <div class="table-container">
            <div class="table-header">
                <h3>Gestion des Prêts</h3>
                <button class="btn-primary-custom">Nouveau Prêt</button>
            </div>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Client</th>
                            <th>Type de Prêt</th>
                            <th>Montant</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Exemple de lignes -->
                        <tr>
                            <td>#001</td>
                            <td>Jean Dupont</td>
                            <td>Prêt Personnel</td>
                            <td>15 000 €</td>
                            <td>2024-01-15</td>
                            <td><span class="status-badge status-approved">Approuvé</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1"><i class="fa fa-eye"></i></button>
                                <button class="btn btn-sm btn-outline-success me-1"><i class="fa fa-edit"></i></button>
                                <button class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                        <!-- Ajouter d'autres lignes dynamiquement -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>