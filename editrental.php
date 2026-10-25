
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

// Go back to the customer list if the rental does not exist
if (!$rental) {
    header("Location: index.php");
    exit();
}

// Get the customer ID so we can return to the correct rental page
$customer_id = $rental['customer_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Rental</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container">
    <header>
        <h1>Edit Rental Record</h1>
        <p>Update the details of this dress rental.</p>
    </header>

    <section class="form-card">

        <!-- Form for updating the selected rental -->
        <form action="core/handleForms.php" method="POST">

            <!-- Keep the IDs needed to update the correct rental -->
            <input type="hidden" name="rental_id" value="<?php echo $rental_id; ?>">
            <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">

            <label for="dress_name">Dress Name</label>
            <input
                type="text"
                id="dress_name"
                name="dress_name"
                value="<?php echo htmlspecialchars($rental['dress_name']); ?>"
                required
            >

            <label for="rental_date">Rental Date</label>
            <input
                type="date"
                id="rental_date"
                name="rental_date"
                value="<?php echo htmlspecialchars($rental['rental_date']); ?>"
                required
            >

            <label for="return_date">Return Date</label>
            <input
                type="date"
                id="return_date"
                name="return_date"
                value="<?php echo htmlspecialchars($rental['return_date']); ?>"
                required
            >

            <label for="rental_fee">Rental Fee (₱)</label>
            <input
                type="number"
                id="rental_fee"
                name="rental_fee"
                min="0"
                step="0.01"
                value="<?php echo htmlspecialchars($rental['rental_fee']); ?>"
                required
            >

            <label for="status">Status</label>
            <select id="status" name="status" required>
                <option value="Rented" <?php echo $rental['status'] === 'Rented' ? 'selected' : ''; ?>>Rented</option>
                <option value="Returned" <?php echo $rental['status'] === 'Returned' ? 'selected' : ''; ?>>Returned</option>
                <option value="Overdue" <?php echo $rental['status'] === 'Overdue' ? 'selected' : ''; ?>>Overdue</option>
            </select>

            <!-- Save the updated rental details -->
            <button type="submit" name="updateRentalBtn">Save Changes</button>

            <!-- Return to this customer's rental list -->
            <a class="view-btn" href="viewrentals.php?customer_id=<?php echo $customer_id; ?>">Cancel</a>
        </form>
    </section>
</div>

</body>
</html>
