-- Author: Calob Saporsky
-- Description: using XAMPP > phpMyAdmin, create database 'cis355_final' and import this file

-- initialize transaction
START TRANSACTION;

-- drop tables if they already exist
DROP TABLE IF EXISTS `users`, `issues`, `comments`;

-- create table 'users'
CREATE TABLE `users` (
    `user_id` int(11) NOT NULL AUTO_INCREMENT,
    `admin` varchar(3) DEFAULT "N",
    `f_name` varchar(50) NOT NULL,
    `l_name` varchar(50) NOT NULL,
    `email` varchar(50) NOT NULL,
    `password` varchar(255) NOT NULL,
    Primary Key (`user_id`)
);

-- create table 'issues'
CREATE TABLE `issues` (
    `issue_id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11),
    `organization` varchar(35) NOT NULL,
    `s_descr` varchar(60) NOT NULL,
    `l_descr` text DEFAULT NULL,
    `open_date` date NOT NULL DEFAULT CURRENT_DATE,
    `close_date` date DEFAULT NULL,
    `priority` varchar(5) NOT NULL,
    `status` varchar(10) DEFAULT 'OPEN',
    Primary Key (`issue_id`)
);

-- create table 'comments'
CREATE TABLE `comments` (
    `comment_id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `issue_id` int(11) NOT NULL,
    `comment` text,
    `posted_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    Primary Key (`comment_id`)
);

-- insert test data

-- users
    -- all test user passwords should evaluate to 'mypassword'
INSERT INTO `users` (`user_id`, `admin`, `f_name`, `l_name`, `email`, `password`)
    VALUES ("1", "Y", "The", "Boss", "bosslife@test.com", "$2a$12$QYTq9bIvZ/asycZCoh.GAOhsycshCzvEahXvXRrCczdqnGFdZ0XVS");
INSERT INTO `users` (`user_id`, `f_name`, `l_name`, `email`, `password`)
    VALUES ("2", "Todd", "Toddo", "oddtodd@test.com", "$2a$12$F.jpatdVlOnrYWLi/lxPNO90T0auUpFnDP5JTb3aTx7z1QSu5nX42");
INSERT INTO `users` (`user_id`, `f_name`, `l_name`, `email`, `password`)
    VALUES ("3", "Anne", "Person", "msperson@test.com", "$2a$12$o9lzPmLOFgpODyhYHUOXO.wojqkQph.fBZKO8k83hromrC0bC4TFi");



-- issues, entered in jumbled order to test sorting

-- closed
INSERT INTO `issues` (`issue_id`, `user_id`, `organization`, `s_descr`, `l_descr`, `open_date`, `close_date`, `priority`, `status`)
    VALUES ("1", "2", "PrimeNet", "Internet Down", "Internet connection went down at approxmiately 08:00.", (SELECT CURRENT_DATE()), (SELECT CURRENT_DATE()), "1", "CLOSED");
INSERT INTO `issues` (`issue_id`, `user_id`, `organization`, `s_descr`, `open_date`, `close_date`, `priority`, `status`)
    VALUES ("2", "2", "TestData", "Closed 1", (SELECT CURRENT_DATE()), (SELECT CURRENT_DATE()), "2", "CLOSED");

-- open
INSERT INTO `issues` (`issue_id`, `organization`, `s_descr`, `open_date`, `priority`, `status`)
    VALUES ("3", "TestData", "Unassigned 1", (SELECT CURRENT_DATE()), "5", "OPEN");
INSERT INTO `issues` (`issue_id`, `organization`, `s_descr`, `open_date`, `priority`, `status`)
    VALUES ("4", "TestData", "Unassigned 2", (SELECT CURRENT_DATE()), "6", "OPEN");
INSERT INTO `issues` (`issue_id`, `organization`, `s_descr`, `open_date`, `priority`, `status`)
    VALUES ("5", "TestData", "Unassigned 3", (SELECT CURRENT_DATE()), "5", "OPEN");

-- closed
INSERT INTO `issues` (`issue_id`, `user_id`, `organization`, `s_descr`, `open_date`, `close_date`, `priority`, `status`)
    VALUES ("6", "3", "TestData", "Closed 2", (SELECT CURRENT_DATE()), (SELECT CURRENT_DATE()), "2", "CLOSED");
INSERT INTO `issues` (`issue_id`, `user_id`, `organization`, `s_descr`, `open_date`, `close_date`, `priority`, `status`)
    VALUES ("7", "3", "TestData", "Closed 3", (SELECT CURRENT_DATE()), (SELECT CURRENT_DATE()), "3", "CLOSED");

-- open and assigned
INSERT INTO `issues` (`issue_id`, `user_id`, `organization`, `s_descr`, `open_date`, `priority`, `status`)
    VALUES ("8", "3", "TestData", "Assigned 1", (SELECT CURRENT_DATE()), "3", "OPEN");



-- comments
INSERT INTO `comments` (`user_id`, `issue_id`, `comment`)
    VALUES ("2", "1", "Unable to ping the provided IP. Please have the resident reboot their router and let me know when that is done so I can attempt again.");
INSERT INTO `comments` (`user_id`, `issue_id`, `comment`)
    VALUES ("3", "1", "Contacted the resident, they have rebooted their router.");
INSERT INTO `comments` (`user_id`, `issue_id`, `comment`)
    VALUES ("2", "1", "I am now able to ping the IP. Please have the resident test their internet again.");
INSERT INTO `comments` (`user_id`, `issue_id`, `comment`)
    VALUES ("3", "1", "The resident confirmed their internet is back up.");
INSERT INTO `comments` (`user_id`, `issue_id`, `comment`)
    VALUES ("2", "1", "Closing issue.");

-- execute
COMMIT;