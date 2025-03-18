<!--
    Author: Calob Saporsky
    Description: database utility class for interaction with table 'comments'
-->

<?php
    //import necessary file
    require 'database.php';

    class CommentUtility {
        
        //add new comment
        public static function postNew (string $issue_id, string $user_id, string $comment) {
            //set query and connect to database
            $qry = "INSERT INTO comments (issue_id, user_id, comment, posted_date)
                      VALUES ($issue_id, $user_id, $comment, (SELECT NOW()))";
            $pdo = Database::connect();

            //execute query
            $pdo->query($qry);

            //disconnect from db
            Database::disconnect();
        }

    }
?>