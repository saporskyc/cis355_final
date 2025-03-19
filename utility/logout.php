<!--
    Author: Calob Saporsky
    Description: this file handles user logout
-->

<?php
    //start session
    session_start();

    //destroy the active session
    session_destroy();

    //redirect to landing page
    header('Location: ../launch_page.php');
?>