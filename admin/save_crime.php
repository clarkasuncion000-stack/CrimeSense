<?php
session_start();

require_once("../configuration/database.php");
require_once("../configuration/session.php");

if(isset($_POST['save']))
{
    $crimeTypeID     = intval($_POST['crimeTypeID']);
    $barangayID      = intval($_POST['barangayID']);
    $date_committed  = $_POST['date_committed'];
    $time_committed  = $_POST['time_committed'];
    $latitude        = $_POST['latitude'];
    $longitude       = $_POST['longitude'];
    $address         = trim($_POST['address']);
    $description     = trim($_POST['description']);
    $status          = $_POST['status'];

    // Logged-in user
    $reported_by = $_SESSION['userID'];

    // Validation
    if(
        empty($crimeTypeID) ||
        empty($barangayID) ||
        empty($date_committed) ||
        empty($time_committed) ||
        empty($latitude) ||
        empty($longitude) ||
        empty($address) ||
        empty($description) ||
        empty($status)
    )
    {
        echo "<script>
                alert('Please complete all required fields.');
                window.history.back();
              </script>";
        exit();
    }

    $sql = "INSERT INTO crime_reports
    (
        crimeTypeID,
        barangayID,
        date_committed,
        time_committed,
        latitude,
        longitude,
        address,
        description,
        status,
        reported_by
    )
    VALUES
    (
        ?,?,?,?,?,?,?,?,?,?
    )";

    $stmt = $conn->prepare($sql);

    if(!$stmt)
    {
        die("Prepare failed : ".$conn->error);
    }

    $stmt->bind_param(
        "iissddsssi",
        $crimeTypeID,
        $barangayID,
        $date_committed,
        $time_committed,
        $latitude,
        $longitude,
        $address,
        $description,
        $status,
        $reported_by
    );

    if($stmt->execute())
    {
        header("Location: crime_map.php?success=1");
        exit();
    }
    else
    {
        header("Location: crime_map.php?error=1");
        exit();
    }

    $stmt->close();
    $conn->close();
}
else
{
    header("Location: crime_map.php");
    exit();
}
?>