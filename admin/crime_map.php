<?php
require_once("../configuration/session.php");
require_once("../configuration/database.php");

include("../includes/header.php");
include("../includes/sidebar.php");
?>
<style>

.legend{
    background:#fff;
    padding:12px;
    border-radius:8px;
    box-shadow:0 0 12px rgba(0,0,0,.3);
    font-size:14px;
    line-height:24px;
    min-width:180px;
}

.legend h6{
    margin:0 0 8px;
    text-align:center;
    font-weight:bold;
}

.legend-item{
    display:flex;
    align-items:center;
    margin-bottom:6px;
}

.legend img{
    margin-right:8px;
}

.boundary-box{
    width:18px;
    height:18px;
    background:rgba(255,255,0,.25);
    border:2px solid red;
    display:inline-block;
    margin-right:8px;
}

</style>
<div class="content">

<?php include("../includes/navbar.php"); ?>

<div class="container-fluid mt-4">

<div class="d-flex justify-content-between align-items-center mb-3">

<h3>
<i class="bi bi-geo-alt-fill"></i>
Crime Map
</h3>

<button
    type="button"
    class="btn btn-primary"
    data-bs-toggle="modal"
    data-bs-target="#addCrimeModal">

    <i class="bi bi-plus-circle"></i>
    Add Crime Report

</button>

</div>

<div class="card shadow">

<div class="card-body">

<div id="map" style="height:600px;"></div>

</div>

</div>

</div>

<!-- Add Crime Modal -->
<div class="modal fade" id="addCrimeModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <form action="save_crime.php" method="POST">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-plus-circle"></i>
                        Add Crime Report
                    </h5>

                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                    </button>
                </div>

                <div class="modal-body">

                    <div class="row">

                        <!-- Crime Type -->
                        <div class="col-md-6 mb-3">

                            <label>Crime Type</label>

                            <select name="crimeTypeID" class="form-select" required>

                                <option value="">Select Crime Type</option>

                                <?php
                                $crime = mysqli_query($conn,"SELECT * FROM crime_types ORDER BY crime_name");

                                while($c=mysqli_fetch_assoc($crime)){
                                ?>

                                <option value="<?= $c['crimeTypeID']; ?>">
                                    <?= $c['crime_name']; ?>
                                </option>

                                <?php } ?>

                            </select>

                        </div>

                        <!-- Barangay -->
                        <div class="col-md-6 mb-3">

                            <label>Barangay</label>

                            <select name="barangayID" class="form-select" required>

                                <option value="">Select Barangay</option>

                                <?php
                                $brgy=mysqli_query($conn,"SELECT * FROM barangays ORDER BY barangay_name");

                                while($b=mysqli_fetch_assoc($brgy)){
                                ?>

                                <option value="<?= $b['barangayID']; ?>">
                                    <?= $b['barangay_name']; ?>
                                </option>

                                <?php } ?>

                            </select>

                        </div>

                        <!-- Date -->
                        <div class="col-md-6 mb-3">

                            <label>Date Committed</label>

                            <input
                                type="date"
                                name="date_committed"
                                class="form-control"
                                required>

                        </div>

                        <!-- Time -->
                        <div class="col-md-6 mb-3">

                            <label>Time Committed</label>

                            <input
                                type="time"
                                name="time_committed"
                                class="form-control"
                                required>

                        </div>

                        <!-- Map -->
                        <div class="col-md-12 mb-3">

                            <label>Select Crime Location</label>

                            <div id="crimeLocationMap"
                                 style="height:400px;border:1px solid #ccc;border-radius:8px;">
                            </div>

                        </div>

                        <!-- Latitude -->
                        <div class="col-md-6 mb-3">

                            <label>Latitude</label>

                            <input
                                type="text"
                                id="latitude"
                                name="latitude"
                                class="form-control"
                                readonly>

                        </div>

                        <!-- Longitude -->
                        <div class="col-md-6 mb-3">

                            <label>Longitude</label>

                            <input
                                type="text"
                                id="longitude"
                                name="longitude"
                                class="form-control"
                                readonly>

                        </div>

                        <div class="col-md-12 mb-3">
                            <label>Address</label>
                            <input
                                type="text"
                                id="address"
                                name="address"
                                class="form-control"
                                readonly>
                        </div>

                        <!-- Description -->
                        <div class="col-md-12 mb-3">

                            <label>Description</label>

                            <textarea
                                name="description"
                                rows="2"
                                class="form-control"
                                required></textarea>

                        </div>

                        <!-- Status -->
                        <div class="col-md-12 mb-3">

                            <label>Status</label>

                            <select name="status" class="form-select">

                                <option>Open</option>
                                <option>Solved</option>
                                <option>Closed</option>

                            </select>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="submit"
                        name="save"
                        class="btn btn-success">

                        <i class="bi bi-save"></i>
                        Save Report

                    </button>

                </div>

            </form>

        </div>
    </div>
</div>
<script>

<!--Main Map-->
var crimeMap = L.map('map').setView([16.333,120.350],13);
console.log("Map created:", crimeMap);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{
    maxZoom:25,
    attribution:'© OpenStreetMap'
}).addTo(crimeMap);

</script>

<!--load all crimes from table-->

<script>

<?php

$sql = "SELECT
        c.crimeID,
        c.date_committed,
        c.time_committed,
        c.latitude,
        c.longitude,
        c.address,
        c.description,
        c.status,
        ct.crime_name,
        ct.icon,
        b.barangay_name
FROM crime_reports c
INNER JOIN crime_types ct
ON c.crimeTypeID = ct.crimeTypeID
INNER JOIN barangays b
ON c.barangayID = b.barangayID";

$result = mysqli_query($conn,$sql);

while($row = mysqli_fetch_assoc($result)){
?>

var icon = L.icon({
    iconUrl: "../assets/icons/<?= $row['icon']; ?>",
    iconSize: [20,20],
    iconAnchor: [18,36],
    popupAnchor: [0,-30]
});

L.marker([
    <?= $row['latitude']; ?>,
    <?= $row['longitude']; ?>
],{
    icon: icon
})
.addTo(crimeMap)
.bindPopup(`
<div style="min-width:250px">

<h6 class="text-primary">
Crime Report #<?= $row['crimeID']; ?>
</h6>

<b>Crime:</b> <?= addslashes($row['crime_name']); ?><br>
<b>Date:</b> <?= date("F d, Y",strtotime($row['date_committed'])); ?><br>
<b>Time:</b> <?= date("h:i A",strtotime($row['time_committed'])); ?><br>
<b>Barangay:</b> <?= addslashes($row['barangay_name']); ?><br>
<b>Address:</b> <?= addslashes($row['address']); ?><br>
<b>Status:</b> <?= addslashes($row['status']); ?><br><br>

<?= nl2br(addslashes($row['description'])); ?>

</div>
`);

<?php } ?>

</script>

<!--Modal Map-->
<script>

var modalMap;
var marker;

document.getElementById('addCrimeModal').addEventListener('shown.bs.modal', function () {

    if (!modalMap) {

        modalMap = L.map('crimeLocationMap').setView([16.333,120.350],13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{
            maxZoom:19,
            attribution:'© OpenStreetMap'
        }).addTo(modalMap);
        
        loadBoundary(modalMap);

        modalMap.on('click', function(e){

            if(marker){
                modalMap.removeLayer(marker);
            }

            marker = L.marker(e.latlng).addTo(modalMap);

            latitude.value = e.latlng.lat.toFixed(7);
            longitude.value = e.latlng.lng.toFixed(7);

            // Reverse Geocoding
            fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${e.latlng.lat}&lon=${e.latlng.lng}`)
            .then(response => response.json())
            .then(data => {

                console.log(data);

                if(data.display_name){
                    document.getElementById("address").value = data.display_name;
                }else{
                    document.getElementById("address").value = "Address not found";
                }

            })
            .catch(err=>{
                console.error(err);
                document.getElementById("address").value = "";
            });

        });

    }

    setTimeout(function(){
        modalMap.invalidateSize();
    },200);

});

</script>

<!--Crime Markers-->
<script>
fetch('../assets/geojson/agoo_boundary.geojson')
.then(response => {
    console.log("Status:", response.status);
    return response.json();
})
.then(data => {

    console.log("GeoJSON:", data);

    var boundary = L.geoJSON(data,{
        style:{
            color:'red',
            weight:2,
            fillColor:'yellow',
            fillOpacity:0.2
        }
    });

    console.log("Layers:", boundary.getLayers().length);

    boundary.addTo(crimeMap);

    crimeMap.fitBounds(boundary.getBounds());

})
.catch(error=>{
    console.error(error);
});
</script>

 

<!--reusable geojson function-->
<script>
    function loadBoundary(map){

    fetch('../assets/geojson/agoo_boundary.geojson')
    .then(response => response.json())
    .then(data=>{

        var boundary = L.geoJSON(data,{
            style:{
                color:'red',
                weight:2,
                fillColor:'yellow',
                fillOpacity:0.10
            }
        }).addTo(map);

        map.fitBounds(boundary.getBounds());

    })
    .catch(err=>console.error(err));

}
</script>

<!--Legend for markers--> 
<script>

var legend = L.control({position:'bottomleft'});

legend.onAdd = function(){

    var div = L.DomUtil.create('div','info legend');

    div.innerHTML = "<h6><b>Crime Legend</b></h6>";

    <?php
    $legend = mysqli_query($conn,"SELECT crime_name, icon FROM crime_types ORDER BY crime_name");

    while($l=mysqli_fetch_assoc($legend)){
    ?>

    div.innerHTML += `
        <div class="legend-item">
            <img src="../assets/icons/<?= $l['icon']; ?>" width="18">
            <?= addslashes($l['crime_name']); ?>
        </div>
    `;

    <?php } ?>

    div.innerHTML += `
        <hr>
        <div class="legend-item">
            <span class="boundary-box"></span>
            Agoo Municipal Boundary
        </div>
    `;

    return div;
};

legend.addTo(crimeMap);

</script>
<!--success modal--> 
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="bi bi-check-circle-fill"></i>
                    Success
                </h5>
            </div>

            <div class="modal-body text-center">

                <i class="bi bi-check-circle-fill text-success"
                   style="font-size:70px;"></i>

                <h4 class="mt-3">
                    Crime Report Saved!
                </h4>

                <p>
                    The crime report has been successfully added to the system.
                </p>

            </div>

            <div class="modal-footer">

                <button
                    class="btn btn-success"
                    data-bs-dismiss="modal">
                    OK
                </button>

            </div>

        </div>
    </div>
</div>

<?php if(isset($_GET['success'])){ ?>

<script>
document.addEventListener("DOMContentLoaded", function(){

    var modal = new bootstrap.Modal(
        document.getElementById("successModal")
    );

    modal.show();

    // Remove ?success=1 from the URL
    window.history.replaceState({}, document.title, window.location.pathname);

});
</script>

<?php } ?>

<!--Error modal--> 
<div class="modal fade" id="errorModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    Error
                </h5>
            </div>

            <div class="modal-body text-center">

                <i class="bi bi-x-circle-fill text-danger"
                   style="font-size:70px;"></i>

                <h4 class="mt-3">
                    Saving Failed
                </h4>

                <p>
                    Unable to save the crime report.
                </p>

            </div>

            <div class="modal-footer">
                <button class="btn btn-danger" data-bs-dismiss="modal">
                    Close
                </button>
            </div>

        </div>
    </div>
</div>

<?php if(isset($_GET['error'])){ ?>

<script>
document.addEventListener("DOMContentLoaded", function(){

    new bootstrap.Modal(
        document.getElementById("errorModal")
    ).show();

    window.history.replaceState({}, document.title, window.location.pathname);

});
</script>

<?php } ?>

<?php include("../includes/footer.php"); ?>
 