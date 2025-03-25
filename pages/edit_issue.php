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
        //clear post and get, redirect to home page
        $_POST = array();
        $_GET = array();
        header('Location: home.php');
    }

    //check if delete was clicked
    if (isset($_POST["delete"])) {
        //delete the issue
        IssueUtility::deleteIssue($_GET["editing_id"]);

        //clear post and get, redirect to home page
        $_POST = array();
        $_GET = array();
        header('Location: home.php');
    }

    //get the issue by id
    $issue = IssueUtility::getOne($_GET["editing_id"]);

    //check for values in post
    if (!empty($_POST)) {
        //init validation vars
        $edits = array();
        $update = true;
        $org_error = false;         //these three are the only required fields that accept input
        $descr_error = false;       //in the form of user-entered text
        $priority_error = false;    //

        //check organization field for modified input
        if (trim($_POST["org"]) != trim($issue["organization"])) {
            $edits["org"] = $_POST["org"];
            $update = true;
        }

        //check descr1/s_descr for modified input
        if (trim($_POST["descr1"]) != trim($issue["s_descr"])) {
            $edits["descr1"] = $_POST["descr1"];
            $update = true;
        }

        //check descr2/l_descr for modified input
        if (trim($_POST["descr2"]) != trim($issue["l_descr"])) {
            $edits["descr2"] = $_POST["descr2"];
            $update = true;
        }

        //check status for modified input
        if (trim($_POST["status"]) != trim($issue["status"])) {
            $edits["status"] = $_POST["status"];
            $update = true;
        }

        //check priority for modified input
        if (trim($_POST["priority"]) != trim($issue["priority"])) {
            $edits["priority"] = $_POST["priority"];
            $update = true;
        }

        //check assigned user for modified input
        if (trim($_POST["assigned"]) != trim($issue["user_id"])) {
            $edits["assigned"] = $_POST["assigned"];
            $update = true;
        }

        //check whether or not to proceed with update
        if ($update) {
            //update the issue
            $success = IssueUtility::updateIssue($_GET["editing_id"], $edits);
            
            //check operation result
            if ($success) {
                //clear post
                $_POST = array();

                //pull the issue again to refresh the information
                $issue = IssueUtility::getOne($_GET["editing_id"]);
            }
        }
    }

    //check what the current issue status is and set a variable to the other option
    $status2 = $issue["status"] == "OPEN" ? "CLOSED" : "OPEN";

    //pull the issue's associated comments
?>

<!DOCTYPE html>
<html lang=en>
    <!-- page body -->
    <div style="text-align: center;">
        <!-- issue information form -->
        <form action= <?php echo '"edit_issue.php?editing_id=' . $_GET["editing_id"] . '"'; ?> method="post">
            <!-- organization -->
            <label for="org">Organization: </label>
            <input id="org" type="text" style="padding-top: 5px;" name="org" value=" <?php echo $issue["organization"]; ?> "><br>
            <br>

            <!-- short description -->
            <label for="descr1">Description: </label>
            <input id="descr1" type="text" style="padding-top: 5px;" name="descr1" value=" <?php echo $issue["s_descr"]; ?> "><br>
            <br>
 
            <!-- long description -->
            <textarea id="descr2" style="width: 625px; height: 150px;" rows="8" cols="35" name="descr2" placeholder="More Details"><?php if ($issue["l_descr"] != null) { echo trim($issue["l_descr"]); } ?></textarea><br>
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
            
            <!-- assigned user dropdown, only display the issue's assigned user unless being viewed by an admin -->
            <label for="assigned" style="padding-left: 25px;">Assigned To: </label>
            <select id="assigned" style="padding-top: 5px; text-align: center; display: inline;" name="assigned" <?php echo $admin ? "" : 'disabled="true"' ?>>
                <?php
                    //check for an assigned user
                    if (!empty($issue["user_id"])) {
                        //display the assigned user
                        echo '<option value=' . $issue["user_id"] . '>' .
                             trim($issue["f_name"]) . ' ' . trim($issue["l_name"]) .
                             '</option>';
                        
                        //insert default value for dropdown
                        echo '<option value="NULL">Unassigned</option>';
                    } else {
                        //insert default value for dropdown
                        echo '<option value="NULL">Unassigned</option>';
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

        <!-- comment display section -->
    </div>
</html>