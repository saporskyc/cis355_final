<!--
    Author: Calob Saporsky
    Description: user login page
                 redirects to issue listing page on successful login or the
                 landing page on cancel
-->

<?php
    echo "hello from login.php<br>";
    //import necessary file
    require "../utility/util_user.php";

    //start session
    session_start();

    //check if cancel was clicked
    if (isset($_POST["cancel"])) {
        //clear post, destroy session, redirect to landing page
        $_POST = array();
        session_destroy();
        header('Location: ../launch_page.php');
    }

    //check for an entered email and password
    if (!empty($_POST["entered_email"]) && !empty($_POST["entered_pass"])) {
        //validate login
        $user = UserUtility::validateLogin($_POST["entered_email"], $_POST["entered_pass"]);

        //check if login succeeded
        if (is_array($user) && !empty($user)) {
            //login success, store user_id in $_SESSION
            $_SESSION["user_id"] = $user["user_id"];
            $_SESSION["admin"] = $user["admin"];

            //clear post, redirect to application home page
            $_POST = array();
            header('Location: home.php');
        } else {
            //login failed, set error display values, destroy session
            echo "login failed";
            session_destroy();
        }
    }
?>

<!DOCTYPE html>
<html lang=en>
    <!-- page body -->
    <div style="text-align: center;">
        <!-- login form -->
        <form action="login.php" method="post">
            <!-- email and password fields -->
            <input type="text" style="padding-top: 5px;" name="entered_email" placeholder="Email"><br>
            <br>
            <input type="password" style="padding-top: 5px;" name="entered_pass" placeholder="Password"><br>
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