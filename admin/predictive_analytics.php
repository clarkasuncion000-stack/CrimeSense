<?php
require_once("../configuration/session.php");
require_once("../configuration/database.php");

include("../includes/header.php");
include("../includes/sidebar.php");

// Dashboard Statistics
$totalCrimes = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) total FROM crime_reports")
)['total'];

$totalBarangays = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(DISTINCT barangayID) total FROM crime_reports")
)['total'];

$totalCrimeTypes = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) total FROM crime_types")
)['total'];

$currentYear = date("Y");
?>
<script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>
<div class="content">

<?php include("../includes/navbar.php"); ?>

<div class="container-fluid mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">
            <i class="bi bi-graph-up-arrow text-primary"></i>
            Predictive Analytics
        </h3>

        <button class="btn btn-primary" id="btnPredict">
            <i class="bi bi-cpu"></i>
            Generate Prediction
        </button>
    </div>

    <!-- SUMMARY CARDS -->

    <div class="row g-3 mb-4">

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow text-white"
                 style="background:linear-gradient(45deg,#0d6efd,#4f8cff);">
                <div class="card-body">
                    <small>Predicted Crimes</small>
                    <h2 class="fw-bold mt-2" id="predictedCrimes">
                        --
                    </h2>
                    <small>Next Month</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow text-white"
                 style="background:linear-gradient(45deg,#dc3545,#ff6b6b);">
                <div class="card-body">
                    <small>High Risk Barangays</small>
                    <h2 class="fw-bold mt-2" id="riskBarangays">
                        --
                    </h2>
                    <small>Predicted Hotspots</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow text-white"
                 style="background:linear-gradient(45deg,#198754,#49d17d);">
                <div class="card-body">
                    <small>Prediction Accuracy</small>
                    <h2 class="fw-bold mt-2">
                        92%
                    </h2>
                    <small>Random Forest</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow text-white"
                 style="background:linear-gradient(45deg,#fd7e14,#ffb347);">
                <div class="card-body">
                    <small>Trend</small>
                    <h2 class="fw-bold mt-2" id="trend">
                        --
                    </h2>
                    <small>Direction</small>
                </div>
            </div>
        </div>

    </div>

    <!-- FILTERS -->

    <div class="card shadow border-0 mb-4">

        <div class="card-header bg-white">

            <strong>
                <i class="bi bi-funnel-fill"></i>
                Prediction Filters
            </strong>

        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-3">

                    <label class="form-label">
                        Crime Type
                    </label>

                    <select class="form-select" id="crimeType">

                        <option value="">All Crimes</option>

                        <?php

                        $types=mysqli_query($conn,
                        "SELECT * FROM crime_types ORDER BY crime_name");

                        while($row=mysqli_fetch_assoc($types))
                        {
                            ?>

                            <option value="<?= $row['crimeTypeID'] ?>">
                                <?= $row['crime_name'] ?>
                            </option>

                            <?php
                        }

                        ?>

                    </select>

                </div>

                <div class="col-md-3">

                    <label class="form-label">
                        Barangay
                    </label>

                    <select class="form-select" id="barangay">

                        <option value="">All Barangays</option>

                        <?php

                        $brgy=mysqli_query($conn,
                        "SELECT * FROM barangays ORDER BY barangay_name");

                        while($b=mysqli_fetch_assoc($brgy))
                        {
                            ?>

                            <option value="<?= $b['barangayID'] ?>">
                                <?= $b['barangay_name'] ?>
                            </option>

                            <?php
                        }

                        ?>

                    </select>

                </div>

                <div class="col-md-2">

                    <label class="form-label">
                        Year
                    </label>

                    <select class="form-select" id="year">

                        <?php

                        for($y=$currentYear;$y>=2020;$y--)
                        {
                            echo "<option>$y</option>";
                        }

                        ?>

                    </select>

                </div>

                <div class="col-md-2">

                    <label class="form-label">
                        Forecast
                    </label>

                    <select class="form-select" id="forecast">

                        <option value="1">
                            Next Month
                        </option>

                        <option value="3">
                            Next 3 Months
                        </option>

                        <option value="6">
                            Next 6 Months
                        </option>

                    </select>

                </div>

                
            </div>

        </div>

    </div>

    <!-- CHARTS -->

    <div class="row">

        <div class="col-lg-8">

            <div class="card shadow border-0 mb-4">

                <div class="card-header bg-white">

                    <strong>
                        Monthly Crime Forecast
                    </strong>

                </div>

                <div class="card-body">

                    <canvas id="forecastChart"
                            height="120">
                    </canvas>

                </div>

            </div>

        </div>

        <div class="col-lg-4">

            <div class="card shadow border-0 mb-4">

                <div class="card-header bg-white">

                    <strong>
                        AI Recommendation
                    </strong>

                </div>

                <div class="card-body">

                    <div id="recommendation">

                        <div class="alert alert-info">

                            Click
                            <strong>
                            Generate Prediction
                            </strong>

                            to analyze crime trends.

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- MAP -->

    <div class="card shadow border-0 mb-4">

        <div class="card-header bg-white">

            <strong>
                Forecast Risk Map
            </strong>

        </div>

        <div class="card-body p-0">

            <div id="forecastMap"
                 style="height:600px;">
            </div>

        </div>

    </div>

    <!-- TABLE -->

    <div class="card shadow border-0">

        <div class="card-header bg-white">

            <strong>
                Prediction Results
            </strong>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover table-bordered align-middle">

                    <thead class="table-dark">

                    <tr>

                        <th>Barangay</th>

                        <th>Crime Type</th>

                        <th>Previous</th>

                        <th>Prediction</th>

                        <th>Difference</th>

                        <th>Risk</th>

                    </tr>

                    </thead>

                    <tbody id="predictionTable">

                    <tr>

                        <td colspan="6"
                            class="text-center">

                            No prediction generated.

                        </td>

                    </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

// ================================
// LEAFLET MAP
// ================================

var map = L.map('forecastMap').setView([16.527,120.357],13);

L.tileLayer(
'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
{
    maxZoom:19
}).addTo(map);

var markers=[];

let chart=null;


// ================================
// LOAD PREDICTION
// ================================

document.addEventListener("DOMContentLoaded", function () {

 

    const btn = document.getElementById("btnPredict");

    console.log(btn);

    btn.addEventListener("click", function () {

 

        loadPrediction();

    });

   // loadPrediction();

});

function loadPrediction()
{
    $.ajax({
        url: "prediction_data.php",
        type: "GET",
        dataType: "json",
        data: {
            crime: $("#crimeType").val(),
            barangay: $("#barangay").val(),
            year: $("#year").val(),
            forecast: $("#forecast").val()
        },
        success: function(res){

            console.log(res); // Check returned data

            updateCards(res.summary);
            drawChart(res.chart);
            updateTable(res.table);
            updateRecommendation(res.summary);
            drawRiskMap(res.risk);

        },
        error: function(xhr){

            console.log(xhr.status);
            console.log(xhr.responseText);

            alert(xhr.responseText);

        }
    });
}



// ================================
// UPDATE DASHBOARD CARDS
// ================================

function updateCards(data)
{

    $("#predictedCrimes").text(data.predictedCrimes);

    $("#riskBarangays").text(data.riskBarangays);

    $("#trend").text(data.trend);

}



// ================================
// CHART.JS
// ================================

function drawChart(data)
{

    if(chart)
    {
        chart.destroy();
    }

    var ctx=document.getElementById("forecastChart");

    chart=new Chart(ctx,{

        type:'line',

        data:{

            labels:data.months,

            datasets:[

            {

                label:'Actual Crimes',

                data:data.actual,

                borderColor:'#0d6efd',

                backgroundColor:'rgba(13,110,253,.20)',

                tension:.4,

                fill:true

            },

            {

                label:'Predicted',

                data:data.predicted,

                borderColor:'#dc3545',

                backgroundColor:'rgba(220,53,69,.20)',

                tension:.4,

                fill:true

            }

            ]

        },

        options:{

            responsive:true,

            maintainAspectRatio:false,

            plugins:{

                legend:{

                    position:'top'

                }

            }

        }

    });

}



// ================================
// TABLE
// ================================

function updateTable(rows)
{

    let html="";

    if(rows.length==0)
    {

        html=`
        <tr>

        <td colspan='6'
            class='text-center'>

        No data found.

        </td>

        </tr>
        `;

    }

    rows.forEach(function(r){

        let badge="secondary";

        if(r.risk=="Very High")
            badge="danger";

        else if(r.risk=="High")
            badge="warning";

        else if(r.risk=="Moderate")
            badge="info";

        else
            badge="success";


        html+=`

        <tr>

            <td>${r.barangay}</td>

            <td>${r.crime}</td>

            <td class='text-center'>${r.actual}</td>

            <td class='text-center fw-bold'>${r.prediction}</td>

            <td class='text-center'>${r.difference}</td>

            <td class='text-center'>

            <span class='badge bg-${badge}'>

            ${r.risk}

            </span>

            </td>

        </tr>

        `;

    });

    $("#predictionTable").html(html);

}



// ================================
// AI RECOMMENDATION
// ================================

function updateRecommendation(summary)
{

    let msg="";

    if(summary.trend=="Increasing")
    {

        msg=`

        <div class="alert alert-danger">

        <h6 class="fw-bold">

        <i class="bi bi-exclamation-triangle-fill"></i>

        Recommendation

        </h6>

        Crime is projected to increase next month.

        <hr>

        • Increase police patrols.

        <br>

        • Monitor hotspot barangays.

        <br>

        • Deploy additional CCTV cameras.

        <br>

        • Coordinate with Barangay Officials.

        </div>

        `;

    }
    else
    {

        msg=`

        <div class="alert alert-success">

        <h6 class="fw-bold">

        <i class="bi bi-check-circle-fill"></i>

        Recommendation

        </h6>

        Crime trend is stable.

        <hr>

        Continue current patrol strategy while maintaining community engagement.

        </div>

        `;

    }

    $("#recommendation").html(msg);

}



// ================================
// RISK MAP
// ================================

function drawRiskMap(list)
{

    markers.forEach(function(m){

        map.removeLayer(m);

    });

    markers=[];

    list.forEach(function(item){

        // Future enhancement:
        // Use barangay polygon or center coordinates.

        // Placeholder marker.
        // Replace with barangay coordinates later.

        var marker=L.marker([16.527,120.357])

        .bindPopup(

        "<b>"+item.barangay+"</b><br>"+

        "Predicted High Risk"

        )

        .addTo(map);

        markers.push(marker);

    });

}

</script>
<?php include("../includes/footer.php"); ?>