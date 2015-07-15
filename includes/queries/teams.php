<?php
include('../config.php');
include('../credentials.php');
include('../functions/security.php');
include('../functions/encoding.php');
header('Content-Type: application/json');

$return = array('status' => 'success', 'messages' => array());

switch ($_POST['action']) {

    // Créer une équipe
    case 'new':
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

        // On vérifie que les champs ont bien été remplis
        if (!isset($_POST['name']) || !isset($_POST['shortname']) || !isset($_POST['description'])) {
            $return['status'] = 'error';
            array_push($return['messages'], 'Veuillez saisir tous les champs.');
        }

        if ($return['status'] == 'success') {
            $mysql_key = "default";
            // TODO: Créer une base de données pour l'équipe
            // TODO: Upload du logo dans /assets/img/teams/shortname.{jpg/png}

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
                break;
        }
        break;

    // Modifier une équipe
    case 'edit':
        break;

    // Désactiver une équipe
    case 'disable':
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
        }
        break;
}

echo json_encode(array_to_utf8($return));