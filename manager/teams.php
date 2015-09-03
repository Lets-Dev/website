<?php
include('../includes/autoload.php');
include('header.php');
include('navbar.php');
include('sidebar.php');
if (isset($_GET['team'])) {
    switch ($_GET['team']) {
        case 'create':
            ?>
            <div class="content-wrapper" onmouseover="changeTitle('Let\'s Dev ! - Créer une équipe')">
                <div class="row">
                    <div class="col-md-4 col-md-offset-4">
                        <div class="box" style="margin-top: 50px;">
                            <div class="box-header">
                                <h3>Créer une équipe</h3>
                            </div>
                            <div class="box-body">
                                <form id="create_team">
                                    <input type="hidden" name="action" value="new"/>

                                    <div class="form-group has-feedback">
                                        <input type="text" class="form-control" placeholder="Nom de l'équipe"
                                               name="name"
                                               id="fullname" onkeyup="suggestShortcut()">
                                        <span class="fa fa-comment form-control-feedback"></span>
                                    </div>
                                    <div class="form-group has-feedback">
                                        <input type="text" class="form-control" placeholder="Nom raccourci"
                                               name="shortname"
                                               id="shortname" onkeyup="checkShortcut()" data-toggle="tooltip"
                                               data-placement="top"
                                               title="Le nom raccourci est utilisé pour associer votre page d'équipe à une adresse, ainsi qu'à des fichiers médias. Seuls les caractères ASCII minuscules sont autorisés.">
                                        <span class="fa fa-at form-control-feedback"></span>
                                    </div>
                                    <div class="form-group has-feedback">
                                        <textarea class="form-control" placeholder="Description" name="description"
                                                  rows="1" style="resize: vertical"></textarea>
                                        <span class="fa fa-file-text form-control-feedback"></span>
                                    </div>
                                    <div class="form-group">
                                        <label>Ajouter un logo</label>
                                        <input type="file" name="logo" placeholder="logo"/>
                                    </div>
                                    <button type="submit" class="btn btn-flat btn-ld btn-block">Créer l'équipe</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                $("#create_team").submit(function () {
                    $('.btn').attr('disabled', 'disabled');
                    var formData = new FormData($(this)[0]);
                    $.ajax({
                        type: "POST",
                        url: "../includes/queries/teams.php",
                        data: formData,
                        success: function (data) {
                            console.log(data);
                            if (data.status === "success") {
                                window.location = "./team/myteam";
                            }
                            else {
                                var i;
                                for (i = 0; i < data.messages.length; i++)
                                    toastr["error"](data.messages[i])
                            }
                            $('.btn').removeAttr('disabled');
                        },
                        cache: false,
                        contentType: false,
                        processData: false
                    });
                    return false;
                });
            </script>
            <?php
            break;
        default:
            if (!getUserTeam(getInformation()))
                redirect("./teams");
            if ($_GET['team'] == "myteam") {
                $queryTeam = $db->prepare("SELECT * FROM teams WHERE team_id=:id");
                $queryTeam->bindValue(':id', getUserTeam(getInformation()), PDO::PARAM_INT);
            } else {
                $queryTeam = $db->prepare("SELECT * FROM teams WHERE team_shortname=:id");
                $queryTeam->bindValue(':id', $_GET['team'], PDO::PARAM_INT);
            }
            $queryTeam->execute();
            $dataTeam = $queryTeam->fetchObject();


            $queryChallenges = $db->prepare("SELECT * FROM challenge_subscriptions
                                             LEFT JOIN challenges ON challenge_id=subscription_challenge
                                             LEFT JOIN language_sets ON challenge_language=set_id
                                             WHERE subscription_team=:team
                                             ORDER BY challenge_start DESC
                                             LIMIT 10");
            $queryChallenges->bindValue(':team', $dataTeam->team_id, PDO::PARAM_INT);
            $queryChallenges->execute();


            $queryMemberNumber = $db->prepare('SELECT count(*) AS nb FROM team_subscriptions WHERE subscription_status=1 AND subscription_leave=0 AND subscription_team=:team');
            $queryMemberNumber->bindValue(':team', $dataTeam->team_id, PDO::PARAM_INT);
            $queryMemberNumber->execute();
            $dataMemberNumber = $queryMemberNumber->fetchObject();


            $queryChallengeNumber = $db->prepare('SELECT count(*) AS nb FROM challenge_subscriptions WHERE subscription_team=:team');
            $queryChallengeNumber->bindValue(':team', $dataTeam->team_id, PDO::PARAM_INT);
            $queryChallengeNumber->execute();
            $dataChallengeNumber = $queryChallengeNumber->fetchObject();


            $queryMembers = $db->prepare("SELECT * FROM team_subscriptions
                                        LEFT JOIN users ON user_id=subscription_user
                                        WHERE subscription_team=:team AND subscription_status=1 AND subscription_leave=0
                                        ORDER BY user_firstname");
            $queryMembers->bindValue(':team', $dataTeam->team_id, PDO::PARAM_INT);
            $queryMembers->execute();


            $querySubscriptions = $db->prepare("SELECT * FROM team_subscriptions
                                              LEFT JOIN users ON user_id=subscription_user
                                              WHERE subscription_team=:team
                                              ORDER BY subscription_status, subscription_time DESC");
            $querySubscriptions->bindValue(':team', $dataTeam->team_id, PDO::PARAM_INT);
            $querySubscriptions->execute();
            ?>
            <div class="content-wrapper" onmouseover="changeTitle('Let\'s Dev ! - Mon équipe')">
                <div class="row" style="margin-top: 50px">
                    <div class="col-md-offset-1 col-md-3">
                        <div class="box text-center">
                            <div class="box-header">
                                <h4>
                                    Dernières participations
                                </h4>
                            </div>
                            <div class="box-body">
                                <?php
                                $i = true;
                                while ($dataChallenges = $queryChallenges->fetchObject()) {
                                    if (!$i)
                                        echo '<hr />';
                                    echo "<p>" . $dataChallenges->set_name . " <br/><small>Le " . date_fr("j F Y", false, $dataChallenges->subscription_time) . "</small></p>";
                                    $i = false;
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="box">
                            <div class="box-body text-center">
                                <?php
                                if (file_exists('../assets/img/public/teams/' . url_slug($dataTeam->team_shortname) . '.png'))
                                    echo '<img src="../assets/img/public/teams/' . url_slug($dataTeam->team_shortname) . '.png" class="logo" style="margin-top:-50px;height:100px"/>';
                                else
                                    echo '<img src="../assets/img/public/default_team.png" class="logo" style="margin-top:-50px;height:100px"/>';
                                ?>

                                <h1><?php echo $dataTeam->team_name ?></h1>

                                <div class="row">
                                    <div class="col-md-4">
                                        <h5>Membres</h5>

                                        <h3 data-toggle="tooltip" title="Nombre de membres de l'équipe">
                                            <?php echo $dataMemberNumber->nb; ?>
                                        </h3>
                                    </div>
                                    <div class="col-md-4">
                                        <h5>Création</h5>

                                        <h3 data-toggle="tooltip" title="Année de création de l'équipe">
                                            <?php echo date('Y', $dataTeam->team_creation) ?>
                                        </h3>
                                    </div>
                                    <div class="col-md-4">
                                        <h5>Challenges</h5>

                                        <h3 data-toggle="tooltip"
                                            title="Nombre de challenges auxquels l'équipe a participé">
                                            <?php echo $dataChallengeNumber->nb; ?>
                                        </h3>
                                    </div>
                                </div>
                                <p></p>
                                <hr/>
                                <div class="row row-centered">
                                    <?php
                                    while ($dataMembers = $queryMembers->fetchObject()) {
                                        echo '<div class="col-md-3 col-centered text-center">
                                                <img src="http://gravatar.com/avatar/' . md5($dataMembers->user_email) . '?s=90&d=' . urlencode($config['users']['default_avatar']) . '" class="img-circle"
                                                     style="width: 75%"/>
                                                <p><b>' . $dataMembers->user_firstname . '</b></p>
                                            </div>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="box text-center">
                            <div class="box-header">
                                <h4>Demandes d'adhésion</h4>
                            </div>
                            <div class="box-body">
                                <?php
                                $i = true;
                                while ($dataSubscriptions = $querySubscriptions->fetchObject()) {
                                    if ($dataSubscriptions->subscription_status != 2) {
                                        if (!$i)
                                            echo '<hr />';
                                        echo '
                                    <div class="row">
                                        <div class="col-md-3">
                                            <img src="http://gravatar.com/avatar/' . md5($dataSubscriptions->user_email) . '?s=90&d=' . urlencode($config['users']['default_avatar']) . '" class="img-responsive img-circle">
                                        </div>
                                        <div class="col-md-9 text-left">
                                            <b>' . $dataSubscriptions->user_firstname . ' ' . $dataSubscriptions->user_lastname . '</b><br />';

                                        switch ($dataSubscriptions->subscription_status) {
                                            case 0:
                                                if (isTeamOwner(getInformation(), $dataSubscriptions->subscription_team))
                                                    echo '<small id="answer_' . $dataSubscriptions->subscription_id . '">
                                                              <a href="#" onclick="answerSubscription(' . $dataSubscriptions->subscription_id . ',\'yes\');return false;">Accepter</a> - <a href="#" onclick="answerSubscription(' . $dataSubscriptions->subscription_id . ',\'no\');return false;">Refuser</a>
                                                          </small>';
                                                elseif (getUserTeam(getInformation()) == $dataSubscriptions->subscription_team)
                                                    echo '<small>En attente depuis le ' . date_fr('j F Y', false, $dataSubscriptions->subscription_time) . '</small>';
                                                break;
                                            case 1:
                                                echo '<small>Membre depuis le ' . date_fr('j F Y', false, $dataSubscriptions->subscription_time) . '</small>';
                                                break;
                                        }
                                        echo '</div>
                                        </div>';
                                        $i = false;
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                function answerSubscription(id, answer) {
                    $.post('../includes/queries/teams.php',
                        {
                            action: 'join_answer',
                            answer: answer,
                            id: id
                        }, function (data) {
                            for (i = 0; i < data.messages.length; i++)
                                toastr[data.status](data.messages[i])
                            if (data.status == 'success')
                                $('#answer_' + id).html('<i>' + data.messages[0] + '</i>')
                        })
                }
            </script>
            <?php
            break;
    }
} else {
    ?>
    <div class="content-wrapper" onmouseover="changeTitle('Let\'s Dev ! - Parcourir les équipes')">
        <div class="row">
            <?php
            $teams = array();
            //Todo modifier la récupération des équipes en utilisant la nouvelle fonction getTeamPoints
            for ($i = 0; $i < 2; $i++) {
                if ($i == 0) {
                    $queryTeam = $db->prepare('SELECT *, SUM(jury_vote_points) AS sum FROM challenge_jury_votes
                                    LEFT JOIN teams ON team_id=jury_vote_team
                                    LEFT JOIN challenges ON challenge_id = jury_vote_challenge
                                    WHERE challenge_start > :start AND challenge_end < :end
                                    GROUP BY jury_vote_team
                                    ORDER BY SUM(jury_vote_points)');
                    $queryTeam->bindValue(':start', getSchoolYear(getCurrentYear())['start'], PDO::PARAM_INT);
                    $queryTeam->bindValue(':end', getSchoolYear(getCurrentYear())['end'], PDO::PARAM_INT);
                }
                else {
                    $queryTeam=$db->prepare('select * from teams order by team_creation desc');
                }

                $queryTeam->execute();

                while ($dataTeam = $queryTeam->fetchObject()) {
                    if (!in_array($dataTeam->team_id, $teams)) {
                        echo '
                <div class="col-md-4">
                    <div class="team-card text-center">';
                        if (file_exists('../assets/img/public/teams/' . url_slug($dataTeam->team_shortname) . '.png'))
                            echo '<img src="../assets/img/public/teams/' . url_slug($dataTeam->team_shortname) . '.png" class="logo"/>&nbsp;&nbsp;';
                        else
                            echo '<img src="../assets/img/public/default_team.png" class="logo"/>&nbsp;&nbsp;';
                        echo '<div class="content">
                            <h3 class="name"><a href="team/' . $dataTeam->team_shortname . '">' . $dataTeam->team_name . '</a></h3>';
                        $queryMembers = $db->prepare('SELECT * FROM team_subscriptions
                                  LEFT JOIN users ON subscription_user=users.user_id
                                  WHERE subscription_team=:team AND subscription_status=1');
                        $queryMembers->bindValue(':team', $dataTeam->team_id, PDO::PARAM_INT);
                        $queryMembers->execute();
                        if ($queryMembers->rowCount() < $config['teams']['min_members'] || $queryMembers->rowCount() > $config['teams']['max_members'])
                            $class = "danger";
                        else $class = "success";

                        if ($queryMembers->rowCount() > 1)
                            $member = 'membres';
                        else
                            $member = 'membre';

                        echo '<div class="members">
                            <span class="label label-' . $class . '"
                            data-toggle="tooltip"
                                               data-placement="bottom"
                                               data-html="true"
                                               title="';
                        while ($dataMembers = $queryMembers->fetchObject())
                            echo $dataMembers->user_firstname . " " . $dataMembers->user_lastname . "<br />";
                        echo '"">' . $queryMembers->rowCount() . ' ' . $member . '</span></div>
                            <p class="description">' . $dataTeam->team_description . '</p>';
                        if (!getUserTeam(getInformation()) && isMember(getInformation(), getCurrentYear())) {
                            if (!hasApplied(getInformation(), $dataTeam->team_id))
                                echo '
                            <div class="team-footer">
                                <button class="btn btn-flat btn-ld" onclick="joinTeam(' . $dataTeam->team_id . ')">Postuler</button>
                            </div>';
                        }
                        echo '</div></div></div>';
                        array_push($teams, $dataTeam->team_id);
                    }
                }
            }
            ?>
        </div>
    </div>
    <?php
}
include('footer.php');
?>