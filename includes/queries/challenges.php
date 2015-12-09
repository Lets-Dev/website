<?php
include('../autoload.php');
header('Content-Type: application/json');

$return = array('status' => 'success', 'messages' => array());

switch ($_POST['action']) {

    // Créer un challenge
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
                $query = $db->prepare("SELECT * FROM language_sets WHERE set_id=:id");
                $query->bindValue(":id", $_POST['language_set'][$i], PDO::PARAM_INT);
                $query->execute();
                $data = $query->fetchObject();

            }
            // TODO: Ajouter des précisions sur le challenge pour la notification Slack
            slack(getInformation("firstname") . " " . getInformation("lastname") . " vient de créer un challenge.");
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
            $query = $db->prepare("UPDATE challenges SET
                                challenge_start = :start,
                                challenge_end = :end,
                                challenge_subjects = :subjects,
                                challenge_subject = :subject,
                                challenge_language = :language,
                                challenge_jury1 = :jury1,
                                challenge_jury2 = :jury2,
                                challenge_ergonomy_jury = :ergonomy
                                WHERE challenge_id = :id");
            $query->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
            $query->bindValue(':start', strtotime($_POST['start']), PDO::PARAM_INT);
            $query->bindValue(':end', strtotime($_POST['end']), PDO::PARAM_INT);
            $query->bindValue(':subjects', strtotime($_POST['subjects']), PDO::PARAM_INT);
            $query->bindValue(':subject', $_POST['subject'], PDO::PARAM_STR);
            $query->bindValue(':language', $_POST['language_set'], PDO::PARAM_INT);
            $query->bindValue(':jury1', $_POST['jury'][1], PDO::PARAM_INT);
            $query->bindValue(':jury2', $_POST['jury'][2], PDO::PARAM_INT);
            $query->bindValue(':ergonomy', $_POST['ergonomy'], PDO::PARAM_INT);
            $query->execute();
            $query->closeCursor();
            $query = $db->prepare("SELECT * FROM language_sets WHERE set_id=:id");
            $query->bindValue(":id", $_POST['language_set'], PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetchObject();


            // TODO: Ajouter des précisions sur le challenge pour la notification Slack
            slack(getInformation("firstname") . " " . getInformation("lastname") . " vient d'éditer un challenge.");
        }
        break;

    case 'delete':
        // On vérifie que les utilisateurs ont la permission
        if (!checkPrivileges(getInformation(), 'desk_president') && !checkPrivileges(getInformation(), 'desk_challenges')) {
            $return['status'] = 'error';
            array_push($return['messages'], 'Vous n\'avez pas la permission.');
        }

        //On vérifie que le challenge ne soit pas déja en cours
        $query = $db->prepare("SELECT * FROM challenges WHERE challenge_id=:id");
        $query->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
        $query->execute();
        $data = $query->fetchObject();

        if (time() > $data->challenge_start) {
            $return['status'] = 'error';
            array_push($return['messages'], 'Le challenge a déjà commencé, il ne peut pas être supprimé.');
        }

        //On supprime
        if ($return['status'] == 'success') {
            $query = $db->prepare("DELETE FROM challenges WHERE challenge_id=:id");
            $query->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
            $query->execute();
            array_push($return['messages'], 'Le challenge a bien été supprimé.');
            // TODO: Ajouter des précisions sur le challenge pour la notification Slack
            slack(getInformation("firstname") . " " . getInformation("lastname") . " vient de supprimer un challenge.");
        }

        break;

    case 'subscribe':
        if (!isTeamOwner(getInformation())) {
            $return['status'] = 'error';
            array_push($return['messages'], 'Vous n\'êtes pas propriétaire d\'une équipe.');
        }

        $query = $db->prepare("SELECT * FROM challenges WHERE challenge_id = :challenge");
        $query->bindValue(':challenge', $_POST['challenge'], PDO::PARAM_INT);
        $query->execute();
        if ($query->rowCount() == 0) {
            $return['status'] = 'error';
            array_push($return['messages'], 'Le challenge n\'existe pas.');
        } else {
            $data = $query->fetchObject();
            if (time() < $data->challenge_start && time() > $data->challenge_subjects) {
                $return['status'] = 'error';
                array_push($return['messages'], 'Nous ne sommes pas dans la période d\'inscriptions.');
            }
        }
        $query->closeCursor();

        if (!canParticipateToChallenge(getUserTeam(getInformation()), $_POST['challenge'])) {
            $return['status'] = 'error';
            array_push($return['messages'], 'Votre équipe n\'est pas éligible pour s\'inscrire à un challenge.');
        }

        $query = $db->prepare("SELECT * FROM challenge_subscriptions WHERE subscription_challenge = :challenge and subscription_team=:team");
        $query->bindValue(':challenge', $_POST['challenge'], PDO::PARAM_INT);
        $query->bindValue(':team', getUserTeam(getInformation()), PDO::PARAM_INT);
        $query->execute();
        if ($query->rowCount() == 0) {
            $return['status'] = 'error';
            array_push($return['messages'], 'Vous êtes déjà inscrit à ce challenge.');
        }
        $query->closeCursor();

        if ($return['status'] == 'success') {
            if (getTeamPoints(getUserTeam(getInformation()), getCurrentYear()) == 0)
            {
                $query = $db->prepare("INSERT INTO team_points (point_team, point_nb, point_year) VALUES (:team, :nb, :year)");
                $query->bindValue(':team', getUserTeam(getInformation()), PDO::PARAM_INT);
                $query->bindValue(':nb', getLowestTeamPoint(getCurrentYear())*0.8, PDO::PARAM_INT);
                $query->bindValue(':year', getCurrentYear(), PDO::PARAM_INT);

                $query->execute();

                $query->closeCursor();
            }

            $query = $db->prepare("INSERT INTO challenge_subscriptions (subscription_team, subscription_challenge, subscription_time)
                              VALUES (:team, :challenge, :time)");
            $query->bindValue(':team', getUserTeam(getInformation()), PDO::PARAM_INT);
            $query->bindValue(':challenge', $_POST['challenge'], PDO::PARAM_INT);
            $query->bindValue(':time', time(), PDO::PARAM_INT);
            $query->execute();
            array_push($return['messages'], 'Votre équipe est bien inscrite au challenge.');
        }
        break;

    case 'rate':
        $query = $db->prepare('SELECT * FROM challenges WHERE challenge_id=:id');
        $query->bindValue(':id', $_POST['challenge'], PDO::PARAM_INT);
        $query->execute();
        if ($query->rowCount() == 0) {
            $return['status'] = 'error';
            array_push($return['messages'], 'Le challenge n\'existe pas.');
        } else {
            $data = $query->fetchObject();
            if (getInformation() != $data->challenge_jury1 && getInformation() != $data->challenge_jury2 && getInformation() != $data->challenge_ergonomy_jury && !checkPrivileges(getInformation())) {
                $return['status'] = 'error';
                array_push($return['messages'], 'Vous n\'avez pas la permission d\'évaluer ce challenge.');
            }

            if (time() > $data->challenge_end+60*60*24*$config['challenges']['days_to_rate'] || time() < $data->challenge_end) {
                $return['status'] = 'error';
                array_push($return['messages'], 'Il n\'est pas l\'heure d\'évaluer ce challenge.');
            }

            foreach ($_POST['points'] as $id => $value)
                if (empty($value))
                    $value = 0;

            if (array_sum($_POST['points']) != $config['challenges']['points_per_challenge']) {
                $return['status'] = 'error';
                array_push($return['messages'], 'Le total de points est différent que celui indiqué.');
            }

            if ($return['status'] == 'success') {
                for ($i = 0; $i < count($_POST['team']); $i++) {
                    $query = $db->prepare("DELETE FROM challenge_jury_votes WHERE jury_vote_challenge=:challenge");
                    $query->bindValue(':challenge', $_POST['challenge'], PDO::PARAM_INT);
                    $query->execute();
                    $query->closeCursor();

                    $query = $db->prepare("INSERT INTO challenge_jury_votes
                                      (jury_vote_team, jury_vote_points, jury_vote_challenge)
                                      VALUES (:team, :points, :challenge)");
                    $query->bindValue(':team', $_POST['team'][$i], PDO::PARAM_INT);
                    $query->bindValue(':points', $_POST['points'][$i], PDO::PARAM_INT);
                    $query->bindValue(':challenge', $_POST['challenge'], PDO::PARAM_INT);
                    $query->execute();
                    $query->closeCursor();
                }
                array_push($return['messages'], 'Les points ont bien été ajoutés aux équipes.');
            }
        }
        break;
}

echo json_encode(array_to_utf8($return));