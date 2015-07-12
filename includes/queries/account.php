<?php
include('../credentials.php');
$return['status'] = 'success';

switch ($_POST['action']) {
    // Modification des informations de compte (téléphone, e-mail)
    case 'edit':

        break;

    // Modification du mot de passe
    case 'edit_password':

        break;

    // Marquer un utilisateur comme cotisant
    case 'add_subscription':

        break;

    // Marquer un utilisateur comme membre d'honneur
    case 'honnor':
        if (checkPrivileges(get_current_user()))
            return;
        break;
    default:
        break;
}

echo json_encode($return);
?>