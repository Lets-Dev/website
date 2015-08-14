<?php

/**
 * @brief Renvoie l'intervalle de temps dans laquelle une ann�e scolaire est d�finie
 * @param $year : ann�e que nous voulons r�cup�rer
 * @return string
 */
function getSchoolYear($year)
{
    return json_encode(array("start" => mktime(0, 0, 0, 9, 1, $year),
        "end" => mktime(0, 0, 0, 9, 1, $year + 1)));
}

/**
 * @brief Fonction permettant de r�cup�rer l'ann�e scolaire dans laquelle nous sommes actuellement
 * @return integer
 */
function getCurrentYear()
{
    if (date("n") > 1 && date("n") < 9)
        return date('Y') - 1;
    else
        return date('Y');
}

function date_fr($format, $ascii = false, $timestamp = false)
{
    if (!$timestamp) $date_en = date($format);
    else               $date_en = date($format, $timestamp);

    $texte_en = array(
        "Monday", "Tuesday", "Wednesday", "Thursday",
        "Friday", "Saturday", "Sunday", "January",
        "February", "March", "April", "May",
        "June", "July", "August", "September",
        "October", "November", "December"
    );
    if (!$ascii)
        $texte_fr = array(
            "Lundi", "Mardi", "Mercredi", "Jeudi",
            "Vendredi", "Samedi", "Dimanche", "Janvier",
            "F&eacute;vrier", "Mars", "Avril", "Mai",
            "Juin", "Juillet", "Ao&ucirc;t", "Septembre",
            "Octobre", "Novembre", "D&eacute;cembre"
        );
    else
        $texte_fr = array(
            "Lundi", "Mardi", "Mercredi", "Jeudi",
            "Vendredi", "Samedi", "Dimanche", "Janvier",
            "Fevrier", "Mars", "Avril", "Mai",
            "Juin", "Juillet", "Aout", "Septembre",
            "Octobre", "Novembre", "Decembre"
        );
    $date_fr = str_replace($texte_en, $texte_fr, $date_en);

    $texte_en = array(
        "Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun",
        "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul",
        "Aug", "Sep", "Oct", "Nov", "Dec"
    );
    if (!$ascii)
        $texte_fr = array(
            "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam", "Dim",
            "Jan", "F&eacute;v", "Mar", "Avr", "Mai", "Jui",
            "Jui", "Ao&ucirc;", "Sep", "Oct", "Nov", "D&eacute;c"
        );
    else
        $texte_fr = array(
            "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam", "Dim",
            "Jan", "Fev", "Mar", "Avr", "Mai", "Jui",
            "Jui", "Aou", "Sep", "Oct", "Nov", "Dec"
        );


    $date_fr = str_replace($texte_en, $texte_fr, $date_fr);

    return $date_fr;
}