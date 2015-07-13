<?php
include('../credentials.php');
include('../functions/security.php');
include('../functions/encoding.php');
header('Content-Type: application/json');
$return = array();
/**
 * Cette page inscrit les utilisateurs dans la base de données.
 * Elle renvoie un tableau de la structure qui suit:
 * status: string
 * messages: array
 */

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
        $return['status'] = 'success';
        $_POST['promotion'] = default_value($_POST['promotion'], 0);
        $_POST['phone'] = default_value($_POST['phone'], 0);

        // Check if all fields are filled
        if (empty($_POST['firstname']) || empty($_POST['lastname']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['confirm'])) {
            $return['status'] = 'error';
            $return['messages'] = 'Veuillez saisir tous les champs.';
        }

        // Check if passwords are the same
        if ($_POST['password'] != $_POST['confirm']) {
            $return['status'] = 'error';
            $return['messages'] = 'Les mots de passe saisis sont différents.';
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
                    $return['messages'] = 'Cet utilisateur existe déjà.';
                }
        }

        // If everything is ok
        if ($return['status'] == 'success') {
            $query = $db->prepare("INSERT INTO users (user_firstname, user_lastname, user_email, user_phone, user_password, user_promotion_year, user_signup)
                            VALUES (:user_firstname, :user_lastname, :user_email, :user_phone, :user_password, :user_promotion_year, :user_signup)");
            $query->bindValue(':user_firstname', $_POST['firstname'], PDO::PARAM_STR);
            $query->bindValue(':user_lastname', $_POST['lastname'], PDO::PARAM_STR);
            $query->bindValue(':user_email', $_POST['email'], PDO::PARAM_STR);
            $query->bindValue(':user_phone', $_POST['phone'], PDO::PARAM_STR);
            $query->bindValue(':user_password', encode($_POST['password']), PDO::PARAM_STR);
            $query->bindValue(':user_promotion_year', $_POST['promotion'], PDO::PARAM_INT);
            $query->bindValue(':user_signup', time(), PDO::PARAM_INT);
            $query->execute();
            $return['messages'] = 'Vous avez bien été inscrit';
        }
        break;
}
echo json_encode(array_to_utf8($return));