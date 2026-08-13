<!DOCTYPE html>
<html>
<head>
    <title>Leaflet Draw Example</title>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"> 
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css">

    <style>
        html, body{
            margin:0;
            padding:0;
        }

        #map{
            height:700px;
            width:100%;
        }

        #save{
            position:absolute;
            top:10px;
            right:10px;
            z-index:1000;
            padding:10px 15px;
        }
    </style>
</head>

<body>

<button id="save">Save GeoJSON</button>

<div id="map"></div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script> <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>

<script>

// Create map
var map = L.map('map').setView([16.324,120.364],13);

// OpenStreetMap
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png',{
    maxZoom:19,
    attribution:'© OpenStreetMap'
}).addTo(map);

// Feature Group
var drawnItems = new L.FeatureGroup();
map.addLayer(drawnItems);

// Draw Controls
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

// Save drawn polygon
map.on(L.Draw.Event.CREATED,function(e){

    var layer = e.layer;

    drawnItems.addLayer(layer);

});

// Download GeoJSON
document.getElementById("save").onclick=function(){

    if(drawnItems.getLayers().length==0){
        alert("Please draw a polygon first.");
        return;
    }

    var geojson = drawnItems.toGeoJSON();

    var data = JSON.stringify(geojson,null,2);

    var blob = new Blob([data],{
        type:"application/json"
    });

    var url = URL.createObjectURL(blob);

    var a = document.createElement("a");

    a.href = url;
    a.download = "agoo_boundary.geojson";
    a.click();

    URL.revokeObjectURL(url);

};

</script>

</body>
</html>