<!--
    Author: Calob Saporsky
    Description: issue edit/management page
                 displays an issues information and all of its associated comments
                 also allows an admin user to delete it
-->

<?php
    echo "hello from edit_issue.php<br>";
    //import necessary files
    require "../utility/util_issue.php";
    require "../utility/util_user.php";
    require "../utility/util_comment.php";

    //start session
    session_start();

    //check login status
    if (!isset($_SESSION["user_id"])) {
        //invalid access, destroy session and redirect
        session_destroy();
        header('Location: ../launch_page.php');
        exit(0);
    }

    //check if admin user
    $admin = false;
    if ($_SESSION["admin"] == "Y") {
        $admin = true;
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

        //check whether or not to proceed with user add
        if ($proceed) {
            //add new issue
            $success = IssueUtility::newIssue($_POST["assigned"], $_POST["org"], $_POST["descr1"], $_POST["descr2"], $_POST["priority"]);
            
            //check operation result
            if ($success != false) {
                //clear post
                $_POST = array();

                //redirect to edit_issue.php
                header('Location: edit_issue.php');
            }
        }
    }

    //get the issue by id
    $issue = IssueUtility::getOne($_GET["editing_id"]);

    //check the current issue status is and set a variable to the other option
    $status2 = $issue["status"] == "OPEN" ? "CLOSED" : "OPEN";
?>

<!DOCTYPE html>
<html lang=en>
    <!-- page body -->
    <div style="text-align: center;">
        <!-- issue information form -->
        <form action="new_issue.php" method="post">
            <!-- organization -->
            <label for="org">Organization: </label>
            <input id="org" type="text" style="padding-top: 5px;" name="org" value=" <?php echo $issue["organization"]; ?> "><br>
            <br>

            <!-- short description -->
            <label for="descr1">Description: </label>
            <input id="descr1" type="text" style="padding-top: 5px;" name="descr1" value=" <?php echo $issue["s_descr"]; ?> "><br>
            <br>
 
            <!-- long description -->
            <textarea id="descr2" style="width: 625px; height: 150px;" rows="8" cols="35" name="descr2" <?php if (empty($issue["l_descr"])) { echo 'placeholder="More Details"';} ?>>
            <?php if (!empty($issue["l_descr"])) { echo trim($issue["l_descr"]);} ?>
            </textarea><br>
            <br>

            <!-- status -->
            <label for="status">Status: </label>
            <select id="status" type="text" style="padding-top: 5px;" name="status">
                <?php echo '<option value="' . $issue["status"] . '">' . $issue["status"] . '</option>' ?>
                <?php echo '<option value="' . $status2 . '">' . $status2 . '</option>' ?>
            </select>

            <!-- priority dropdown -->
            <label for="priority" style="padding-left: 25px;">Priority: </label>
            <select id="priority" type="text" style="text-align: center; display: inline;" name="priority">
                <?php
                    //insert current issue value as default
                    echo '<option value="' . $issue["priority"] . '">' . $issue["priority"] . '</option>';

                    //insert the other options (1 - 6)
                    for ($i = 1; $i < 7; $i++) {
                        if (strval($i) != $issue["priority"]) {
                            echo '<option value="' . $i . '">' . $i . '</option>';
                        }
                    }
                ?>
            </select>
            
            <!-- assigned user dropdown, only display the currently assigned user unless being viewed by an admin -->
            <label for="assigned" style="padding-left: 25px;">Assigned To: </label>
            <select id="assigned" style="padding-top: 5px; text-align: center; display: inline;" name="assigned" <?php echo $admin ? "" : 'disabled="true"' ?>>
                <?php
                    //if there is an assigned user, display them
                    if (!empty($issue["user_id"])) {
                        echo '<option value=' . $issue["user_id"] . '>' .
                             trim($issue["f_name"]) . ' ' . trim($issue["l_name"]) .
                             '</option>';
                    } else {
                        //insert default value for dropdown
                        echo '<option value=""></option>';
                    }

                    //check if the user is an admin
                    if ($admin) {
                        //pull all users
                        $users = UserUtility::getAll();

                        //loop over users and populate the dropdown
                        foreach ($users as $user) {
                            if ($issue["user_id"] != $user["user_id"]) {
                                echo '<option value=' . $user["user_id"] . '>' .
                                     trim($user["f_name"]) . ' ' . trim($user["l_name"]) .
                                     '</option>';
                            }
                        }
                    }
                ?>
            </select><br>
            <br>

            <!-- open date -->
            <label for="open_date">Open Date: </label>
            <label id="open_date" type="text" style="padding-top: 5px;"> <?php echo $issue["open_date"] ?> </label>

            <!-- close date -->
            <?php if (!empty($issue["close_date"])) { ?>
                <label for="close_date" style="padding-left: 25px;">Close Date: </label>
                <label id="close_date" type="text" style="padding-top: 5px; display: inline;"> <?php echo $issue["close_date"] ?> </label>
            <?php } ?>
            <br>
            <br>

            <!-- confirm button -->
            <button id="confirm_button" name="confirm" type="submit">
                Confirm
            </button>

            <!-- cancel button -->
            <button id="cancel_button" name="cancel" value="true" type="submit">
                Cancel
            </button>

            <!-- delete button, only display if it as admin user -->
            <?php if ($admin) { ?>
                <button id="delete_button" name="delete" value="true" type="submit">
                    Delete
                </button>
            <?php } ?>
        </form>
    </div>
</html>