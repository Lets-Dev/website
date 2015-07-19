<?php

/**
 * @file includes/queries/signup.php
 * @author Sofiane
 * @brief Fichier g�rant l'inscription d'un utilisateur
 * @warning Ne pas oublier de d�finir $_POST['method'] lors de la requ�te � ce fichier
 * @return array: Tableau contenant une colonne "status", et un tableau contenant les messages dans la colonne "messages"
 */

include('../credentials.php');
include('../functions/security.php');
include('../functions/encoding.php');
include('../functions/users.php');
header('Content-Type: application/json; charset=utf-8');
$return = array('status' => 'success', 'messages' => array());

switch ($_POST['method']) {
    // Sign Up with Facebook
    // TODO: Facebook Sign-Up
    case 'facebook':
        break;

    // Sign Up with Github
    // TODO: Github Sign-Up
    case 'github':
        break;

    // Sign Up with Google
    // TODO: Google Sign-Up
    case 'google':
        break;

    // Sign Up with Twitter
    // TODO: Twitter Sign-Up
    case 'twitter':
        break;

    // Sign Up with... Let's Dev !
    default:
        // Set default values
        if (!isset($_POST['promotion']))
            $_POST['promotion']=null;
        if (!isset($_POST['phone']))
            $_POST['phone']=null;

        // Check if all fields are filled
        if (empty($_POST['firstname']) || empty($_POST['lastname']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['confirm'])) {
            $return['status'] = 'error';
            array_push($return['messages'], 'Veuillez saisir tous les champs.');
        }

        // Check if passwords are the same
        if ($_POST['password'] != $_POST['confirm']) {
            $return['status'] = 'error';
            array_push($return['messages'], 'Les mots de passe saisis sont différents.');
        }

        // Check if e-mail is already used
        if ($return['status'] == 'success') {
            $query = $db->prepare("SELECT count(*) AS nb FROM users WHERE user_email = :email OR (user_firstname=:firstname AND user_lastname=:lastname)");
            $query->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
            $query->bindValue(':firstname', $_POST['firstname'], PDO::PARAM_STR);
            $query->bindValue(':lastname', $_POST['lastname'], PDO::PARAM_STR);
            $query->execute();
            if ($data = $query->fetchObject())
                if ($data->nb != 0) {
                    $return['status'] = 'error';
                    array_push($return['messages'], 'Cet utilisateur existe déjà.');
                }
        }

        // If everything is ok
        if ($return['status'] == 'success') {
            addUser($_POST['firstname'],$_POST['lastname'], $_POST['email'], $_POST['phone'], encode($_POST['password']),$_POST['promotion']);
            array_push($return['messages'], 'Vous avez bien été inscrit.');
        }
        break;
}
echo json_encode(array_to_utf8($return));
?>