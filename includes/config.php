<?php
$config = array(
    'association' =>
        array(
            'subscription_price' => 5
        ),
    'teams' =>
        array(
            'min_members' => 3,
            'max_members' => 10
        ),
    'users' =>
        array(
            'max_teams' => 1,
            'default_avatar' => 'http://projects.sofianeg.com/lets-dev/assets/img/public/default_avatar.png'
        ),
    'challenges' =>
        array(
            'languages_per_challenge' => 2,
            'points_per_challenge' => 1000,
            'days_to_rate' => 15
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
    'expressions' => array(
        'member' => 'Membre de Let\'s Dev !',
        'non-member' => 'Utilisateur de Let\'s Dev !'
    )
);
?>