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

function canParticipateToChallenge($team, $challenge = null)
{
    global $db, $config;

    $query = $db->prepare("SELECT * FROM team_subscriptions WHERE subscription_team=:team AND subscription_status=1 AND subscription_leave=0");
    $query->bindValue(':team', $team, PDO::PARAM_INT);
    $query->execute();
    $count = $query->rowCount();
    if ($count >= $config['teams']['min_members'] && $count <= $config['teams']['max_members'])
        if ($challenge == null)
            return true;
        else
            if (!subscribedToChallenge($team, $challenge))
                return true;
    return false;
}

function getTeamPoints($team, $year = null) {
    global $db;

    if ($year == null) {
        $query = $db->prepare("select sum(jury_vote_points) as points from challenge_jury_votes
                            where jury_vote_team=:team");
        $query->bindValue(':team', $team, PDO::PARAM_INT);
        $query->execute();
        $data = $query->fetchObject();
        return $data->points;
    }
    else {
        $query = $db->prepare("select sum(jury_vote_points) as points from challenge_jury_votes
                            left join challenges on jury_vote_challenge=challenge_id
                            where jury_vote_team=:team and challenge_start > :start and challenge_end < :end");
        $query->bindValue(':team', $team, PDO::PARAM_INT);
        $query->bindValue(':start', getSchoolYear($year)['start'], PDO::PARAM_INT);
        $query->bindValue(':end', getSchoolYear($year)['end'], PDO::PARAM_INT);
        $query->execute();
        $data = $query->fetchObject();
        return $data->points;
    }
}