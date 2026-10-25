
<?php
require_once 'core/models.php';

$message = "";

// Process the registration form
if (isset($_POST['registerBtn'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $address = trim($_POST['address']);
    $age = (int) $_POST['age'];
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (
        $first_name === '' ||
        $last_name === '' ||
        $address === '' ||
        $age <= 0 ||
        $username === '' ||
        $password === ''
    ) {
        $message = "Please complete all fields correctly.";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
    } elseif (getUserByUsername($pdo, $username)) {
        $message = "Username already exists. Please choose another.";
    } else {
        if (registerUser(
            $pdo,
            $first_name,
            $last_name,
            $address,
            $age,
            $username,
            $password
        )) {
            header("Location: login.php?registered=1");
            exit();
        } else {
            $message = "Registration failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container">
    <header>
        <h1>Create an Account</h1>
        <p>Register to access the Dress Rental System.</p>
    </header>

    <section class="form-card">
        <h2>User Registration</h2>

        <?php if ($message !== '') { ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php } ?>

        <form action="register.php" method="POST">
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name" required>

            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name" required>

            <label for="address">Address</label>
            <input type="text" id="address" name="address" required>

            <label for="age">Age</label>
            <input type="number" id="age" name="age" min="1" required>

            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <button type="submit" name="registerBtn">Register</button>
        </form>

        <p>Already have an account? <a href="login.php">Log in here</a>.</p>
    </section>
</div>

</body>
</html>
