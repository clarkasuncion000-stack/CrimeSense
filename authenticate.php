<?php

session_start();

require_once("configuration/database.php");

$username = trim($_POST['username']);
$password = trim($_POST['password']);

$sql = "SELECT * FROM users
        WHERE username=?
        AND status='Active'
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s",$username);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows==1){

    $user = $result->fetch_assoc();

    if(password_verify($password,$user['password'])){

        $_SESSION['userID']   = $user['userID'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['role']     = $user['role'];

        // Activity Log
        $activity="Logged in";

        $log=$conn->prepare("INSERT INTO activity_logs(userID,activity)
                             VALUES(?,?)");
        $log->bind_param("is",$user['userID'],$activity);
        $log->execute();

        if($user['role']=="Administrator"){
            header("Location: admin/dashboard.php");
        }else{
            header("Location: police/dashboard.php");
        }

        exit();

    }

}

header("Location: login.php?error=1");
exit();

?>