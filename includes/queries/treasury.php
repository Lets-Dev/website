<?php
include('../autoload.php');
header('Content-Type: application/json');

$return = array('status' => 'success', 'messages' => array());

switch ($_POST['action']) {
    case 'add':
        if (!checkPrivileges(getInformation(), 'desk_president') && !checkPrivileges(getInformation(), 'desk_treasurer')) {
            $return['status'] = 'error';
            array_push($return['messages'], "Vous n'avez pas la permission de faire ceci.");
        }

        if (empty($_POST['time']) || (empty($_POST['designation']) && empty($_POST['cotisation'])) || empty($_POST['amount'])) {
            $return['status'] = 'error';
            array_push($return['messages'], "Veuillez saisir l'intégralité des champs.");
        }

        if ($return['status'] == 'success') {
            $time = strtotime($_POST['time']);

            if ($_POST['cotisation'] == "true") {
                $query = $db->prepare("INSERT INTO treasury (transaction_amount, transaction_designation, transaction_time, transaction_creation_time)
                                     VALUES (:amount, :designation, :time, :creation)");
                $query->bindValue(':amount', $_POST['amount'], PDO::PARAM_STR);
                $query->bindValue(':designation', "Cotisation de ".getInformation('firstname', $_POST['user'])." ".getInformation('lastname', $_POST['user']), PDO::PARAM_STR);
                $query->bindValue(':time', $time, PDO::PARAM_INT);
                $query->bindValue(':creation', time(), PDO::PARAM_INT);
                $query->execute();
                $query->closeCursor();


                $query = $db->prepare("INSERT INTO user_subscriptions (subscription_school_year, subscription_user)
                                     VALUES (:year, :user)");
                $query->bindValue(":year", getCurrentYear(), PDO::PARAM_INT);
                $query->bindValue(":user", $_POST['user'], PDO::PARAM_INT);
                $query->execute();
            }
            else {
                $query = $db->prepare("INSERT INTO treasury (transaction_amount, transaction_designation, transaction_time, transaction_creation_time)
                                     VALUES (:amount, :designation, :time, :creation)");
                $query->bindValue(':amount', $_POST['amount'], PDO::PARAM_STR);
                $query->bindValue(':designation', $_POST['designation'], PDO::PARAM_STR);
                $query->bindValue(':time', $time, PDO::PARAM_INT);
                $query->bindValue(':creation', time(), PDO::PARAM_INT);
                $query->execute();
                $query->closeCursor();
            }

            array_push($return["messages"], "La transaction a bien été effectuée.");
            $return['messages']['month'] = strtolower(date("F", $time));
            $return['messages']['year'] = strtolower(date("Y", $time));
        }
        break;
    case 'delete':
        if (!checkPrivileges(getInformation(), 'desk_president') && !checkPrivileges(getInformation(), 'desk_treasurer')) {
            $return['status'] = 'error';
            array_push($return['messages'], "Vous n'avez pas la permission de faire ceci.");
        }
        else {
            $query = $db -> prepare('select * from treasury where transaction_id=:id');
            $query -> bindValue(':id', $_POST['id']);
            $query->execute();
            if ($query->rowCount()>0) {
                $data = $query->fetchObject();
                if ($data->transaction_creation_time > time()-(60*5)) {
                    $query->closeCursor();
                    $query=$db->prepare("delete from treasury where transaction_id=:id");
                    $query -> bindValue(':id', $_POST['id']);
                    $query->execute();
                    array_push($return['messages'], "La transaction a bien été supprimée.");
                }
                else {
                    $return['status'] = 'error';
                    array_push($return['messages'], "Cette transaction n'est pas éditable.");
                }
            }
            else {
                $return['status'] = 'error';
                array_push($return['messages'], "Cette transaction n'existe pas.");
            }
        }
        break;
}
echo json_encode(array_to_utf8($return));