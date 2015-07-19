<?php
function addUser($firstname, $lastname, $email, $phone=null, $password, $promotion=null, $facebook=null, $github=null, $google=null, $twitter=null) {
    global $db;
    $query = $db->prepare("INSERT INTO users (user_firstname, user_lastname, user_email, user_phone, user_password, user_promotion_year, user_signup, user_facebook_token, user_github_token, user_google_token, user_twitter_token)
                            VALUES (:user_firstname, :user_lastname, :user_email, :user_phone, :user_password, :user_promotion_year, :user_signup, :facebook, :github, :google, :twitter)");
    $query->bindValue(':user_firstname', $firstname, PDO::PARAM_STR);
    $query->bindValue(':user_lastname', $lastname, PDO::PARAM_STR);
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
