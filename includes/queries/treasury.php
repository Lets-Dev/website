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

        if (!isset($_POST['time']) || (!isset($_POST['designation']) && !isset($_POST['cotisation'])) || !isset($_POST['amount'])) {
            $return['status'] = 'error';
            array_push($return['messages'], "Veuillez saisir l'intégralité des champs.");
        }

        if ($return['status'] == 'success') {
            $time = strtotime($_POST['time']);

            if ($_POST['cotisation'] == "true") {
                $query = $db->prepare("INSERT INTO treasury (transaction_amount, transaction_designation, transaction_time)
                                     VALUES (:amount, :designation, :time)");
                $query->bindValue(':amount', $_POST['amount'], PDO::PARAM_STR);
                $query->bindValue(':designation', "Cotisation de ".getInformation('firstname', $_POST['user'])." ".getInformation('lastname', $_POST['user']), PDO::PARAM_STR);
                $query->bindValue(':time', $time, PDO::PARAM_STR);
                $query->execute();
                $query->closeCursor();


                $query = $db->prepare("INSERT INTO user_subscriptions (subscription_school_year, subscription_user)
                                     VALUES (:year, :user)");
                $query->bindValue(":year", getCurrentYear(), PDO::PARAM_INT);
                $query->bindValue(":user", $_POST['user'], PDO::PARAM_INT);
                $query->execute();
            }
            else {
                $query = $db->prepare("INSERT INTO treasury (transaction_amount, transaction_designation, transaction_time)
                                     VALUES (:amount, :designation, :time)");
                $query->bindValue(':amount', $_POST['amount'], PDO::PARAM_STR);
                $query->bindValue(':designation', $_POST['designation'], PDO::PARAM_STR);
                $query->bindValue(':time', $time, PDO::PARAM_STR);
                $query->execute();
                $query->closeCursor();
            }

            array_push($return["messages"], "La transaction a bien été effectuée.");
            $return['messages']['month'] = strtolower(date("F", $time));
            $return['messages']['year'] = strtolower(date("Y", $time));
        }
        break;
}
echo json_encode(array_to_utf8($return));