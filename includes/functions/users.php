<?php
function addUser($firstname, $lastname, $email, $phone = null, $password, $promotion = null, $facebook = null, $github = null, $google = null, $twitter = null)
{
    global $db;
    $query = $db->prepare("INSERT INTO users (user_firstname, user_lastname, user_email, user_phone, user_password, user_promotion_year, user_signup, user_facebook_token, user_github_token, user_google_token, user_twitter_token)
                            VALUES (:user_firstname, :user_lastname, :user_email, :user_phone, :user_password, :user_promotion_year, :user_signup, :facebook, :github, :google, :twitter)");
    $query->bindValue(':user_firstname', ucfirst(strtolower($firstname)), PDO::PARAM_STR);
    $query->bindValue(':user_lastname', ucfirst(strtolower($lastname)), PDO::PARAM_STR);
    $query->bindValue(':user_email', $email, PDO::PARAM_STR);
    $query->bindValue(':user_phone', $phone, PDO::PARAM_STR);
    $query->bindValue(':user_password', $password, PDO::PARAM_STR);
    $query->bindValue(':user_promotion_year', $promotion, PDO::PARAM_INT);
    $query->bindValue(':user_signup', time(), PDO::PARAM_INT);
    $query->bindValue(':facebook', $facebook, PDO::PARAM_STR);
    $query->bindValue(':github', $github, PDO::PARAM_STR);
    $query->bindValue(':google', $google, PDO::PARAM_STR);
    $query->bindValue(':twitter', $twitter, PDO::PARAM_STR);
    $query->execute();
    return true;
}

function hasTeam($user)
{
    global $db;
    $query = $db->prepare('SELECT * FROM team_joins WHERE join_user = :user AND join_leave=0 AND join_status = 1');
    $query->bindValue(':user', $user, PDO::PARAM_INT);
    $query->execute();
    if ($query->rowCount() > 0)
        return true;
    return false;
}

function hasApplied($user, $team)
{
    global $db;
    $query = $db->prepare('SELECT * FROM team_joins WHERE join_user = :user and join_team=:team');
    $query->bindValue(':user', $user, PDO::PARAM_INT);
    $query->bindValue(':team', $team, PDO::PARAM_INT);
    $query->execute();
    if ($query->rowCount() > 0)
        return true;
    return false;
}