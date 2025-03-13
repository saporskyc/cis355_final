-- Author: Calob Saporsky
-- Description: using XAMPP > phpMyAdmin, create database 'cis355_final' and import this file

-- initialize
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- create table persons, set primary key, auto increment
CREATE TABLE `iss_persons` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `admin` varchar(255) DEFAULT "N",
    `fname` varchar(255) NOT NULL,
    `lname` varchar(255) NOT NULL,
    `mobile` varchar(255),
    `email` varchar(255) NOT NULL,
    `pwd_hash` varchar(255) NOT NULL,
    `pwd_salt` varchar(255) NOT NULL,
    Primary Key (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- create table issues, set primary key, auto increment
CREATE TABLE `iss_issues` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `short_description` varchar(255),
    `long_description` text,
    `open_date` date NOT NULL,
    `close_date` date DEFAULT NULL,
    `priority` varchar(255) NOT NULL,
    `status` varchar(255) NOT NULL,
    `org` varchar(255),
    `project` varchar(255),
    `per_id` int(11),
    Primary Key (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- create table comments, set primary key, auto increment
CREATE TABLE `iss_comments` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `per_id` int(11) NOT NULL,
    `iss_id` int(11) NOT NULL,
    `short_comment` varchar(255),
    `long_comment` text,
    `posted_date` date,
    Primary Key (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- insert test data

-- users
-- all test user passwords should evaluate to 'mypassword'
INSERT INTO `users` (`id`, `admin`, `fname`, `lname`, `email`, `pwd_hash`)
    VALUES ("1", "Y", "The", "Boss", "bosslife@email.com", "$2a$12$QYTq9bIvZ/asycZCoh.GAOhsycshCzvEahXvXRrCczdqnGFdZ0XVS");
INSERT INTO `users` (`id`, `fname`, `lname`, `email`, `pwd_hash`)
    VALUES ("2", "Todd", "Toddo", "oddtodd@email.com", "$2a$12$F.jpatdVlOnrYWLi/lxPNO90T0auUpFnDP5JTb3aTx7z1QSu5nX42");
INSERT INTO `users` (`id`, `fname`, `lname`, `email`, `pwd_hash`)
    VALUES ("3", "Anne", "Person", "msperson@email.com", "$2a$12$o9lzPmLOFgpODyhYHUOXO.wojqkQph.fBZKO8k83hromrC0bC4TFi");

-- issues
INSERT INTO `issues` (`id`, `short_description`, `long_description`, `open_date`, `close_date`, `priority`, `status`, `org`, `project`)
    VALUES ("1", "Internet Down", "Internet connection went down at approxmiately 08:00.", "2025-02-19", "2025-02-19", "1", "CLOSED", "ProNET", "Support");

-- comments
INSERT INTO `comments` (`user_id`, `issue_id`, `long_comment`, `posted_date`)
    VALUES ("2", "1", "Unable to ping the provided IP. Please have the resident reboot their router and let me know when that is done so I can attempt again.", (SELECT CURRENT_DATE()));
INSERT INTO `comments` (`user_id`, `issue_id`, `long_comment`, `posted_date`)
    VALUES ("3", "1", "Contacted the resident, they have rebooted their router.", (SELECT CURRENT_DATE()));
INSERT INTO `comments` (`user_id`, `issue_id`, `long_comment`, `posted_date`)
    VALUES ("2", "1", "I am now able to ping the IP. Please have the resident test their internet again.", (SELECT CURRENT_DATE()));
INSERT INTO `comments` (`user_id`, `issue_id`, `long_comment`, `posted_date`)
    VALUES ("3", "1", "The resident confirmed their internet is back up.", (SELECT CURRENT_DATE()));
INSERT INTO `comments` (`user_id`, `issue_id`, `short_comment`, `posted_date`)
    VALUES ("3", "1", "Closing issue.", (SELECT CURRENT_DATE()));

-- execute
COMMIT;