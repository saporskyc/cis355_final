<!--
    Author: Calob Saporsky
    Description: application home page
                 displays all current issues and allows navigation to issues assigned to user,
                 issue creation, issue details, user profile for self-editing, and user
                 management screen if the user is an admin
-->

<?php
    echo "hello from home.php<br>";
    //import necessary file
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

    //pull user currently logged in
    $user = UserUtility::getOne($_SESSION["user_id"]);

    $issues = null;
    if (!isset($_POST["view_type"])) {
        //pull all open issues if the user is an admin, otherwise pull issues assigned to the current user
        if ($issues = $_SESSION["admin"] == "Y") {
            $issues = IssueUtility::getOpen();
            $_POST["view_type"] = "open";
        } else {
            $issues = IssueUtility::getAssigned($_SESSION["user_id"]);
            $_POST["view_type"] = "assigned";
        }
    } else if ($_POST["view_type"] == "closed") {
        //pull the closed issues
        $issues = IssueUtility::getClosed();
    } else if ($_POST["view_type"] == "open") {
        //pull only open issues
        $issues = IssueUtility::getOpen();
    } else if ($_POST["view_type"] == "assigned") {
        //pull the user's assigned issues
        $issues = IssueUtility::getAssigned($_SESSION["user_id"]);
    }
?>

<!DOCTYPE html>
<html lang=en>
    <!-- page body -->
    <div style="text-align: center;">
        <!-- current user info -->
        <h3>
            <?php
                //display name and email
                echo $user["f_name"] . " " . $user["l_name"] . " | " . $user["email"];
            ?>
        </h3>
        <br>

        <!-- buttons -->
        <row>
            <!-- logout button -->
            <form style="display: inline; padding-right: 15px;" action="../utility/logout.php">
                <button type="submit">
                    Logout
                </button>
            </form>
            
            <!-- user management, admin only -->
            <?php if ($user["admin"] == "Y") {?>
                <form style="display: inline;" action="user_management.php">
                    <button type="submit">
                        User Management
                    </button>
                </form>
            <?php } ?>

            <!-- user profile button -->
            <form style="display: inline; padding-right: 75px;" method="GET" action="edit_user.php?page_form=">
                <button name="page_form" type="submit" value="my_profile">
                    My Profile
                </button>
            </form>
            
            <!-- new issue button -->
            <form style="display: inline;" action="new_issue.php">
                <button type="submit">
                    New Issue
                </button>
            </form>

            <!-- table filtering -->
            <!-- view user's assigned issues -->
            <form style="display: inline; padding-left: 75px;" action="home.php" method="POST">
                <button type="submit" name="view_type" value="assigned">
                    My Issues
                </button>
            </form>

            <!-- view open issues -->
            <form style="display: inline;" action="home.php" method="POST">
                <button type="submit" name="view_type" value="open">
                    Open Issues
                </button>
            </form>

            <!-- view closed issues -->
            <form style="display: inline;" action="home.php" method="POST">
                <button type="submit" name="view_type" value="closed">
                    Closed Issues
                </button>
            </form>
        </row>

        <!-- spacing -->
         <h3> </h3>

        <!-- issues display -->
        <table style="border: 1px solid black; width: 60%; margin: 0px auto; border-collapse: collapse;">
            <!-- column names -->
            <thead>
                <tr>
                    <th style="border-bottom: 1px solid black;">Organization</th>
                    <th style="border-bottom: 1px solid black;">Description</th>
                    <th style="border-bottom: 1px solid black;">Opened</th>
                    <th style="border-bottom: 1px solid black;">Status</th>
                    <th style="border-bottom: 1px solid black;">Priority</th>
                    <th style="border-bottom: 1px solid black;">Assigned</th>
                    <th style="border-bottom: 1px solid black;">Actions</th>
                </tr>
            </thead>

            <!-- rows -->
            <?php
                //loop over all issues and populate table with data
                $num_issues = count($issues);
                if ($num_issues > 0) {
                    $iteration = 1;
                    $bg_style = '';
                    foreach ($issues as $issue) {
                        $iteration % 2 == 0 ? $bg_style = 'background-color:rgb(191, 204, 204);' : $bg_style = '';
                        echo '<tr style="text-align: center;'. $bg_style .'">';
                            //organization
                            echo '<td>' . trim($issue["organization"]) . "</td>";
                            //short description
                            echo '<td>' . trim($issue["s_descr"]) . "</td>";
                            //open date
                            echo '<td>' . trim($issue["open_date"]) . "</td>";
                            //status
                            echo '<td>' . trim($issue["status"]) . "</td>";
                            //priority
                            echo '<td>' . trim($issue["priority"]) . "</td>";
                            //full name
                            echo '<td>' . trim($issue["f_name"]) . " " . trim($issue["l_name"]) . "</td>";
                            //manage button
                            echo '<td>' .
                                    '<form style="display: inline; padding-right: 5px;" method="GET" action="edit_issue.php?id=' . $issue["issue_id"] .'">' .
                                        '<button name="id" value="' . $issue["issue_id"] . '">' .
                                            'Manage' .
                                        '</button>' .
                                    '</form>' .
                                '</td>';
                        echo "<tr>";
                        $iteration++;
                    }
                } else {
                    echo '<tr style="text-align: center;"><td>No ' . $_POST["view_type"] . ' issues to display</td></tr>';
                }
            ?>
        </table>
    </div>
</html>