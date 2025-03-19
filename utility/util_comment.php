<!--
    Author: Calob Saporsky
    Description: database utility class for interaction with table 'comments'
                 comments desgined to not exist outside of an issue, so all
                 fetching utility is built into util_issue.php
-->

<?php
    //import necessary file
    require_once 'database.php';

    class CommentUtility {
        
        //add new comment
        public static function newComment (string $issue_id, string $user_id, string $comment) {
            //set query and connect to database
            $qry = "INSERT INTO comments (issue_id, user_id, comment, posted_date)
                      VALUES ($issue_id, $user_id, $comment, (SELECT NOW()))";
            $pdo = Database::connect();

            //add new comment
            $pdo->query($qry);

            //disconnect from db
            Database::disconnect();
        }

        //delete comment
        public static function deleteComment (string $comment_id) {
            //set query and connect to database
            $qry = "DELETE FROM comments WHERE comment_id = $comment_id";
            $pdo = Database::connect();

            //delete comment
            $pdo->query($qry);

            //disconnect
            Database::disconnect();
        }
    }
?>