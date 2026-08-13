<?php

// Temporary Password Hash Generator

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $password = $_POST['password'];
    $hash = password_hash($password, PASSWORD_DEFAULT);

    echo "<h3>Password Hash</h3>";
    echo "<textarea rows='3' cols='100'>$hash</textarea>";
    echo "<br><br>";
    echo "<strong>Original Password:</strong> " . htmlspecialchars($password);
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Password Hash Generator</title>
</head>
<body>

<h2>Generate Password Hash</h2>

<form method="POST">
    <label>Password:</label><br>
    <input type="text" name="password" required>
    <br><br>

    <button type="submit">Generate Hash</button>
</form>

</body>
</html>