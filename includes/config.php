<?php
$config = array(
    'teams' =>
        array(
            'min_members' => 3,
            'max_members' => 10
        ),
    'users' =>
        array(
            'max_teams' => 1
        ),
    'challenges' =>
        array(
            'languages_per_challenge' => 2
        ),
    'graphic' =>
        array(
            'colors' => array(
                'main' => '#2B2B2B',
                'contrast' => '#CB7730'
            ),
            'logo' => array(
                'font' => 'Vermin Vibes'
            )

        ),
    'path' => '/lets-dev',
    'expressions' => array(
        'member' => 'Membre de Let\'s Dev !',
        'non-member' => 'Utilisateur de Let\'s Dev !'
    )
);
?>