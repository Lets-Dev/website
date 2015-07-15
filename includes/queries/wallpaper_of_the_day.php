<?php
include('../credentials.php');
header('Content-Type: application/json');

$query = $db->prepare('SELECT * FROM wallpapers WHERE wallpaper_date = CURDATE()');
$query->execute();
if ($query->rowCount() > 0) {
    $data = $query->fetchObject();
    $return['url'] = $data->wallpaper_url;
    $query->closeCursor();
}
else {
    $query->closeCursor();
    $json = file_get_contents('http://www.splashbase.co/api/v1/images/random?images_only=true');
    $json = json_decode($json);
    $query = $db->prepare('INSERT INTO wallpapers (wallpaper_url, wallpaper_date) VALUES (:url, CURDATE())');
    $query->bindValue(':url', $json->url, PDO::PARAM_STR);
    $query->execute();
    $return['url'] = $json->url;
}

echo json_encode($return);