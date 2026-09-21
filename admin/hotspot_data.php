<?php
// Connect to the database so we can read the crime coordinates.
require_once("../configuration/database.php");

// Return the result as JSON for the JavaScript heatmap.
header("Content-Type: application/json");

// Build optional filters for crime type and date range.
$where = [];

if(!empty($_GET['crimeType']))
{
    $where[] = "crimeTypeID=" . intval($_GET['crimeType']);
}

if(!empty($_GET['from']))
{
    $where[] = "date_committed >= '" . mysqli_real_escape_string($conn, $_GET['from']) . "'";
}

if(!empty($_GET['to']))
{
    $where[] = "date_committed <= '" . mysqli_real_escape_string($conn, $_GET['to']) . "'";
}

// Start with the base query to fetch all crime coordinates.
$sql = "SELECT latitude, longitude
        FROM crime_reports";

if(count($where))
{
    $sql .= " WHERE " . implode(" AND ", $where);
}

$query = mysqli_query($conn, $sql);

$data = [];

// Convert each record into the format expected by the Leaflet heatmap plugin.
while($row = mysqli_fetch_assoc($query))
{
    $data[] = [
        "lat"   => (float)$row['latitude'],
        "lng"   => (float)$row['longitude'],
        "count" => 1
    ];
}

// Output the heatmap data as JSON.
echo json_encode($data);