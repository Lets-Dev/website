<?php
include('includes/autoload.php');
?>
<!Doctype HTML>
<html>
<head>
    <title>Let's Dev !</title>

    <meta charset="utf-8">

    <link rel="icon" href="assets/img/public/logo.png"/>

    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="assets/css/AdminLTE.min.css"/>
    <link rel="stylesheet" type="text/css" href="assets/css/fontawesome.min.css"/>
    <link rel="stylesheet" type="text/css" href="assets/css/lets-dev.min.css"/>
    <link href='https://fonts.googleapis.com/css?family=Titillium+Web' rel='stylesheet' type='text/css'>
    <style>
        h1 {
            font-family: 'Titillium Web', sans-serif;
        }
    </style>
    <script src="assets/js/jquery.min.js"></script>
</head>
<body onload="getWallpaper(1)">
<div class="container-fluid" style="padding-top: 50px; padding-bottom: 50px;" id="wallpaper">
    <div class="container">
        <nav class="navbar navbar-transparent">
            <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                            data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <i class="fa fa-bars"></i>
                    </button>
                    <a class="navbar-brand" href="#">
                        <img src="assets/img/public/banner.png"
                             style="height:50px; margin-top: -15px;margin-left:-15px">
                    </a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav navbar-right" style="margin-top: 3px;">
                        <?php
                        if (checkSession())
                            echo '<li><a href="manager">Accéder au manager</a></li>';
                        else
                            echo '<li><a href="signin">Connexion</a></li>
                              <li><a href="signup">Inscription</a></li>';
                        ?>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="row" style="margin-top:150px;">
            <div class="col-md-2 col-md-offset-5">
                <img src="assets/img/public/logo.png" class="img-responsive"/>
            </div>
        </div>
        <div class="row" style="margin-bottom:150px;">
            <div class="col-md-6 col-md-offset-3 text-center">
                <h1 class="motto">ICI UN SLOGAN QUI PÊTE SA MERE</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header">
                        <h2 class="text-center">Le concept</h2>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <i class="fa fa-code fa-3x"></i>

                                <h3>Les challenges</h3>

                                <p>
                                    Les challenges sont des épreuves d'une durée de 2 mois durant lesquels les
                                    différentes équipes s'y inscrivent et apprennent les langages proposés et
                                    développent, avec ces langages, un projet selon le sujet donné.
                                </p>
                            </div>
                            <div class="col-md-4 text-center">
                                <i class="fa fa-users fa-3x"></i>

                                <h3>Les équipes</h3>

                                <p>
                                    Les membres doivent former des équipes de
                                    <?php echo $config['teams']['min_members'] ?>
                                    à <?php echo $config['teams']['max_members'] ?>
                                    membres afin de participer aux challenges. L'équipe accumulera au fil des
                                    challenges des points, et les trois équipes avec le plus de points se
                                    verront récompensées à la fin de l'année scolaire.
                                </p>
                            </div>
                            <div class="col-md-4 text-center">
                                <i class="fa fa-comments fa-3x"></i>

                                <h3>Les jurys</h3>

                                <p>
                                    A chaque challenge, un nouveau jury est désigné selon ses compétences dans les
                                    langages choisis. Les jurys ont la lourde tâche de
                                    répartir <?php echo $config['challenges']['points_per_challenge'] ?>
                                    points entre les équipes participant à un challenge.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <ul class="timeline">
                    <li class="time-label">
                    <span class="bg-blue">
                        Déroulement d'un challenge
                    </span>
                    </li>
                    <li>
                        <i class="fa fa-lightbulb-o bg-red"></i>

                        <div class="timeline-item">

                            <h3 class="timeline-header">Premier mois</h3>

                            <div class="timeline-body">
                                <ul>
                                    <li>Découverte des langages
                                        des <?php echo $config['challenges']['languages_per_challenge'] ?> challenges
                                        proposés
                                    </li>
                                    <li>Inscription à un des challenges proposés</li>
                                    <li>Apprentissage des langages</li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li>
                        <i class="fa fa-code bg-orange"></i>

                        <div class="timeline-item">

                            <h3 class="timeline-header">Second mois</h3>

                            <div class="timeline-body">
                                <ul>
                                    <li>Découverte des sujets</li>
                                    <li>Développement des projets</li>
                                    <li>Envoi des projets avant la fin du mois pour évaluation</li>
                                </ul>
                            </div>
                        </div>
                    <li>
                        <i class="fa fa-heart-o bg-green"></i>

                        <div class="timeline-item">

                            <h3 class="timeline-header"><?php echo $config['challenges']['days_to_rate'] ?> jours
                                après</h3>

                            <div class="timeline-body">
                                <ul>
                                    <li>Répartition de <?php echo $config['challenges']['points_per_challenge'] ?>
                                        points
                                        entre
                                        les équipes
                                    </li>
                                </ul>
                            </div>

                        </div>
                    </li>
                    <li>
                        <i class="fa fa-trophy bg-purple"></i>

                        <div class="timeline-item">

                            <h3 class="timeline-header">Fin de l'année</h3>

                            <div class="timeline-body">
                                <ul>
                                    <li>
                                        Gratification des trois équipes ayant le plus de points
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li>
                        <i class="fa fa-clock-o bg-gray"></i>
                    </li>
                </ul>
            </div>
            <div class="col-md-8">
                <div class="row">
                    <?php
                    $query = $db -> prepare('select * from desks
                                             where desk_year = :year');
                    $query->bindValue(':year', getCurrentYear(), PDO::PARAM_INT);
                    $query->execute();
                    if ($data = $query->fetchObject()) {
                        echo '
                    <div class="col-md-6">
                        <div class="user-card no-padding">
                            <div class="user-pic">
                                <img class="img-circle" src="http://gravatar.com/avatar/'.md5(getInformation('email', $data->desk_president)) .'?s=90&d='.urlencode($config['users']['default_avatar']) .'" style="width: 90px;">
                            </div>
                            <div class="user-description">
                                <div class="box">
                                    <div class="box-header text-center">
                                        <h3>'.getInformation('firstname', $data->desk_president).' '.getInformation('lastname', $data->desk_president).'</h3>
                                        <h5>Président</h5>
                                    </div>
                                    <div class="box-footer text-center">
                                        <a href="mailto:'.getInformation('email', $data->desk_president).'">'.getInformation('email', $data->desk_president).'</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="user-card no-padding">
                            <div class="user-pic">
                                <img class="img-circle" src="http://gravatar.com/avatar/'.md5(getInformation('email', $data->desk_secretary))  .'?s=90&d='.urlencode($config['users']['default_avatar']) .'" style="width: 90px;">
                            </div>
                            <div class="user-description">
                                <div class="box">
                                    <div class="box-header text-center">
                                        <h3>'.getInformation('firstname', $data->desk_secretary).' '.getInformation('lastname', $data->desk_secretary).'</h3>
                                        <h5>Secrétaire</h5>
                                    </div>
                                    <div class="box-footer text-center">
                                        <a href="mailto:'.getInformation('email', $data->desk_secretary).'">'.getInformation('email', $data->desk_secretary).'</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="user-card">
                            <div class="user-pic">
                                <img class="img-circle" src="http://gravatar.com/avatar/'.md5(getInformation('email', $data->desk_treasurer))  .'?s=90&d='.urlencode($config['users']['default_avatar']) .'" style="width: 90px;">
                            </div>
                            <div class="user-description">
                                <div class="box">
                                    <div class="box-header text-center">
                                        <h3>'.getInformation('firstname', $data->desk_treasurer).' '.getInformation('lastname', $data->desk_treasurer).'</h3>
                                        <h5>Trésorier</h5>
                                    </div>
                                    <div class="box-footer text-center">
                                        <a href="mailto:'.getInformation('email', $data->desk_treasurer).'">'.getInformation('email', $data->desk_treasurer).'</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="user-card">
                            <div class="user-pic">
                                <img class="img-circle" src="http://gravatar.com/avatar/'.md5(getInformation('email', $data->desk_communication))  .'?s=90&d='.urlencode($config['users']['default_avatar']) .'" style="width: 90px;">
                            </div>
                            <div class="user-description">
                                <div class="box">
                                    <div class="box-header text-center">
                                        <h3>'.getInformation('firstname', $data->desk_communication).' '.getInformation('lastname', $data->desk_communication).'</h3>
                                        <h5>Responsable communication</h5>
                                    </div>
                                    <div class="box-footer text-center">
                                        <a href="mailto:'.getInformation('email', $data->desk_communication).'">'.getInformation('email', $data->desk_communication).'</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="user-card">
                            <div class="user-pic">
                                <img class="img-circle" src="http://gravatar.com/avatar/'.md5(getInformation('email', $data->desk_challenges)) .'?s=90&d='.urlencode($config['users']['default_avatar']) .'" style="width: 90px;">
                            </div>
                            <div class="user-description">
                                <div class="box">
                                    <div class="box-header text-center">
                                        <h3>'.getInformation('firstname', $data->desk_challenges).' '.getInformation('lastname', $data->desk_challenges).'</h3>
                                        <h5>Responsable challenges</h5>
                                    </div>
                                    <div class="box-footer text-center">
                                        <a href="mailto:'.getInformation('email', $data->desk_challenges).'">'.getInformation('email', $data->desk_challenges).'</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="user-card">
                            <div class="user-pic">
                                <img class="img-circle" src="http://gravatar.com/avatar/'.md5(getInformation('email', $data->desk_jurys)) .'?s=90&d='.urlencode($config['users']['default_avatar']) .'" style="width: 90px;">
                            </div>
                            <div class="user-description">
                                <div class="box">
                                    <div class="box-header text-center">
                                        <h3>'.getInformation('firstname', $data->desk_jurys).' '.getInformation('lastname', $data->desk_jurys).'</h3>
                                        <h5>Responsable jurys</h5>
                                    </div>
                                    <div class="box-footer text-center">
                                        <a href="mailto:'.getInformation('email', $data->desk_jurys).'">'.getInformation('email', $data->desk_jurys).'</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<div style="margin-bottom: 203px;"></div>
<footer class="index-footer">
    <div class="container">
        <div class="row">
            <div class="col-md-2 col-md-offset-5 text-center">
                <img src="assets/img/public/logo.png" class="logo"/>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-md-offset-3 text-center">
                Let's Dev !<br/>
                IG
                <small>2</small>
                I<br/>
                13 rue Jean Souvraz<br/>
                62300 Lens
            </div>
        </div>

    </div>
</footer>


<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/lets-dev.min.js"></script>
</body>
</html>