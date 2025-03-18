<!--
    Author: Calob Saporsky
    Description: user registration/creation page
                 creates a new user and redirects them to the home page, or
                 redirects to landing page on cancel
-->

<?php
    //import necessary file
    require "../utility/util_user.php";

    //start session
    session_start();
    echo "hello from new_user.php<br>";

    //check if cancel was clicked
    if (isset($_POST["cancel"])) {
        //destroy session, clear post, redirect to landing page
        $_POST = array();
        session_destroy();
        header('Location: launch_page.php');
    }
?>

<!DOCTYPE html>
<html lang=en>
    <!-- page body -->
    <div style="text-align: center;">
        <!-- login form -->
        <form action="login.php" method="post">
            <!-- email and password fields -->
            <input type="text" style="padding-top: 5px;" name="entered_email" placeholder="email"><br>
            <br>
            <input type="text" style="padding-top: 5px;" name="entered_pass" placeholder="password"><br>
            <br>

            <!-- login button -->
            <button id="login_button" style="padding-right: 15px" type="submit">
                Login
            </button>

            <!-- cancel button -->
            <button id="cancel_button" name="cancel" value="true" type="submit">
                Cancel
            </button>
        </form>
    </div>
</html>