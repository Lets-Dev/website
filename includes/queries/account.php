<?php
include('../autoload.php');
$return = array('status' => 'success', 'messages' => array());

switch ($_POST['action']) {
    // Modification des informations de contact (téléphone, e-mail)
    case 'edit_contact':
        // TODO: checkEmail()
        //if (checkEmail($_POST['email'])) {
        $query = $db->prepare('UPDATE users SET user_email=:email AND user_phone = :phone');
        $query->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
        $query->bindValue(':phone', $_POST['phone'], PDO::PARAM_STR);
        $query->execute();
        array_push($return['messages'], 'Les informations de contact ont bien été modifiées.');
        //}
//        else {
//        $return['status']='error';
//        array_push($return['messages'], 'L\'adresse e-mail saisie n\'est pas valide.');
//      }
        break;

    // Modification du mot de passe
    case 'edit_password':
        $query = $db->prepare('SELECT user_id FROM users WHERE user_email=:email AND user_password=:password');
        $query->bindValue(':email', getInformation('email'), PDO::PARAM_STR);
        $query->bindValue(':password', encode($_POST['current']), PDO::PARAM_STR);
        $query->execute();
        // On vérifie que le formulaire est rempli
        if (!isset($_POST['current']) || !isset($_POST['new']) || !isset($_POST['confirm'])) {
            $return['status'] = 'error';
            array_push($return['messages'], 'Veuillez saisir tous les champs.');
        }
        // On vérifie que les deux nouveaux mots de passe sont les mêmes
        if ($_POST['new'] != $_POST['confirm']) {
            $return['status'] = 'error';
            array_push($return['messages'], 'Les mots de passe entrés ne correspondent pas.');
        }
        // On vérifie que le compte existe
        if ($query->rowCount() > 0) {
            $return['status'] = 'error';
            array_push($return['messages'], 'Le mot de passe actuel entré est incorrect.');
        }
        // Si tout est ok
        if ($return['status'] == 'success') {
            $query->closeCursor();
            $query = $db->prepare('UPDATE users SET user_password= :new WHERE user_email=:email AND user_password=:password');
            $query->bindValue(':email', getInformation('email'), PDO::PARAM_STR);
            $query->bindValue(':password', encode($_POST['current']), PDO::PARAM_STR);
            $query->bindValue(':new', encode($_POST['new']), PDO::PARAM_STR);
            $query->execute();
            array_push($return['messages'], 'Le mot de passe a bien été changé');
        }
        break;

    // Marquer un utilisateur comme cotisant
    case 'add_subscription':

        break;

    // Marquer un utilisateur comme membre d'honneur
    case 'honnor':
        if (checkPrivileges(get_current_user(), null))
            return;
        break;
    default:
        break;
}

echo json_encode($return);
?>