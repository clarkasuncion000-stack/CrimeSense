<?php
// Start the session so we can check whether the current user is authenticated.
session_start();

// If no userID is stored in the session, the user is not logged in.
// In that case, redirect them to the login page and stop processing the page.
if(!isset($_SESSION['userID'])){
    header("Location: ../login.php");
    exit();
}
?>