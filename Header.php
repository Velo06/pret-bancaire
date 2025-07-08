<!-- Header.php -->
<!-- Header Top -->
<div class="header__top">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 col-md-8">
                <div class="header__top__widget">
                    <ul>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4 col-md-4">
                <div class="header__top__language ms-auto">
                    <span>E-BANK</span>
                    <i class="fa fa-angle-down"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Header Principal -->
<header class="header">
    <link rel="stylesheet" href="style/style.css">

    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-3 col-md-3">
                <div class="header__logo">
                    <!-- <a href="#"><img src="https://via.placeholder.com/150x50/88C417/ffffff?text=LOANDAY" alt="Loanday"></a> -->
                </div>
            </div>
            <div class="col-lg-9 col-md-9">
                <div class="header__nav">
                    <nav class="header__menu">
                        <ul>
                            <li><a href="formulaire_ajout_fond.php" class="<?php echo ($activePage == 'accueil') ? 'active' : ''; ?>">Accueil</a></li>
                            <li><a href="liste-client.php" class="<?php echo ($activePage == 'clients') ? 'active' : ''; ?>">Clients</a></li>
                            <li><a href="interetStat.php" class="<?php echo ($activePage == 'interets') ? 'active' : ''; ?>">Int&eacute;r&ecirc;ts</a></li>
                            <li><a href="ajout-type-pret.php" class="<?php echo ($activePage == 'typepret') ? 'active' : ''; ?>">Type Prêt</a></li>
                            <li><a href="RemboursementClient.php" class="<?php echo ($activePage == 'remboursement') ? 'active' : ''; ?>">Remboursement client</a></li>
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