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

    //check if cancel was clicked
    if (isset($_POST["cancel"])) {
        //clear post, redirect to landing page
        $_POST = array();
        header('Location: ../launch_page.php');
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
                header('Location: login.php');
            }
        }
    }
?>

<!DOCTYPE html>
<html lang=en>
    <!-- page body -->
    <div style="text-align: center;">
        <!-- login form -->
        <form action="new_user.php" method="post">
            <!-- email and password fields -->
            <input type="text" style="padding-top: 5px;" name="entered_email" placeholder="Email"><br>
            <br>
            <input type="text" style="padding-top: 5px;" name="entered_pass" placeholder="Password"><br>
            <br>
            <input type="text" style="padding-top: 5px;" name="entered_fname" placeholder="First Name"><br>
            <br>
            <input type="text" style="padding-top: 5px;" name="entered_lname" placeholder="Last Name"><br>
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