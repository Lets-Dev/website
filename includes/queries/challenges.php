<?php
include('../autoload.php');
header('Content-Type: application/json');

$return = array('status' => 'success', 'messages' => array());

switch ($_POST['action']) {

    // Créer une équipe
    case 'new':
        // On vérifie que les utilisateurs ont la permission
        if (!checkPrivileges(getInformation(), 'desk_president') && !checkPrivileges(getInformation(), 'desk_challenges')) {
            $return['status'] = 'error';
            array_push($return['messages'], 'Vous n\'avez pas la permission.');
        }

        // On vérifie que tous les champs sont remplis
        if (empty($_POST['start']) || empty($_POST['subjects']) || empty($_POST['end'])) {
            $return['status'] = 'error';
            array_push($return['messages'], 'Veuillez saisir tous les champs.');
        }

        // On vérifie que la fin est bien après le début
        if (strtotime($_POST['start']) > strtotime($_POST['end']) || strtotime($_POST['subjects']) > strtotime($_POST['end']) || strtotime($_POST['start']) > strtotime($_POST['subjects'])) {
            $return['status'] = 'error';
            array_push($return['messages'], 'Le début du challenge doit être avant la fin.');
        }

        if ($return['status'] == 'success') {
            for ($i = 1; $i <= $config['challenges']['languages_per_challenge']; $i++) {
                $query = $db->prepare("INSERT INTO challenges (challenge_start, challenge_end, challenge_subjects, challenge_subject, challenge_language, challenge_jury1, challenge_jury2, challenge_ergonomy_jury)
                                        VALUES (:start, :end, :subjects, :subject, :language, :jury1, :jury2, :ergonomy)");
                $query->bindValue(':start', strtotime($_POST['start']), PDO::PARAM_INT);
                $query->bindValue(':end', strtotime($_POST['end']), PDO::PARAM_INT);
                $query->bindValue(':subjects', strtotime($_POST['subjects']), PDO::PARAM_INT);
                $query->bindValue(':subject', $_POST['subject'][$i], PDO::PARAM_STR);
                $query->bindValue(':language', $_POST['language_set'][$i], PDO::PARAM_INT);
                $query->bindValue(':jury1', $_POST['jury'][$i][1], PDO::PARAM_INT);
                $query->bindValue(':jury2', $_POST['jury'][$i][2], PDO::PARAM_INT);
                $query->bindValue(':ergonomy', $_POST['ergonomy'], PDO::PARAM_INT);
                $query->execute();
                $query->closeCursor();
                $query = $db -> prepare("select * from language_sets where set_id=:id");
                $query->bindValue(":id", $_POST['language_set'][$i], PDO::PARAM_INT);
                $query->execute();
                $data = $query->fetchObject();
                slack($_POST['subject'][$i], true, getInformation("firstname") . " " . getInformation("lastname") . " vient d'ajouter un challenge.", $data->set_name, "green");
            }
        }
        break;

    // Modifier une équipe
    case 'edit':
        break;

    // Rejoindre une équipe
    case 'join':
        break;
}

echo json_encode(array_to_utf8($return));