<?php
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
    <header class="main-header">
        <a href="./index" class="logo hidden-xs">
            <span class="logo-lg"><img src="../assets/img/public/banner_borderless.png" class="img-responsive"></span>
        </a>
        <nav class="navbar navbar-static-top" role="navigation">
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>

            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li>
                        <a href="https://letsdevatig2i.slack.com/signup" title="Slack" target="_blank">
                            <i class="fa fa-slack"></i>
                        </a>
                    </li>
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
                                    <a href="./profile" class="btn btn-default btn-flat">Mon Compte</a>
                                </div>
                                <div class="pull-right">
                                    <a href="../signout" class="btn btn-default btn-flat">Déconnexion</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>