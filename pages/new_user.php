<!--
    Author: Calob Saporsky
    Description: user registration/creation page
                 creates a new user and redirects them to the login page
                 if the user add was successful
-->

<?php
    echo "hello from new_user.php<br>";
    //import necessary file
    require "../utility/util_user.php";

    //start session
    session_start();

    //check login status
    $redirect = null;
    if (isset($_SESSION["user_id"])) {
        //check admin
        if ($_SESSION["admin"] == "Y") {
            //set different redirect
            $redirect = 'Location: user_management.php';
        }
    }

    //check if cancel was clicked
    if (isset($_POST["cancel"])) {
        //check if redirect has been set
        if ($redirect != null) {
            //clear post, redirect to user management page
            $_POST = array();
            header($redirect);
        } else {
            //clear post, destroy session, redirect to landing page
            session_destroy();
            $_POST = array();
            header('Location: ../launch_page.php');
        }
    }

    //check for values in post
    if (!empty($_POST)) {
        //init validation vars
        $proceed = true;
        $email_error = false;
        $password_error = false;
        $fname_error = false;
        $lname_error = false;

        //check email field for input
        if (!empty($_POST["entered_email"])) {
            //make sure it is of form email
            if (!filter_var($_POST["entered_email"], FILTER_VALIDATE_EMAIL)) {
                $proceed = false;
                $email_error = true;
            }
        }

        //check password for real input
        if (empty($_POST["entered_pass"])) {
            $proceed = false;
            $password_error = true;
        }

        //check fname for real input
        if (empty($_POST["entered_fname"])) {
            $proceed = false;
            $fname_error = true;
        }

        //check lname for real input
        if (empty($_POST["entered_lname"])) {
            $proceed = false;
            $lname_error = true;
        }

        //check whether or not to proceed with user add
        if ($proceed) {
            //add new user
            $success = UserUtility::newUser($_POST["entered_fname"], $_POST["entered_lname"], $_POST["entered_email"], $_POST["entered_pass"]);
            
            //check operation result
            if ($success != false) {
                //clear post
                $_POST = array();

                //redirect to login page
                $redirect != null ? header($redirect) : header('Location: login.php');
            }
        }
    }
?>

<!DOCTYPE html>
<html lang=en>
    <!-- page body -->
    <div style="text-align: center;">
        <!-- input form -->
        <form action="new_user.php" method="post">
            <!-- email -->
            <label for="email">Email: </label>
            <input id="email" type="text" style="padding-top: 5px;" name="entered_email" placeholder="abcefg@outlook.com"><br>
            <br>

            <!-- password -->
            <label for="password">Password: </label>
            <input id="password" type="password" style="padding-top: 5px;" name="entered_pass" placeholder="Password"><br>
            <br>

            <!-- first name -->
            <label for="fname">First Name: </label>
            <input id="fname" type="text" style="padding-top: 5px;" name="entered_fname" placeholder="John"><br>
            <br>

            <!-- last name -->
            <label id="lname" for="email">Last Name: </label>
            <input id="lname" type="text" style="padding-top: 5px;" name="entered_lname" placeholder="Doe"><br>
            <br>

            <!-- confirm button -->
            <button id="confirm_button" name="confirm" type="submit">
                Confirm
            </button>

            <!-- cancel button -->
            <button id="cancel_button" name="cancel" value="true" type="submit">
                Cancel
            </button>
        </form>
    </div>
</html>