<!--
    Author: Calob Saporsky
    Description: user profile page
                 allows a user to edit their own information
-->

<?php
    echo "hello from user_profile.php<br>";
    //start session
    session_start();

    //import necessary file
    require "../utility/util_user.php";

    //check login status
    if (!isset($_SESSION["user_id"])) {
        //invalid access, destroy session and redirect
        session_destroy();
        header('Location: ../launch_page.php');
        exit(0);
    }

    //check if home was clicked
    if (isset($_POST["home"])) {
        //clear post, redirect to landing page
        $_POST = array();
        header('Location: home.php');
    }

    //pull information of currently logged in user
    $user = UserUtility::getOne($_SESSION["user_id"]);

    //check for values in post
    if (!empty($_POST)) {
        //init validation vars and array to store edits
        $proceed = false;
        $edits = array();

        //check email field for input
        if (!empty($_POST["entered_email"])) {
            //make sure it is of form email
            if (filter_var($_POST["entered_email"], FILTER_VALIDATE_EMAIL)) {
                $edits["entered_email"] = $_POST["entered_email"];
                $proceed = true;
            }
        }

        //check password for real input
        if (!empty($_POST["entered_pass"])) {
            $edits["entered_pass"] = $_POST["entered_pass"];
            $proceed = true;
        }

        //check fname for real input
        if (!empty($_POST["entered_fname"])) {
            $edits["entered_fname"] = $_POST["entered_fname"];
            $proceed = true;
        }

        //check lname for real input
        if (!empty($_POST["entered_lname"])) {
            $edits["entered_lname"] = $_POST["entered_lname"];
            $proceed = true;
        }

        //check whether or not to proceed with user add
        if ($proceed) {
            //edit current user
            $success = UserUtility::updateUser($_SESSION["user_id"], $edits);
            
            //check operation result
            if ($success != false) {
                //clear post
                $_POST = array();

                //pull the user again to display changes
                $user = UserUtility::getOne($_SESSION["user_id"]);
            }
        }
    }
?>

<!DOCTYPE html>
<html lang=en>
    <!-- page body -->
    <div style="text-align: center;">
        <!-- login form -->
        <form action="user_profile.php" method="post">
            <!-- email and password fields -->
            <label for="email">
                Email: 
            </label>
            <input type="text" style="padding-top: 5px;" id="email" name="entered_email" placeholder=<?php echo '"' . $user["email"] . '"' ?>><br>
            <br>
            <label for="pass">
                Password: 
            </label>
            <input type="password" style="padding-top: 5px;" id="pass" name="entered_pass" placeholder="***********"><br>
            <br>
            <label for="fname">
                First Name: 
            </label>
            <input type="text" style="padding-top: 5px;" id="fname" name="entered_fname" placeholder=<?php echo '"' . $user["f_name"] . '"' ?>><br>
            <br>
            <label for="lname">
                Last Name: 
            </label>
            <input type="text" style="padding-top: 5px;" id="lname" name="entered_lname" placeholder=<?php echo '"' . $user["l_name"] . '"' ?>><br>
            <br>

            <!-- confirm button -->
            <button name="confirm" type="submit">
                Save Changes
            </button>

            <!-- cancel button -->
            <button name="home" value="true" type="submit">
                Home
            </button>
        </form>
    </div>
</html>