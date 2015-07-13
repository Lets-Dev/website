<?php
function array_to_utf8($array) {
    if (is_array($array)) {
        foreach ($array as $k => $v) {
            $array[$k] = array_to_utf8($v);
        }
    } else if (is_string ($array)) {
        return utf8_encode($array);
    }
    return $array;
}

function default_value($var, $default) {
    return empty($var) ? $default : $var;
}