<!--
    Author: Calob Saporsky
    Description: database utility class for interaction with table 'issues'
-->

<?php
    //import necessary files
    require_once 'database.php';

    class IssueUtility {

        //pull all issues
        public static function getAll () {
            //set query and connect to database
            $qry = "SELECT * FROM issues";
            $pdo = Database::connect();

            //run query, convert data into array
            $data = $pdo->query($qry);

            //disconnect
            Database::disconnect();

            //return data as array of all returned records
            return $data->fetch_all(PDO::FETCH_DEFAULT);
        }

        //pull one issue, its comments, and the users who left the comments
        public static function getOne (string $id) {
            //set query and connect to database
            $qry = "SELECT issues.*, comments.comment, users.f_name, users.l_name
                    FROM issues
                    LEFT JOIN comments ON comments.comment_id = issues.issue_id
                    LEFT JOIN users ON users.user_id = comments.comment_id
                    WHERE issues.issue_id = $id";
            $pdo = Database::connect();

            //execute query, convert data to array
            $data = $pdo->query($qry);

            //disconnect
            Database::disconnect();

            //return data as array indexed by column
            return $data->fetch(PDO::FETCH_ASSOC);
        }

        //create a new issue
        public static function newIssue (string $s_descr, string $l_descr, string $priority, string $status, string $org, string $project) {
            //set query and connect to database
            $qry = "INSERT INTO issues (s_desrc, l_desrc, open_date, priority, status, org, project)
                    VALUES ($s_descr, $l_descr, (SELECT CURRENT_DATE()), $priority, $status, $org, $status)";
            $pdo = Database::connect();

            //execute query
            $pdo->query(array($qry));

            //disconnect
            Database::disconnect();
        }

        //delete an existing issue and its comments by issue id
        public static function deleteIssue (String $id) {
            //set queries and connect to database
            $delIssue = "DELETE FROM issues WHERE issues.issue_id = $id";
            $delComments = "DELETE FROM comments WHERE comments.issue_id = $id";
            $pdo = Database::connect();
            
            //execute deletions
            $pdo->query($delIssue);
            $pdo->query($delComments);

            //disconnect
            Database::disconnect();
        }

        //update an existing issue
        public static function updateIssue (string $id, array $edits) {
            //connect to database
            $pdo = Database::connect();

            //pull the existing record
            $qry = "SELECT * FROM issues WHERE issues.issue_id = $id";
            $data = $pdo->query($qry);
            $existingRec = $data->fetch(PDO::FETCH_ASSOC);

            //check if an existing record was found
            if ($existingRec != false) {
                //initiliaze update query and variable determining whether update is necessary
                $qry = "UPDATE issues SET";
                $update = false;
                
                //check existing keys in edits array and modify query
                if (array_key_exists("s_descr", $edits) && $existingRec["s_descr"] != $edits["s_descr"]) {
                    $qry = $qry . ' s_descr = ' . $edits["s_descr"];
                    $update = true;
                }

                if (array_key_exists("l_descr", $edits) && $existingRec["l_descr"] != $edits["l_descr"]) {
                    $qry = $qry . ' l_descr = ' . $edits["l_descr"];
                    $update = true;
                }

                if (array_key_exists("priority", $edits) && $existingRec["priority"] != $edits["priority"]) {
                    $qry = $qry . ' priority = ' . $edits["priority"];
                    $update = true;
                }

                if (array_key_exists("status", $edits) && $existingRec["status"] != $edits["status"]) {
                    $qry = $qry . ' status = ' . $edits["status"];
                    $update = true;
                }

                //check for need to update
                if ($update) {
                    //finalize query
                    $qry = $qry . " WHERE issues.issue_id = $id";

                    //execute
                    $pdo->execute($qry);
                }
            }

            //disconnect
            Database::disconnect();
        }
    }
?>