<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            <li class="<?php activeFile('index.php') ?>">
                <a href="index"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="treeview <?php activeFile('teams.php') ?>">
                <a href="#">
                    <i class="fa fa-users"></i> <span>Équipes</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li>
                        <?php
                        if (getUserTeam(getInformation()))
                            echo '<a href="team/myteam">Mon Équipe</a>';
                        else
                            echo '<a href="team/create">Créer une Équipe</a>';
                        ?>
                        <a href="teams">Parcourir les Équipes</a>
                    </li>
                </ul>
            </li>
            <li class="treeview <?php activeFile('challenges.php', 'action', 'current');activeFile('challenges.php', 'action', 'all') ?>">
                <a href="#">
                    <i class="fa fa-code"></i> <span>Challenges</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li>
                        <a href="challenges/current">Challenges en cours</a>
                        <a href="challenges/all">Challenges précédents</a>
                    </li>
                </ul>
            </li>
            <?php
            if (checkPrivileges(getInformation())) {
                ?>
                <li class="header">Gestion de l'association</li>
                <li class="treeview <?php activeFile('users.php') ?>">
                    <a href="#">
                        <i class="fa fa-user"></i> <span>Utilisateurs</span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a href="users/manage">Gérer les utilisateurs</a>
                            <?php
                            if (checkPrivileges(getInformation(), 'desk_president'))
                                echo '<a href="users/desks/manage">Gérer le bureau</a>';
                            ?>
                        </li>
                    </ul>
                </li>
            <?php
            if (checkPrivileges(getInformation(), 'desk_challenges') || checkPrivileges(getInformation(), 'desk_president') || checkPrivileges(getInformation(), 'desk_jurys')) {
                ?>
                <li class="treeview <?php activeFile('challenges.php','action','create');activeFile('challenges.php','action','manage') ?>">
                    <a href="#">
                        <i class="fa fa-bolt"></i> <span>Challenges</span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a href="challenges/create">Créer un Challenge</a>
                            <a href="challenges/manage">Gérer les Challenges</a>
                        </li>
                    </ul>
                </li>
                <?php
            }
                ?>
                <li class="treeview <?php activeFile('languages.php') ?>">
                    <a href="#">
                        <i class="fa fa-code"></i> <span>Langages</span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                            <a href="languages/manage">Gérer les langages</a>
                            <a href="languages/sets">Gérer les sets de langages</a>
                        </li>
                    </ul>
                </li>
                <?php
                if (checkPrivileges(getInformation(), 'desk_treasurer') || checkPrivileges(getInformation(), 'desk_president')) {
                    ?>
                    <li class="treeview <?php activeFile('treasury.php') ?>">
                        <a href="#">
                            <i class="fa fa-money"></i> <span>Comptabilité</span> <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li>
                                <a href="treasury/<?php echo date("Y"); ?>/<?php echo url_slug(date("F")); ?>">Bilan mensuel</a>
                                <a href="treasury/<?php echo date("Y"); ?>">Bilan annuel</a>
                                <a href="treasury/add">Ajouter un déplacement d'argent</a>
                            </li>
                        </ul>
                    </li>
                    <?php
                }
                ?>
                <?php
            }
            ?>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>