<?php
// Start the PHP session so we can check whether the user is already logged in.
session_start();

// If the user is already authenticated, send them to the admin dashboard.
// This prevents a logged-in user from seeing the login page again.
if(isset($_SESSION['userID'])){
    header("Location: admin/dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CrimSense - Login</title>

    <!-- Bootstrap 5 framework for layout and form styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons for decorative icons in the login page -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* Center the login form vertically and horizontally on the page */
        body{
            background:#eef3f8;
            height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            font-family:Segoe UI, sans-serif;
        }

        /* Card container styling for the login panel */
        .login-card{
            width:420px;
            border:none;
            border-radius:15px;
            box-shadow:0 10px 25px rgba(0,0,0,.15);
        }

        /* Blue header area of the card */
        .card-header{
            background:#0d6efd;
            color:white;
            text-align:center;
            border-radius:15px 15px 0 0 !important;
            padding:30px;
        }

        /* Logo icon size */
        .logo{
            font-size:55px;
        }

        /* System name styling */
        .system-title{
            font-weight:bold;
            font-size:28px;
        }

        /* Make the login button fill the available width */
        .btn-login{
            width:100%;
        }

        /* Input height styling */
        .form-control{
            height:45px;
        }
    </style>
</head>

<body>

<div class="card login-card">

    <!-- Top section of the login screen with brand name and icon -->
    <div class="card-header">

        <div class="logo">
            <i class="bi bi-shield-lock-fill"></i>
        </div>

        <div class="system-title">
            CrimSense
        </div>

        <small>
            Crime Mapping and Predictive Analytics System
        </small>

    </div>

    <!-- Form area for username and password -->
    <div class="card-body p-4">
 
        <form action="authenticate.php" method="POST">

            <div class="mb-3">

                <label class="form-label">
                    Username
                </label>

                <input
                    type="text"
                    name="username"
                    class="form-control"
                    required>

            </div>

            <div class="mb-4">

                <label class="form-label">
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    class="form-control"
                    required>

            </div>

            <button class="btn btn-primary btn-login">

                <i class="bi bi-box-arrow-in-right"></i>

                Login

            </button>

        </form>

    </div>

    <!-- Footer with the current year -->
    <div class="card-footer text-center">

        <small>
            © <?php echo date("Y"); ?> CrimSense
        </small>

    </div>

</div>

<!-- Include Bootstrap JavaScript so modal popup can work -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

<!-- Error modal shown when login fails -->
<div class="modal fade" id="loginErrorModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-danger text-white py-2">
                <h6 class="modal-title mb-0">
                    <i class="bi bi-x-circle-fill"></i>
                    Login Failed
                </h6>
            </div>

            <div class="modal-body text-center py-3">

                <i class="bi bi-x-circle-fill text-danger"
                   style="font-size:50px;"></i>

                <p class="mt-3 mb-0">
                    Invalid username or password.
                </p>

            </div>

            <div class="modal-footer py-2">

                <button
                    type="button"
                    class="btn btn-danger btn-sm"
                    data-bs-dismiss="modal">
                    OK
                </button>

            </div>

        </div>
    </div>
</div>

<?php if(isset($_GET['error'])){ ?>

<script>
document.addEventListener("DOMContentLoaded", function () {

    // Create the Bootstrap modal instance when the page loads
    var modal = new bootstrap.Modal(
        document.getElementById("loginErrorModal")
    );

    // Show the login error popup
    modal.show();

    // Remove the ?error query string from the URL after the message is shown
    window.history.replaceState({}, document.title, window.location.pathname);

});
</script>

<?php } ?>
</body>
</html>