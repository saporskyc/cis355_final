<!-- --
    Author: Calob Saporsky
    Description: database utility class for interaction with table 'iss_issues'
-->

<?php
    //import necessary files
    require 'database.php';

    class IssueUtility {

        //pull all issues
        public static function getAll () {
            //set query and connect to database
            $qry = "SELECT * FROM iss_issues";
            $pdo = Database::connect();

            //run query, convert data into array
            $data = $pdo->query($qry);
            $data = $data->fetch_all(PDO::FETCH_DEFAULT);

            //disconnect
            Database::disconnect();

            //return data as array of all returned records
            return $data;
        }

        //pull one issue, its comments, and the users who left the comments
        public static function getOne (string $id) {
            //set query and connect to database
            $qry = "SELECT iss_issues.*, comments.short_comment, comments.long_comment, users.fname, users.lname
                    FROM iss_issues
                    LEFT JOIN iss_comments ON iss_comments.id = iss_issues.id
                    LEFT JOIN iss_users ON iss_persons.id = iss_comments.id
                    WHERE iss_issues.id = $id";
            $pdo = Database::connect();

            //execute query, convert data to array
            $data = $pdo->query($qry);
            $data = $data->fetch(PDO::FETCH_ASSOC);

            //disconnect
            Database::disconnect();

            //return data as array indexed by column
            return $data;
        }

        //create a new issue
        public static function newIssue (string $s_descr, string $l_descr, string $priority, string $status, string $org, string $project) {
            //set query and connect to database
            $qry = "INSERT INTO iss_issues (short_description, long_description, open_date, priority, status, org, project)
                    VALUES ($s_descr, $l_descr, (SELECT CURRENT_DATE()), $priority, $status, $org, $status)";
            $pdo = Database::connect();

            //execute query
            $pdo->query(array($qry));

            //disconnect
            Database::disconnect();
        }

        //delete an existing issue and its comments by id
        public static function deleteIssue (String $id) {
            //set queries and connect to database
            $delIssue = "DELETE FROM iss_issues WHERE iss_issues.id = $id";
            $delComments = "DELETE FROM iss_comments WHERE iss_comments.id = $id";
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
            $qry = "SELECT * FROM iss_issues WHERE iss_issues.id = $id";
            $data = $pdo->query($qry);
            $existingRec = $data->fetch(PDO::FETCH_ASSOC);

            //check if an existing record was found
            if ($existingRec != false) {
                //initiliaze update query and variable determining whether update is necessary
                $qry = "UPDATE iss_issues SET";
                $update = false;
                
                //check existing keys in edits array and modify query
                if (array_key_exists("s_descr", $edits) && $existingRec["short_description"] != $edits["s_descr"]) {
                    $qry = $qry . ' short_description = ' . $edits["s_descr"];
                    $update = true;
                }

                if (array_key_exists("l_descr", $edits) && $existingRec["long_description"] != $edits["l_descr"]) {
                    $qry = $qry . ' long_description = ' . $edits["l_descr"];
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
                    $qry = $qry . " WHERE iss_issues.id = $id";

                    //execute
                    $pdo->execute($qry);
                }
            }

            //disconnect
            Database::disconnect();
        }
    }
?>