<?php
function updateAllPasswords()
{
    global $db;
    $query = $db->prepare("SELECT user_id FROM users");
    $query->execute();

    while($data = $query->fetchObject())
    {
        updatePassword($data->user_id);
    }
}

function updatePassword($id)
{
    global $db;
    $query = $db->prepare("SELECT * FROM users WHERE user_id = :user_id");
    $query->bindValue(":user_id", $id, PDO::PARAM_INT);
    $query->execute();

    if ($query->rowCount())
    {
        $data = $query->fetchObject();

        $user_salt = generateToken(64);

        $newQuery = $db->prepare("UPDATE users SET user_password=:password, user_salt=:salt WHERE user_id=:user_id");
        $newQuery->bindValues(':password', hash("sha256", $user_salt . decode($data->user_password)), PDO::PARAM_STR);
        $newQuery->bindValues(':salt', $user_salt, PDO::PARAM_STR);
        $newQuery->bindValues(':user_id', $id, PDO::PARAM_INT);
        $newQuery->execute();
    }
}

?>