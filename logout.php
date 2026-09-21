<?php
// Start the session so we can clear the current user's session data.
session_start();

// Destroy all session variables such as userID, fullname, and role.
session_destroy();

// Send the user back to the login page after logout.
header("Location: login.php");

exit();
?>