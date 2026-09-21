<?php
// Start the session so we know which user is saving the report.
session_start();

// Load the database and login checks before allowing the action.
require_once("../configuration/database.php");
require_once("../configuration/session.php");

// Only process this page when the form was submitted with the save action.
if(isset($_POST['save']))
{
    // Read the submitted report values and convert IDs into integers.
    $crimeTypeID     = intval($_POST['crimeTypeID']);
    $barangayID      = intval($_POST['barangayID']);
    $date_committed  = $_POST['date_committed'];
    $time_committed  = $_POST['time_committed'];
    $latitude        = $_POST['latitude'];
    $longitude       = $_POST['longitude'];
    $address         = trim($_POST['address']);
    $description     = trim($_POST['description']);
    $status          = $_POST['status'];

    // Store the currently logged-in user as the person who reported the crime.
    $reported_by = $_SESSION['userID'];

    // Validate that all required fields are filled before inserting the record.
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

    // Insert a new record into the crime_reports table.
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

    // If the SQL statement fails to prepare, stop and show the database error.
    if(!$stmt)
    {
        die("Prepare failed : " . $conn->error);
    }

    // Bind all values to the prepared statement.
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

    // If the insert succeeds, return to the map page with a success flag.
    if($stmt->execute())
    {
        header("Location: crime_map.php?success=1");
        exit();
    }
    else
    {
        // If the insert fails, redirect back with an error flag.
        header("Location: crime_map.php?error=1");
        exit();
    }

    $stmt->close();
    $conn->close();
}
else
{
    // If someone reaches this page without submitting the form, send them back.
    header("Location: crime_map.php");
    exit();
}
?>