<?php
// Start the session so we can set login details after successful authentication.
session_start();

// Connect to the database using the project configuration file.
require_once("configuration/database.php");

// Read the submitted login form values and trim whitespace.
$username = trim($_POST['username']);
$password = trim($_POST['password']);

// Search for the user with the entered username and only if the account is active.
$sql = "SELECT * FROM users
        WHERE username=?
        AND status='Active'
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();

// Verify that exactly one matching active user exists.
if($result->num_rows == 1){

    // Fetch the user record into an associative array.
    $user = $result->fetch_assoc();

    // Check whether the entered password matches the hashed password stored in the database.
    if(password_verify($password, $user['password'])){

        // Save the user's key information in the session for later pages.
        $_SESSION['userID']   = $user['userID'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['role']     = $user['role'];

        // Save a login activity log so the system can track user actions.
        $activity = "Logged in";

        $log = $conn->prepare("INSERT INTO activity_logs(userID, activity)
                             VALUES(?, ?)");
        $log->bind_param("is", $user['userID'], $activity);
        $log->execute();

        // Redirect the user based on their role.
        if($user['role'] == "Administrator"){
            header("Location: admin/dashboard.php");
        } else {
            header("Location: police/dashboard.php");
        }

        exit();
    }
}

// If the credentials are invalid, return to the login page with an error flag.
header("Location: login.php?error=1");
exit();

?>