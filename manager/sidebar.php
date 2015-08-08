<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            <li class="active">
                <a href="index"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-users"></i> <span>Équipes</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li>
                        <?php
                        if (hasTeam(getInformation()))
                            echo '<a href="team/myteam">Mon Équipe</a>';
                        else
                            echo '<a href="team/create">Créer une Équipe</a>';
                        ?>
                        <a href="teams">Parcourir les Équipes</a>
                    </li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-code"></i> <span>Challenges</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li>
                        <a href="challenges/current">Challenge en cours</a>
                        <a href="challenges/all">Tous les Challenges</a>
                    </li>
                </ul>
            </li>
            <li class="header">Gestion de l'association</li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-user"></i> <span>Utilisateurs</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li>
                        <a href="users/manage">Gérer les utilisateurs</a>
                        <a href="users/desk">Gérer le bureau</a>
                    </li>
                </ul>
            </li>
            <li class="treeview">
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
            <li class="treeview">
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
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>