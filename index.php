<?php
session_start();

if (isset($_SESSION['userID'])) {

    // Redirect based on user role
    if ($_SESSION['role'] == "Administrator") {
        header("Location: admin/dashboard.php");
    } elseif ($_SESSION['role'] == "Police") {
        header("Location: police/dashboard.php");
    } else {
        session_destroy();
        header("Location: login.php");
    }

    exit();
}

// If not logged in, go to login page
header("Location: login.php");
exit();
?>