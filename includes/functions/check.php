<?php

function checkShortName($name)
{
    return preg_match('#^[a-z0-9_-]+$#', $name);
}

?>