<!--
    Author: Calob Saporsky
    Description: issue creation page
                 creates a new issue and redirects to edit_issue.php
                 on success
-->

<?php
    echo "hello from new_issue.php<br>";
    //import necessary files
    require "../utility/util_issue.php";
    require "../utility/util_user.php";

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
        $org_error = false;
        $descr_error = false;
        $priority_error = false;

        //check organization field for input
        if (empty($_POST["org"])) {
            $proceed = false;
            $org_error = true;
        }

        //check descr1/s_descr for real input
        if (empty($_POST["descr1"])) {
            $proceed = false;
            $descr_error = true;
        }

        //check priority for real input
        if (empty($_POST["priority"])) {
            $proceed = false;
            $priority_error = true;
        }

        //check whether or not to proceed with issue add
        if ($proceed) {
            //add new issue
            $new_id = IssueUtility::newIssue($_POST["assigned"], $_POST["org"], $_POST["descr1"], $_POST["descr2"], $_POST["priority"]);
            
            //check operation result
            if (!empty($new_id) && $new_id != false) {
                //clear post
                $_POST = array();

                //redirect to edit_issue.php
                header('Location: edit_issue.php?id=' . $new_id["LAST_INSERT_ID()"]);
            }
        }
    }

    //pull users for dropdown list
    $users = UserUtility::getAll();
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
            <textarea id="descr2" style="width: 625px; height: 150px;" rows="8" cols="35" name="descr2" placeholder="More Details"></textarea><br>
            <br>

            <!-- priority dropdown -->
            <label for="priority">Priority: </label>
            <select id="priority" type="text" style="text-align: center;" name="priority">
                <?php
                    //insert default value for dropdown
                    echo '<option value=""></option>';

                    //create options 1 - 6
                    for ($i = 1; $i < 7; $i++) {
                        echo '<option value="' . $i . '">' . $i . '</option>';
                    }
                ?>
            </select>
            
            <!-- assigned user dropdown -->
            <label for="assigned" style="padding-left: 25px;">Assigned To: </label>
            <select id="assigned" style="padding-top: 5px; text-align: center; display: inline;" name="assigned">
                <?php
                    //insert default value for dropdown
                    echo '<option value="">Unassigned</option>';

                    //loop over existing users and populate the dropdown
                    foreach ($users as $user) {
                        echo '<option value=' . $user["user_id"] . '>' .
                             trim($user["f_name"]) . ' ' . trim($user["l_name"]) .
                             '</option>';
                    }
                ?>
            </select><br>
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