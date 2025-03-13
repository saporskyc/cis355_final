<!-- --
    Author: Calob Saporsky
    Description: database utility class for interaction with table 'issues'
-->

<?php
    //import necessary files
    require 'database.php';

    class IssueUtility {

        //pull all issues
        public static function getAll () {
            //set route and connect to database
            $qry = "SELECT * FROM issues";
            $pdo = Database::connect();

            //run query and collect data
            $data = $pdo->query($qry);
            $data = $data->fetch_all(PDO::FETCH_DEFAULT);

            //disconnect
            Database::disconnect();

            //return data as array of all returned records
            return $data;
        }

        //pull one issue from the table
        public static function getOne (string $id) {
            //set route and connect to database
            $qry = "SELECT issues.*, comments.comment
                    FROM issues
                    LEFT JOIN comments ON comment.issue_id = $id
                    WHERE issues.issue_id = $id";
            $pdo = Database::connect();

            //run query, convert data to array
            $data = $pdo->query($qry);
            $data = $data->fetch(PDO::FETCH_ASSOC);

            //disconnect
            Database::disconnect();

            //return data as array indexed by column
            return $data;
        }

        //create a new issue
        public static function newIssue (string $s_descr, string $l_descr, string $priority, string $status) {
            //set route and connect to database
            $qry = "INSERT INTO issues (s_descr, l_descr, open_date, priority, status)
                    VALUES ($s_descr, $l_descr, (SELECT CURRENT_DATE()), $priority, $status)";
            $pdo = Database::connect();

            //execute query
            $pdo->query(array($qry));

            //disconnect
            Database::disconnect();
        }

        //delete an existing issue by id
        public static function deleteIssue (String $id) {
            //set route and connect to database
            $qry = "DELETE FROM issues WHERE issues.issue_id $id";
            $pdo = Database::connect();
            
            //execute query
            $pdo->query($qry);

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

            //initiliaze update query and variable determining whether update is necessary
            $qry = "UPDATE issues SET";
            $update = false;
            
            //check keys and modify query
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

            //disconnect
            Database::disconnect();
        }
    }
?>