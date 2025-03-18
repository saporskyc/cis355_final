-- Author: Calob Saporsky
-- Description: using XAMPP > phpMyAdmin, create database 'cis355_final' and import this file

-- configure and initialize transaction
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- create table persons, set primary key, auto increment
CREATE TABLE `users` (
    `user_id` int(11) NOT NULL AUTO_INCREMENT,
    `admin` varchar(255) DEFAULT "N",
    `f_name` varchar(255) NOT NULL,
    `l_name` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `password` varchar(255) NOT NULL,
    Primary Key (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- create table issues, set primary key, auto increment
CREATE TABLE `issues` (
    `issue_id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11),
    `organization` varchar(255),
    `s_descr` varchar(255),
    `l_descr` text,
    `open_date` date NOT NULL,
    `close_date` date DEFAULT NULL,
    `priority` varchar(255) NOT NULL,
    `status` varchar(255) NOT NULL,
    Primary Key (`issue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- create table comments, set primary key, auto increment
CREATE TABLE `comments` (
    `comment_id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `issue_id` int(11) NOT NULL,
    `comment` text,
    `posted_date` datetime,
    Primary Key (`comment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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
INSERT INTO `issues` (`issue_id`, `user_id`, `organization`, `s_descr`, `l_descr`, `open_date`, `close_date`, `priority`, `status`)
    VALUES ("1", "2", "PrimeNet", "Internet Down", "Internet connection went down at approxmiately 08:00.", (SELECT CURRENT_DATE()), (SELECT CURRENT_DATE()), "1", "CLOSED");

-- comments
    -- SELECT NOW() returns the datetime the statement executes at
    -- all times returned by this function are in timezone GMT per database config
INSERT INTO `comments` (`user_id`, `issue_id`, `comment`, `posted_date`)
    VALUES ("2", "1", "Unable to ping the provided IP. Please have the resident reboot their router and let me know when that is done so I can attempt again.", (SELECT NOW()));
INSERT INTO `comments` (`user_id`, `issue_id`, `comment`, `posted_date`)
    VALUES ("3", "1", "Contacted the resident, they have rebooted their router.", (SELECT NOW()));
INSERT INTO `comments` (`user_id`, `issue_id`, `comment`, `posted_date`)
    VALUES ("2", "1", "I am now able to ping the IP. Please have the resident test their internet again.", (SELECT NOW()));
INSERT INTO `comments` (`user_id`, `issue_id`, `comment`, `posted_date`)
    VALUES ("3", "1", "The resident confirmed their internet is back up.", (SELECT NOW()));
INSERT INTO `comments` (`user_id`, `issue_id`, `comment`, `posted_date`)
    VALUES ("2", "1", "Closing issue.", (SELECT NOW()));

-- execute
COMMIT;