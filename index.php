<?php
// Start the session to see whether the user is already logged in.
session_start();

// If a user session exists, determine where they should go.
if (isset($_SESSION['userID'])) {

    // Send administrators to the admin dashboard.
    if ($_SESSION['role'] == "Administrator") {
        header("Location: admin/dashboard.php");
    }
    // Send police users to the police dashboard.
    elseif ($_SESSION['role'] == "Police") {
        header("Location: police/dashboard.php");
    }
    // If the stored role is unknown, clear the session and send them to login.
    else {
        session_destroy();
        header("Location: login.php");
    }

    exit();
}

// If no session is found, redirect the user to the login page.
header("Location: login.php");
exit();
?>