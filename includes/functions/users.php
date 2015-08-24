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

function getUserTeam($user)
{
    global $db;
    $query = $db->prepare("select * from team_subscriptions WHERE subscription_user=:user and subscription_status=1 and subscription_leave=0");
    $query->bindValue(':user', $user, PDO::PARAM_INT);
    $query->execute();
    if ($query->rowCount() > 0) {
        $data = $query->fetchObject();
        return $data->subscription_team;
    }
    return false;
}

function hasApplied($user, $team)
{
    global $db;
    $query = $db->prepare('SELECT * FROM team_subscriptions WHERE subscription_user = :user AND subscription_team=:team');
    $query->bindValue(':user', $user, PDO::PARAM_INT);
    $query->bindValue(':team', $team, PDO::PARAM_INT);
    $query->execute();
    if ($query->rowCount() > 0)
        return true;
    return false;
}

function isTeamOwner($user)
{
    global $db;
    $query = $db->prepare('SELECT * FROM team_subscriptions WHERE subscription_user = :user AND subscription_leave=0 AND subscription_status = 1');
    $query->bindValue(':user', $user, PDO::PARAM_INT);
    $query->execute();
    if ($query->rowCount() > 0) {
        $data = $query->fetchObject();
        $team = $data->join_team;
        $query->closeCursor();

        $query = $db->prepare("SELECT * FROM teams WHERE team_creation=:user AND team_id = :team");
        $query->bindValue(":user", $user, PDO::PARAM_INT);
        $query->bindValue(":team", $team, PDO::PARAM_INT);
        $query->execute();
        if ($query -> rowCount()>0)
            return true;
    }
    return false;
}