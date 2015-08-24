<?php
include('../autoload.php');
$return = array('status' => 'success', 'messages' => array());

switch ($_POST['action']) {
    // Ajouter un bureau
    case 'add_desk':
        $desk = array(
            'president' => $_POST['president'],
            'secretary' => $_POST['secretary'],
            'treasurer' => $_POST['treasurer'],
            'communication' => $_POST['communication'],
            'jurys' => $_POST['jurys'],
            'challenges' => $_POST['challenges']
        );

        // On vérifie que l'utilisateur est bien le président
        if (!checkPrivileges(getInformation(), 'desk_president')) {
            $return['status'] = "error";
            array_push($return['messages'], "Vous n'avez pas la permission d'ajouter un bureau.");
        }

        // On vérifie que chaque poste est pourvu par quelqu'un de différent
        if (ArrayHasDuplicates($desk)) {
            $return['status'] = "error";
            array_push($return['messages'], "Chaque poste doit être pourvu par un membre différent.");
        }

        // On vérifie que le bureau n'existe pas encore
        $query = $db->prepare("SELECT count(*) AS nb FROM desks WHERE desk_year=:year");
        $query->bindValue(':year', getCurrentYear() + 1, PDO::PARAM_INT);
        $query->execute();
        $data = $query->fetchObject();
        if ($data->nb > 0) {
            $return['status'] = "error";
            array_push($return['messages'], "Le bureau a déjà été saisi et ne peut être modifié.");
        }
        $query->closeCursor();

        if ($return['status'] == 'success') {
            $query = $db->prepare("INSERT INTO desks (desk_year, desk_president, desk_secretary, desk_treasurer, desk_communication, desk_jurys, desk_challenges)
                                     VALUES (:year, :president, :secretary, :treasurer, :communication, :jurys, :challenges)");
            $query->bindValue(':year', getCurrentYear() + 1, PDO::PARAM_INT);
            $query->bindValue(':president', $_POST['president'], PDO::PARAM_INT);
            $query->bindValue(':secretary', $_POST['secretary'], PDO::PARAM_INT);
            $query->bindValue(':treasurer', $_POST['treasurer'], PDO::PARAM_INT);
            $query->bindValue(':communication', $_POST['communication'], PDO::PARAM_INT);
            $query->bindValue(':jurys', $_POST['jurys'], PDO::PARAM_INT);
            $query->bindValue(':challenges', $_POST['challenges'], PDO::PARAM_INT);
            $query->execute();
            slack("Président: " . getInformation('firstname', $_POST['president']) . " " . getInformation('lastname', $_POST['president']) . "\n
Secrétaire: " . getInformation('firstname', $_POST['secretary']) . " " . getInformation('lastname', $_POST['secretary']) . "\n
Trésorier: " . getInformation('firstname', $_POST['treasurer']) . " " . getInformation('lastname', $_POST['treasurer']) . "\n
Communication: " . getInformation('firstname', $_POST['communication']) . " " . getInformation('lastname', $_POST['communication']) . "\n
Jurys: " . getInformation('firstname', $_POST['jurys']) . " " . getInformation('lastname', $_POST['jurys']) . "\n
Challenges: " . getInformation('firstname', $_POST['challenges']) . " " . getInformation('lastname', $_POST['challenges']) . "\n", true, getInformation('firstname') . " " . getInformation('lastname') . " vient d'ajouter le bureau de l'année scolaire " . (getCurrentYear() + 1) . "-" . (getCurrentYear() + 2), "Bureau " . (getCurrentYear() + 1) . "-" . (getCurrentYear() + 2));
            array_push($return['messages'], "Le bureau a bien été ajouté.");
        }
        break;
    case 'edit_desk':

        $desk = array(
            'president' => $_POST['president'],
            'secretary' => $_POST['secretary'],
            'treasurer' => $_POST['treasurer'],
            'communication' => $_POST['communication'],
            'jurys' => $_POST['jurys'],
            'challenges' => $_POST['challenges']
        );

        // On vérifie que l'utilisateur est bien le président
        if (!checkPrivileges(getInformation(), 'desk_president')) {
            $return['status'] = "error";
            array_push($return['messages'], "Vous n'avez pas la permission d'ajouter un bureau.");
        }

        // On vérifie que chaque poste est pourvu par quelqu'un de différent
        if (ArrayHasDuplicates($desk)) {
            $return['status'] = "error";
            array_push($return['messages'], "Chaque poste doit être pourvu par un membre différent.");
        }

        // On vérifie que le bureau n'existe pas encore
        if ($_POST['desk'] < getCurrentYear()) {
            $return['status'] = "error";
            array_push($return['messages'], "Ce bureau ne peut plus être modifié.");
        }
        $query->closeCursor();

        if ($return['status'] == 'success') {
            $query = $db->prepare("UPDATE desks SET desk_president=:president, desk_secretary=:secretary, desk_treasurer=:treasurer, desk_communication=:communication, desk_jurys=:jurys, desk_challenges=:challenges where desk_year=:year");
            $query->bindValue(':year', $_POST['desk'], PDO::PARAM_INT);
            $query->bindValue(':president', $_POST['president'], PDO::PARAM_INT);
            $query->bindValue(':secretary', $_POST['secretary'], PDO::PARAM_INT);
            $query->bindValue(':treasurer', $_POST['treasurer'], PDO::PARAM_INT);
            $query->bindValue(':communication', $_POST['communication'], PDO::PARAM_INT);
            $query->bindValue(':jurys', $_POST['jurys'], PDO::PARAM_INT);
            $query->bindValue(':challenges', $_POST['challenges'], PDO::PARAM_INT);
            $query->execute();
            slack("*Président:* " . getInformation('firstname', $_POST['president']) . " " . getInformation('lastname', $_POST['president']) . "\n
*Secrétaire:* " . getInformation('firstname', $_POST['secretary']) . " " . getInformation('lastname', $_POST['secretary']) . "\n
*Trésorier:* " . getInformation('firstname', $_POST['treasurer']) . " " . getInformation('lastname', $_POST['treasurer']) . "\n
*Communication:* " . getInformation('firstname', $_POST['communication']) . " " . getInformation('lastname', $_POST['communication']) . "\n
*Jurys:* " . getInformation('firstname', $_POST['jurys']) . " " . getInformation('lastname', $_POST['jurys']) . "\n
*Challenges:* " . getInformation('firstname', $_POST['challenges']) . " " . getInformation('lastname', $_POST['challenges']) . "\n", true, getInformation('firstname') . " " . getInformation('lastname') . " vient d'éditer le bureau de l'année scolaire " . $_POST['desk'] . "-" . ($_POST['desk'] + 1), "Bureau " . $_POST['desk'] . "-" . ($_POST['desk'] + 1));
            array_push($return['messages'], "Le bureau a bien été modifié.");
        }
        break;
}

echo json_encode(array_to_utf8($return));