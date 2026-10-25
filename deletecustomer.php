
<?php
// Get the database connection and functions
require_once 'core/models.php';

// Make sure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if a valid customer ID was provided
if (!isset($_GET['customer_id']) || !is_numeric($_GET['customer_id'])) {
    header("Location: index.php");
    exit();
}

// Get the selected customer's information
$customer_id = (int) $_GET['customer_id'];
$customer = getCustomerByID($pdo, $customer_id);

// Go back if the customer does not exist
if (!$customer) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Customer</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container">
    <header>
        <h1>Delete Customer</h1>
        <p>Please confirm before deleting this customer.</p>
    </header>

    <section class="form-card">
        <h2>Are you sure?</h2>

        <p>
            You are about to delete
            <strong><?php echo htmlspecialchars($customer['customer_name']); ?></strong>.
        </p>

        <p>
            This will also delete all rental records belonging to this customer.
        </p>

        <!-- Ask for confirmation before deleting the customer -->
        <form action="core/handleForms.php" method="POST">
            <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">

            <button type="submit" name="deleteCustomerBtn">Yes, Delete Customer</button>

            <!-- Return to the customer list without deleting -->
            <a class="view-btn" href="index.php">Cancel</a>
        </form>
    </section>
</div>

</body>
</html>
