<!--
    Author: Calob Saporsky
    Description: issue edit/management page
                 displays an issue's information and all of its associated comments
                 also allows an admin user to delete the issue if it is still open
                 navigates to home.php
-->

<?php
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
        IssueUtility::deleteIssue($_GET["id"]);

        //clear post and get, redirect to home page
        $_POST = array();
        $_GET = array();
        header('Location: home.php');
    }

    //check if post_comment was clicked
    $comment_error = false;
    if (isset($_POST["post_comment"])) {
        if (trim($_POST["new_comment"]) == "") {
            //invalid input received for new_comment
            $comment_error = true;
        } else {
            //add the new comment
            CommentUtility::newComment($_GET["id"], $_SESSION["user_id"], $_POST["new_comment"]);
        }

        //clear post
        $_POST = array();
    }

    //get the issue by id
    $issue = IssueUtility::getOne($_GET["id"]);

    //check if admin user
    $admin = false;
    if ($_SESSION["admin"] == "Y") {
        $admin = true;
    }

    //if admin, pull users for dropdown
    $users = null;
    if ($admin) {
        $users = UserUtility::getAll();
    }

    //check if the user is the one assigned to the issue
    $my_issue = false;
    if ($_SESSION["user_id"] == $issue["user_id"]) {
        $my_issue = true;
    }

    //check whether or not to disable the input fields
    $disable = false;
    if ((!$admin && !$my_issue) || $issue["status"] == "CLOSED") {
        $disable = true;
    }

    //input validation vars, these are the only two required fields that accept user text input
    $org_error = false;
    $descr_error = false;
    if (!empty($_POST)) {
        //init operation vars
        $edits = array();
        $update = true;

        //check organization field for modified input
        if (trim($_POST["org"]) != trim($issue["organization"])) {
            if (trim($_POST["org"]) == "") {
                $org_error = true;
                $update = false;
            } else {
                $edits["org"] = $_POST["org"];
                $update = true;
            }
        }

        //check descr1/s_descr for modified input
        if (trim($_POST["descr1"]) != trim($issue["s_descr"])) {
            if (trim($_POST["descr1"]) == "") {
                $descr_error = true;
                $update = false;
            } else {
                $edits["descr1"] = $_POST["descr1"];
                $update = true;
            }
        }

        //check descr2/l_descr for modified input
        if (trim($_POST["descr2"]) != trim($issue["l_descr"])) {
            $edits["descr2"] = $_POST["descr2"];
            $update = true;
        }

        //check status for modified input
        if ($_POST["status"] != $issue["status"]) {
            //make sure status has a valid value
            if ($_POST["status"] != "OPEN" || $_POST["status"] != "CLOSED") {
                $update = false;
            } else {
                $edits["status"] = $_POST["status"];
                $update = true;
            }
        }

        //check priority for modified input
        if ($_POST["priority"] != $issue["priority"]) {
            //make sure priority has a valid value
            if (intval($_POST["priority"]) < 1 || intval($_POST["priority"]) > 6) {
                $update = false;
            } else {
                $edits["priority"] = $_POST["priority"];
                $update = true;
            }
        }

        //check assigned user for modified input
        if ($_SESSION["admin"] == "Y" && $_POST["assigned"] != $issue["user_id"]) {
            $edits["assigned"] = $_POST["assigned"];
            $update = true;
        }

        //check whether or not to proceed with update
        if ($update) {
            //update the issue
            $success = IssueUtility::updateIssue($_GET["id"], $edits);
            
            //check operation result
            if ($success) {
                //clear post
                $_POST = array();

                //pull the issue with updated information
                $issue = IssueUtility::getOne($_GET["id"]);
            }
        }
    }

    //check what the current issue status is and set a variable to the other option
    $status2 = $issue["status"] == "OPEN" ? "CLOSED" : "OPEN";

    //pull the issue's associated comments
    $comments = CommentUtility::getAssociated($_GET["id"]);
?>

<!DOCTYPE html>
<html lang=en>
    <!-- page body -->
    <div style="text-align: center;">
        <!-- issue information form, disable all fields unless the user is the one assigned or an admin -->
        <form action= <?php echo '"edit_issue.php?id=' . $_GET["id"] . '"'; ?> method="post">
            <!-- organization -->
            <label for="org">Organization: </label>
            <input id="org" type="text" style="padding-top: 5px;" name="org" value=" <?php echo $issue["organization"]; ?> " <?php if ($disable) {echo 'disabled="true"';} ?>><br>
            <?php if ($org_error) {echo '<label style="color: red;">An invalid organization was entered</label><br>';} ?>
            <br>

            <!-- short description -->
            <label for="descr1">Description: </label>
            <input id="descr1" type="text" style="padding-top: 5px;" name="descr1" value=" <?php echo $issue["s_descr"]; ?> " <?php if ($disable) {echo 'disabled="true"';} ?>><br>
            <?php if ($descr_error) {echo '<label style="color: red;">An invalid description was entered</label><br>';} ?>
            <br>
 
            <!-- long description -->
            <textarea id="descr2" style="width: 625px; height: 150px;" rows="8" cols="35" name="descr2" placeholder="More Details" <?php if ($disable) {echo 'disabled="true"';} ?>><?php if ($issue["l_descr"] != null) { echo trim($issue["l_descr"]); } ?></textarea><br>
            <br>

            <!-- status -->
            <label for="status">Status: </label>
            <select id="status" type="text" style="padding-top: 5px;" name="status" <?php if ($disable) {echo 'disabled="true"';} ?>>
                <?php echo '<option value="' . $issue["status"] . '">' . $issue["status"] . '</option>' ?>
                <?php echo '<option value="' . $status2 . '">' . $status2 . '</option>' ?>
            </select>

            <!-- priority dropdown -->
            <label for="priority" style="padding-left: 25px;">Priority: </label>
            <select id="priority" type="text" style="text-align: center; display: inline;" name="priority" <?php if ($disable) {echo 'disabled="true"';} ?>>
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
            
            <!-- assigned user dropdown. if the issue is closed or the user is not an admin only display the issue's assigned user -->
            <label for="assigned" style="padding-left: 25px;">Assigned To: </label>
            <select id="assigned" style="padding-top: 5px; text-align: center; display: inline;" name="assigned"
            <?php if (!$admin || $issue["status"] == "CLOSED") {echo 'disabled="true"';} ?>>
                <?php
                    //check for an assigned user
                    if (!empty($issue["user_id"])) {
                        //display the assigned user
                        echo '<option value=' . $issue["user_id"] . '>' .
                             trim($issue["f_name"]) . ' ' . trim($issue["l_name"]) .
                             '</option>';
                    } else {
                        //insert default value for dropdown
                        echo '<option value="NULL">Unassigned</option>';
                    }

                    //check if the user is an admin and the issue is open
                    if ($admin && $issue["status"] == "OPEN") {
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
            <label for="open_date"><b>Open Date: </b></label>
            <label id="open_date" type="text" style="padding-top: 5px;"> <?php echo $issue["open_date"] ?> </label>

            <!-- close date -->
            <?php if (!empty($issue["close_date"])) { ?>
                <label for="close_date" style="padding-left: 25px;"><b>Close Date: </b></label>
                <label id="close_date" type="text" style="padding-top: 5px; display: inline;"> <?php echo $issue["close_date"] ?> </label>
            <?php } ?>
            <br>
            <br>

            <!-- confirm button -->
            <?php if (!$disable) { ?>
                <button id="confirm_button" name="confirm" type="submit">
                    Save Issue Changes
                </button>
            <?php } ?>

            <!-- cancel button -->
            <button id="cancel_button" name="cancel" value="true" type="submit">
                Cancel
            </button>

            <!-- delete button, only display if it as admin user and the issue is not closed -->
            <?php if ($admin && $issue["status"] != 'CLOSED') { ?>
                <button id="delete_button" name="delete" value="true" type="submit">
                    Delete
                </button>
            <?php } ?>
        </form>
        <br>

        <!-- comment form -->
        <form action= <?php echo '"edit_issue.php?id=' . $_GET["id"] . '"'; ?> method="post">
            <!-- loop through existing comments and display them -->
            <?php
                foreach ($comments as $comment) {
                    echo $comment["f_name"] . ' ' . $comment["l_name"] . ' -- ' . $comment["posted_date"] . '<br>';
                    echo $comment["comment"] . '<br>';
                    echo '<br>';
                }
            ?>

            <!-- new comment text area, only display this if issue status is open -->
            <?php if ($issue["status"] == "OPEN") { ?>
                <textarea id="new_comment" style="width: 625px; height: 115px;" rows="8" cols="35" name="new_comment" placeholder="New Comment"></textarea><br>
                <?php if ($comment_error) {echo '<label style="color: red;">An invalid comment was entered</label><br>';} ?>
                <br>

                <!-- post comment button -->
                <button id="post_comment" name="post_comment" value="true" type="submit">
                    Post Comment
                </button>
            <?php } ?>
        </form>
    </div>
</html>