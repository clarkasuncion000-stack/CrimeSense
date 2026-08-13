<?php
require_once("../configuration/session.php");
require_once("../configuration/database.php");

include("../includes/header.php");
include("../includes/sidebar.php");
?>

<div class="content">

<?php include("../includes/navbar.php"); ?>

<div class="container-fluid mt-4">

<div class="card shadow">

<div class="card-header bg-danger text-white">
<h4 class="mb-0">
<i class="bi bi-fire"></i>
Crime Hotspot Analysis (Kernel Density Estimation)
</h4>
</div>

<div class="card-body">

<div class="row mb-3">

<div class="col-md-3">

<select id="crimeType" class="form-select">

<option value="">All Crime Types</option>

<?php
$q=mysqli_query($conn,"SELECT * FROM crime_types ORDER BY crime_name");

while($r=mysqli_fetch_assoc($q)){
?>

<option value="<?= $r['crimeTypeID']; ?>">
<?= $r['crime_name']; ?>
</option>

<?php } ?>

</select>

</div>

<div class="col-md-3">
<input type="date" id="fromDate" class="form-control">
</div>

<div class="col-md-3">
<input type="date" id="toDate" class="form-control">
</div>

<div class="col-md-3">

<button class="btn btn-primary w-100" id="btnLoad">

<i class="bi bi-fire"></i>

Generate Hotspot

</button>

</div>

</div>

<div id="map" style="height:700px;"></div>

</div>

</div>

</div>

</div>

<script>

var map=L.map('map');

L.tileLayer(
'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
{
    maxZoom:19
}).addTo(map);

var heatLayer=null;

//=========================
// Load Boundary
//=========================

function loadBoundary(){

fetch("../assets/geojson/agoo_boundary.geojson")

.then(res=>res.json())

.then(function(data){

var boundary=L.geoJSON(data,{

style:{
color:"red",
weight:3,
fillColor:"yellow",
fillOpacity:0.10
}

}).addTo(map);

map.fitBounds(boundary.getBounds());

});

}

//=========================
// Load Heatmap
//=========================

function loadData(){

if(heatLayer){
map.removeLayer(heatLayer);
}

$.getJSON("hotspot_data.php",{

crimeType:$("#crimeType").val(),
from:$("#fromDate").val(),
to:$("#toDate").val()

},function(data){

let heat=[];

data.forEach(function(row){

heat.push([
row.lat,
row.lng,
row.count
]);

});

heatLayer=L.heatLayer(heat,{

radius:35,
blur:25,
maxZoom:17,

gradient:{

0.20:"blue",
0.40:"lime",
0.60:"yellow",
0.80:"orange",
1.00:"red"

}

}).addTo(map);

});

}

//=========================
// Button
//=========================

$("#btnLoad").click(function(){

loadData();

});

// Auto Reload

$("#crimeType,#fromDate,#toDate").change(function(){

loadData();

});

//=========================
// Legend
//=========================

var legend=L.control({position:"bottomleft"});

legend.onAdd=function(){

var div=L.DomUtil.create("div");

div.style.background="white";
div.style.padding="10px";
div.style.borderRadius="8px";
div.style.boxShadow="0 0 10px rgba(0,0,0,.3)";
div.style.lineHeight="25px";

div.innerHTML=`

<b>Hotspot Legend</b>

<hr>

🟦 Low Density<br>

🟩 Moderate Density<br>

🟨 High Density<br>

🟧 Very High Density<br>

🟥 Extreme Hotspot

<hr>

<span style="
display:inline-block;
width:18px;
height:18px;
background:rgba(255,255,0,.2);
border:2px solid red;
margin-right:8px;
"></span>

Agoo Boundary

`;

return div;

};

legend.addTo(map);

//=========================
// Initialize
//=========================

loadBoundary();

loadData();

</script>

<?php include("../includes/footer.php"); ?>