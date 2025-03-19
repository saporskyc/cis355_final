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
            $qry = "SELECT users.user_id, users.f_name, users.l_name, users.email FROM users";
            $pdo = Database::connect();

            //run query, convert data into array
            $data = $pdo->query($qry);

            //disconnect
            Database::disconnect();

            //return data as array of all returned records
            return $data->fetch_all(PDO::FETCH_DEFAULT);;
        }

        //pull one user with a matching id
        public static function getOne (string $id) {
            //set query and connect to database
            $qry = "SELECT * FROM users WHERE users.user_id = $id";
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