<?php
include('../includes/autoload.php');
include('header.php');
include('navbar.php');
include('sidebar.php');

switch ($_GET['action']) {
    case 'create':
        if (!checkPrivileges(getInformation(), 'desk_president') && !checkPrivileges(getInformation(), 'desk_challenges') && !checkPrivileges(getInformation(), 'desk_jurys'))
            redirect("./challenges/current");
        else {
            ?>
            <div class="content-wrapper" onmouseover="changeTitle('Let\'s Dev ! - Ajouter un challenge')">
            <form id="add_challenge">
                <div class="row">
                    <div class="col-md-4">
                        <div class="box" style="margin-top: 50px;">
                            <div class="box-header">
                                <h3>Ajouter un challenge</h3>
                            </div>
                            <div class="box-body">
                                <input type="hidden" name="action" value="new"/>

                                <div class="form-group">
                                    <label for="start">Début du challenge</label>
                                    <input type="date" class="form-control" id="start"
                                           name="start" required>
                                </div>
                                <div class="form-group">
                                    <label for="subjects">Découverte des sujets</label>
                                    <input type="date" class="form-control" id="subjects"
                                           name="subjects" required>
                                </div>
                                <div class="form-group">
                                    <label for="end">Fin du challenge</label>
                                    <input type="date" class="form-control" id="end"
                                           name="end" required>
                                </div>
                                <div class="form-group">
                                    <label for="ergonomy">Jury Ergonomie</label>
                                    <select name="ergonomy" id="ergonomy" class="form-control">
                                        <?php
                                        $query = $db->prepare('SELECT * FROM users WHERE user_ban=0 ORDER BY user_lastname');
                                        $query->execute();
                                        while ($data = $query->fetchObject()) {
                                            echo '<option value="' . $data->user_id . '">' . $data->user_lastname . ' ' . $data->user_firstname . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>

                            </div>
                        </div>
                    </div>
                    <?php
                    for ($i = 0; $i < $config['challenges']['languages_per_challenge']; $i++) {
                        ?>
                        <div class="col-md-4">
                            <div class="box" style="margin-top: 50px;">
                                <div class="box-header">
                                    <h3>Set de langage #<?php echo $i + 1; ?></h3>
                                </div>
                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="language_set">Set de langage</label>
                                        <select name="language_set[<?php echo $i + 1; ?>]" id="language_set"
                                                class="form-control">
                                            <?php
                                            $query = $db->prepare('SELECT * FROM language_sets ORDER BY set_name');
                                            $query->execute();
                                            while ($data = $query->fetchObject()) {
                                                echo '<option value="' . $data->set_id . '">' . $data->set_name . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="jury1">Jury #1</label>
                                        <select name="jury[<?php echo $i + 1; ?>][1]" id="jury1"
                                                class="form-control">
                                            <?php
                                            $query = $db->prepare('SELECT * FROM users WHERE user_ban=0 ORDER BY user_lastname');
                                            $query->execute();
                                            while ($data = $query->fetchObject()) {
                                                echo '<option value="' . $data->user_id . '">' . $data->user_lastname . ' ' . $data->user_firstname . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="jury2">Jury #2</label>
                                        <select name="jury[<?php echo $i + 1; ?>][2]" id="jury2"
                                                class="form-control">
                                            <?php
                                            $query = $db->prepare('SELECT * FROM users WHERE user_ban=0 ORDER BY user_lastname');
                                            $query->execute();
                                            while ($data = $query->fetchObject()) {
                                                echo '<option value="' . $data->user_id . '">' . $data->user_lastname . ' ' . $data->user_firstname . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="subject">Sujet du challenge</label>
                                        <textarea data-provide="markdown" data-hidden-buttons="cmdImage"
                                                  name="subject[<?php echo $i + 1; ?>]" id="subject"
                                                  class="form-control"
                                                  placeholder="Saisissez le sujet en MarkDown..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <div class="row">
                    <div class="col-md-4 col-md-offset-4">
                        <button type="submit" class="btn btn-flat btn-ld btn-block">Ajouter le
                            challenge
                        </button>
                    </div>
                </div>
            </form>
            <link rel="stylesheet" type="text/css"
                  href="../assets/js/bootstrap-markdown/css/bootstrap-markdown.min.css"/>
            <script src="../assets/js/bootstrap-markdown/js/bootstrap-markdown.min.js"></script>
            <script src="../assets/js/markdown-js/markdown.js"></script>
            <script src="../assets/js/markdown-js/to-markdown.js"></script>
            <script src="../assets/js/jquery-hotkeys/hotkeys.min.js"></script>
            <script>
                $("#add_challenge").submit(function () {
                    $('.btn').attr('disabled', 'disabled');
                    var formData = new FormData($(this)[0]);
                    $.ajax({
                        type: "POST",
                        url: "../includes/queries/challenges.php",
                        data: formData,
                        success: function (data) {
                            console.log(data);
                            if (data.status === "success") {
                                window.location = "./challenges/manage";
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
        }
        break;
    case 'manage':
        ?>
        <div class="content-wrapper" onmouseover="changeTitle('Let\'s Dev ! - Gestion des langages')">
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header">
                            <h3>Gestion des challenges
                                <a class="btn btn-ld btn-flat pull-right" href="challenges/create">Ajouter un challenge</a>
                            </h3>
                        </div>
                        <div class="box-body">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Challenge</th>
                                    <th>Dates</th>
                                    <th>Remarques</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                //On récupère tous les challenges, du plus récent au plus ancien
                                $query = $db->prepare("SELECT * FROM challenges,language_sets WHERE challenge_language=set_id ORDER BY challenge_start DESC");
                                $query->execute();
                                while ($data = $query->fetchObject()) {
                                    echo '<tr>
                                        <td>';
                                        //On affiche le titre, les date de début, de milieu et de fin
                                    echo $data->set_name . '</td>
                                        <td>
                                            ' . date_fr("j F Y", false, $data->challenge_start) . ' <i class="fa fa-chevron-right"></i>
                                            ' . date_fr("j F Y", false, $data->challenge_subjects) . ' <i class="fa fa-chevron-right"></i>
                                            ' . date_fr("j F Y", false, $data->challenge_end) . '
                                        </td>
                                        <td>';
                                        if (empty($data->challenge_subject))
                                                                                echo '<span class="text-danger">Ce challenge n\'a pas encore de sujet</span>';
                                        echo '</td>
                                        <td class="text-right">';
                                        //On affiche le bouton d'édition et/ou de suppression si les conditions sont remplies
                                        if (time() < $data->challenge_end)
                                            echo '<a href="challenges/edit/' . $data->challenge_id . '" class="btn btn-flat btn-xs btn-warning">Modifier</a> ';
                                        if (time() < $data->challenge_start)
                                            echo '<button class="btn btn-flat btn-xs btn-danger" onclick="deleteChallenge(' . $data->challenge_id . ')">Supprimer</button>';
                                        if (time() > $data->challenge_end && time() < ($data->challenge_end + 60*60*24*15))
                                            echo '<a href="challenges/evaluate/' . $data->challenge_id . '" class="btn btn-flat btn-xs btn-success">Évaluer</a> ';
                                        echo '</td>
                                    </tr>';
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <link rel="stylesheet" type="text/css" href="../assets/js/datatables/dataTables.bootstrap.css" />
        <script src="../assets/js/datatables/jquery.dataTables.min.js"></script>
        <script src="../assets/js/datatables/dataTables.bootstrap.min.js"></script>

        <script>
            function deleteChallenge(id) {
                var button = $(event.target);
                $('.btn').attr('disabled', 'disabled');
                $.post('../includes/queries/challenges.php', {
                        action: "delete",
                        id: id
                    },
                    function (data) {
                        var i;
                        console.log(data);
                        if (data.status == "success") {
                            button.closest('tr').remove();
                            for (i = 0; i < data.messages.length; i++)
                                toastr["success"](data.messages[i])
                        }
                        else
                            for (i = 0; i < data.messages.length; i++)
                                toastr["error"](data.messages[i])
                        $('.btn').removeAttr('disabled');
                    })
            }
            $('.table').DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": true,
                "ordering": false,
                "info": false,
                "autoWidth": false
            })
        </script>
        <?php
        break;
    case 'edit' :
        //Insérer le formulaire d'édition de challenges ici
        if (!checkPrivileges(getInformation(), 'desk_president') && !checkPrivileges(getInformation(), 'desk_challenges'))
            redirect("./challenges/current");
        else {
            $query = $db -> prepare('SELECT * FROM challenges WHERE challenge_id=:id');
            $query ->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
            $query ->execute();
            if ($query->rowCount()==0)
                redirect("./challenges/manage");
            else {
                $data = $query -> fetchObject();
            ?>
            <div class="content-wrapper" onmouseover="changeTitle('Let\'s Dev ! - Modifier un challenge')">
            <form id="add_challenge">
                <div class="row">
                    <div class="col-md-4 col-md-offset-2">
                        <div class="box" style="margin-top: 50px;">
                            <div class="box-header">
                                <h3>Modifier un challenge</h3>
                            </div>
                            <div class="box-body">
                                <input type="hidden" name="action" value="edit"/>
                                <input type="hidden" name="id" value="<?php echo $_GET['id'] ?>"/>

                                <div class="form-group">
                                    <label for="start">Début du challenge</label>
                                    <input type="date" class="form-control" id="start"
                                           name="start" required value="<?php echo date("Y-m-d", $data->challenge_start); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="subjects">Découverte des sujets</label>
                                    <input type="date" class="form-control" id="subjects"
                                           name="subjects" required value="<?php echo date("Y-m-d", $data->challenge_subjects); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="end">Fin du challenge</label>
                                    <input type="date" class="form-control" id="end"
                                           name="end" required value="<?php echo date("Y-m-d", $data->challenge_end); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="ergonomy">Jury Ergonomie</label>
                                    <select name="ergonomy" id="ergonomy" class="form-control">
                                        <?php
                                        $query1 = $db->prepare('SELECT * FROM users ORDER BY user_lastname');
                                        $query1->execute();
                                        while ($data1 = $query1->fetchObject()) {
                                            if ($data->challenge_ergonomy_jury == $data1->user_id)
                                                echo '<option value="' . $data1->user_id . '" selected="selected">' . $data1->user_lastname . ' ' . $data1->user_firstname . '</option>';
                                            else
                                                echo '<option value="' . $data1->user_id . '">' . $data1->user_lastname . ' ' . $data1->user_firstname . '</option>';
                                        }
                                        $query1->closeCursor();
                                        ?>
                                    </select>
                                </div>

                            </div>
                        </div>
                    </div>
                        <div class="col-md-4">
                            <div class="box" style="margin-top: 50px;">
                                <div class="box-header">
                                    <h3>Set de langage</h3>
                                </div>
                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="language_set">Set de langage</label>
                                        <select name="language_set" id="language_set"
                                                class="form-control">
                                            <?php
                                            $query1 = $db->prepare('SELECT * FROM language_sets ORDER BY set_name');
                                            $query1->execute();
                                            while ($data1 = $query1->fetchObject()) {
                                                if ($data->challenge_language == $data1->set_id)
                                                    echo '<option value="' . $data1->set_id . '" selected="selected">' . $data1->set_name . '</option>';
                                                else
                                                    echo '<option value="' . $data1->set_id . '">' . $data1->set_name . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="jury1">Jury #1</label>
                                        <select name="jury[1]" id="jury1"
                                                class="form-control">
                                            <?php
                                                $query1 = $db->prepare('SELECT * FROM users ORDER BY user_lastname');
                                                $query1->execute();
                                                while ($data1 = $query1->fetchObject()) {
                                                    if ($data->challenge_jury1 == $data1->user_id)
                                                        echo '<option value="' . $data1->user_id . '" selected="selected">' . $data1->user_lastname . ' ' . $data1->user_firstname . '</option>';
                                                    else
                                                        echo '<option value="' . $data1->user_id . '">' . $data1->user_lastname . ' ' . $data1->user_firstname . '</option>';
                                                }
                                                $query1->closeCursor();
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="jury2">Jury #2</label>
                                        <select name="jury[2]" id="jury2"
                                                class="form-control">
                                            <?php
                                                $query1 = $db->prepare('SELECT * FROM users ORDER BY user_lastname');
                                                $query1->execute();
                                                while ($data1 = $query1->fetchObject()) {
                                                    if ($data->challenge_jury2 == $data1->user_id)
                                                        echo '<option value="' . $data1->user_id . '" selected="selected">' . $data1->user_lastname . ' ' . $data1->user_firstname . '</option>';
                                                    else
                                                        echo '<option value="' . $data1->user_id . '">' . $data1->user_lastname . ' ' . $data1->user_firstname . '</option>';
                                                }
                                                $query1->closeCursor();
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="subject">Sujet du challenge</label>
                                        <textarea data-provide="markdown" data-hidden-buttons="cmdImage"
                                                  name="subject" id="subject"
                                                  class="form-control"
                                                  placeholder="Saisissez le sujet en MarkDown..."><?php echo $data->challenge_subject ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
                <div class="row">
                    <div class="col-md-4 col-md-offset-4">
                        <button type="submit" class="btn btn-flat btn-ld btn-block">Modifier le
                            challenge
                        </button>
                    </div>
                </div>
            </form>
            <link rel="stylesheet" type="text/css"
                  href="../assets/js/bootstrap-markdown/css/bootstrap-markdown.min.css"/>
            <script src="../assets/js/bootstrap-markdown/js/bootstrap-markdown.min.js"></script>
            <script src="../assets/js/markdown-js/markdown.js"></script>
            <script src="../assets/js/markdown-js/to-markdown.js"></script>
            <script src="../assets/js/jquery-hotkeys/hotkeys.min.js"></script>
            <script>
                $("#add_challenge").submit(function () {
                    $('.btn').attr('disabled', 'disabled');
                    var formData = new FormData($(this)[0]);
                    $.ajax({
                        type: "POST",
                        url: "../includes/queries/challenges.php",
                        data: formData,
                        success: function (data) {
                            console.log(data);
                            if (data.status === "success") {
                                window.location = "./challenges/manage";
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
            }
        }
        break;
    case 'current':
    ?>

            <div class="content-wrapper" onmouseover="changeTitle('Let\'s Dev ! - Challenges en cours')">
        <?php
        $query = $db->prepare("SELECT * FROM challenges
                                     LEFT JOIN language_sets ON set_id = challenge_language
                                     WHERE challenge_start < :time
                                     AND challenge_end > :time");
        $query->bindValue(':time', time(), PDO::PARAM_INT);
        $query->execute();
        $row = true;
        $loop = array();
        for ($i = $config['challenges']['languages_per_challenge']-1; $i >= 0; $i--) {
            array_push($loop, $i);
        }
        while ($data = $query->fetchObject()) {
            if ($row)
                echo '<div class="row">
                        <div class="col-md-offset-1 col-md-4">';
            else
                echo '<div class="col-md-offset-2 col-md-4">';
            ?>
            <div class="box" style="margin-top: 50px;">
                    <div class="row">
                        <div class="col-md-12">
                                <?php
                                    $progress = (time() - $data->challenge_start)/($data->challenge_end- $data->challenge_start)*100;
                                    $color = gradient("00a65", "dd4b39", 100);
                                    $int_progress = intval($progress);
                                    $color = "#".$color[$int_progress];
                                ?>
                            <div class="progress progress-xxs" style="margin-bottom: 0">
                                <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100" style="background-color:<?php echo $color; ?>;width: <?php echo $progress; ?>%">
                                    <span class="sr-only"><?php echo $progress; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <div class="box-header">
                    <h3>
                        Challenge #<?php
                            echo ($data->challenge_id/$config['challenges']['languages_per_challenge'] + $loop[0]*(1/$config['challenges']['languages_per_challenge']))." - ".$data->set_name;
                        ?>
                    </h3>
                    <div class="row">
                    <div class="col-md-4 text-center">
                        <span class="label label-success" data-toggle="tooltip" title="Découverte du set de langages" ><?php echo date_fr("j M Y", false, $data->challenge_start); ?></span>
                    </div>
                        <div class="col-md-4 text-center">
                        <?php
                        if ($data->challenge_subjects < time())
                            echo '<span class="label label-success" data-toggle="tooltip" title="Découverte du sujet" >'.date_fr("j M Y", false, $data->challenge_subjects).'</span>';
                        else
                            echo '<span class="label label-warning" data-toggle="tooltip" title="Découverte du sujet">'.date_fr("j M Y", false, $data->challenge_subjects).'</span>';
                        ?>
                        </div>
                        <div class="col-md-4 text-center">
                        <?php
                            echo '<span class="label label-danger" data-toggle="tooltip" title="Date limite de retour des projets pour évaluation">'.date_fr("j M Y", false, $data->challenge_end).'</span>';
                        ?>
                        </div>
                    </div>
                </div>
                <div class="box-body text-center">
            <?php
                if ($data->challenge_subjects <= time()) {
                    $subject = new Parsedown();
                    $subject = $subject->text($data->challenge_subject);
                    echo "<button class='btn btn-flat btn-ld' type='button' onclick='displayModal(\"Sujet du challenge $data->set_name\", $(\"#challenge$data->challenge_id\").html())'>Afficher le sujet</button>";
                    echo "<div hidden id='challenge$data->challenge_id'>$subject</div>";

                }
            ?>
                    <h4 class="text-center">Langages</h4>
                    <div class="text-center">
                    <?php
                        $query2 = $db->prepare('SELECT * FROM language_set_association
                                                LEFT JOIN languages ON language_id=association_language
                                                WHERE  association_set = :set
                                                ORDER BY language_name');
                        $query2->bindValue(':set', $data->set_id);
                        $query2->execute();
                        $i = false;
                        while ($data2 = $query2->fetchObject()) {
                            echo '<a href="' . $data2->language_documentation . '" target="_blank">';
                            if (file_exists('../assets/img/private/languages/' . url_slug($data2->language_name) . '.png'))
                                echo '<img src="../assets/img/private/languages/' . url_slug($data2->language_name) . '.png" height="50px" data-toggle="tooltip" title="' . $data2->language_name . '"/>';
                            else {
                                echo $data2->language_name;
                            }
                            $i = true;
                            echo '&nbsp;&nbsp;</a>';
                        }
                        $query2->closeCursor();
                    ?>
                    </div>
                    <h4 class="text-center">Jury du challenge</h4>
                    <div class="row text-center">
                        <div class="col-md-4">
                            <img src="http://gravatar.com/avatar/<?php echo md5(getInformation('email', $data->challenge_jury1)) ?>?s=50&d=<?php echo urlencode($config['users']['default_avatar'])?>" class="img-circle" data-toggle="tooltip" title="Jury Développement" height="50px">
                            <p>
                                <?php echo getInformation('firstname', $data->challenge_jury1).'<br />'.getInformation('lastname', $data->challenge_jury1) ?>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <img src="http://gravatar.com/avatar/<?php echo md5(getInformation('email', $data->challenge_ergonomy_jury)) ?>?s=50&d=<?php echo urlencode($config['users']['default_avatar'])?>" class="img-circle" data-toggle="tooltip" title="Jury Ergonomie" height="50px">
                            <p>
                                <?php echo getInformation('firstname', $data->challenge_ergonomy_jury).'<br />'.getInformation('lastname', $data->challenge_ergonomy_jury) ?>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <img src="http://gravatar.com/avatar/<?php echo md5(getInformation('email', $data->challenge_jury2)) ?>?s=50&d=<?php echo urlencode($config['users']['default_avatar'])?>" class="img-circle" data-toggle="tooltip" title="Jury Développement" height="50px">
                            <p>
                                <?php echo getInformation('firstname', $data->challenge_jury2).'<br />'.getInformation('lastname', $data->challenge_jury2) ?>
                            </p>
                        </div>
                    </div>
                    <h4 class="text-center">Équipes inscrites</h4>
                    <div class="row row-centered">
                    <?php
                        $query3 = $db->prepare("SELECT * FROM challenge_subscriptions
                                                LEFT JOIN teams ON subscription_team=team_id
                                                WHERE subscription_challenge=:challenge");
                        $query3->bindValue(':challenge', $data->challenge_id, PDO::PARAM_INT);
                        $query3->execute();
                        while($data3 = $query3->fetchObject()) {
                    ?>
                        <div class="col-md-3 col-centered text-center">
                            <a href="./team/<?php echo $data3->team_shortname; ?>">
                                <img src="../assets/img/public/<?php echo getTeamLogo($data3->team_id) ?>" class="img-responsive" data-toggle="tooltip" title="<?php echo htmlspecialchars($data3->team_name); ?>" height="50px">
                            </a>
                        </div>
                    <?php
                        }
                    ?>
                    </div>
                    <?php
                        if (subscribedToChallenge(getUserTeam(getInformation()), $data->challenge_id))
                            echo "<p class='text-center'><b>Votre équipe est inscrite à ce challenge</b></p>";
                        else if (isTeamOwner(getInformation()) && time() >= $data -> challenge_start && time() < $data->challenge_subjects && canParticipateToChallenge(getUserTeam(getInformation()), $data->challenge_id)) { ?>
                    <div class="text-center">
                       <button class="btn btn-flat btn-ld" onclick="subscribeToChallenge(<?php echo $data->challenge_id ?>)">Inscrire mon équipe à ce challenge</button>
                    </div>
                        <?php }

                     ?>
                </div>
            </div>
            </div>

                <?php
                if (!$row)
                    echo '</div>';
                $row = !$row;
                array_push($loop, array_shift($loop));
        }
        ?>
        <script>
            function subscribeToChallenge(challenge_id) {
                var button = $(event.target);
                button.attr("disabled", "disabled");
                $.post('../includes/queries/challenges', {
                        action: 'subscribe',
                        challenge: challenge_id
                    }, function (data) {
                        var i;
                        for (i = 0; i < data.messages.length; i++)
                            toastr[data.status](data.messages[i])
                        if (data.status == "success") {
                            event.target.className = '';
                            button.addClass('btn btn-flat btn-success').html('<i class="fa fa-check"></i> Inscription effectuée')
                        }
                        button.attr('disabled', 'disabled')
                    }
                )
            }
        </script>
        <?php
        break;
    case 'all':
    ?>

        <div class="content-wrapper" onmouseover="changeTitle('Let\'s Dev ! - Challenges en cours')">
    <?php
    $query = $db->prepare("SELECT * FROM challenges
                                 LEFT JOIN language_sets ON set_id = challenge_language
                                 WHERE challenge_end < :time
                                 ORDER BY challenge_start DESC");
     $query->bindValue(':time',time(), PDO::PARAM_INT);
    $query->execute();
    $row = true;
    $loop = array();
    for ($i = $config['challenges']['languages_per_challenge']-1; $i >= 0; $i--) {
        array_push($loop, $i);
    }
    while ($data = $query->fetchObject()) {
        if ($row)
            echo '<div class="row">
                    <div class="col-md-offset-1 col-md-4">';
        else
            echo '<div class="col-md-offset-2 col-md-4">';
        ?>
        <div class="box" style="margin-top: 50px;">
            <div class="box-header">
                <h3>
                    Challenge #<?php
                        echo ($data->challenge_id/$config['challenges']['languages_per_challenge'] + $loop[0]*(1/$config['challenges']['languages_per_challenge']))." - ".$data->set_name;
                    ?>
                </h3>
                <div class="row">
                <div class="col-md-4 text-center">
                    <span class="label label-success" data-toggle="tooltip" title="Découverte du set de langages"><?php echo date_fr("j M Y", false, $data->challenge_start); ?></span>
                </div>
                    <div class="col-md-4 text-center">
                    <?php
                    if ($data->challenge_subjects < time())
                        echo '<span class="label label-success" data-toggle="tooltip" title="Découverte du sujet" >'.date_fr("j M Y", false, $data->challenge_subjects).'</span>';
                    else
                        echo '<span class="label label-warning" data-toggle="tooltip" title="Découverte du sujet">'.date_fr("j M Y", false, $data->challenge_subjects).'</span>';
                    ?>
                    </div>
                    <div class="col-md-4 text-center">
                    <?php
                        echo '<span class="label label-danger" data-toggle="tooltip" title="Date limite de retour des projets pour évaluation">'.date_fr("j M Y", false, $data->challenge_end).'</span>';
                    ?>
                    </div>
                </div>
            </div>
                <div class="box-body text-center">
            <?php
                if ($data->challenge_subjects <= time()) {
                    $subject = new Parsedown();
                    $subject = $subject->text($data->challenge_subject);
                    echo "<button class='btn btn-flat btn-ld' type='button' onclick='displayModal(\"Sujet du challenge $data->set_name\", $(\"#challenge$data->challenge_id\").html())'>Afficher le sujet</button>";
                    echo "<div hidden id='challenge$data->challenge_id'>$subject</div>";

                }
            ?>
                <h4 class="text-center">Langages</h4>
                <div class="text-center">
                <?php
                    $query2 = $db->prepare('SELECT * FROM language_set_association
                                            LEFT JOIN languages ON language_id=association_language
                                            WHERE  association_set = :set
                                            ORDER BY language_name');
                    $query2->bindValue(':set', $data->set_id);
                    $query2->execute();
                    $i = false;
                    while ($data2 = $query2->fetchObject()) {
                        echo '<a href="' . $data2->language_documentation . '" target="_blank">';
                        if (file_exists('../assets/img/private/languages/' . url_slug($data2->language_name) . '.png'))
                            echo '<img src="../assets/img/private/languages/' . url_slug($data2->language_name) . '.png" height="50px" data-toggle="tooltip" title="' . $data2->language_name . '"/>';
                        else {
                            echo $data2->language_name;
                        }
                        $i = true;
                        echo '&nbsp;&nbsp;</a>';
                    }
                    $query2->closeCursor();
                ?>
                </div>
                <h4 class="text-center">Jury du challenge</h4>
                <div class="row text-center">
                    <div class="col-md-4">
                        <img src="http://gravatar.com/avatar/<?php echo md5(getInformation('email', $data->challenge_jury1)) ?>?s=50&d=<?php echo urlencode($config['users']['default_avatar'])?>" class="img-circle" data-toggle="tooltip" title="Jury Développement" height="50px">
                        <p>
                            <?php echo getInformation('firstname', $data->challenge_jury1).'<br />'.getInformation('lastname', $data->challenge_jury1) ?>
                        </p>
                    </div>
                    <div class="col-md-4">
                        <img src="http://gravatar.com/avatar/<?php echo md5(getInformation('email', $data->challenge_ergonomy_jury)) ?>?s=50&d=<?php echo urlencode($config['users']['default_avatar'])?>" class="img-circle" data-toggle="tooltip" title="Jury Ergonomie" height="50px">
                        <p>
                            <?php echo getInformation('firstname', $data->challenge_ergonomy_jury).'<br />'.getInformation('lastname', $data->challenge_ergonomy_jury) ?>
                        </p>
                    </div>
                    <div class="col-md-4">
                        <img src="http://gravatar.com/avatar/<?php echo md5(getInformation('email', $data->challenge_jury2)) ?>?s=50&d=<?php echo urlencode($config['users']['default_avatar'])?>" class="img-circle" data-toggle="tooltip" title="Jury Développement" height="50px">
                        <p>
                            <?php echo getInformation('firstname', $data->challenge_jury2).'<br />'.getInformation('lastname', $data->challenge_jury2) ?>
                        </p>
                    </div>
                </div>
                    <h4 class="text-center">Équipes inscrites</h4>
                    <div class="row row-centered">
                    <?php
                        $query3 = $db->prepare("SELECT * FROM challenge_subscriptions
                                                LEFT JOIN teams ON subscription_team=team_id
                                                WHERE subscription_challenge=:challenge");
                        $query3->bindValue(':challenge', $data->challenge_id, PDO::PARAM_INT);
                        $query3->execute();
                        while($data3 = $query3->fetchObject()) {
                    ?>
                        <div class="col-md-3 col-centered text-center">
                            <a href="./team/<?php echo $data3->team_shortname; ?>">
                                <img src="../assets/img/public/<?php echo getTeamLogo($data3->team_id) ?>" class="img-responsive" data-toggle="tooltip" title="<?php echo htmlspecialchars($data3->team_name); ?>" height="50px">
                            </a>
                            <div class="badge" data-toggle="tooltip" data-html="true" title="Nombre de points acquis par <?php echo htmlspecialchars($data3->team_name) ?>">
                            <?php echo getTeamChallengePoints($data3->team_id, $data->challenge_id); ?>
                            </div>
                        </div>
                    <?php
                        }
                    ?>
                    </div>
                <?php
                if ((getInformation() == $data->challenge_jury1 || getInformation() == $data->challenge_jury2 || getInformation() == $data->challenge_ergonomy_jury || checkPrivileges(getInformation())) && (time() <= $data->challenge_end+60*60*24*$config['challenges']['days_to_rate'] && time() >= $data->challenge_end))
                        echo '
                    <hr />
                    <div class="text-center">
                       <a class="btn btn-flat btn-success" href="./challenges/evaluate/'.$data->challenge_id.'">Évaluer le challenge</a>
                    </div>'
                    ?>
            </div>
        </div>
        </div>

            <?php
            if (!$row)
                echo '</div>';
            $row = !$row;
            array_push($loop, array_shift($loop));
    }
        break;
    case 'evaluate':
            $query = $db -> prepare('SELECT * FROM challenges WHERE challenge_id=:id');
            $query ->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
            $query ->execute();
            if ($query->rowCount()==0)
                redirect("./challenges/current");
            else {
                $data = $query -> fetchObject();
                if ((getInformation() != $data->challenge_jury1 && getInformation() != $data->challenge_jury2 && getInformation() != $data->challenge_ergonomy_jury && !checkPrivileges(getInformation())) || (time() > $data->challenge_end+60*60*24*$config['challenges']['days_to_rate'] || time() < $data->challenge_end)) {
                    redirect("./challenges/current");
                }
                else {
                    ?>
                    <div class="content-wrapper" onmouseover="changeTitle('Let\'s Dev ! - Évaluation d\'un challenge')">
                    <div class="row">
                    <div class="col-md-4 col-md-offset-4">
                    <div class="box" style="margin-top: 50px;">
                    <div class="box-header">
                        <h3>Évaluation des équipes</h3>
                    </div>
                    <div class="box-body">
                    <p>
                        Nous vous rappelons que l'évaluation consiste à répartir <b><?php echo $config['challenges']['points_per_challenge'] ?> points</b>
                        en fonction de la qualité de chaque projet qui vous a été rendu.
                    </p>
                    <h4>Nombre de points restants à donner: <span id="points_nb"><?php echo $config['challenges']['points_per_challenge'] ?></span></h4>
                    <form id="rate_challenge">
                    <input type="hidden" name="action" value="rate"/>
                    <input type="hidden" name="challenge" value="<?php echo $_GET['id'] ?>"/>
                    <?php
                    $query1 = $db->prepare("SELECT * FROM challenge_subscriptions
                                            LEFT JOIN teams ON team_id=subscription_team
                                            WHERE subscription_challenge = :challenge");
                    $query1->bindValue(':challenge', $_GET['id'], PDO::PARAM_INT);
                    $query1->execute();
                    while ($data1 = $query1->fetchObject()) {
                        $query2 = $db->prepare("SELECT * FROM challenge_jury_votes WHERE jury_vote_challenge=:challenge AND jury_vote_team=:team");
                        $query2->bindValue(':challenge', $_GET['id'], PDO::PARAM_INT);
                        $query2->bindValue(':team', $data1->team_id, PDO::PARAM_INT);
                        $query2->execute();
                        if ($query2->rowCount() > 0)
                        {
                        $data2 = $query2->fetchObject();
                        $total = $data2->jury_vote_points;
                        }
                        else $total = 0;
                        $query2->closeCursor();

                        echo '<div class="form-group">
                                <label>'.$data1->team_name.'</label>
                                <input type="hidden" name="team[]" value="'.$data1->team_id.'" />
                                <input type="number" name="points[]" class="form-control" value="'.$total.'" />
                            </div>';
                    }
                    ?>
                    <button class="btn btn-flat btn-block btn-ld" disabled>Évaluer le challenge</button>
                    </form>
                    </div>
                    </div>
                    </div>
                    </div>
                    </div>
                    <script>
                        function total() {
                            var total = 0, max=<?php echo $config['challenges']['points_per_challenge'] ?>;
                            $('#rate_challenge .form-control').each(function() {
                                if (!$(this).val())
                                    value = 0
                                else
                                    value = parseInt($(this).val())
                                total += value
                            })
                            $('#points_nb').text(max - total)

                            if (total != max)
                                $('#rate_challenge button').attr('disabled', 'disabled')
                            else
                                $('#rate_challenge button').removeAttr('disabled')
                        }
                        $('#rate_challenge .form-control').on('input', function() {
                            total()
                        })
                        $("#rate_challenge").submit(function () {
                            $('.btn').attr('disabled', 'disabled');
                            $.ajax({
                                type: "POST",
                                url: "../includes/queries/challenges.php",
                                data: $("#rate_challenge").serialize(),
                                success: function (data) {
                                    console.log(data);
                                    if (data.status === "success") {
                                        window.location = "./challenges/all";
                                    }
                                    else {
                                        var i;
                                        for (i = 0; i < data.messages.length; i++)
                                            toastr["error"](data.messages[i])
                                    }
                                    $('.btn').removeAttr('disabled');
                                }
                            });
                            return false;
                        });
                    </script>
                    <?php
                }
            }
        break;
}
include('footer.php');
?>