<!DOCTYPE html>
<html>
<head>
    <!-- Page title displayed in the browser tab. -->
    <title>Leaflet Draw Example</title>

<!-- Load Leaflet's base map styles. -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"> 
<!-- Load the styles for Leaflet Draw controls. -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css">

    <style>
        html, body{
            margin:0;
            padding:0;
        }

        #map{
            /* Give the map a fixed visible height and full page width. */
            height:700px;
            width:100%;
        }

        #save{
            /* Place the save button above the map in the top-right corner. */
            position:absolute;
            top:10px;
            right:10px;
            z-index:1000;
            padding:10px 15px;
        }
    </style>
</head>

<body>

<!-- Button used to download all drawn shapes as a GeoJSON file. -->
<button id="save">Save GeoJSON</button>

<!-- Leaflet will render the interactive map inside this element. -->
<div id="map"></div>

<!-- Load Leaflet's map functionality. -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script> <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>

<script>

// Create the map and set its initial center and zoom level.
var map = L.map('map').setView([16.324,120.364],13);

// Add OpenStreetMap tiles as the map's background layer.
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png',{
    maxZoom:19,
    attribution:'© OpenStreetMap'
}).addTo(map);

// Store all polygons drawn by the user in one editable feature group.
var drawnItems = new L.FeatureGroup();
map.addLayer(drawnItems);

// Configure the drawing toolbar and allow polygon drawing only.
var drawControl = new L.Control.Draw({

    edit:{
        featureGroup: drawnItems
    },

    draw:{
        polygon:true,
        polyline:false,
        rectangle:false,
        circle:false,
        marker:false,
        circlemarker:false
    }

});

map.addControl(drawControl);

// When a polygon is created, add it to the feature group so it remains visible and editable.
map.on(L.Draw.Event.CREATED,function(e){

    var layer = e.layer;

    drawnItems.addLayer(layer);

});

// Convert the drawn features to GeoJSON and download them when Save GeoJSON is clicked.
document.getElementById("save").onclick=function(){

    // Stop if the user has not drawn any shape yet.
    if(drawnItems.getLayers().length==0){
        alert("Please draw a polygon first.");
        return;
    }

    // Convert every drawn layer into a GeoJSON object.
    var geojson = drawnItems.toGeoJSON();

    // Format the object as readable JSON text.
    var data = JSON.stringify(geojson,null,2);

    // Create an in-memory file containing the JSON text.
    var blob = new Blob([data],{
        type:"application/json"
    });

    // Create a temporary browser URL for the generated file.
    var url = URL.createObjectURL(blob);

    // Create an invisible download link and activate it programmatically.
    var a = document.createElement("a");

    a.href = url;
    a.download = "agoo_boundary.geojson";
    a.click();

    // Release the temporary URL after the download has been started.
    URL.revokeObjectURL(url);

};

</script>

</body>
</html>