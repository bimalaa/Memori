<?php
include 'connection.php';

$nameError = $usernameError = $emailError = $passwordError = $confirmPasswordError = $numberError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $number = trim($_POST['number']);

    // Server-side validation
    if (empty($name) || strlen($name) < 3) {
        $nameError = "Name must be at least 3 characters.";
    }

    if (empty($username) || strlen($username) < 3) {
        $usernameError = "Username must be at least 3 characters.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailError = "Invalid email format.";
    }

    if (strlen($password) < 6) {
        $passwordError = "Password must be at least 6 characters.";
    }

    if ($password !== $confirm_password) {
        $confirmPasswordError = "Passwords do not match.";
    }

    if (!preg_match('/^[0-9]{10}$/', $number)) {
        $numberError = "Phone number must be 10 digits.";
    }

    // If no errors, insert data into the database
    if (!$nameError && !$usernameError && !$emailError && !$passwordError && !$confirmPasswordError && !$numberError) {
        $hashed_password = md5($password);
        $sql = "INSERT INTO users (name, username, email, password, number) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $name, $username, $email, $hashed_password, $number);

        if ($stmt->execute()) {
            echo "<p style='color: green;'>Registration successful! <a href='login.php'>Login</a></p>";
        } else {
            echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="logreg.css">
</head>
<body>
    <div class="container">
        <h1>Register</h1>
        <form method="post" action="">
        <input type="text" name="name" placeholder="Full Name" value="<?php echo isset($name) ? $name : ''; ?>" >
        <div style="color: red;"><?php echo $nameError; ?></div>

        <input type="text" name="username" placeholder="Username" value="<?php echo isset($username) ? $username : ''; ?>" >
        <div style="color: red;"><?php echo $usernameError; ?></div>

        <input type="email" name="email" placeholder="Email" value="<?php echo isset($email) ? $email : ''; ?>" >
        <div style="color: red;"><?php echo $emailError; ?></div>

        <input type="password" name="password" placeholder="Password" >
        <div style="color: red;"><?php echo $passwordError; ?></div>

        <input type="password" name="confirm_password" placeholder="Confirm Password" >
        <div style="color: red;"><?php echo $confirmPasswordError; ?></div>

        <input type="text" name="number" placeholder="Phone Number" value="<?php echo isset($number) ? $number : ''; ?>" >
        <div style="color: red;"><?php echo $numberError; ?></div>

        <button type="submit">Register</button>
    </form>
        <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </div>
</body>
</html>
