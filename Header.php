<!-- Header.php -->
<!-- Header Top -->
<div class="header__top">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-8 col-md-8">
        <div class="header__top__widget">
          <ul></ul>
        </div>
      </div>
      <div class="col-lg-4 col-md-4">
        <div class="header__top__language ms-auto">
          <!-- <img src="https://via.placeholder.com/24x24/88C417/ffffff?text=US" alt="Flag"> -->
          <span>E-BANK</span>
          <i class="fa fa-angle-down"></i>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Header Principal -->
<!-- Header Principal -->
<header class="header">
  <link rel="stylesheet" href="style/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-3 col-md-3">
        <div class="header__logo">
          <!-- Logo optionnel -->
        </div>
      </div>
      <div class="col-lg-9 col-md-9">
        <div class="header__nav">
          <nav class="header__menu">
            <ul>
              <li>
                <a href="formulaire_ajout_fond.php" class="active">Accueil</a>
              </li>

              <!-- Groupe : Gestion des Clients -->
              <li class="dropdown">
                <a href="#">Clients</a>
                <ul class="dropdown-menu">
                  <li><a href="liste-client.php">Liste des clients</a></li>
                  <li><a href="fiche-client.php">Fiche client</a></li>
                </ul>
              </li>

              <!-- Groupe : Prêts -->
              <li class="dropdown">
                <a href="#">Prêts</a>
                <ul class="dropdown-menu">
                  <li><a href="PretCreationClient.php">Créer un prêt</a></li>
                  <li><a href="ajout-type-pret.php">Types de prêts</a></li>
                  <li><a href="details-pret.php">Détails prêt</a></li>
                  <li><a href="simulationPret.php">Simulation</a></li>
                </ul>
              </li>

              <!-- Groupe : Financier -->
              <li class="dropdown">
                <a href="#">Suivi financier</a>
                <ul class="dropdown-menu">
                  <li><a href="RemboursementClient.php">Remboursements</a></li>
                  <li>
                    <a href="interetStat.php">Statistiques des intérêts</a>
                  </li>
                  <!-- <li><a href="interets.php">Tableau des intérêts</a></li> -->

                  <li><a href="simulationPret.php">Simulation d'interet</a></li>
                </ul>
              </li>

              <!-- Groupe : Autres -->
              <li class="dropdown">
                <a href="#">Autres</a>
                <ul class="dropdown-menu">
                  <li><a href="connexion.html">Connexion</a></li>
                </ul>
              </li>
            </ul>
          </nav>

          <div class="header__search">
            <i class="fa fa-search"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>
