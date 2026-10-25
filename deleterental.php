
<?php
// Get the database connection and functions
require_once 'core/models.php';

// Make sure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if a valid rental ID was provided
if (!isset($_GET['rental_id']) || !is_numeric($_GET['rental_id'])) {
    header("Location: index.php");
    exit();
}

// Get the selected rental record
$rental_id = (int) $_GET['rental_id'];
$rental = getRentalByID($pdo, $rental_id);

// Return to the customer list if the rental does not exist
if (!$rental) {
    header("Location: index.php");
    exit();
}

// Get the customer ID to return to the correct rental page
$customer_id = $rental['customer_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Rental</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container">
    <header>
        <h1>Delete Rental Record</h1>
        <p>Please confirm before deleting this rental.</p>
    </header>

    <section class="form-card">
        <h2>Are you sure?</h2>

        <p>
            You are about to delete the rental for
            <strong><?php echo htmlspecialchars($rental['dress_name']); ?></strong>.
        </p>

        <p>This action cannot be undone.</p>

        <!-- Ask for confirmation before deleting the rental -->
        <form action="core/handleForms.php" method="POST">
            <input type="hidden" name="rental_id" value="<?php echo $rental_id; ?>">
            <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">

            <button type="submit" name="deleteRentalBtn">Yes, Delete Rental</button>

            <!-- Return to the rental list without deleting -->
            <a class="view-btn" href="viewrentals.php?customer_id=<?php echo $customer_id; ?>">
                Cancel
            </a>
        </form>
    </section>
</div>

</body>
</html>
