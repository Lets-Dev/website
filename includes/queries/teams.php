<?php
include('../autoload.php');
header('Content-Type: application/json');

$return = array('status' => 'success', 'messages' => array());

switch ($_POST['action']) {

    // Créer une équipe
    case 'new':
        // On vérifie que le membre est bien un membre
        if (!isMember(getInformation(), getCurrentYear()) && !checkPrivileges(getInformation())) {
            $return['status'] = 'error';
            array_push($return['messages'], 'Vous devez être membre cotisant pour vous inscrire à une équipe.');
        }

        // On vérifie que l'utilisateur n'est pas déjà inscrit dans une équipe
        $query = $db->prepare('SELECT * FROM team_subscriptions WHERE subscription_user = :id AND subscription_status = 1');
        $query->bindValue(':id', getInformation(), PDO::PARAM_INT);
        $query->execute();
        if ($query->rowCount() > $config['users']['max_teams'] - 1) {
            $return['status'] = 'error';
            array_push($return['messages'], 'Vous ne pouvez vous être inscrit que dans une seule équipe.');
        }
        $query->closeCursor();

        // On vérifie que les champs ont bien été remplis
        if (empty($_POST['name']) || empty($_POST['shortname']) || empty($_POST['description'])) {
            $return['status'] = 'error';
            array_push($return['messages'], 'Veuillez saisir tous les champs.');
        }

        // On vérifie le shortname
        if (!checkShortName($_POST['shortname'])) {
            $return['status'] = 'error';
            array_push($return['messages'], 'L\'alias de votre nom d\'équipe est invalide. Nous vous proposons: ' . url_slug($_POST['name']));
        }

        // On vérifie que le nom d'équipe n'est pas déjà pris
        if (!checkEntryAvailability($_POST['name'], 'team_name', 'teams')) {
            $return['status'] = 'error';
            array_push($return['messages'], 'Le nom d\'équipe choisi n\'est pas disponible');
        }
        if (!checkEntryAvailability($_POST['shortname'], 'team_shortname', 'teams') || $_POST['shortname'] == 'myteam') {
            $return['status'] = 'error';
            array_push($return['messages'], 'L\'alias de nom d\'équipe choisi n\'est pas disponible');
        }

        if ($return['status'] == 'success') {
            $mysql_key = "default";

            if (is_uploaded_file($_FILES['logo']['tmp_name'])) {
                include('../libraries/SimpleImage.php');
                $img = new abeautifulsite\SimpleImage($_FILES['logo']['tmp_name']);
                $img->best_fit(500, 500)->save('../../assets/img/public/teams/' . $_POST['shortname'] . '.png');
            }
            // TODO: Créer une base de données pour l'équipe

            // On crée l'équipe
            $query = $db->prepare('INSERT INTO teams (team_name, team_shortname, team_description, team_mysql_key, team_owner, team_status, team_creation)
                                VALUES (:team_name, :team_shortname, :team_description, :team_mysql_key, :team_owner, :team_status, :team_creation)');
            $query->bindValue(':team_name', $_POST['name'], PDO::PARAM_STR);
            $query->bindValue(':team_shortname', $_POST['shortname'], PDO::PARAM_STR);
            $query->bindValue(':team_description', $_POST['description'], PDO::PARAM_STR);
            $query->bindValue(':team_mysql_key', $mysql_key, PDO::PARAM_STR);
            $query->bindValue(':team_owner', getInformation(), PDO::PARAM_INT);
            $query->bindValue(':team_status', 1, PDO::PARAM_INT);
            $query->bindValue(':team_creation', time(), PDO::PARAM_INT);
            $query->execute();
            array_push($return['messages'], 'Votre équipe a bien été créée.');
            $team_id = $db->lastInsertId('team_id');
            $query->closeCursor();

            // On fait rejoindre l'utilisateur à l'équipe
            $query = $db->prepare('INSERT INTO team_subscriptions (subscription_user, subscription_team, subscription_time, subscription_leave, subscription_status)
                                VALUES (:user, :team, :time, 0, 1)');
            $query->bindValue(':user', getInformation(), PDO::PARAM_INT);
            $query->bindValue(':team', $team_id, PDO::PARAM_INT);
            $query->bindValue(':time', time(), PDO::PARAM_INT);
            $query->execute();
            array_push($return['messages'], 'Vous avez bien rejoint l\'équipe.');
            $query->closeCursor();

            slack($_POST['description'], true, "Une nouvelle équipe vient d'être créée.", $_POST['name']);
        }
        break;

    // Vérification, suggestion du shortname
    case 'shortname':
        switch ($_POST['step']) {

            // Suggestion du shortname
            case 'suggest':
                $return['messages'] = url_slug($_POST['name']);
                break;

            // Vérification du shortname
            case 'check':
                if (!checkShortName($_POST['shortname'])) {
                    $return['status'] = 'error';
                    array_push($return['messages'], 'L\'alias de votre nom d\'équipe est invalide. Nous vous proposons: ' . url_slug($_POST['name']));
                } else
                    array_push($return['messages'], 'L\'alias de votre nom d\'équipe est valide.');
                break;
        }
        break;

    // Modifier une équipe
    case 'edit':

        // On vérifie que l'utilisateur est bien le propriétaire de l'équipe
        if (!isTeamOwner(getInformation(), $_POST['id'])) {
            $return['status'] = 'error';
            array_push($return['messages'], 'Vous n\'avez pas la permission de modifier cette équipe');
        }

        if ($return['status'] == 'success')
            switch ($_POST['step']) {
                case 'form':
                    $query = $db->prepare("select * from teams where team_id = :id");
                    $query->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
                    $query->execute();

                    $data = $query->fetchObject();
                    $return['display'] = "<form id='editTeam'>
                                              <input type='hidden' name='action' value='edit' />
                                              <input type='hidden' name='step' value='query' />
                                              <input type='hidden' name='id' value='$data->team_id' />
                                              <input type='hidden' name='shortname' value='$data->team_shortname' />
                                              <img src='../assets/img/public/".getTeamLogo($data->team_id)."' class='logo' style='margin-top:-50px;height:100px'/>
                                              <div class='form-group'>
                                                  <label>Nom de l'équipe</label>
                                                  <input type='text' class='form-control' value='$data->team_name' disabled/>
                                              </div>
                                              <div class='form-group'>
                                                  <label>Modifier le logo</label>
                                                  <input type='file' name='logo'>
                                              </div>
                                              <div class='form-group'>
                                                  <label>Description de l'équipe</label>
                                                  <textarea name='description' class='form-control'>$data->team_description</textarea>
                                              </div>
                                              <button type='submit' class='btn btn-ld btn-flat'>Valider</button>
                                          </form>
                                          <script>
                                                $('#editTeam').submit(function () {
                                                    $('.btn').attr('disabled', 'disabled');
                                                    var formData = new FormData($(this)[0]);
                                                    $.ajax({
                                                        type: 'POST',
                                                        url: '../includes/queries/teams.php',
                                                        data: formData,
                                                        success: function (data) {
                                                        console.log(data);
                                                        if (data.status === 'success') {
                                                            window.location = './team/myteam';
                                                        }
                                                        else {
                                                            var i;
                                                            for (i = 0; i < data.messages.length; i++)
                                                                    toastr['error'](data.messages[i]);
                                                            }
                                                        $('.btn').removeAttr('disabled');
                                                    },
                                                        cache: false,
                                                        contentType: false,
                                                        processData: false
                                                    });
                                                    return false;
                                                });
                                        </script>";
                    break;
                case 'query':
                    // On vérifie que les champs ont bien été remplis
                    if (empty($_POST['description'])) {
                        $return['status'] = 'error';
                        array_push($return['messages'], 'Veuillez saisir tous les champs.');
                    }

                    if ($return['status'] == 'success') {
                        if (is_uploaded_file($_FILES['logo']['tmp_name'])) {
                            include('../libraries/SimpleImage.php');
                            $img = new abeautifulsite\SimpleImage($_FILES['logo']['tmp_name']);
                            $img->best_fit(500, 500)->save('../../assets/img/public/teams/' . $_POST['shortname'] . '.png');
                        }

                        $query = $db->prepare('UPDATE teams SET team_description=:description WHERE team_id=:id');
                        $query->bindValue(':description', $_POST['description'], PDO::PARAM_STR);
                        $query->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
                        $query->execute();
                        array_push($return['messages'], "L'équipe a bien été mise à jour.");
                    }
                    break;
            }
        break;

    // Désactiver une équipe
    case 'disable':
        // On vérifie que l'utilisateur est bien le propriétaire de l'équipe
        $query = $db->prepare('SELECT * FROM teams WHERE team_id = :id AND team_owner = :owner');
        $query->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
        $query->bindValue(':owner', getInformation(), PDO::PARAM_INT);
        $query->execute();
        if ($query->rowCount() == 0) {
            $return['status'] = 'error';
            array_push($return['messages'], 'Vous n\'avez pas la permission de modifier cette équipe');
        }
        $query->closeCursor();

        if ($return['status'] == 'success') {
            $query = $db->prepare('UPDATE teams SET team_status=0 WHERE team_id=:id');
            $query->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
            $query->execute();
            array_push($return['messages'], "L'équipe a bien été désactivée.");
        }
        break;

    // Rejoindre une équipe
    case 'join':
        // On vérifie que le membre est bien un membre
        if (!isMember(getInformation(), getCurrentYear())) {
            $return['status'] = 'error';
            array_push($return['messages'], 'Vous devez être membre cotisant pour vous inscrire à une équipe.');
        }

        // On vérifie que l'utilisateur n'est pas déjà inscrit dans une équipe
        $query = $db->prepare('SELECT * FROM team_subscriptions WHERE subscription_user = :id AND subscription_status = 1');
        $query->bindValue(':id', getInformation(), PDO::PARAM_INT);
        $query->execute();
        if ($query->rowCount() > $config['users']['max_teams'] - 1) {
            $return['status'] = 'error';
            array_push($return['messages'], 'Vous ne pouvez vous être inscrit que dans une seule équipe.');
        }
        $query->closeCursor();

        // On vérifie que l'équipe n'est pas complète
        $query = $db->prepare('SELECT * FROM team_subscriptions WHERE subscription_team = :team AND subscription_status = 1');
        $query->bindValue(':team', $_POST['team'], PDO::PARAM_INT);
        $query->execute();
        if ($query->rowCount() >= $config['teams']['max_members']) {
            $return['status'] = 'error';
            array_push($return['messages'], 'L\'équipe choisie est déjà complète.');
        }
        $query->closeCursor();

        // Si tout est ok
        if ($return['status'] == 'success') {
            $query = $db->prepare('INSERT INTO team_subscriptions (subscription_user, subscription_team, subscription_time, subscription_leave, subscription_status)
                              VALUES (:user, :team, :time, 0, 0)');
            $query->bindValue(':user', getInformation(), PDO::PARAM_INT);
            $query->bindValue(':team', $_POST['team'], PDO::PARAM_INT);
            $query->bindValue(':time', time(), PDO::PARAM_INT);
            $query->execute();
            array_push($return['messages'], 'Votre demande d\'adhésion à l\'équipe a bien été effectuée.');
            $query->closeCursor();

            $query = $db->prepare('SELECT * FROM teams WHERE team_id = :team');
            $query->bindValue(':team', $_POST['team'], PDO::PARAM_INT);
            $query->execute();
            if ($data = $query->fetchObject()) {
                $notification = new Notifications($data->team_owner);
                $notification->create("Un utilisateur a demandé à rejoindre votre équipe.");
            }
        }

        break;

    case 'join_answer':
        if ($_POST['answer'] == 'yes')
            $status = 1;
        else if ($_POST['answer'] == 'no')
            $status = 2;
        else
            $status = 0;

        $query = $db->prepare("SELECT * FROM team_subscriptions WHERE subscription_id=:id");
        $query->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
        $query->execute();
        if ($query->rowCount() == 0) {
            $return['status'] = 'error';
            array_push($return['messages'], 'Cette inscription n\'existe pas.');
        } else {
            $data = $query->fetchObject();

            // On vérifie que l'utilisateur est bien le propriétaire de l'équipe
            $query1 = $db->prepare('SELECT * FROM teams WHERE team_id = :id AND team_owner = :owner');
            $query1->bindValue(':id', $data->subscription_team, PDO::PARAM_INT);
            $query1->bindValue(':owner', getInformation(), PDO::PARAM_INT);
            $query1->execute();
            if ($query1->rowCount() == 0) {
                $return['status'] = 'error';
                array_push($return['messages'], 'Vous n\'avez pas la permission de modifier cette équipe');
            }
            $query1->closeCursor();

            if ($return['status'] == 'success' && ($status == 1 || $status == 2)) {
                $query1 = $db->prepare('UPDATE team_subscriptions SET subscription_status=:status, subscription_time = :time WHERE subscription_id=:id');
                $query1->bindValue(':status', $status, PDO::PARAM_INT);
                $query1->bindValue(':time', time(), PDO::PARAM_INT);
                $query1->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
                $query1->execute();

                if ($status == 1) {
                    $query1 = $db->prepare('SELECT * FROM teams WHERE team_id = :team');
                    $query1->bindValue(':team', $data->subscription_team, PDO::PARAM_INT);
                    $query1->execute();
                    if ($data1 = $query1->fetchObject()) {
                        $notification = new Notifications($data->subscription_user);
                        $notification->create("Vous avez été accepté dans l'équipe \"" . $data1->team_name . "\".");
                    }


                    $query2 = $db->prepare('UPDATE team_subscriptions SET subscription_status=2, subscription_time = :time WHERE subscription_user=:user AND subscription_status=0');
                    $query2->bindValue(':time', time(), PDO::PARAM_INT);
                    $query2->bindValue(':user', $data->subscription_user, PDO::PARAM_INT);
                    $query2->execute();
                    $notification = new Notifications($data->subscription_user);
                    $notification->create("Nous avons décliné automatiquement toutes les autres potentielles demandes d'adhésion que vous avez envoyées aux autres équipes.");

                    array_push($return['messages'], 'La demande d\'adhésion a bien été acceptée.');
                } else {
                    $query1 = $db->prepare('SELECT * FROM teams WHERE team_id = :team');
                    $query1->bindValue(':team', $data->subscription_team, PDO::PARAM_INT);
                    $query1->execute();
                    if ($data1 = $query1->fetchObject()) {
                        $notification = new Notifications($data->subscription_user);
                        $notification->create("Votre demande d'adhésion à \"" . $data1->team_name . "\" a été rejetée.");
                    }
                    array_push($return['messages'], 'La demande d\'adhésion a bien été rejetée');
                }
            }
        }
        break;

    case 'kick':
        // On vérifie que l'utilisateur est bien le propriétaire de l'équipe
        $query = $db->prepare('SELECT * FROM teams WHERE team_id = :id AND team_owner = :owner');
        $query->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
        $query->bindValue(':owner', getInformation(), PDO::PARAM_INT);
        $query->execute();
        if ($query->rowCount() == 0) {
            $return['status'] = 'error';
            array_push($return['messages'], 'Vous n\'avez pas la permission de modifier cette équipe');
        }
        $query->closeCursor();

        if ($return['status'] == 'success') {
            $query = $db->prepare('UPDATE team_subscriptions SET subscription_status=:status, subscription_leave = :time WHERE subscription_team=:team AND subscription_user=:user');
            $query->bindValue(':status', 3, PDO::PARAM_INT);
            $query->bindValue(':time', time(), PDO::PARAM_INT);
            $query->bindValue(':team', $_POST['team'], PDO::PARAM_INT);
            $query->bindValue(':user', $_POST['user'], PDO::PARAM_INT);
            $query->execute();
            array_push($return['messages'], 'L\'utilisateur a bien été exclu de votre équipe.');
            $query->closeCursor();

            $query = $db->prepare('SELECT * FROM teams WHERE team_id = :team');
            $query->bindValue(':team', $_POST['team'], PDO::PARAM_INT);
            $query->execute();
            if ($data = $query->fetchObject()) {
                $notification = new Notifications($_POST['user']);
                $notification->create("Vous avez été exclu de l'équipe \"" . $data->team_name . "\".");
            }
        }
        break;
}

echo json_encode(array_to_utf8($return));