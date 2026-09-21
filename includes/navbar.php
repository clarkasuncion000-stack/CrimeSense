<nav class="navbar navbar-expand-lg bg-white shadow-sm rounded mb-4">

<div class="container-fluid">

    <!-- Page title shown in the top navigation area -->
    <h4 class="mb-0">
        Dashboard
    </h4>

    <!-- Display the logged-in user's full name -->
    <div>
        Welcome,
        <strong>
            <?php echo $_SESSION['fullname']; ?>
        </strong>
    </div>

</div>

</nav>