<!--
    Author: Calob Saporsky
    Description: issue edit/manage page
                 pulls up an existing issue by id and diplsays it information as well
                 as the comment section, including any comments associated with the issue
-->

<?php
    echo "hello from edit_issue.php<br>";
    //import necessary file
    require "../utility/util_issue.php";

    //start session
    session_start();

    //check login status
    if (!isset($_SESSION["user_id"])) {
        //invalid access, destroy session and redirect
        session_destroy();
        header('Location: ../launch_page.php');
        exit(0);
    }

    //check if cancel was clicked
    if (isset($_POST["cancel"])) {
        //clear post, redirect to home page
        $_POST = array();
        header('Location: home.php');
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

                //redirect to edit_issue.php
                $redirect != null ? header($redirect) : header('Location: login.php');
            }
        }
    }
?>

<!DOCTYPE html>
<html lang=en>
    <!-- page body -->
    <div style="text-align: center;">
        <!-- issue information form -->
        <form action="new_issue.php" method="post">
            <!-- organization -->
            <label for="org">Organization: </label>
            <input id="org" type="text" style="padding-top: 5px;" name="org"><br>
            <br>

            <!-- short description -->
            <label for="descr1">Description: </label>
            <input id="descr1" type="text" style="padding-top: 5px;" name="descr1"><br>
            <br>
 
            <!-- long description -->
            <textarea id="descr2" style="padding-top: 5px;" rows="8" cols="35" name="descr2" placeholder="More Details"></textarea><br>
            <br>

            <!-- status, priority, assigned user -->
            <label for="status">Status: </label>
            <input id="status" type="text" style="padding-top: 5px; size:7; text-align: center;" name="status" placeholder="OPEN - CLOSED">

            <label for="priority" style="padding-left: 25px;">Priority: </label>
            <select id="priority" type="text" style="text-align: center; display: inline;" name="priority">
                <?php
                    //insert default value for dropdown
                    echo '<option value=""></option>';

                    //create options 1 - 6
                    for ($i = 1; $i < 7; $i++) {
                        echo '<option value="' . $i . '">' . $i . '</option>';
                    }
                ?>
            </select>
            
            <label for="assigned" style="padding-left: 25px;">Assigned To: </label>
            <select id="assigned" style="padding-top: 5px; text-align: center; display: inline;" name="assigned">
                <?php
                    //insert default value for dropdown
                    echo '<option value=""></option>';

                    //loop over existing users and populate the dropdown
                    foreach ($users as $user) {
                        echo '<option value=' . $user["user_id"] . '>' .
                             trim($user["f_name"]) . ' ' . trim($user["l_name"]) .
                             '</option>';
                    }
                ?>
            </select><br>
            <br>

            <!-- open and close dates -->
            <label for="status">Opened On: </label>
            <input id="status" type="text" style="padding-top: 5px; size:7; text-align: center;" name="status" placeholder="OPEN - CLOSED">

            <label for="priority" style="padding-left: 25px;">Closed On: </label>
            <input id="priority" type="text" style="padding-top: 5px; size:7; text-align: center; display: inline;" name="priority" placeholder="1 - 6"><br>
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