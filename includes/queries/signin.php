<?php

/**
 * @file includes/queries/signin.php
 * @author Sofiane
 * @brief Fichier gérant la connexion d'un utilisateur
 * @warning Ne pas oublier de définir $_POST['method'] lors de la requête à ce fichier
 * @return array: Tableau contenant une colonne "status", et un tableau contenant les messages dans la colonne "messages"
 */

include('../autoload.php');
if (!isset($_SESSION['connected']))
    $_SESSION['connected'] = false;
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
        if (!checkSession()) {
            // Setting default values
            if (empty($_POST['type']))
                $_POST['type'] = 'session';

            // Check if all fields are filled
            if (empty($_POST['email']) || empty($_POST['password'])) {
                $return['status'] = 'error';
                array_push($return['messages'], 'Veuillez saisir tous les champs.');
            }

            // Check if the account exists
            if ($return['status'] == 'success') {
                $query = $db->prepare("SELECT * FROM users WHERE user_email = :email");
                $query->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
                $query->execute();
                // If the account exists
                if ($query->rowCount() > 0) {
                    $data = $query->fetchObject();
                    // We check the password
                    if ($data->user_password == hash("sha256", $data->user_salt . $_POST['password'])) {
                        require_once '../libraries/user_agent.php';
                        array_push($return['messages'], 'Vous êtes bien connecté.');

                        // We generate a token and a key, and we check if the combination exists
                        do {
                            $token = generateToken(50);
                            $key = generateToken(50);
                            $check = $db->prepare("SELECT * FROM user_logins WHERE login_token=:token AND login_key=:key");
                            $check->bindValue(':token', $token, PDO::PARAM_STR);
                            $check->bindValue(':key', $key, PDO::PARAM_STR);
                            $check->execute();
                        } while ($check->rowCount() != 0);

                        // On enregistre la connexion
                        $insert = $db->prepare("INSERT INTO user_logins (login_token, login_key, login_user, login_time, login_platform, login_browser, login_ip)
                                      VALUES (:login_token, :login_key, :login_user, :login_time, :login_platform, :login_browser, :login_ip)");
                        $insert->bindValue(':login_token', $token, PDO::PARAM_STR);
                        $insert->bindValue(':login_key', $key, PDO::PARAM_STR);
                        $insert->bindValue(':login_user', $data->user_id, PDO::PARAM_INT);
                        $insert->bindValue(':login_time', time(), PDO::PARAM_INT);
                        $insert->bindValue(':login_platform', parse_user_agent()['platform'], PDO::PARAM_STR);
                        $insert->bindValue(':login_browser', parse_user_agent()['browser'] . " " . parse_user_agent()['version'], PDO::PARAM_STR);
                        $insert->bindValue(':login_ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
                        $insert->execute();

                        // Par cookie, on enregistre le token et la clé
                        if ($_POST['type'] == 'cookie') {
                            setcookie("login", json_encode(array('token' => $token, 'key' => $key)), time() + 60 * 60 * 24 * 365, $config['path']);
                        } else {
                            $_SESSION['connected'] = true;
                            $_SESSION['informations'] = array("id" => $data->user_id,
                                "email" => $data->user_email,
                                "firstname" => $data->user_firstname,
                                "lastname" => $data->user_lastname);
                        }
                    } else {
                        $return['status'] = 'error';
                        array_push($return['messages'], 'Les informations de connexion sont erronnées.');
                    }

                } else {
                    $return['status'] = 'error';
                    array_push($return['messages'], 'L\'adresse e-mail saisie n\'a pas été reconnue.');
                }
            }
        } else {
            $return['status'] = 'error';
            array_push($return['messages'], 'Vous êtes déjà connecté.');
        }
        break;
}
echo json_encode(array_to_utf8($return));