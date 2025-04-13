<!--
    Author: Calob Saporsky
    Description: user management page
                 displays all current users
                 allows admin users to add/edit/delete other users, including an option to make them admin users
                 navigates to edit_user.php, new_user.php, or home.php 
-->

<?php
    echo "hello from user_management.php<br>";
    //import necessary file
    require "../utility/util_user.php";

    //start session
    session_start();

    //check login status
    if (!isset($_SESSION["user_id"]) || $_SESSION["admin"] != "Y") {
        //invalid access, destroy session and redirect
        session_destroy();
        header('Location: ../launch_page.php');
        exit(0);
    }

    //check if delete was clicked
    if (isset($_POST["delete"])) {
        //delete the selected user
        UserUtility::deleteUser($_POST["delete"]);
    }

    //pull all users
    $users = UserUtility::getAll();
?>

<!DOCTYPE html>
<html lang=en>
    <!-- margin -->
    <h3> </h3>

    <!-- page body -->
    <div style="text-align: center;">
        <!-- buttons -->
        <row>
            <!-- logout button -->
            <form style="display: inline; padding-right: 45px" action="../utility/logout.php">
                <button type="submit">
                    Logout
                </button>
            </form>

            <!-- home button -->
            <form style="display: inline; padding-right: 45px" action="home.php">
                <button type="submit">
                    Home
                </button>
            </form>

            <!-- new user button -->
            <form style="display: inline;" action="new_user.php">
                <button type="submit">
                    New User
                </button>
            </form>
        </row>

        <!-- spacing -->
        <h3> </h3>

        <!-- users table -->
        <table style="border: 1px solid black; width: 60%; margin: 0px auto; border-collapse: collapse">
            <!-- column names -->
            <thead>
                <tr>
                    <th style="border-bottom: 1px solid black;">Name</th>
                    <th style="border-bottom: 1px solid black;">Admin</th>
                    <th style="border-bottom: 1px solid black;">Email</th>
                    <th style="border-bottom: 1px solid black;">Actions</th>
                </tr>
            </thead>

            <!-- rows -->
            <?php
                //loop over all users and populate table with data
                $last_user = array_key_last($users);
                $interval = 0;
                $bg_style = '';
                foreach ($users as $user) {
                    $interval % 2 != 0 ? $bg_style = 'background-color:rgb(191, 204, 204);' : $bg_style = '';
                    echo '<tr style="text-align: center;'. $bg_style .'">';
                        //full name
                        echo '<td>' . trim($user["f_name"]) . " " . trim($user["l_name"]) . "</td>";
                        //admin
                        echo '<td>' . trim($user["admin"]) . "</td>";
                        //email
                        echo '<td>' . trim($user["email"]) . "</td>";
                        //edit and delete buttons
                        echo '<td>' .
                                '<form style="display: inline; padding-right: 5px;" method="GET" action="edit_user.php?id=' . $user["user_id"] .'">' .
                                    '<button name="id" value="' . $user["user_id"] . '">' .
                                        'Edit' .
                                    '</button>' .
                                '</form>' .
                                '<form style="display: inline;" method="POST" action="user_management.php">' .
                                    '<button name="delete" value="' . $user["user_id"] . '">' .
                                        'Delete' .
                                    '</button>' .
                                '</form>' .
                             '</td>';
                    echo "<tr>";
                    $interval++;
                }
            ?>
        </table>
    </div>
</html>