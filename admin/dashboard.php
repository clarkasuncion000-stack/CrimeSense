<?php
// Ensure this page can only be opened by a logged-in user.
require_once("../configuration/session.php");

// Include the shared page layout and sidebar navigation.
include("../includes/header.php");
include("../includes/sidebar.php");

// Connect to the database to read summary statistics.
require_once("../configuration/database.php");

// Count all crime reports in the system.
$totalCrimes = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM crime_reports")
)['total'];

// Count all reports that are still marked as open.
$openCases = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM crime_reports WHERE status='Open'")
)['total'];

// Count all reports that are marked solved.
$solvedCases = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM crime_reports WHERE status='Solved'")
)['total'];

/*
   Hotspot logic:
   This identifies barangays where at least 5 crime reports were recorded.
   The system uses this as a simple indicator of higher-risk areas.
*/
$hotspots = mysqli_num_rows(
    mysqli_query($conn, "
        SELECT barangayID
        FROM crime_reports
        GROUP BY barangayID
        HAVING COUNT(*) >= 5
    ")
);

// Count all reports marked as closed.
$closedCases = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total
                        FROM crime_reports
                        WHERE status='Closed'")
)['total'];
?>

<div class="content">

<?php include("../includes/navbar.php"); ?>

<div class="row g-3">

    <!-- Total Crimes card: shows the overall number of reported crimes -->
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

    <!-- Open Cases card: counts all active or unresolved cases -->
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

    <!-- Solved Cases card: counts crimes that have been resolved -->
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

    <!-- Closed Cases card: counts records closed by authorities -->
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

    <!-- Hotspots card: number of barangays classified as high-risk areas -->
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

    <!-- Left chart: monthly trend of crimes -->
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

    <!-- Right chart: distribution of crime types -->
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

<?php
// Gather all crime type names and counts from the database for the pie chart.
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
// Create the line chart for monthly crime trends.
// The data is currently static sample values for demonstration.
new Chart(document.getElementById('crimeChart'), {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'Crime',
            data: [5, 8, 12, 9, 15, 7]
        }]
    }
});

// Convert PHP arrays into JavaScript arrays for the pie chart.
const pieData = <?= json_encode($crimeData); ?>;

// Create the pie chart showing crime distribution by type.
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
// Footer is included at the end of the page.
include("../includes/footer.php");
?>