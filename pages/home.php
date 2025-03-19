<!--
    Author: Calob Saporsky
    Description: application home page
                 displays all current issues and redirects to either the issue details page, or
                 the user management page
-->

<?php
    echo "hello from home.php";
    //import necessary file
    require "../utility/util_issue.php";

    //start session
    session_start();

    //check login status
?>

<!DOCTYPE html>
<html lang=en>
    <!-- page body -->
    <div style="text-align: center;">
        <!-- current user info -->
        <h3>
            <?php
                //pull current user

                //display name and email
            ?>
        </h3>
        <br>

        <!-- logout button -->
        <form action="../utility/logout.php">
            <button type="submit">
                Logout
            </button>
        </form>

        <!-- admin buttons -->

        <!-- issues display -->
    </div>
</html>