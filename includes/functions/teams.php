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

function getTeamPoints($team, $year)
{
    global $db;
    $challengePointsQuery = $db->prepare("SELECT SUM(jury_vote_points) AS sum FROM challenge_jury_votes
                                    LEFT JOIN challenges ON challenge_id = jury_vote_challenge
                                    WHERE challenge_start > :start AND challenge_end < :end AND jury_vote_team = :team");
    $challengePointsQuery->bindValue(':start', getSchoolYear(getCurrentYear())['start'], PDO::PARAM_INT);
    $challengePointsQuery->bindValue(':end', getSchoolYear(getCurrentYear())['end'], PDO::PARAM_INT);
    $challengePointsQuery->bindValue(':team', $team, PDO::PARAM_INT);
    $challengePointsQuery->execute();
    if ($challengePointsQuery->rowCount())
        $challengePointsData = $challengePointsQuery->fetchObject()['sum'];
    else
        $challengePointsData = 0;
    $yearPointsQuery = $db->prepare("SELECT * FROM team_points
                                     WHERE point_team = :team AND point_year = :year");
    $yearPointsQuery->bindValue(':team', $team, PDO::PARAM_INT);
    $yearPointsQuery->bindValue(':year', $year, PDO::PARAM_INT);
    $yearPointsQuery->execute();
    if ($yearPointsQuery->rowCount())
        $yearPointsData = $yearPointsQuery->fetchObject()['point_nb'];
    else
        $yearPointsData = 0;
    return $challengePointsData + $yearPointsData;
}

function getLowestTeamPoint($year)
{
    global $db;
    $min = 0;
    $teamsQuery = $db->prepare("SELECT team_id FROM teams");
    $teamsQuery->execute();
    while ($team = $teamsQuery->fetchObject()) {
        $points = getTeamPoints($team, $year);
        if ($points && (!$min || $points < $min)) {
            $min = $points;
        }
    }
    return $min;
}

function getTeamLogo($team)
{
    global $db, $config;

    $query = $db->prepare("SELECT team_shortname FROM teams WHERE team_id=:id");
    $query->bindValue(":id", $team, PDO::PARAM_INT);
    $query->execute();
    if ($data = $query->fetchObject())
        if (file_exists('../assets/img/public/teams/' . $data->team_shortname . '.png')||file_exists('../../assets/img/public/teams/' . $data->team_shortname . '.png'))
            return 'teams/' . $data->team_shortname . '.png';
    return 'default_team.png';
}