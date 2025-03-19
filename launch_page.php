<!--
    Author: Calob Saporsky
    Description: landing page of application, redirects to either the
                 login page or the registration page
-->

<?php
    echo "hello from launch_page.php";
?>

<!DOCTYPE html>
<html lang=en>
    <!-- page body -->
    <div style="text-align: center;">
        <!-- header text -->
        <h3>
            CIS355 Final Project - Author: Calob Saporsky
        </h3>
        <br>

        <!-- login button -->
        <form action="pages/login.php">
            <button typ="submit">
                Login
            </button>
        </form>
        
        <!-- new user button -->
        <form action="pages/new_user.php">
            <button type="submit">
                New User
            </button>
        </form>
    </div>
</html>