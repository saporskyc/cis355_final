<!--
    Author: Calob Saporsky
    Description: database utility class for interaction with table 'users'
-->

<?php
    //import necessary files
    require 'database.php';

    class UserUtility {

        //pull all users
        public static function getAll () {
            //set query and connect to database
            $qry = "SELECT users.user_id, users.f_name, users.l_name, users.email FROM users";
            $pdo = Database::connect();

            //run query, convert data into array
            $data = $pdo->query($qry);
            $data = $data->fetch_all(PDO::FETCH_DEFAULT);

            //disconnect
            Database::disconnect();

            //return data as array of all returned records
            return $data;
        }

        //pull one user with a matching id
        public static function getOne (string $id) {
            //set query and connect to database
            $qry = "SELECT * FROM users WHERE users.user_id = $id";
            $pdo = Database::connect();

            //execute query, convert data to array
            $data = $pdo->query($qry);
            $data = $data->fetch(PDO::FETCH_ASSOC);

            //disconnect
            Database::disconnect();

            //return data as array indexed by column
            return $data;
        }

        //create a new user
        public static function newUser ($admin, string $f_name, string $l_name, string $email, string $password) {
            //hash password
            $password = password_hash($password, PASSWORD_BCRYPT);
            
            //set query
            $qry = "";
            if ($admin == "Y") {
                $qry = "INSERT INTO users (admin, f_name, l_name, email, password)
                        VALUES ($admin, $f_name, $l_name, $email, $password)";
            } else {
                $qry = "INSERT INTO users (f_name, l_name, email, password)
                        VALUES ($f_name, $l_name, $email, $password)";
            }
            
            //connect to database
            $pdo = Database::connect();

            //execute query
            $pdo->query(array($qry));

            //disconnect
            Database::disconnect();
        }

        //delete an existing user by id
        public static function deleteIssue (String $id) {
            //set query and connect to database
            $qry = "DELETE FROM users WHERE users.user_id = $id";
            $pdo = Database::connect();
            
            //execute query
            $pdo->query($qry);

            //disconnect
            Database::disconnect();
        }

        //update an existing user
        public static function updateUser (string $id, array $edits) {
            //connect to database
            $pdo = Database::connect();

            //pull the existing record
            $qry = "SELECT * FROM users WHERE users.user_id = $id";
            $data = $pdo->query($qry);
            $existingRec = $data->fetch(PDO::FETCH_ASSOC);


            //check if an existing record was found
            if ($existingRec != false) {
                //initiliaze update query and variable determining whether update is necessary
                $qry = "UPDATE users SET";
                $update = false;
                
                //check existing keys in edits array and modify query
                if (array_key_exists("admin", $edits) && $existingRec["admin"] != $edits["admin"]) {
                    $qry = $qry . ' admin = ' . $edits["admin"];
                    $update = true;
                }
                
                if (array_key_exists("f_name", $edits) && $existingRec["f_name"] != $edits["f_name"]) {
                    $qry = $qry . ' f_name = ' . $edits["f_name"];
                    $update = true;
                }

                if (array_key_exists("l_name", $edits) && $existingRec["l_name"] != $edits["l_name"]) {
                    $qry = $qry . ' l_name = ' . $edits["l_name"];
                    $update = true;
                }

                if (array_key_exists("email", $edits) && $existingRec["email"] != $edits["email"]) {
                    $qry = $qry . ' email = ' . $edits["email"];
                    $update = true;
                }

                if (array_key_exists("password", $edits) && $edits["password"] != null && trim($edits["password"]) != "") {
                    //hash the new password
                    $password = password_hash($edits["password"], PASSWORD_BCRYPT);
                    $qry = $qry . ' password = ' . $password;
                    $update = true;
                }

                //check for need to update
                if ($update) {
                    //finalize query
                    $qry = $qry . "WHERE users.user_id = $id";

                    //execute
                    $pdo->execute($qry);
                }
            }

            //disconnect
            Database::disconnect();
        }

        //login validation function - returns the user if valid login, else false
        public static function validateLogin (string $email, string $password) {
            //connect to database
            $pdo = Database::connect();

            //pull users with a matching email
            $qry = "SELECT * FROM users WHERE users.email = '$email'";
            $data = null;
            $pulled = false;
            try {
                $data = $pdo->query($qry);
                $pulled = true;
            } catch (PDOException $e) {
                Database::disconnect();
                return false;
            }

            //check if any users with a matching email were pulled
            if ($pulled == true) {
                //loop through the users and check passwords
                foreach ($data as $user) {
                    // return "entered loop";
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