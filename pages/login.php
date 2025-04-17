<!--
    Author: Calob Saporsky
    Description: user login page
                 navigates to home.php on successful login, or launch_page.php on cancel/failed login
-->

<?php
    //display test users on login screen
    echo 'admin user --> bosslife@test.com | mypassword<br>';
    echo 'not an admin user --> oddtodd@test.com | mypassword<br>';

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
    $login_error = "";
    if (!empty($_POST["entered_email"]) && !empty($_POST["entered_pass"])) {
        //validate login
        $result = UserUtility::validateLogin($_POST["entered_email"], $_POST["entered_pass"]);

        //check if login succeeded
        if (is_array($result) && !empty($result)) {
            //login success, store user_id in $_SESSION
            $_SESSION["user_id"] = $result["user_id"];
            $_SESSION["admin"] = $result["admin"];
            //clear post, redirect to application home page
            $_POST = array();
            header('Location: home.php');
        } else {
            //login failed, set error display values, destroy session to clear stored values
            $login_error = $result;
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

            <!-- display the login error if there is one -->
            <?php if ($login_error != "") { ?>
                <label style="color: red;"> <?php echo $login_error; ?> </label><br>
                <br>
            <?php } ?>

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