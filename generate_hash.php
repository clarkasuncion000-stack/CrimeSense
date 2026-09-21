<?php

// Temporary utility for generating a password hash before saving it in the database.

// Only generate a hash after the form has been submitted with a POST request.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Read the password entered in the form.
    $password = $_POST['password'];

    // Convert the plain-text password into a secure one-way hash.
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Display the generated hash so it can be copied into the users table.
    echo "<h3>Password Hash</h3>";
    echo "<textarea rows='3' cols='100'>$hash</textarea>";
    echo "<br><br>";

    // Escape the original password before displaying it as HTML text.
    echo "<strong>Original Password:</strong> " . htmlspecialchars($password);
}

?>

<!DOCTYPE html>
<html>
<head>
    <!-- Browser title shown on the hash generator tab. -->
    <title>Password Hash Generator</title>
</head>
<body>

<!-- Heading that identifies the purpose of this temporary page. -->
<h2>Generate Password Hash</h2>

<!-- Submit the password back to this same PHP file using POST. -->
<form method="POST">
    <label>Password:</label><br>
    <!-- The required attribute prevents submitting an empty password. -->
    <input type="text" name="password" required>
    <br><br>

    <!-- Start the server-side hashing process. -->
    <button type="submit">Generate Hash</button>
</form>

</body>
</html>