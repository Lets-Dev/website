<?php

/**
 * @brief Permet d'encoder une chaîne de caractères
 * @param $Text_To_Encode : texte à encoder
 * @return string: text encodé
 */
function encode($Text_To_Encode)
{
    global $salt;
    $key = $salt;
    $data = serialize($Text_To_Encode);
    $td = mcrypt_module_open(MCRYPT_DES, "", MCRYPT_MODE_ECB, "");
    $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
    mcrypt_generic_init($td, $key, $iv);
    $data = base64_encode(mcrypt_generic($td, '!' . $data));
    mcrypt_generic_deinit($td);
    return $data;
}

/**
 * @brief Permet de décoder une chaîne de caractères
 * @param $Text_To_Decode : texte à décoder
 * @return string: texte décodé
 */
function decode($Text_To_Decode)
{
    global $salt;
    $key = $salt;
    $td = mcrypt_module_open(MCRYPT_DES, "", MCRYPT_MODE_ECB, "");
    $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
    mcrypt_generic_init($td, $key, $iv);
    $data = mdecrypt_generic($td, base64_decode($Text_To_Decode));
    mcrypt_generic_deinit($td);
    if (substr($data, 0, 1) != '!') {
        return false;
    }
    $data = substr($data, 1, strlen($data) - 1);
    return unserialize($data);
}

/**
 * @brief Fonction permettant de vérifier si l'utilisateur passé en paramètre est dans le bureau actuel
 * @param $user : ID de l'utilisateur recherché
 * @return bool
 */
function checkPrivileges($user)
{
    require_once "dates.php";
    global $db;
    $query = $db->prepare('SELECT count(*) AS nb FROM desks WHERE :user IN (desk_president, desk_secretary, desk_treasurer, desk_challenges, desk_communication, desk_jurys) AND desk_year = :year');
    $query->bindValue(':user', $user, PDO::PARAM_INT);
    $query->bindValue(':year', getCurrentYear(), PDO::PARAM_INT);
    $query->execute();
    if ($data = $query->fetchObject())
        if ($data->nb > 0)
            return true;
    return false;
}

function generateToken($length = 50)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ/*-+';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function checkSession()
{
    global $db;
    if ($_SESSION['connected'] == false && isset($_COOKIE['login'])) {
        $login = json_decode($_COOKIE['login']);

        $query = $db->prepare("SELECT * FROM user_logins
                          LEFT JOIN users ON login_user = users.user_id
                          WHERE login_token = :token AND login_key = :key");
        $query->bindValue(':token', $login['token'], PDO::PARAM_STR);
        $query->bindValue(':key', $login['key'], PDO::PARAM_STR);
        $query->execute();
        if ($query->rowCount() > 0) {
            $data = $query->fetchObject();
            $_SESSION['connected'] = true;
            $_SESSION['informations'] = array("id" => $data->user_id,
                "email" => $data->user_email,
                "firstname" => $data->user_firstname,
                "lastname" => $data->user_lastname);
            return true;
        }
    }
    return false;
}

?>