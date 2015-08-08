<?php
include('../includes/autoload.php');
include('header.php');
include('navbar.php');
include('sidebar.php');

switch ($_GET['action']) {
    case 'create':
        if (!checkPrivileges(getInformation(), 'desk_president') && !checkPrivileges(getInformation(), 'desk_challenges'))
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
                                        $query = $db->prepare('SELECT * FROM users ORDER BY user_lastname');
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
                                            $query = $db->prepare('SELECT * FROM users ORDER BY user_lastname');
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
                                            $query = $db->prepare('SELECT * FROM users ORDER BY user_lastname');
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
        <?php
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
                        <span class="label label-success"><?php echo date_fr("j M Y", $data->challenge_start); ?></span>
                    </div>
                        <div class="col-md-4 text-center">
                        <?php
                        if ($data->challenge_subjects < time())
                            echo '<span class="label label-success">'.date_fr("j M Y", $data->challenge_subjects).'</span>';
                        else
                            echo '<span class="label label-warning">'.date_fr("j M Y", $data->challenge_subjects).'</span>';
                        ?>
                        </div>
                        <div class="col-md-4 text-center">
                        <?php
                            echo '<span class="label label-danger">'.date_fr("j M Y", $data->challenge_end).'</span>';
                        ?>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                <?php
                    if ($data->challenge_subjects <= time()) {
                        $subject = new Parsedown();
                        echo "<h4 class='text-center'>Sujet</h4>";
                        echo $subject->text($data->challenge_subject);

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
                    <div class="text-center">
                        <img src="http://gravatar.com/avatar/<?php echo md5(getInformation('email', $data->challenge_jury1)) ?>?s=50&d=<?php echo urlencode($config['users']['default_avatar'])?>" class="img-circle" data-toggle="tooltip" title="Jury Développement">&nbsp;&nbsp;
                        <img src="http://gravatar.com/avatar/<?php echo md5(getInformation('email', $data->challenge_ergonomy_jury)) ?>?s=50&d=<?php echo urlencode($config['users']['default_avatar'])?>" class="img-circle" data-toggle="tooltip" title="Jury Ergonomie">&nbsp;&nbsp;
                        <img src="http://gravatar.com/avatar/<?php echo md5(getInformation('email', $data->challenge_jury2)) ?>?s=50&d=<?php echo urlencode($config['users']['default_avatar'])?>" class="img-circle" data-toggle="tooltip" title="Jury Développement">
                    </div>
                    <div class="box-footer text-center">
                        <button class="btn btn-flat btn-ld">S'inscrire</button>
                    </div>
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
}
include('footer.php');
?>