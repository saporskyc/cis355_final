<!--
    Author: Calob Saporsky
    Description: database utility class for interaction with table 'comments'
                 comments desgined to not exist outside of an issue, and they are
                 not editable once posted on an issue
-->

<?php
    //import necessary file
    require_once '../database/database.php';

    class CommentUtility {

        //pull all comments associated with the passed in issue id, as well as the information of the user who posted it
        public static function getAssociated (string $id) {
            //set query and connect to database
            $qry = "SELECT comments.*, users.f_name, users.l_name
                    FROM comments
                    LEFT JOIN users ON users.user_id = comments.user_id
                    WHERE comments.issue_id = '$id'";
            $pdo = Database::connect();

            //run query, convert data into array
            $data = $pdo->query($qry);

            //disconnect
            Database::disconnect();

            //return data as array of all returned records
            return $data->fetchAll(PDO::FETCH_DEFAULT);
        }
        
        //add new comment
        public static function newComment (string $issue_id, string $user_id, string $comment) {
            //set query and connect to database
            $qry = "INSERT INTO comments (issue_id, user_id, comment)
                    VALUES ('$issue_id', '$user_id', '$comment')";
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