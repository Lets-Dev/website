<?php
include('../includes/autoload.php');
include('header.php');
include('navbar.php');
include('sidebar.php');
?>

    <div class="content-wrapper" onmouseover="changeTitle('Let\'s Dev ! - Manager')">
        <div class="row">
            <div class="col-md-3">
                <div class="box">
                    <div class="box-header">
                        <h3>Challenges en cours</h3>
                    </div>
                    <div class="box-body">
                        <?php
                        $query = $db->prepare("SELECT * FROM challenges
                        LEFT JOIN language_sets ON set_id = challenge_language
                        WHERE challenge_start < :time
                        AND challenge_end > :time");
                        $query->bindValue(':time', time(), PDO::PARAM_INT);
                        $query->execute();
                        $i = true;

                        while ($data = $query->fetchObject()) {
                            if (!$i)
                                echo '<hr />';
                            echo '<div>
                                    <p>
                                      <a href="challenges/current">' . $data->set_name . '</a>
                                    </p>
                                    <small>Du ' . date_fr('d F', false, $data->challenge_start) . ' au ' . date_fr('d F Y', false, $data->challenge_end) . '</small>
                                  </div>';
                            $i = false;
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="box">
                    <div class="box-header">
                        <h3>Informations</h3>
                    </div>
                    <div class="box-body">
                        <h4>Enfin la rentrée !</h4>
                        <p>
                            Nous le savons, vous l'attendiez tous cette rentrée, non ? Dans tous les cas,
                            nous, nous l'attendions. Nous êtions même impatients de relancer Let's Dev !
                            et de créer de nouveaux challenges pour vous.
                        </p>
                        <p>
                            Nous vous rappelons les règles du jeu:
                        </p>
                            <ul>
                                <li>Seuls les cotisants peuvent accéder aux challenges et aux parties avancées du site</li>
                                <li>Tout au long de l'année, nous vous proposons des challenges. Chaque challenge délivrera 1000 points, répartis par le jury selon la qualité des travaux rendus</li>
                                <li>Les challenges ont une durée de deux mois: un mois d'apprentissage, un mois de développement</li>
                                <li>A la fin de l'année, les trois meilleures équipes remportent un prix</li>
                                <li>Pour participer à un challenge, il faut faire partie d'une équipe ayant entre 3 et 10 membres</li>
                                <li>L'inscription au challenge n'est possible que depuis le compte du propriétaire de l'équipe</li>
                            </ul>
                        <p>Nous restons à votre disposition: <a href="mailto:lets-dev@ig2i.fr">lets-dev(at)ig2i.fr</a></p>
                        <p>Bonne rentrée à vous, et bon développement <i class="fa fa-hand-peace-o"></i></p>
                    </div>
                    <div class="box-footer">
                        Nous vous demandons de désactiver AdBlock afin d'avoir un accès intégral sur le contenu.
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="box">
                    <div class="box-header">
                        <h3>Derniers Tweets</h3>
                    </div>
                    <div class="box-body">
                        <a class="twitter-timeline" href="https://twitter.com/LetsDevAtIG2I"
                           data-widget-id="636223700262617088"
                           data-chrome="noheader nofooter noborders noscrollbar"
                           data-tweet-limit="3"></a>
                        <script>!function (d, s, id) {
                                var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https';
                                if (!d.getElementById(id)) {
                                    js = d.createElement(s);
                                    js.id = id;
                                    js.src = p + "://platform.twitter.com/widgets.js";
                                    fjs.parentNode.insertBefore(js, fjs);
                                }
                            }(document, "script", "twitter-wjs");</script>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
include('footer.php');
?>