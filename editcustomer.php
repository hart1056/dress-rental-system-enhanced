
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

// Get the information of the selected customer
$customer_id = (int) $_GET['customer_id'];
$customer = getCustomerByID($pdo, $customer_id);

// Go back to the customer list if the customer is not found
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
    <title>Edit Customer</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container">
    <header>
        <h1>Edit Customer</h1>
        <p>Update the customer's information below.</p>
    </header>

    <section class="form-card">

        <!-- Form for updating the customer's details -->
        <form action="core/handleForms.php" method="POST">

            <!-- Keep the customer ID for the update process -->
            <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">

            <label for="customer_name">Customer Name</label>
            <input
                type="text"
                id="customer_name"
                name="customer_name"
                value="<?php echo htmlspecialchars($customer['customer_name']); ?>"
                required
            >

            <label for="contact_number">Contact Number</label>
            <input
                type="text"
                id="contact_number"
                name="contact_number"
                value="<?php echo htmlspecialchars($customer['contact_number']); ?>"
                required
            >

            <!-- Save the updated customer information -->
            <button type="submit" name="updateCustomerBtn">Save Changes</button>

            <!-- Go back without saving -->
            <a class="view-btn" href="index.php">Cancel</a>
        </form>
    </section>
</div>

</body>
</html>
