<!-- formulaire.html -->
<?php include('Header.php'); ?>
<div class="form-container">
    <div class="form-header">
        <h3>Demande de Prêt</h3>
        <p>Remplissez le formulaire ci-dessous pour soumettre votre demande</p>
    </div>

    <form>
        <!-- Informations Personnelles -->
        <h4 class="mb-3">Informations Personnelles</h4>
        <div class="form-row">
            <div class="form-group">
                <label for="firstName">Prénom *</label>
                <input type="text" id="firstName" class="form-control-custom" placeholder="Votre prénom" required>
            </div>
            <div class="form-group">
                <label for="lastName">Nom *</label>
                <input type="text" id="lastName" class="form-control-custom" placeholder="Votre nom" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" class="form-control-custom" placeholder="votre@email.com" required>
            </div>
            <div class="form-group">
                <label for="phone">Téléphone *</label>
                <input type="tel" id="phone" class="form-control-custom" placeholder="01 23 45 67 89" required>
            </div>
        </div>

        <!-- Informations sur le Prêt -->
        <h4 class="mb-3 mt-4">Informations sur le Prêt</h4>
        <div class="form-row">
            <div class="form-group">
                <label for="loanType">Type de Prêt *</label>
                <select id="loanType" class="form-control-custom" required>
                    <option value="">Sélectionnez un type</option>
                    <option value="personal">Prêt Personnel</option>
                    <option value="auto">Prêt Auto</option>
                    <option value="home">Prêt Immobilier</option>
                    <option value="business">Prêt Professionnel</option>
                    <option value="education">Prêt Étudiant</option>
                </select>
            </div>
            <div class="form-group">
                <label for="loanAmount">Montant Demandé *</label>
                <input type="number" id="loanAmount" class="form-control-custom" placeholder="Ex: 50000" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="duration">Durée (mois) *</label>
                <select id="duration" class="form-control-custom" required>
                    <option value="">Sélectionnez une durée</option>
                    <option value="12">12 mois</option>
                    <option value="24">24 mois</option>
                    <option value="36">36 mois</option>
                    <option value="48">48 mois</option>
                    <option value="60">60 mois</option>
                    <option value="120">120 mois</option>
                    <option value="240">240 mois</option>
                </select>
            </div>
            <div class="form-group">
                <label for="income">Revenus Mensuels *</label>
                <input type="number" id="income" class="form-control-custom" placeholder="Ex: 3000" required>
            </div>
        </div>

        <div class="form-group">
            <label for="purpose">Objet du Prêt</label>
            <textarea id="purpose" class="form-control-custom" rows="4" placeholder="Décrivez brièvement l'objet de votre demande de prêt..."></textarea>
        </div>

        <!-- Actions -->
        <div class="form-actions">
            <button type="reset" class="btn btn-outline-secondary">Annuler</button>
            <button type="submit" class="btn-primary-custom">Soumettre la Demande</button>
        </div>
    </form>
</div>
