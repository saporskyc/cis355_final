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
            $qry = "SELECT issues.*, users.f_name, users.l_name FROM issues
                    LEFT JOIN users ON users.user_id = issues.user_id
                    ORDER BY issues.priority, issues.open_date";
            $pdo = Database::connect();

            //run query, convert data into array
            $data = $pdo->query($qry);

            //disconnect
            Database::disconnect();

            //return data as array of all returned records
            return $data->fetchAll(PDO::FETCH_DEFAULT);
        }

        //pull all open issues
        public static function getOpen () {
            //set query and connect to database
            $qry = "SELECT issues.*, users.f_name, users.l_name FROM issues
                    LEFT JOIN users ON users.user_id = issues.user_id
                    WHERE issues.status = 'OPEN'
                    ORDER BY issues.priority";
            $pdo = Database::connect();

            //run query, convert data into array
            $data = $pdo->query($qry);

            //disconnect
            Database::disconnect();

            //return data as array of all returned records
            return $data->fetchAll(PDO::FETCH_DEFAULT);
        }

        //pull all closed issues
        public static function getClosed () {
            //set query and connect to database
            $qry = "SELECT issues.*, users.f_name, users.l_name FROM issues
                    LEFT JOIN users ON users.user_id = issues.user_id
                    WHERE issues.status = 'CLOSED'
                    ORDER BY issues.close_date, issues.priority";
            $pdo = Database::connect();

            //run query, convert data into array
            $data = $pdo->query($qry);

            //disconnect
            Database::disconnect();

            //return data as array of all returned records
            return $data->fetchAll(PDO::FETCH_DEFAULT);
        }

        //pull all open issues associated with a specific user
        public static function getAssigned (string $id) {
            //set query and connect to database
            $qry = "SELECT issues.*, users.f_name, users.l_name FROM issues
                    LEFT JOIN users ON users.user_id = issues.user_id
                    WHERE issues.status = 'OPEN' AND issues.user_id = '$id'
                    ORDER BY issues.priority";
            $pdo = Database::connect();

            //run query, convert data into array
            $data = $pdo->query($qry);

            //disconnect
            Database::disconnect();

            //return data as array of all returned records
            return $data->fetchAll(PDO::FETCH_DEFAULT);
        }

        //pull one issue, and the assigned user's id, first name and last name
        public static function getOne (string $id) {
            //set query and connect to database
            $qry = "SELECT issues.*, users.user_id, users.f_name, users.l_name
                    FROM issues
                    LEFT JOIN users ON users.user_id = issues.user_id
                    WHERE issues.issue_id = '$id'";
            $pdo = Database::connect();

            //execute query, convert data to array
            $data = $pdo->query($qry);

            //disconnect
            Database::disconnect();

            //return data as array indexed by column
            return $data->fetch(PDO::FETCH_ASSOC);
        }

        //create a new issue
        public static function newIssue (string $assigned_user, string $org, string $s_descr, string $l_descr, string $priority) {
            //set the insert query and connect to database
            $qry = "INSERT INTO issues (user_id, organization, s_descr, l_descr, priority)
                    VALUES ('$assigned_user', '$org', '$s_descr', '$l_descr', '$priority');";
            $pdo = Database::connect();

            try{
                //execute insert
                $pdo->query($qry);

                //get the id of the new issue
                $id = $pdo->query("SELECT LAST_INSERT_ID()");
                
                //disconnect
                Database::disconnect();

                //return new id
                return $id->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                //an error occurred. disconnect from database, display error, return false
                Database::disconnect();
                echo $e->getMessage() . "<br>";
                return false;
            };
        }

        //delete an existing issue and its comments by issue id
        public static function deleteIssue (String $id) {
            //set queries and connect to database
            $delIssue = "DELETE FROM issues WHERE issues.issue_id = $id";
            $delComments = "DELETE FROM comments WHERE comments.issue_id = $id";
            $pdo = Database::connect();
            
            try {
                //execute deletions, disconnect from database
                $pdo->query($delIssue);
                $pdo->query($delComments);
                Database::disconnect();
            } catch (PDOException $e) {
                //an error occurred. disconnect from database, display error, return false
                Database::disconnect();
                echo $e->getMessage() . "<br>";
            }
        }

        //update an existing issue
        public static function updateIssue (string $id, array $edits) {
            //connect to database
            $pdo = Database::connect();

            //pull the existing record
            $qry = "SELECT * FROM issues WHERE issues.issue_id = '$id'";
            $data = $pdo->query($qry);
            $existingRec = $data->fetch(PDO::FETCH_ASSOC);

            //check if an existing record was found
            if ($existingRec != null && $existingRec != false) {
                //initiliaze update query and variable determining whether update is necessary
                $qry = "UPDATE issues SET";
                $update = false;
                $firstMod = true;
                
                //check existing keys in edits array and modify query
                if (array_key_exists("org", $edits) && $existingRec["organization"] != $edits["org"]) {
                    if ($firstMod) {
                        $qry = $qry . ' organization = "' . $edits["org"] . '"';
                        $firstMod = false;
                    } else if (substr($qry, -1) != ",") {
                        $qry = $qry . ', organization = "' . $edits["org"] . '"';
                    } else {
                        $qry = $qry . ' organization = "' . $edits["org"] . '",';
                    }
                    $update = true;
                }

                if (array_key_exists("descr1", $edits) && $existingRec["s_descr"] != $edits["descr1"]) {
                    if ($firstMod) {
                        $qry = $qry . ' s_descr = "' . $edits["descr1"] . '"';
                        $firstMod = false;
                    } else if (substr($qry, -1) != ",") {
                        $qry = $qry . ', s_descr = "' . $edits["descr1"] . '"';
                    } else {
                        $qry = $qry . ' s_descr = "' . $edits["descr1"] . '",';
                    }
                    $update = true;
                }

                if (array_key_exists("descr2", $edits) && $existingRec["l_descr"] != $edits["descr2"]) {
                    if ($firstMod) {
                        $qry = $qry . ' l_descr = "' . $edits["descr2"] . '"';
                        $firstMod = false;
                    } else if (substr($qry, -1) != ",") {
                        $qry = $qry . ', l_descr = "' . $edits["descr2"] . '"';
                    } else {
                        $qry = $qry . ' l_descr = "' . $edits["descr2"] . '",';
                    }
                    $update = true;
                }

                if (array_key_exists("priority", $edits) && $existingRec["priority"] != $edits["priority"]) {
                    if ($firstMod) {
                        $qry = $qry . ' priority = "' . $edits["priority"] . '"';
                        $firstMod = false;
                    } else if (substr($qry, -1) != ",") {
                        $qry = $qry . ', priority = "' . $edits["priority"] . '"';
                    } else {
                        $qry = $qry . ' priority = "' . $edits["priority"] . '",';
                    }
                    $update = true;
                }

                if (array_key_exists("status", $edits) && $existingRec["status"] != $edits["status"]) {
                    if ($firstMod) {
                        $qry = $qry . ' status = "' . $edits["status"] . '"';
                        $firstMod = false;
                    } else if (substr($qry, -1) != ",") {
                        $qry = $qry . ', status = "' . $edits["status"] . '"';
                    } else {
                        $qry = $qry . ' status = "' . $edits["status"] . '",';
                    }
                    $update = true;
                }

                if (array_key_exists("assigned", $edits) && $existingRec["user_id"] != $edits["assigned"]) {
                    if ($firstMod) {
                        $qry = $qry . ' user_id = "' . $edits["assigned"] . '"';
                        $firstMod = false;
                    } else if (substr($qry, -1) != ",") {
                        $qry = $qry . ', user_id = "' . $edits["assigned"] . '"';
                    } else {
                        $qry = $qry . ' user_id = "' . $edits["assigned"] . '",';
                    }
                    $update = true;
                }

                //check for need to update
                if ($update) {
                    //finalize query
                    $qry = $qry . " WHERE issues.issue_id = $id";

                    //execute the query, return true or false based on success
                    try {
                        $pdo->query($qry);
                        Database::disconnect();
                        return true;
                    } catch (PDOException $e) {
                        Database::disconnect();
                        echo $e->getMessage() . "<br>";
                        return false;
                    }
                }
            }
        }
    }
?>