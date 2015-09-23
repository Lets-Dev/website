CREATE TABLE `users` (
  `user_id` INT NOT NULL AUTO_INCREMENT,
  `user_firstname` VARCHAR(50) NOT NULL,
  `user_lastname` VARCHAR(50) NOT NULL,
  `user_email` VARCHAR(100) NOT NULL,
  `user_phone` VARCHAR(20),
  `user_salt` VARCHAR(100),
  `user_password` VARCHAR(100) NOT NULL,
  `user_honor` INT(1) NOT NULL,
  `user_promotion_year` INT(4),
  `user_signup` INT(10) NOT NULL,
  `user_last_connection` INT(10) NOT NULL,
  `user_facebook_token` VARCHAR(100),
  `user_github_token` VARCHAR(100),
  `user_google_token` VARCHAR(100),
  `user_twitter_token` VARCHAR(100),
  PRIMARY KEY (`user_id`)
);

CREATE TABLE `teams` (
  `team_id` INT NOT NULL AUTO_INCREMENT,
  `team_name` VARCHAR(50) NOT NULL,
  `team_shortname` VARCHAR(50) NOT NULL,
  `team_description` TEXT NOT NULL,
  `team_mysql_key` VARCHAR(100) NOT NULL,
  `team_status` INT NOT NULL,
  `team_owner` INT NOT NULL,
  `team_creation` INT(10) NOT NULL,
  PRIMARY KEY (`team_id`)
);

CREATE TABLE `challenges` (
  `challenge_id` INT NOT NULL AUTO_INCREMENT,
  `challenge_start` INT(10) NOT NULL,
  `challenge_end` INT(10) NOT NULL,
  `challenge_subjects` INT(10) NOT NULL,
  `challenge_language` INT NOT NULL,
  `challenge_subject` TEXT NOT NULL,
  `challenge_jury1` INT NOT NULL,
  `challenge_jury2` INT NOT NULL,
  `challenge_ergonomy_jury` INT NOT NULL,
  PRIMARY KEY (`challenge_id`)
);

CREATE TABLE `language_sets` (
  `set_id` INT NOT NULL AUTO_INCREMENT,
  `set_name` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`set_id`)
);

CREATE TABLE `languages` (
  `language_id` INT NOT NULL AUTO_INCREMENT,
  `language_name` VARCHAR(50) NOT NULL,
  `language_documentation` VARCHAR(250) NOT NULL,
  `language_icon` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`language_id`)
);

CREATE TABLE `challenge_votes` (
  `vote_id` INT NOT NULL AUTO_INCREMENT,
  `vote_challenge` INT NOT NULL,
  `vote_user` INT NOT NULL,
  `vote_team` INT NOT NULL,
  `vote_time` INT(10) NOT NULL,
  PRIMARY KEY (`vote_id`)
);

CREATE TABLE `challenge_jury_votes` (
  `jury_vote_id` INT NOT NULL AUTO_INCREMENT,
  `jury_vote_team` INT NOT NULL,
  `jury_vote_points` INT NOT NULL,
  `jury_vote_challenge` INT NOT NULL,
  PRIMARY KEY (`jury_vote_id`)
);

CREATE TABLE `user_subscriptions` (
  `subscription_id` INT NOT NULL AUTO_INCREMENT,
  `subscription_school_year` INT(4) NOT NULL,
  `subscription_user` INT NOT NULL,
  PRIMARY KEY (`subscription_id`)
);

CREATE TABLE `team_subscriptions` (
  `subscription_id` INT NOT NULL AUTO_INCREMENT,
  `subscription_user` INT NOT NULL,
  `subscription_team` INT NOT NULL,
  `subscription_time` INT(10) NOT NULL,
  `subscription_leave` INT(10) NOT NULL DEFAULT '0',
  `subscription_status` INT(1) NOT NULL,
  PRIMARY KEY (`subscription_id`)
);

CREATE TABLE `desks` (
  `desk_id` INT NOT NULL AUTO_INCREMENT,
  `desk_year` INT NOT NULL,
  `desk_president` INT NOT NULL,
  `desk_secretary` INT NOT NULL,
  `desk_treasurer` INT NOT NULL,
  `desk_communication` INT NOT NULL,
  `desk_jurys` INT NOT NULL,
  `desk_challenges` INT NOT NULL,
  PRIMARY KEY (`desk_id`)
);

CREATE TABLE `user_logins` (
  `login_id` INT NOT NULL AUTO_INCREMENT,
  `login_token` VARCHAR(100) NOT NULL,
  `login_key` VARCHAR(100) NOT NULL,
  `login_user` INT NOT NULL,
  `login_time` INT(10) NOT NULL,
  `login_platform` VARCHAR(255) NOT NULL,
  `login_browser` VARCHAR(255) NOT NULL,
  `login_ip` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`login_id`)
);

CREATE TABLE `notifications` (
  `notification_id` INT NOT NULL AUTO_INCREMENT,
  `notification_user` INT NOT NULL,
  `notification_text` VARCHAR(255) NOT NULL,
  `notification_time` INT(10) NOT NULL,
  `notification_status` INT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`notification_id`)
);

CREATE TABLE `wallpapers` (
  `walpaper_id` INT NOT NULL AUTO_INCREMENT,
  `wallpaper_url` VARCHAR(255) NOT NULL,
  `wallpaper_date` DATE NOT NULL,
  PRIMARY KEY (`walpaper_id`)
);

CREATE TABLE `language_set_association` (
  `association_id` INT NOT NULL AUTO_INCREMENT,
  `association_set` INT NOT NULL,
  `association_language` INT NOT NULL,
  PRIMARY KEY (`association_id`)
);

CREATE TABLE `treasury` (
  `transaction_id` INT NOT NULL AUTO_INCREMENT,
  `transaction_amount` FLOAT NOT NULL,
  `transaction_designation` VARCHAR(255) NOT NULL,
  `transaction_time` INT(10) NOT NULL,
  PRIMARY KEY (`transaction_id`)
);

CREATE TABLE `challenge_subscriptions` (
  `subscription_id` INT NOT NULL AUTO_INCREMENT,
  `subscription_team` INT NOT NULL,
  `subscription_challenge` INT NOT NULL,
  `subscription_time` INT(10) NOT NULL,
  PRIMARY KEY (`subscription_id`)
);

ALTER TABLE `teams` ADD CONSTRAINT `teams_fk0` FOREIGN KEY (`team_owner`) REFERENCES `users`(`user_id`);

ALTER TABLE `challenges` ADD CONSTRAINT `challenges_fk0` FOREIGN KEY (`challenge_language`) REFERENCES `language_sets`(`language_id`);

ALTER TABLE `challenges` ADD CONSTRAINT `challenges_fk1` FOREIGN KEY (`challenge_jury1`) REFERENCES `users`(`user_id`);

ALTER TABLE `challenges` ADD CONSTRAINT `challenges_fk2` FOREIGN KEY (`challenge_jury2`) REFERENCES `users`(`user_id`);

ALTER TABLE `challenges` ADD CONSTRAINT `challenges_fk3` FOREIGN KEY (`challenge_ergonomy_jury`) REFERENCES `users`(`user_id`);

ALTER TABLE `challenge_votes` ADD CONSTRAINT `challenge_votes_fk0` FOREIGN KEY (`vote_challenge`) REFERENCES `challenges`(`challenge_id`);

ALTER TABLE `challenge_votes` ADD CONSTRAINT `challenge_votes_fk1` FOREIGN KEY (`vote_user`) REFERENCES `users`(`user_id`);

ALTER TABLE `challenge_votes` ADD CONSTRAINT `challenge_votes_fk2` FOREIGN KEY (`vote_team`) REFERENCES `teams`(`team_id`);

ALTER TABLE `challenge_jury_votes` ADD CONSTRAINT `challenge_jury_votes_fk0` FOREIGN KEY (`jury_vote_team`) REFERENCES `teams`(`team_id`);

ALTER TABLE `challenge_jury_votes` ADD CONSTRAINT `challenge_jury_votes_fk1` FOREIGN KEY (`jury_vote_challenge`) REFERENCES `challenges`(`challenge_id`);

ALTER TABLE `user_subscriptions` ADD CONSTRAINT `user_subscriptions_fk0` FOREIGN KEY (`subscription_user`) REFERENCES `users`(`user_id`);

ALTER TABLE `team_subscriptions` ADD CONSTRAINT `team_subscriptions_fk0` FOREIGN KEY (`subscription_user`) REFERENCES `users`(`user_id`);

ALTER TABLE `team_subscriptions` ADD CONSTRAINT `team_subscriptions_fk1` FOREIGN KEY (`subscription_team`) REFERENCES `teams`(`team_id`);

ALTER TABLE `desks` ADD CONSTRAINT `desks_fk0` FOREIGN KEY (`desk_president`) REFERENCES `users`(`user_id`);

ALTER TABLE `desks` ADD CONSTRAINT `desks_fk1` FOREIGN KEY (`desk_secretary`) REFERENCES `users`(`user_id`);

ALTER TABLE `desks` ADD CONSTRAINT `desks_fk2` FOREIGN KEY (`desk_treasurer`) REFERENCES `users`(`user_id`);

ALTER TABLE `desks` ADD CONSTRAINT `desks_fk3` FOREIGN KEY (`desk_communication`) REFERENCES `users`(`user_id`);

ALTER TABLE `desks` ADD CONSTRAINT `desks_fk4` FOREIGN KEY (`desk_jurys`) REFERENCES `users`(`user_id`);

ALTER TABLE `desks` ADD CONSTRAINT `desks_fk5` FOREIGN KEY (`desk_challenges`) REFERENCES `users`(`user_id`);

ALTER TABLE `user_logins` ADD CONSTRAINT `user_logins_fk0` FOREIGN KEY (`login_user`) REFERENCES `users`(`user_id`);

ALTER TABLE `notifications` ADD CONSTRAINT `notifications_fk0` FOREIGN KEY (`notification_user`) REFERENCES `users`(`user_id`);

ALTER TABLE `language_set_association` ADD CONSTRAINT `language_set_association_fk0` FOREIGN KEY (`association_set`) REFERENCES `language_sets`(`set_id`);

ALTER TABLE `language_set_association` ADD CONSTRAINT `language_set_association_fk1` FOREIGN KEY (`association_language`) REFERENCES `languages`(`language_id`);

ALTER TABLE `challenge_subscriptions` ADD CONSTRAINT `challenge_subscriptions_fk0` FOREIGN KEY (`subscription_team`) REFERENCES `teams`(`team_id`);

ALTER TABLE `challenge_subscriptions` ADD CONSTRAINT `challenge_subscriptions_fk1` FOREIGN KEY (`subscription_challenge`) REFERENCES `challenges`(`challenge_id`);