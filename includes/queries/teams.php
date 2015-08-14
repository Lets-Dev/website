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
        $query = $db->prepare('SELECT * FROM team_joins WHERE join_user = :id AND join_status = 1');
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
            $query->closeCursor();

            // On fait rejoindre l'utilisateur à l'équipe
            $query = $db->prepare('INSERT INTO team_joins (join_user, join_team, join_time, join_leave, join_status)
                                VALUES (:user, :team, :time, 0, 1)');
            $query->bindValue(':user', getInformation(), PDO::PARAM_INT);
            $query->bindValue(':team', $db->lastInsertId('team_id'), PDO::PARAM_INT);
            $query->bindValue(':time', time(), PDO::PARAM_INT);
            $query->execute();
            array_push($return['messages'], 'Vous avez bien rejoint l\'équipe.');
            $query->closeCursor();

            // On ajoute l'équipe aux équipes de l'année
            $query = $db->prepare("INSERT INTO team_points (point_team, point_nb, point_year)
                                     VALUES (:team, 0, :year)");
            $query->bindValue(':team', $db->lastInsertId('team_id'), PDO::PARAM_INT);
            $query->bindValue(':year', getCurrentYear(), PDO::PARAM_INT);
            $query->execute();
            array_push($return['messages'], 'Votre équipe est dans la liste des équipes de l\'année.');
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
        $query = $db->prepare('SELECT * FROM teams WHERE team_id = :id AND team_owner = :owner');
        $query->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
        $query->bindValue(':owner', getInformation(), PDO::PARAM_INT);
        $query->execute();
        if ($query->rowCount() == 0) {
            $return['status'] = 'error';
            array_push($return['messages'], 'Vous n\'avez pas la permission de modifier cette équipe');
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
        if (!checkEntryAvailability($_POST['shortname'], 'team_shortname', 'teams')) {
            $return['status'] = 'error';
            array_push($return['messages'], 'L\'alias de nom d\'équipe choisi n\'est pas disponible');
        }

        if ($return['status'] == 'success') {
            $query = $db->prepare('UPDATE teams SET team_name = :name, team_shortname=:shortname, team_description=:description WHERE team_id=:id');
            $query->bindValue(':name', $_POST['name'], PDO::PARAM_STR);
            $query->bindValue(':shortname', $_POST['shortname'], PDO::PARAM_STR);
            $query->bindValue(':description', $_POST['description'], PDO::PARAM_STR);
            $query->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
            $query->execute();
            array_push($return['messages'], "L'équipe a bien été mise à jour.");
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
        $query = $db->prepare('SELECT * FROM team_joins WHERE join_user = :id AND join_status = 1');
        $query->bindValue(':id', getInformation(), PDO::PARAM_INT);
        $query->execute();
        if ($query->rowCount() > $config['users']['max_teams'] - 1) {
            $return['status'] = 'error';
            array_push($return['messages'], 'Vous ne pouvez vous être inscrit que dans une seule équipe.');
        }
        $query->closeCursor();

        // On vérifie que l'équipe n'est pas complète
        $query = $db->prepare('SELECT * FROM team_joins WHERE join_team = :team AND join_status = 1');
        $query->bindValue(':team', $_POST['team'], PDO::PARAM_INT);
        $query->execute();
        if ($query->rowCount() >= $config['teams']['max_members']) {
            $return['status'] = 'error';
            array_push($return['messages'], 'L\'équipe choisie est déjà complète.');
        }
        $query->closeCursor();

        // Si tout est ok
        if ($return['status'] == 'success') {
            $query = $db->prepare('INSERT INTO team_joins (join_user, join_team, join_time, join_leave, join_status)
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

        if ($return['status'] == 'success' && $status == 1) {
            $query = $db->prepare('UPDATE team_joins SET join_status=:status, join_time = :time WHERE join_team=:team AND join_user=:user');
            $query->bindValue(':status', $status, PDO::PARAM_INT);
            $query->bindValue(':time', time(), PDO::PARAM_INT);
            $query->bindValue(':team', $_POST['team'], PDO::PARAM_INT);
            $query->bindValue(':user', $_POST['user'], PDO::PARAM_INT);
            $query->execute();
            array_push($return['messages'], 'L\'utilisateur a bien été ajouté à votre équipe.');

            $query = $db->prepare('SELECT * FROM teams WHERE team_id = :team');
            $query->bindValue(':team', $_POST['team'], PDO::PARAM_INT);
            $query->execute();
            if ($data = $query->fetchObject()) {
                $notification = new Notifications($_POST['user']);
                $notification->create("Vous avez été accepté dans l'équipe \"" . $data->team_name . "\".");
                $notification->destruct();
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
            $query = $db->prepare('UPDATE team_joins SET join_status=:status, join_leave = :time WHERE join_team=:team AND join_user=:user');
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