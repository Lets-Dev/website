<?php

function subscribedToChallenge($team, $challenge)
{
    global $db;

    $query = $db->prepare("SELECT * FROM challenge_subscriptions WHERE subscription_challenge=:challenge AND subscription_team=:team");
    $query->bindValue(':challenge', $challenge, PDO::PARAM_INT);
    $query->bindValue(':team', $team, PDO::PARAM_INT);
    $query->execute();

    if ($query->rowCount() > 0)
        return true;
    return false;
}