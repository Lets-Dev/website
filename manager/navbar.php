<?php
include('../includes/version.php');
$onload = "getWallpaper('manager');";
if (isset($_GET['alert'])) {
    switch ($_GET['alert']) {
        case 'loggedin':
            $onload .= "toastr['success']('Vous êtes bien connecté.')";
            break;
    }
}
?>
<body class="skin-black fixed" onload="<?php echo $onload; ?>">
<div class="wrapper" id="wallpaper">
    <div
        style="position: fixed;bottom: 10px; right: 10px; color: #FEFEFE; text-shadow: 1px 0 0 #000,-1px 0 0 #000,0 1px 0 #000,0 -1px 0 #000"
        class="text-right">
        <?php
        foreach ($version as $versioning => $title) {
            $html = '<b>' . $versioning . '</b><p>' . $title . '</p>';
        }
        echo $html;

        ?>
    </div>
    <header class="main-header">
        <!-- Logo -->
        <a href="./index" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><img src="../assets/img/public/logo.png" class="img-responsive"></span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><img src="../assets/img/public/banner_borderless.png" class="img-responsive"></span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>

            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <?php
                    $n = new Notifications(getInformation());
                    $unread = $n->unread();
                    ?>
                    <li class="dropdown notifications-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-bell-o"></i>
                            <?php
                            if ($n->nb_unread() > 0)
                                echo '<span class="label label-warning" id="notification_count">'.$n->nb_unread().'</span>';
                            ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header">
                                Notifications
                                <span class="pull-right">
                                    <a href="#" role="button" onclick="readNotifications(); return false;">Marquer comme lu</a>
                                </span>
                            </li>
                            <li>
                                <!-- inner menu: contains the actual data -->
                                <ul class="menu" id="notifications">
                                    <?php
                                    foreach ($unread['notifications'] as $key => $notification) {
                                        echo '<li>
                                        <a href="#">
                                            '.$notification['text'].' <small style="color: #AAA">'.date_fr("d M Y \à H:i",false,$notification['time']).'</small>
                                        </a>
                                    </li>';
                                    }
                                    ?>
                                </ul>
                            </li>
                            <li class="footer"><a href="#" role="button" onclick="viewAllNotifications(); return false;">Voir toutes les notifications</a></li>
                        </ul>
                    </li>
                    <script type="text/javascript">
                        $('.dropdown-menu').click(function(e) {
                            e.stopPropagation();
                        });
                    </script>
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <img
                                src="http://gravatar.com/avatar/<?php echo md5(getInformation('email')) ?>?s=25&d=<?php echo urlencode($config['users']['default_avatar']) ?>"
                                class="user-image" alt="User Image"/>
                        <span
                            class="hidden-xs"><?php echo getInformation('firstname') . ' ' . getInformation('lastname') ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">
                                <img
                                    src="http://gravatar.com/avatar/<?php echo md5(getInformation('email')) ?>?s=90&d=<?php echo urlencode($config['users']['default_avatar']) ?>"
                                    class="img-circle" alt="User Image"/>

                                <p>
                                    <?php echo getInformation('firstname') . ' ' . getInformation('lastname') ?>
                                    <small>
                                        <?php if (isMember(getInformation(), date('Y'))) {
                                            echo '<i class="fa fa-check"></i> ' . $config['expressions']['member'];
                                        } else {
                                            echo $config['expressions']['non-member'];
                                        } ?>
                                    </small>
                                </p>
                            </li>
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="./profile.php" class="btn btn-default btn-flat">Mon Compte</a>
                                </div>
                                <div class="pull-right">
                                    <a href="../signout.php" class="btn btn-default btn-flat">Déconnexion</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                    <!-- Control Sidebar Toggle Button -->
                    <li>
                        <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>