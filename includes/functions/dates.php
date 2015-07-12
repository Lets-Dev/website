<?php

/**
 * @brief Renvoie l'intervalle de temps dans laquelle une ann�e scolaire est d�finie
 * @param $year: ann�e que nous voulons r�cup�rer
 * @return string
 */
function getSchoolYear($year) {
    return json_encode(array("start" => mktime(0,0,0,9,1,$year),
        "end" => mktime(0,0,0,9,1,$year+1)));
}

/**
 * @brief Fonction permettant de r�cup�rer l'ann�e scolaire dans laquelle nous sommes actuellement
 * @return integer
 */
function getCurrentYear() {
    if (date("n") > 1 && date("n") < 9)
        return date('Y') - 1;
    else
        return date('Y');
}