
<?php
require_once 'core/models.php';

$message = "";

// Check if registration was successful
if (isset($_GET['registered'])) {
    $message = "Registration successful! You can now log in.";
}

// Process the login form
if (isset($_POST['loginBtn'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Check the username and password
    $user = loginUser($pdo, $username, $password);

    if ($user) {
        // Store the logged-in user's information in the session
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['first_name'] = $user['first_name'];

        header("Location: index.php");
        exit();
    } else {
        $message = "Incorrect username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container">
    <header>
        <h1>Dress Rental System</h1>
        <p>Please log in to continue.</p>
    </header>

    <section class="form-card">
        <h2>Login</h2>

        <?php if ($message !== '') { ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php } ?>

        <form action="login.php" method="POST">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" name="loginBtn">Log In</button>
        </form>

        <p>Don't have an account? <a href="register.php">Register here</a>.</p>
    </section>
</div>

</body>
</html>
