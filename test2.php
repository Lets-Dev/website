<?php

include ("includes/functions/passTransition.php");
include('includes/autoload.php');

$query = $db->prepare("UPDATE users SET user_password =" . encode("12345") . " WHERE user_id = 16");
$query->execute();