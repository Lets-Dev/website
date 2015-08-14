<?php
function gradient($hexstart, $hexend, $steps)
{

    $start['r'] = hexdec(substr($hexstart, 0, 2));
    $start['g'] = hexdec(substr($hexstart, 2, 2));
    $start['b'] = hexdec(substr($hexstart, 4, 2));

    $end['r'] = hexdec(substr($hexend, 0, 2));
    $end['g'] = hexdec(substr($hexend, 2, 2));
    $end['b'] = hexdec(substr($hexend, 4, 2));

    $step['r'] = ($start['r'] - $end['r']) / ($steps - 1);
    $step['g'] = ($start['g'] - $end['g']) / ($steps - 1);
    $step['b'] = ($start['b'] - $end['b']) / ($steps - 1);

    $gradient = array();

    for ($i = 0; $i <= $steps; $i++) {

        $rgb['r'] = floor($start['r'] - ($step['r'] * $i));
        $rgb['g'] = floor($start['g'] - ($step['g'] * $i));
        $rgb['b'] = floor($start['b'] - ($step['b'] * $i));

        $hex['r'] = sprintf('%02x', ($rgb['r']));
        $hex['g'] = sprintf('%02x', ($rgb['g']));
        $hex['b'] = sprintf('%02x', ($rgb['b']));

        $gradient[] = implode(NULL, $hex);

    }

    return $gradient;

}

function activeFile($file, $get = null, $value = null)
{
    if ($get != null) {
        if (getCurrentFile() == $file && $_GET[$get] == $value)
            echo "active";
    }
    else
        if (getCurrentFile() == $file)
            echo "active";
}