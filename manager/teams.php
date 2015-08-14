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
    }
} else {
    ?>
    <div class="content-wrapper" onmouseover="changeTitle('Let\'s Dev ! - Parcourir les équipes')">
        <div class="row">
            <?php
            $query = $db->prepare('SELECT * FROM team_points
                              LEFT JOIN teams ON point_team=teams.team_id
                              LEFT JOIN users ON team_owner=users.user_id
                              WHERE point_year = :year ORDER BY point_nb');
            $query->bindValue(':year', getCurrentYear(), PDO::PARAM_INT);
            $query->execute();
            while ($data = $query->fetchObject()) {
                echo '
                <div class="col-md-4">
                    <div class="team-card text-center">';
                    if (file_exists('../assets/img/public/teams/' . url_slug($data->team_shortname) . '.png'))
                        echo '<img src="../assets/img/public/teams/' . url_slug($data->team_shortname) . '.png" class="logo"/>&nbsp;&nbsp;';
                else
                    echo '<img src="../assets/img/public/default_team.png" class="logo"/>&nbsp;&nbsp;';
                        echo '<div class="content">
                            <h3 class="name"><a href="team/' . $data->team_shortname . '">' . $data->team_name . '</a></h3>';
                $query2 = $db->prepare('SELECT * FROM team_joins
                                  LEFT JOIN users ON join_user=users.user_id
                                  WHERE join_team=:team AND join_status=1');
                $query2->bindValue(':team', $data->team_id, PDO::PARAM_INT);
                $query2->execute();
                $coef = $query2->rowCount() / $config['teams']['max_members'];
                if ($query2->rowCount() < $config['teams']['max_members'] || $coef >= 1)
                    $class = "danger";
                else $class = "success";

                if ($query2->rowCount() > 1)
                    $member = 'membres';
                else
                    $member = 'membre';

                echo '<div class="members">
                            <span class="label label-' . $class . '"
                            data-toggle="tooltip"
                                               data-placement="bottom"
                                               data-html="true"
                                               title="';
                while ($data2 = $query2->fetchObject())
                    echo $data2->user_firstname . " " . $data2->user_lastname . "<br />";
                echo '"">' . $query2->rowCount() . ' ' . $member . '</span></div>
                            <p class="description">' . $data->team_description . '</p>';
                    if(!hasTeam(getInformation()) && isMember(getInformation(), getCurrentYear())) {
                        if (!hasApplied(getInformation(), $data->team_id))
                        echo '
                            <div class="team-footer">
                                <button class="btn btn-flat btn-ld" onclick="joinTeam('.$data->team_id.')">Postuler</button>
                            </div>';
                    }
                        echo '</div>
                    </div>
                </div>';
            }
            for ($i = 0; $i < 21; $i++) {
                ?>
            <?php } ?>
        </div>
    </div>
    <?php
}
include('footer.php');
?>