<!--
    Author: Calob Saporsky
    Description: application home page
                 displays all current issues and redirects to either the issue details page, or
                 the user management page
-->

<?php
    //import necessary file
    require "../utility/util_issue.php";

    //start session
    session_start();
    echo "hello from home.php";

    //check login status
?>

<!DOCTYPE html>
<html lang=en>
    <!-- page body -->
    <div style="text-align: center;">
        <!-- logout button -->
        <form action="../utility/logout.php">
            <button type="submit">
                Logout
            </button>
        </form>
    </div>
</html>