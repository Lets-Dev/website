<?php
session_start();
session_destroy();
setcookie("login", null, time()-1000, $config['path']);
header('Location: ./?alert=loggedout');
?>