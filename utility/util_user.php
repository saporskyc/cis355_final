<!--
    Author: Calob Saporsky
    Description: database utility class for interaction with table 'users'
-->

<?php
    //import necessary files
    require_once 'database.php';

    class UserUtility {

        //pull all users
        public static function getAll () {
            //set query and connect to database
            $qry = "SELECT users.user_id, users.admin, users.f_name, users.l_name, users.email FROM users";
            $pdo = Database::connect();

            //run query, convert data into array
            $data = $pdo->query($qry);

            //disconnect
            Database::disconnect();

            //return data as array of all returned records
            return $data->fetchAll(PDO::FETCH_DEFAULT);
        }

        //pull one user with a matching id
        public static function getOne (string $id) {
            //set query and connect to database
            $qry = "SELECT * FROM users WHERE users.user_id = '$id'";
            $pdo = Database::connect();

            //execute query, convert data to array
            $data = $pdo->query($qry);

            //disconnect
            Database::disconnect();

            //return data as array indexed by column
            return $data->fetch(PDO::FETCH_ASSOC);
        }

        /*
            create a new user
            new users are not admins by default
            a user must be set to admin through the edit screen or the database directly
        */
        public static function newUser (string $f_name, string $l_name, string $email, string $password) {
            //hash password
            $password = password_hash($password, PASSWORD_BCRYPT);
            
            //set query
            $qry = "INSERT INTO users (`f_name`, `l_name`, `email`, `password`)
                    VALUES ('$f_name', '$l_name', '$email', '$password')";

            //connect to database
            $pdo = Database::connect();

            //execute query, disconnect, return true or false based on success
            try{
                $pdo->query($qry);
                Database::disconnect();
                return true;
            } catch (PDOException $e) {
                Database::disconnect();
                echo $e->getMessage() . "<br>";
                return false;
            }
        }

        //delete an existing user by id
        public static function deleteUser (String $id) {
            //set query and connect to database
            $qry = "DELETE FROM users WHERE users.user_id = $id";
            $pdo = Database::connect();
            
            //execute query and disconnect from database
            try{
                $pdo->query($qry);
                Database::disconnect();
            } catch (PDOException $e) {
                Database::disconnect();
                echo $e->getMessage() . "<br>";
            }
        }

        //update an existing user
        public static function updateUser (string $id, array $edits) {
            //connect to database
            $pdo = Database::connect();

            //pull the existing record. on error, return false and display error
            $qry = "SELECT * FROM users WHERE users.user_id = '$id'";
            $existingRec = null;
            try {
                $data = $pdo->query($qry);
                $existingRec = $data->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                Database::disconnect();
                echo $e->getMessage() . "<br>";
                return false;
            }


            //check if an existing record was found
            if ($existingRec != null && $existingRec != false) {
                //init vars
                $qry = "UPDATE users SET";
                $update = false;
                $firstMod = true;
                
                //check existing keys in edits array and modify query
                if (array_key_exists("entered_admin", $edits) && $existingRec["admin"] != $edits["entered_admin"] && ($edits["entered_admin"] == 'Y' || $edits["entered_admin"] == 'N')) {
                    if ($firstMod) {
                        $qry = $qry . ' admin = "' . $edits["entered_admin"] . '"';
                        $firstMod = false;
                    } else if (substr($qry, -1) != ",") {
                        $qry = $qry . ', admin = "' . $edits["entered_admin"] . '"';
                    } else {
                        $qry = $qry . ' admin = "' . $edits["entered_admin"] . '",';
                    }
                    $update = true;
                }
                
                if (array_key_exists("entered_fname", $edits) && $existingRec["f_name"] != $edits["entered_fname"] && !empty($edits["entered_fname"])) {
                    if ($firstMod) {
                        $qry = $qry . ' f_name = "' . $edits["entered_fname"]  . '"';
                        $firstMod = false;
                    } else if (substr($qry, -1) != ",") {
                        $qry = $qry . ', f_name = "' . $edits["entered_fname"] . '"';
                    } else {
                        $qry = $qry . ' f_name = "' . $edits["entered_fname"] . '",';
                    }
                    $update = true;
                }

                if (array_key_exists("entered_lname", $edits) && $existingRec["l_name"] != $edits["entered_lname"] && !empty($edits["entered_lname"])) {
                    if ($firstMod) {
                        $qry = $qry . ' l_name = "' . $edits["entered_lname"]  . '"';
                        $firstMod = false;
                    } else if (substr($qry, -1) != ",") {
                        $qry = $qry . ', l_name = "' . $edits["entered_lname"] . '"';
                    } else {
                        $qry = $qry . ' l_name = "' . $edits["entered_lname"] . '",';
                    }
                    $update = true;
                }

                if (array_key_exists("entered_email", $edits) && $existingRec["email"] != $edits["entered_email"] && !empty($edits["entered_email"])) {
                    if ($firstMod) {
                        $qry = $qry . ' email = "' . $edits["entered_email"]  . '"';
                        $firstMod = false;
                    } else if (substr($qry, -1) != ",") {
                        $qry = $qry . ', email = "' . $edits["entered_email"] . '"';
                    } else {
                        $qry = $qry . ' email = "' . $edits["entered_email"] . '",';
                    }
                    $update = true;
                }

                if (array_key_exists("entered_pass", $edits) && !empty($edits["entered_pass"])) {
                    //check for new password
                    $password = null;
                    if ($edits["entered_pass"] != $existingRec['password']) {
                        //hash the new password
                        $password = password_hash($edits["entered_pass"], PASSWORD_BCRYPT);
                        if ($firstMod) {
                            $qry = $qry . ' password = "' . $password . '"';
                            $firstMod = false;
                        } else if (substr($qry, -1) != ",") {
                            $qry = $qry . ', password = "' . $password . '"';
                        } else {
                            $qry = $qry . ' password = "' . $password . '",';
                        }
                        $update = true;
                    }
                }

                //check for need to update
                if ($update) {
                    //finalize query
                    $qry = $qry . " WHERE users.user_id = $id";

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

        //login validation function - returns the user if valid login, else false
        public static function validateLogin (string $email, string $password) {
            //connect to database
            $pdo = Database::connect();

            //pull users with a matching email
            $qry = "SELECT * FROM users WHERE users.email = '$email'";
            $data = null;
            try {
                $data = $pdo->query($qry);
            } catch (PDOException $e) {
                Database::disconnect();
                echo $e->getMessage() . "<br>";
                return false;
            }

            //check if any users with a matching email were pulled
            if ($data != null) {
                //loop through the users and check passwords
                foreach ($data as $user) {
                    if (password_verify($password, $user['password'])) {
                        //disconnect from db and return user
                        Database::disconnect();
                        return $user;
                    }
                }
            } else {
                //disconnect from db and return false
                Database::disconnect();
                return false;
            }
        }
    }
?>