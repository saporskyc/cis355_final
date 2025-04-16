<!--
    Author: Calob Saporsky
    Description: issue creation page
                 creates a new issue and redirects to edit_issue.php on success
-->

<?php
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
    }

    //check if the user is an admin
    $admin = false;
    if ($_SESSION['admin'] == 'Y') {
        $admin = true;
    }

    //check if cancel was clicked
    if (isset($_POST["cancel"])) {
        //clear post, redirect to home page
        $_POST = array();
        header('Location: home.php');
    }

    //input validation vars
    $org_error = false;
    $descr_error = false;
    $priority_error = false;
    if (!empty($_POST)) {
        //bool to check before carrying out add issue operation
        $proceed = true;

        //check organization field for input
        if (trim($_POST["org"]) == "") {
            $proceed = false;
            $org_error = true;
        }

        //check descr1/s_descr for real input
        if (trim($_POST["descr1"]) == "") {
            $proceed = false;
            $descr_error = true;
        }

        //check priority for real input
        if (trim($_POST["priority"]) == "") {
            $proceed = false;
            $priority_error = true;
        }

        //check whether or not to proceed with issue add
        if ($proceed) {
            //check whether or not to default assigned user
            $assigned = '';
            if ($admin) {
                $assigned = $_POST["assigned"];
            } else {
                $assigned = $_SESSION["user_id"];
            }

            //add new issue
            $new_id = IssueUtility::newIssue($assigned, $_POST["org"], $_POST["descr1"], $_POST["descr2"], $_POST["priority"]);
            
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
            <input id="org" type="text" style="padding-top: 5px;" name="org" value="<?php echo isset($_POST["org"]) ? $_POST["org"] : ""; ?>"><br>
            <?php if ($org_error) {echo '<label style="color: red;"> Please enter a valid organization</label><br>';} ?>
            <br>

            <!-- short description -->
            <label for="descr1">Description: </label>
            <input id="descr1" type="text" style="padding-top: 5px;" name="descr1" value="<?php echo isset($_POST["descr1"]) ? $_POST["descr1"] : ""; ?>"><br>
            <?php if ($descr_error) {echo '<label style="color: red;"> Please enter a valid description</label><br>';} ?>
            <br>
 
            <!-- long description -->
            <textarea id="descr2" style="width: 625px; height: 150px;" rows="8" cols="35" name="descr2" placeholder="More Details"><?php if (isset($_POST["descr2"])) {echo $_POST["descr2"];} ?></textarea><br>
            <br>

            <!-- priority dropdown -->
            <?php
                if ($priority_error) {
                    echo '<label for="priority" style="color: red;"> Please enter a valid priority</label>';
                } else {
                    echo '<label for="priority">Priority: </label>';
                }
            ?>
            <select id="priority" type="text" style="text-align: center;" name="priority">
                <?php
                    if (isset($_POST["priority"])) {
                        //a selection has been made, make it the first option
                        echo '<option value="'. $_POST["priority"] .'">'. $_POST["priority"] .'</option>';
                        
                        //create the rest of the options
                        for ($i = 1; $i < 7; $i++) {
                            if ($i != $_POST["priority"]) {
                                echo '<option value="' . $i . '">' . $i . '</option>';
                            }
                        }
                    } else {
                        //fresh page, create default list
                        echo '<option value=""></option>';
                        for ($i = 1; $i < 7; $i++) {
                            echo '<option value="' . $i . '">' . $i . '</option>';
                        }
                    }
                ?>
            </select>
            
            <!-- assigned user dropdown, only display if the user is an admin, otherwise default this to the user creating the issue -->
            <?php if ($admin) { ?>
                <label for="assigned" style="padding-left: 25px;">Assigned To: </label>
                <select id="assigned" style="padding-top: 5px; text-align: center; display: inline;" name="assigned">
                    <?php
                        if (isset($_POST["assigned"])) {
                            //a selection has already been made, make sure it is the first in the list of options
                            //loop over potential options and locate what was selected
                            foreach ($users as $user) {
                                if ($user["user_id"] == $_POST["assigned"]) {
                                    echo '<option value=' . $user["user_id"] . '>' . trim($user["f_name"]) . ' ' . trim($user["l_name"]) . '</option>';
                                    break;
                                }
                            }

                            //output the rest of the options
                            foreach ($users as $user) {
                                if ($user["user_id"] != $_POST["assigned"]) {
                                    echo '<option value=' . $user["user_id"] . '>' . trim($user["f_name"]) . ' ' . trim($user["l_name"]) . '</option>';
                                }
                            }
                            echo '<option value="">Unassigned</option>';
                        } else {
                            //fresh page, create default list
                            echo '<option value="">Unassigned</option>';
                            foreach ($users as $user) {
                                echo '<option value=' . $user["user_id"] . '>' . trim($user["f_name"]) . ' ' . trim($user["l_name"]) . '</option>';
                            }
                        }
                    ?>
                </select><br>
            <?php } ?>
            <br>
            <br>

            <!-- confirm button -->
            <button id="confirm_button" name="confirm" type="submit">
                Create
            </button>

            <!-- cancel button -->
            <button id="cancel_button" name="cancel" value="true" type="submit">
                Cancel
            </button>
        </form>
    </div>
</html>