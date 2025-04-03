<!--
    Author: Calob Saporsky
    Description: user profile page
                 allows a user to edit their own information
-->

<?php
    echo "hello from edit_user.php<br>";
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

    //check if cancel was clicked
    if (isset($_POST["cancel"])) {
        //clear post, redirect to landing page
        $_POST = array();
        header('Location: user_management.php');
    }

    //check if admin user
    $is_admin = false;
    if(isset($_GET["id"]) && !empty($_GET["id"]) && $_SESSION["admin"] == "Y") {
        $is_admin = true;
    }

    //check for values in post
    $user = null;
    if (!empty($_POST)) {
        //values in post, performing edit
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

        //check lname for real input
        if (!empty($_POST["entered_admin"])) {
            $edits["entered_admin"] = $_POST["entered_admin"];
            $proceed = true;
        }

        //check whether or not to proceed with user add
        if ($proceed) {
            //edit current user
            if($is_admin) {
                //management performing edit
                $success = UserUtility::updateUser($_GET["id"], $edits);
            } else {
                //editing my profile
                $success = UserUtility::updateUser($_SESSION["user_id"], $edits);
            }
            
            //check operation result
            if ($success != false) {
                //clear post
                $_POST = array();

                //pull the user again to display changes
                if($is_admin) {
                    //arrived through admin page
                    $user = UserUtility::getOne($_GET["id"]);
                } else {
                    //arrived through my profile button
                    $user = UserUtility::getOne($_SESSION["user_id"]);
                }
            }
        }
    } else {
        //make initial user pull
        if ($is_admin) {
            //arrived through admin page
            $user = UserUtility::getOne($_GET["id"]);
        } else {
            //arrived through my profile button
            $user = UserUtility::getOne($_SESSION["user_id"]);
        }
    }
?>

<!DOCTYPE html>
<html lang=en>
    <!-- page body -->
    <div style="text-align: center;">
        <!-- input form -->
        <form action="<?php  $is_admin ? 'edit_user.php?id=' . $_GET["id"] : 'edit_user.php'; ?>" method="post">
            <!-- user information fields -->
            <!-- email -->
            <label for="email">Email: </label>
            <input type="text" style="padding-top: 5px;" id="email" name="entered_email" placeholder=<?php echo '"' . $user["email"] . '"' ?>><br>
            <br>

            <!-- password -->
            <label for="pass">Password: </label>
            <input type="password" style="padding-top: 5px;" id="pass" name="entered_pass" placeholder="***********"><br>
            <br>

            <!-- first name -->
            <label for="fname">First Name: </label>
            <input type="text" style="padding-top: 5px;" id="fname" name="entered_fname" placeholder=<?php echo '"' . $user["f_name"] . '"' ?>><br>
            <br>

            <!-- last name -->
            <label for="lname">Last Name: </label>
            <input type="text" style="padding-top: 5px;" id="lname" name="entered_lname" placeholder=<?php echo '"' . $user["l_name"] . '"' ?>><br>
            <br>

            <!-- only display admin field if the user came from user management and is an admin -->
            <!-- admin -->
            <?php if ($is_admin) { ?>
                <label for="admin">Admin User: </label>
                <input type="text" style="padding-top: 5px;" id="admin" name="entered_admin" placeholder=<?php echo '"' . $user["admin"] . '"' ?>><br>
                <br>
            <?php }?>

            <!-- confirm button -->
            <button name="confirm" type="submit">
                Save Changes
            </button>

            <!-- cancel button, displays if coming from user_management.php -->
            <?php if ($is_admin) { ?>
                <button name="cancel" value="true" type="submit">
                    Cancel
                </button>
            <?php } else { ?>
                <!-- home button, displays if arrived via my profile button -->
                <button name="home" value="true" type="submit">
                    Home
                </button>
            <?php } ?>
        </form>
    </div>
</html>