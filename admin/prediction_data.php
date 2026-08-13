<?php

require_once("../configuration/database.php");

header("Content-Type: application/json");

// -----------------------------------
// Filters
// -----------------------------------

$crime      = $_GET['crimeTypeID'] ?? "";
$barangay   = $_GET['barangay_name'] ?? "";
$year       = $_GET['year'] ?? date("Y");

// -----------------------------------
// WHERE CLAUSE
// -----------------------------------

$where = "WHERE YEAR(date_committed) = '$year'";

if($crime != "")
{
    $where .= " AND crimeTypeID='$crime'";
}

if($barangay != "")
{
    $where .= " AND barangayID='$barangay'";
}

// -----------------------------------
// Monthly Crime Count
// -----------------------------------

$sql = "

SELECT

MONTH(date_committed) month,

COUNT(*) total

FROM crime_reports

$where

GROUP BY MONTH(date_committed)

ORDER BY MONTH(date_committed)

";

$result = mysqli_query($conn,$sql);

$actual=[];

while($row=mysqli_fetch_assoc($result))
{
    $actual[(int)$row['month']] = (int)$row['total'];
}

// -----------------------------------
// Build 12 months
// -----------------------------------

$months=[
"Jan","Feb","Mar","Apr","May","Jun",
"Jul","Aug","Sep","Oct","Nov","Dec"
];

$actualData=[];
$predictedData=[];

$totalActual=0;

for($i=1;$i<=12;$i++)
{

    $value=$actual[$i] ?? 0;

    $actualData[]=$value;

    $totalActual += $value;

    // Simple Prediction (+15%)

    $predictedData[] = round($value*1.15);

}

// -----------------------------------
// Summary
// -----------------------------------

$predictedCrimes=array_sum($predictedData);

$trend="Stable";

if($predictedCrimes>$totalActual)
{
    $trend="Increasing";
}

$riskBarangays=0;

// -----------------------------------
// Risk Barangays
// -----------------------------------

$risk=[];

$sql="

SELECT

b.barangay_name,

COUNT(*) total

FROM crime_reports c

INNER JOIN barangays b

ON c.barangayID=b.barangayID

$where

GROUP BY c.barangayID

ORDER BY total DESC

LIMIT 5

";

$res=mysqli_query($conn,$sql);

while($r=mysqli_fetch_assoc($res))
{

    $risk[]=[
        "barangay"=>$r['barangay_name']
    ];

}

$riskBarangays=count($risk);

// -----------------------------------
// Prediction Table
// -----------------------------------

$table=[];

$sql="

SELECT

b.barangay_name,

ct.crime_name,

COUNT(*) total

FROM crime_reports c

INNER JOIN barangays b

ON c.barangayID=b.barangayID

INNER JOIN crime_types ct

ON c.crimeTypeID=ct.crimeTypeID

$where

GROUP BY c.barangayID,c.crimeTypeID

ORDER BY total DESC

";

$res=mysqli_query($conn,$sql);

while($r=mysqli_fetch_assoc($res))
{

    $prediction=round($r['total']*1.15);

    $difference=$prediction-$r['total'];

    if($prediction>=20)
        $riskLevel="Very High";
    elseif($prediction>=15)
        $riskLevel="High";
    elseif($prediction>=8)
        $riskLevel="Moderate";
    else
        $riskLevel="Low";

    $table[]=[

        "barangay"=>$r['barangay_name'],

        "crime"=>$r['crime_name'],

        "actual"=>$r['total'],

        "prediction"=>$prediction,

        "difference"=>$difference>0 ? "+".$difference : $difference,

        "risk"=>$riskLevel

    ];

}

// -----------------------------------
// Output JSON
// -----------------------------------

echo json_encode([

"summary"=>[

"predictedCrimes"=>$predictedCrimes,

"riskBarangays"=>$riskBarangays,

"trend"=>$trend

],

"chart"=>[

"months"=>$months,

"actual"=>$actualData,

"predicted"=>$predictedData

],

"table"=>$table,

"risk"=>$risk

]);

?>