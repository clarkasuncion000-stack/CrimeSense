
<?php

require_once("../configuration/database.php");

header("Content-Type: application/json");

$where = [];

if(!empty($_GET['crimeType']))
{
    $where[] = "crimeTypeID=" . intval($_GET['crimeType']);
}

if(!empty($_GET['from']))
{
    $where[] = "date_committed >= '" . mysqli_real_escape_string($conn,$_GET['from']) . "'";
}

if(!empty($_GET['to']))
{
    $where[] = "date_committed <= '" . mysqli_real_escape_string($conn,$_GET['to']) . "'";
}

$sql = "SELECT latitude, longitude
        FROM crime_reports";

if(count($where))
{
    $sql .= " WHERE " . implode(" AND ", $where);
}

$query = mysqli_query($conn,$sql);

$data = [];

while($row = mysqli_fetch_assoc($query))
{
    $data[] = [
        "lat"   => (float)$row['latitude'],
        "lng"   => (float)$row['longitude'],
        "count" => 1
    ];
}

echo json_encode($data);