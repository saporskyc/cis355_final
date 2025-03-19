<!--
    Author: Calob Saporsky
    Description: application home page
                 displays all current issues and redirects to either the issue details page, or
                 the user management page
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
                <form style="display: inline;" action="">
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

            <!-- user's issues button -->
            <form style="display: inline;" action="">
                <button type="submit">
                    My Issues
                </button>
            </form>

            <!-- user profile button -->
            <form style="display: inline;" action="">
                <button type="submit">
                    My Profile
                </button>
            </form>
        </row>

        <!-- issues display -->
    </div>
</html>