<?php
include('../autoload.php');
header('Content-Type: application/json');
$return = array('status' => 'success', 'messages' => array());

switch ($_POST['action']) {
    // Modification des informations de contact (téléphone, e-mail)
    case 'edit_contact':
        if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $query = $db->prepare('UPDATE users SET user_email=:email, user_phone = :phone WHERE user_id=:id');
            $query->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
            $query->bindValue(':phone', $_POST['phone'], PDO::PARAM_STR);
            $query->bindValue(':id', getInformation(), PDO::PARAM_STR);
            $query->execute();
            array_push($return['messages'], 'Les informations de contact ont bien été modifiées.');
        }
        else {
            $return['status'] = 'error';
            array_push($return['messages'], 'L\'adresse e-mail saisie est invalide.');
        }
        break;

    // Modification du mot de passe
    case 'edit_password':
        $query = $db->prepare('SELECT * FROM users WHERE user_email=:email');
        $query->bindValue(':email', getInformation('email'), PDO::PARAM_STR);
        $query->execute();
        $data = $query->fetchObject();

        // On vérifie que le formulaire est rempli
        if (empty($_POST['current']) || empty($_POST['new']) || empty($_POST['confirm'])) {
            $return['status'] = 'error';
            array_push($return['messages'], 'Veuillez saisir tous les champs.');
        }
        // On vérifie que les deux nouveaux mots de passe sont les mêmes
        if ($_POST['new'] != $_POST['confirm']) {
            $return['status'] = 'error';
            array_push($return['messages'], 'Les mots de passe entrés ne correspondent pas.');
        }
        // On vérifie que le compte existe
        if ($data->user_password = hash("sha256", $data->user_salt . $_POST['current'])) {
            $return['status'] = 'error';
            array_push($return['messages'], 'Le mot de passe actuel entré est incorrect.');
        }

        // Si tout est ok
        if ($return['status'] == 'success') {
            $query->closeCursor();
            $query = $db->prepare('UPDATE users SET user_password= :new WHERE user_email=:email AND user_password=:password');
            $query->bindValue(':email', getInformation('email'), PDO::PARAM_STR);
            $query->bindValue(':password', hash("sha256", $data->user_salt .$_POST['new']), PDO::PARAM_STR);
            $query->bindValue(':new', encode($_POST['new']), PDO::PARAM_STR);
            $query->execute();
            array_push($return['messages'], 'Le mot de passe a bien été changé.');
        }
        break;

    // Marquer un utilisateur comme cotisant
    case 'add_subscription':

        break;

    // Marquer un utilisateur comme membre d'honneur
    case 'honor':
        if (checkPrivileges(getInformation())) {
            $query = $db->prepare('UPDATE users SET user_honor=1 WHERE user_id=:id');
            $query->bindValue(':id', $_POST['user_id'], PDO::PARAM_INT);
            $query->execute();
            array_push($return['messages'], 'L\'utilisateur a bien été marqué comme membre d\'honneur.');
        }
        else {
            $return['status'] = 'error';
            array_push($return['messages'], 'Vous n\'avez pas la permission.');
        }
        break;
}

echo json_encode(array_to_utf8($return));