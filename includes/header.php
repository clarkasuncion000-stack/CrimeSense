<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>CrimSense</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>
<style>

body{
    background:#f4f6f9;
}

.sidebar{

    position:fixed;
    left:0;
    top:0;
    width:260px;
    height:100vh;
    background:#0d6efd;
    color:white;
}

.sidebar h3{

    padding:20px;
    text-align:center;
    font-weight:bold;
}

.sidebar a{

    display:block;
    color:white;
    padding:15px 25px;
    text-decoration:none;
}

.sidebar a:hover{

    background:rgba(255,255,255,.15);
}

.content{

    margin-left:260px;
    padding:25px;
}

.card{

    border:none;
    border-radius:15px;
    box-shadow:0 5px 15px rgba(0,0,0,.08);
}

#map{

    height:450px;
}

</style>
 

<body>