<?php
// Load the database connection and session protection before processing the form.
require_once("../configuration/database.php");
require_once("../configuration/session.php");

// If the form was submitted with the Save button, insert the report into the database.
if(isset($_POST['save']))
{
    // Read the submitted values from the form.
    $crimeTypeID = $_POST['crimeTypeID'];
    $barangayID = $_POST['barangayID'];
    $date = $_POST['date_committed'];
    $time = $_POST['time_committed'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $description = $_POST['description'];
    $status = $_POST['status'];

    // Record who reported the crime based on the current logged-in session user.
    $reported_by = $_SESSION['userID'];

    // SQL insert command for a new crime report.
    $sql = "INSERT INTO crime_reports
            (
                crimeTypeID,
                barangayID,
                date_committed,
                time_committed,
                latitude,
                longitude,
                description,
                status,
                reported_by
            )

            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?
            )";

    // Prepare the query so values are inserted safely.
    $stmt = $conn->prepare($sql);

    // Bind each value to its matching database field.
    $stmt->bind_param(
        "iissddssi",
        $crimeTypeID,
        $barangayID,
        $date,
        $time,
        $latitude,
        $longitude,
        $description,
        $status,
        $reported_by
    );

    // If insertion succeeds, show a success alert and redirect to the list page.
    if($stmt->execute())
    {
        echo "<script>
            alert('Crime report saved successfully.');
            window.location='crime_reports.php';
        </script>";
    }
    else
    {
        // If insertion fails, display the database error for debugging.
        echo "<div class='alert alert-danger'>
                ".$stmt->error."
              </div>";
    }
}
?>

<!DOCTYPE html>
<html>

<?php include("../includes/header.php"); ?>

<body>

<?php include("../includes/navbar.php"); ?>
<?php include("../includes/sidebar.php"); ?>

<div class="container-fluid mt-4">

<div class="card shadow">

<div class="card-header bg-primary text-white">
<h4>Add Crime Report</h4>
</div>

<div class="card-body">

<form method="POST">

<div class="row">

<!-- Map section where the user clicks a location for the incident -->
<div class="col-md-12 mb-3">

    <label>Select Crime Location</label>

    <div id="map" style="height:450px; border:1px solid #ccc; border-radius:10px;"></div>

</div>

<!-- Read-only latitude field filled by map click -->
<div class="col-md-6 mb-3">

    <label>Latitude</label>

    <input
        type="text"
        id="latitude"
        name="latitude"
        class="form-control"
        readonly
        required>

</div>

<!-- Read-only longitude field filled by map click -->
<div class="col-md-6 mb-3">

    <label>Longitude</label>

    <input
        type="text"
        id="longitude"
        name="longitude"
        class="form-control"
        readonly
        required>

</div>

<!-- Barangay selector for the reported location -->
<div class="col-md-6 mb-3">

<label>Barangay</label>

<select name="barangayID" class="form-select" required>

<option value="">Select Barangay</option>

<?php
$result = mysqli_query($conn, "SELECT * FROM barangays ORDER BY barangay_name");

while($row = mysqli_fetch_assoc($result))
{
?>

<option value="<?= $row['barangayID']; ?>">
<?= $row['barangay_name']; ?>
</option>

<?php } ?>

</select>

</div>

<!-- Date when the crime happened -->
<div class="col-md-6 mb-3">

<label>Date Committed</label>

<input
type="date"
name="date_committed"
class="form-control"
required>

</div>

<!-- Time when the crime happened -->
<div class="col-md-6 mb-3">

<label>Time Committed</label>

<input
type="time"
name="time_committed"
class="form-control"
required>

</div>

<!-- Alternative manual latitude input (not usually used with map click) -->
<div class="col-md-6 mb-3">

<label>Latitude</label>

<input
type="text"
name="latitude"
class="form-control"
placeholder="16.8732456">

</div>

<!-- Alternative manual longitude input -->
<div class="col-md-6 mb-3">

<label>Longitude</label>

<input
type="text"
name="longitude"
class="form-control"
placeholder="120.4567890">

</div>

<!-- Description of the event -->
<div class="col-md-12 mb-3">

<label>Description</label>

<textarea
name="description"
rows="5"
class="form-control"
required></textarea>

</div>

<!-- Case status dropdown -->
<div class="col-md-12 mb-3">

<label>Status</label>

<select
name="status"
class="form-select">

<option>Open</option>
<option>Solved</option>
<option>Closed</option>

</select>

</div>

<!-- Save or cancel button -->
<div class="col-md-12">

<button
class="btn btn-success"
name="save">

<i class="bi bi-save"></i>
Save Report

</button>

<a href="crime_reports.php"
class="btn btn-secondary">
Cancel
</a>

</div>

</div>

</form>

</div>

</div>

</div>

<script>
// Initialize the Leaflet map used for selecting a location.
var map = L.map('map').setView([16.5236, 120.4870], 13);

// Load the OpenStreetMap tiles as the base map.
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

var marker;

// When the user clicks the map, place a marker and fill the latitude/longitude fields.
map.on('click', function(e) {

    if (marker) {
        map.removeLayer(marker);
    }

    marker = L.marker(e.latlng).addTo(map);

    document.getElementById('latitude').value = e.latlng.lat.toFixed(7);
    document.getElementById('longitude').value = e.latlng.lng.toFixed(7);

});
</script>

<?php include("../includes/footer.php"); ?>

</body>
</html>