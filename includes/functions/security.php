<?php
include('../credentials.php');

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
 * @param $user: ID de l'utilisateur recherché
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

?>