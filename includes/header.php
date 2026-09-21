<!DOCTYPE html>
<html lang="en">
<head>

<!-- Basic HTML metadata -->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>CrimSense</title>

<!-- CSS frameworks and libraries used throughout the system -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Leaflet and map-related scripts for crime mapping interfaces -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>

<style>

/* Light gray background for the application pages */
body{
    background:#f4f6f9;
}

/* Left sidebar layout */
.sidebar{
    position:fixed;
    left:0;
    top:0;
    width:260px;
    height:100vh;
    background:#0d6efd;
    color:white;
}

/* Brand section inside the sidebar */
.sidebar h3{
    padding:20px;
    text-align:center;
    font-weight:bold;
}

/* Sidebar menu items */
.sidebar a{
    display:block;
    color:white;
    padding:15px 25px;
    text-decoration:none;
}

/* Hover effect for sidebar links */
.sidebar a:hover{
    background:rgba(255,255,255,.15);
}

/* Main content area that sits to the right of the sidebar */
.content{
    margin-left:260px;
    padding:25px;
}

/* Standard card styling for dashboard panels */
.card{
    border:none;
    border-radius:15px;
    box-shadow:0 5px 15px rgba(0,0,0,.08);
}

/* Full-size map container */
#map{
    height:450px;
}

</style>

<body>