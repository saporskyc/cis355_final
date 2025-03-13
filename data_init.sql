-- Author: Calob Saporsky
-- Description: using XAMPP > phpMyAdmin, create database 'cis355_final' and import this file
--              referenced github.com/cis355/fr for the creation of this file

-- initialize
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- create table persons, set primary key, auto increment
CREATE TABLE `users` (
    `user_id` int(11) NOT NULL,
    `title` varchar(255) DEFAULT "N",
    `f_name` varchar(255) NOT NULL,
    `l_name` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
ALTER TABLE `users` ADD PRIMARY KEY (`user_id`);
ALTER TABLE `users` MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

-- create table issues, set primary key, auto increment
CREATE TABLE `issues` (
    `issue_id` int(11) NOT NULL,
    `s_descr` varchar(255),
    `l_descr` text,
    `open_date` date NOT NULL,
    `close_date` date DEFAULT NULL,
    `priority` varchar(255) NOT NULL,
    `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
ALTER TABLE `issues` ADD PRIMARY KEY (`issue_id`);
ALTER TABLE `issues` MODIFY `issue_id` int(11) NOT NULL AUTO_INCREMENT;

-- create table comments, set primary key, auto increment
CREATE TABLE `comments` (
    `comment_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `issue_id` int(11) NOT NULL,
    `comment` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
ALTER TABLE `comments` ADD PRIMARY KEY (`comment_id`);
ALTER TABLE `comments` MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT;

-- insert test data

-- users
-- all test user passwords should evaluate to 'mypassword'
INSERT INTO `users` (`user_id`, `admin`, `f_name`, `l_name`, `email`, `password`)
    VALUES ("1", "Y", "The", "Boss", "bosslife@email.com", "$2a$12$QYTq9bIvZ/asycZCoh.GAOhsycshCzvEahXvXRrCczdqnGFdZ0XVS");
INSERT INTO `users` (`user_id`, `f_name`, `l_name`, `email`, `password`)
    VALUES ("2", "Todd", "Toddo", "oddtodd@email.com", "$2a$12$F.jpatdVlOnrYWLi/lxPNO90T0auUpFnDP5JTb3aTx7z1QSu5nX42");
INSERT INTO `users` (`user_id`, `f_name`, `l_name`, `email`, `password`)
    VALUES ("3", "Anne", "Person", "msperson@email.com", "$2a$12$o9lzPmLOFgpODyhYHUOXO.wojqkQph.fBZKO8k83hromrC0bC4TFi");

-- issues
INSERT INTO `issues` (`issue_id`, `s_descr`, `l_descr`, `open_date`, `close_date`, `priority`,`status`)
    VALUES ("1", "Internet Down", "Internet connection went down at approxmiately 08:00.", "2025-02-19", "2025-02-19", "1", "CLOSED");

-- comments
INSERT INTO `comments` (`user_id`, `issue_id`, `comment`)
    VALUES ("1", "1", "Unable to ping the provided IP. Please have the resident reboot their router and let me know when that is done so I can attempt again.");
INSERT INTO `comments` (`user_id`, `issue_id`, `comment`)
    VALUES ("2", "1", "Contacted the resident, they have rebooted their router.");
INSERT INTO `comments` (`user_id`, `issue_id`, `comment`)
    VALUES ("1", "1", "I am now able to ping the IP. Please have the resident test their internet again.");
INSERT INTO `comments` (`user_id`, `issue_id`, `comment`)
    VALUES ("2", "1", "The resident confirmed their internet is back up. Closing issue.");

-- execute
COMMIT;