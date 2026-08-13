<?php

require_once("../configuration/session.php");

include("../includes/header.php");

include("../includes/sidebar.php");

require_once("../configuration/database.php");


$totalCrimes = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) AS total FROM crime_reports")
)['total'];

$openCases = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) AS total FROM crime_reports WHERE status='Open'")
)['total'];

$solvedCases = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) AS total FROM crime_reports WHERE status='Solved'")
)['total'];

/* Example:
   Hotspot = barangays with at least 5 crime reports
   Adjust the threshold as needed.
*/
$hotspots = mysqli_num_rows(
    mysqli_query($conn,"
        SELECT barangayID
        FROM crime_reports
        GROUP BY barangayID
        HAVING COUNT(*) >= 5
    ")
);

$closedCases = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) AS total
                        FROM crime_reports
                        WHERE status='Closed'")
)['total'];
?>


<div class="content">

<?php include("../includes/navbar.php"); ?>

<div class="row g-3">

    <!-- Total Crimes -->
    <div class="col-xl col-lg-4 col-md-6">
        <div class="card text-white shadow h-100"
     style="background:linear-gradient(135deg,#dc3545,#b02a37);">
            <div class="card-body d-flex justify-content-between align-items-center">

                <div>
                    <h6 class="fw-bold text-white mb-1">Total Crimes</h6>
                    <h2 class="mb-0"><?= $totalCrimes ?></h2>
                </div>

                <div class="fs-1 text-danger">
                    <i class="bi bi-shield-fill-exclamation"></i>
                </div>

            </div>
        </div>
    </div>

    <!-- Open Cases -->
    <div class="col-xl col-lg-4 col-md-6">
        <div class="card text-white shadow h-100"
     style="background:linear-gradient(135deg,#fd7e14,#d96b00);">
            <div class="card-body d-flex justify-content-between align-items-center">

                <div>
                    <h6 class="fw-bold text-white mb-1">Open Cases</h6>
                    <h2 class="mb-0"><?= $openCases ?></h2>
                </div>

                <div class="fs-1 text-warning">
                    <i class="bi bi-folder2-open"></i>
                </div>

            </div>
        </div>
    </div>

    <!-- Solved Cases -->
    <div class="col-xl col-lg-4 col-md-6">
        <div class="card text-white shadow h-100"
     style="background:linear-gradient(135deg,#198754,#157347);">
            <div class="card-body d-flex justify-content-between align-items-center">

                <div>
                    <h6 class="fw-bold text-white mb-1">Solved Cases</h6>
                    <h2 class="mb-0"><?= $solvedCases ?></h2>
                </div>

                <div class="fs-1 text-success">
                    <i class="bi bi-check-circle-fill"></i>
                </div>

            </div>
        </div>
    </div>

    <!-- Closed Cases -->
    <div class="col-xl col-lg-4 col-md-6">
        <div class="card text-white shadow h-100"
     style="background:linear-gradient(135deg,#6c757d,#495057);">
            <div class="card-body d-flex justify-content-between align-items-center">

                <div>
                    <h6 class="fw-bold text-white mb-1">Closed Cases</h6>
                    <h2 class="mb-0"><?= $closedCases ?></h2>
                </div>

                <div class="fs-1 text-secondary">
                    <i class="bi bi-archive-fill"></i>
                </div>

            </div>
        </div>
    </div>

    <!-- Hotspots -->
    <div class="col-xl col-lg-4 col-md-6">
        <div class="card text-white shadow h-100"
     style="background:linear-gradient(135deg,#6f42c1,#59359c);">
            <div class="card-body d-flex justify-content-between align-items-center">

                <div>
                    <h6 class="fw-bold text-white mb-1">Hotspots</h6>
                    <h2 class="mb-0"><?= $hotspots ?></h2>
                </div>

                <div class="fs-1 text-danger">
                    <i class="bi bi-fire"></i>
                </div>

            </div>
        </div>
    </div>

</div>

<br>

<div class="row">

<div class="col-md-8">

<div class="card">

<div class="card-header">

Monthly Crime Trend

</div>

<div class="card-body">

<canvas id="crimeChart"></canvas>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card">

<div class="card-header">

Crime Distribution

</div>

<div class="card-body">

<canvas id="pieChart"></canvas>

</div>

</div>

</div>

</div>

<br>

 

 

</div>

</div>
<!--retrieve data from DB for pie chart--> 
<?php

$crimeLabels = [];
$crimeData = [];

$sql = "
SELECT
    ct.crime_name,
    COUNT(*) AS total
FROM crime_reports cr
INNER JOIN crime_types ct
    ON cr.crimeTypeID = ct.crimeTypeID
GROUP BY ct.crimeTypeID
ORDER BY total DESC
";

$result = mysqli_query($conn, $sql);

while($row = mysqli_fetch_assoc($result))
{
    $crimeLabels[] = $row['crime_name'];
    $crimeData[] = $row['total'];
}

?>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>

new Chart(document.getElementById('crimeChart'),{

type:'line',

data:{

labels:['Jan','Feb','Mar','Apr','May','Jun'],

datasets:[{

label:'Crime',

data:[5,8,12,9,15,7]

}]

}

});

const pieData = <?= json_encode($crimeData); ?>;

new Chart(document.getElementById('pieChart'), {

    type: 'pie',

    data: {
        labels: <?= json_encode($crimeLabels); ?>,
        datasets: [{
            data: pieData,
            backgroundColor: [
                '#dc3545',
                '#0d6efd',
                '#198754',
                '#ffc107',
                '#6f42c1',
                '#fd7e14',
                '#20c997',
                '#6610f2',
                '#0dcaf0',
                '#6c757d'
            ]
        }]
    },

    plugins: [ChartDataLabels],

    options: {

        responsive: true,

        plugins: {

            legend: {
                position: 'bottom'
            },

            datalabels: {

                color: '#fff',

                font: {
                    weight: 'bold',
                    size: 13
                },

                formatter: (value, context) => {

                    const data = context.chart.data.datasets[0].data;

                    const total = data.reduce((a, b) => a + b, 0);

                    const percentage = (value / total * 100).toFixed(1);

                    return percentage + "%";

                }

            }

        }

    }

});

</script>

<?php

include("../includes/footer.php");

?>