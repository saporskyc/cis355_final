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

    //pull all issues
    $issues = IssueUtility::getAll();

    //sort the issues
    $issues = IssueUtility::sortIssues($_SESSION["user_id"], $issues);
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
            <form style="display: inline; padding-right: 75px;" action="../utility/logout.php">
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

            <!-- new issue button -->
            <form style="display: inline;" action="">
                <button type="submit">
                    New Issue
                </button>
            </form>

            <!-- user profile button -->
            <form style="display: inline;" method="GET" action="edit_user.php?page_form=">
                <button name="page_form" type="submit" value="my_profile">
                    My Profile
                </button>
            </form>
        </row>

        <!-- spacing -->
         <h3> </h3>

        <!-- issues display -->
        <table style="border: 1px solid black; width: 60%; margin: 0px auto;">
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
                //loop over all users and populate table with data
                $num_issues = count($issues);
                $iteration = 1;
                foreach ($issues as $issue) {
                    echo '<tr style="text-align: center;">';
                        if ($iteration != $num_issues) {
                            echo '<td style="border-bottom: 1px solid black;">' . trim($issue["organization"]) . "</td>";
                            echo '<td style="border-bottom: 1px solid black;">' . trim($issue["s_descr"]) . "</td>";
                            echo '<td style="border-bottom: 1px solid black;">' . trim($issue["open_date"]) . "</td>";
                            echo '<td style="border-bottom: 1px solid black;">' . trim($issue["status"]) . "</td>";
                            echo '<td style="border-bottom: 1px solid black;">' . trim($issue["priority"]) . "</td>";
                            echo '<td style="border-bottom: 1px solid black;">' . trim($issue["f_name"]) . " " . trim($issue["l_name"]) . "</td>";
                            echo '<td style="border-bottom: 1px solid black;">' .
                                    '<form style="display: inline; padding-right: 5px;" method="GET" action="edit_user.php?editing_id=' . $user["user_id"] .'">' .
                                        '<button name="editing_id" value="' . $user["user_id"] . '">' .
                                            'Manage' .
                                        '</button>' .
                                    '</form>' .
                                 "</td>";
                        } else {
                            echo '<td>' . trim($issue["organization"]) . "</td>";
                            echo '<td>' . trim($issue["s_descr"]) . "</td>";
                            echo '<td>' . trim($issue["open_date"]) . "</td>";
                            echo '<td>' . trim($issue["status"]) . "</td>";
                            echo '<td>' . trim($issue["priority"]) . "</td>";
                            echo '<td>' . trim($issue["f_name"]) . " " . trim($issue["l_name"]) . "</td>";
                            echo '<td>' .
                                    '<form style="display: inline; padding-right: 5px;" method="GET" action="edit_user.php?editing_id=' . $user["user_id"] .'">' .
                                        '<button name="editing_id" value="' . $user["user_id"] . '">' .
                                            'Manage' .
                                        '</button>' .
                                    '</form>' .
                                 "</td>";
                        }
                    echo "<tr>";
                    $iteration++;
                }
            ?>
        </table>
    </div>
</html>