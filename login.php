<?php
include 'connection.php';
session_start();

$emailError = $passwordError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Server-side validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailError = "Invalid email format.";
    }

    if (strlen($password) < 6) {
        $passwordError = "Password must be at least 6 characters.";
    }

    if (!$emailError && !$passwordError) {
        $hashed_password = md5($password);

        $sql = "SELECT * FROM users WHERE email = ? AND password = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $hashed_password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            header("Location: home.php");
            exit;
        } else {
            $emailError = "Invalid email or password!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="logreg.css">
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        <form method="post" action="">
        <input type="email" name="email" placeholder="Email" value="<?php echo isset($email) ? $email : ''; ?>">
        <div style="color: red;"><?php echo $emailError; ?></div>

        <input type="password" name="password" placeholder="Password">
        <div style="color: red;"><?php echo $passwordError; ?></div>

        <button type="submit">Login</button>
    </form>
        <p>Don't have an account? <a href="register.php">Register here</a>.</p>
    </div>
</body>
</html>
