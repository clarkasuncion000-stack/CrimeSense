<?php
// Connect to the database so this endpoint can read the crime records it needs.
require_once("../configuration/database.php");

// Return the prediction output to the browser as JSON so the chart/table can render it.
header("Content-Type: application/json");

// --------------------------------------------------
// Read the AJAX filters submitted from the predictive analytics page.
// --------------------------------------------------

$crime      = $_GET['crimeTypeID'] ?? "";
$barangay   = $_GET['barangay_name'] ?? "";
$year       = $_GET['year'] ?? date("Y");

// --------------------------------------------------
// Build the SQL WHERE clause based on the selected filters.
// --------------------------------------------------

$where = "WHERE YEAR(date_committed) = '$year'";

if($crime != "")
{
    $where .= " AND crimeTypeID='$crime'";
}

if($barangay != "")
{
    $where .= " AND barangayID='$barangay'";
}

// --------------------------------------------------
// Count monthly crimes for the selected year and store them by month number.
// --------------------------------------------------

$sql = "
SELECT
    MONTH(date_committed) month,
    COUNT(*) total
FROM crime_reports
$where
GROUP BY MONTH(date_committed)
ORDER BY MONTH(date_committed)
";

$result = mysqli_query($conn, $sql);

$actual = [];

while($row = mysqli_fetch_assoc($result))
{
    $actual[(int)$row['month']] = (int)$row['total'];
}

// --------------------------------------------------
// Create the 12-month labels and forecast values for the chart.
// --------------------------------------------------

$months = [
    "Jan", "Feb", "Mar", "Apr", "May", "Jun",
    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
];

$actualData = [];
$predictedData = [];

$totalActual = 0;

for($i = 1; $i <= 12; $i++)
{
    $value = $actual[$i] ?? 0;

    $actualData[] = $value;
    $totalActual += $value;

    // Simple forecasting method: assume a 15% increase for each month.
    $predictedData[] = round($value * 1.15);
}

// --------------------------------------------------
// Compute summary values shown in the top dashboard cards.
// --------------------------------------------------

$predictedCrimes = array_sum($predictedData);

$trend = "Stable";

if($predictedCrimes > $totalActual)
{
    $trend = "Increasing";
}

$riskBarangays = 0;

// --------------------------------------------------
// Count the barangays with the most crime incidents and list them as risk areas.
// --------------------------------------------------

$risk = [];

$sql = "
SELECT
    b.barangay_name,
    COUNT(*) total
FROM crime_reports c
INNER JOIN barangays b
    ON c.barangayID = b.barangayID
$where
GROUP BY c.barangayID
ORDER BY total DESC
LIMIT 5
";

$res = mysqli_query($conn, $sql);

while($r = mysqli_fetch_assoc($res))
{
    $risk[] = [
        "barangay" => $r['barangay_name']
    ];
}

$riskBarangays = count($risk);

// --------------------------------------------------
// Prepare table rows showing actual vs. predicted values by barangay and crime type.
// --------------------------------------------------

$table = [];

$sql = "
SELECT
    b.barangay_name,
    ct.crime_name,
    COUNT(*) total
FROM crime_reports c
INNER JOIN barangays b
    ON c.barangayID = b.barangayID
INNER JOIN crime_types ct
    ON c.crimeTypeID = ct.crimeTypeID
$where
GROUP BY c.barangayID, c.crimeTypeID
ORDER BY total DESC
";

$res = mysqli_query($conn, $sql);

while($r = mysqli_fetch_assoc($res))
{
    $prediction = round($r['total'] * 1.15);
    $difference = $prediction - $r['total'];

    if($prediction >= 20)
        $riskLevel = "Very High";
    elseif($prediction >= 15)
        $riskLevel = "High";
    elseif($prediction >= 8)
        $riskLevel = "Moderate";
    else
        $riskLevel = "Low";

    $table[] = [
        "barangay" => $r['barangay_name'],
        "crime" => $r['crime_name'],
        "actual" => $r['total'],
        "prediction" => $prediction,
        "difference" => $difference > 0 ? "+" . $difference : $difference,
        "risk" => $riskLevel
    ];
}

// --------------------------------------------------
// Return all computed values to the frontend as JSON.
// --------------------------------------------------

echo json_encode([
    "summary" => [
        "predictedCrimes" => $predictedCrimes,
        "riskBarangays" => $riskBarangays,
        "trend" => $trend
    ],
    "chart" => [
        "months" => $months,
        "actual" => $actualData,
        "predicted" => $predictedData
    ],
    "table" => $table,
    "risk" => $risk
]);
?>