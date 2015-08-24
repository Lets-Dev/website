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
                //Modification du message slack pour plus de clarté sur les challenges
                slack($_POST['subject'][$i], true, getInformation("firstname") . " " . getInformation("lastname") . " vient d'ajouter un challenge se déroulant entre le ". date_fr("j M Y", false, $data->challenge_debut) . " et le " . date_fr("j M Y", false, $data->challenge_fin) . ".", $data->set_name, "green");
            }
        }
        break;

    // Modifier un challenge
    case 'edit':
        // On vérifie que les utilisateurs ont la permission
        if (!checkPrivileges(getInformation(), 'desk_president') && !checkPrivileges(getInformation(), 'desk_challenges')) {
            $return['status'] = 'error';
            array_push($return['messages'], 'Vous n\'avez pas la permission.');
        }

        // On vérifie que tous les champs sont remplis
        if (empty($_POST['start']) || empty($_POST['subjects']) || empty($_POST['end']) || empty($_POST['id'])) {
            $return['status'] = 'error';
            array_push($return['messages'], 'Veuillez saisir tous les champs.');
        }

        // On vérifie que la fin est bien après le début
        if (strtotime($_POST['start']) > strtotime($_POST['end']) || strtotime($_POST['subjects']) > strtotime($_POST['end']) || strtotime($_POST['start']) > strtotime($_POST['subjects'])) {
            $return['status'] = 'error';
            array_push($return['messages'], 'Le début du challenge doit être avant la fin.');
        }

        //On vérifie que le challenge n'est pas déja terminé
        $query = $db -> prepare("select * from challenges WHERE challenge_id=:id");
        $query->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
        $query->execute();
        $data = $query->fetchObject();

        if (time() > $data->challenge_end)
        {
            $return['status'] = 'error';
            array_push($return['messages'], 'Le challenge est terminé. Il ne peut plus être modifié.');
        }


        //On modifie les valeurs
        if ($return['status'] == 'success') {
            $query = $db->prepare("UPDATE challenges
                                   SET challenge_start=:start, challenge_end=:end, challenge_subjects=:subjects, challenge_language=:language, challenge_subject=:subject, challenge_jury1=:jury1, challenge_jury2=:jury2, challenge_ergonomy_jury=:ergonomy
                                   WHERE challenge_id=:id");
            $query->bindValue(':start', strtotime($_POST['start']), PDO::PARAM_INT);
            $query->bindValue(':end', strtotime($_POST['end']), PDO::PARAM_INT);
            $query->bindValue(':subjects', strtotime($_POST['subjects']), PDO::PARAM_INT);
            $query->bindValue(':subject', $_POST['subject'], PDO::PARAM_STR);
            $query->bindValue(':language', $_POST['language_set'], PDO::PARAM_INT);
            $query->bindValue(':jury1', $_POST['jury'][1], PDO::PARAM_INT);
            $query->bindValue(':jury2', $_POST['jury'][2], PDO::PARAM_INT);
            $query->bindValue(':ergonomy', $_POST['ergonomy'], PDO::PARAM_INT);
            $query->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
            $query->execute();
            $query->closeCursor();
            $query = $db -> prepare("select * from language_sets where set_id=:id");
            $query->bindValue(":id", $_POST['language_set'], PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetchObject();
            slack($_POST['subject'], true, getInformation("firstname") . " " . getInformation("lastname") . " vient de modifier un challenge se déroulant entre le ". date_fr("j M Y", false, $data->challenge_debut) . " et le " . date_fr("j M Y", false, $data->challenge_fin) . ".", $data->set_name, "blue");

        }
        break;

    // Rejoindre une équipe
    case 'join':
        break;

    case 'delete':
        // On vérifie que les utilisateurs ont la permission
        if (!checkPrivileges(getInformation(), 'desk_president') && !checkPrivileges(getInformation(), 'desk_challenges'))
        {
            $return['status'] = 'error';
            array_push($return['messages'], 'Vous n\'avez pas la permission.');
        }

        //On vérifie que le challenge ne soit pas déja en cours
        $query = $db -> prepare("select * from challenges WHERE challenge_id=:id");
        $query->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
        $query->execute();
        $data = $query->fetchObject();

        if (time() > $data->challenge_start)
        {
            $return['status'] = 'error';
            array_push($return['messages'], 'Le challenge a déjà commencé, il ne peut pas être supprimé.');
        }

        //On supprime
        if ($return['status'] == 'success')
        {
            $query = $db->prepare("DELETE FROM challenges WHERE challenge_id=:id");
            $query->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
            $query->execute();
            array_push($return['messages'], 'Le challenge a bien été supprimé.');
            slack($data->challenge_subject, true, getInformation("firstname") . " " . getInformation("lastname") . " vient de modifier un challenge se déroulant entre le ". date_fr("j M Y", false, $data->challenge_debut) . " et le " . date_fr("j M Y", false, $data->challenge_fin) . ".", $data->set_name, "red");
        }

        break;
}

echo json_encode(array_to_utf8($return));