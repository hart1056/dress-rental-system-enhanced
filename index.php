
<?php
// Get the database connection and functions
require_once 'core/models.php';

// Make sure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get the list of registered customers
$customers = getAllCustomers($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dress Rental System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container">
    <header>
        <h1>Dress Rental System</h1>
        <p>Manage customers and their dress rental records.</p>
        <p>
            Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?>!
            <a href="logout.php">Logout</a>
        </p>
    </header>

    <section class="form-card">
        <h2>Add New Customer</h2>

        <form action="core/handleForms.php" method="POST">
            <label for="customer_name">Customer Name</label>
            <input type="text" id="customer_name" name="customer_name" required>

            <label for="contact_number">Contact Number</label>
            <input type="text" id="contact_number" name="contact_number" required>

            <button type="submit" name="insertCustomerBtn">Add Customer</button>
        </form>
    </section>

    <section class="table-card">
        <h2>Customer List</h2>

        <?php if (count($customers) > 0) { ?>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer Name</th>
                            <th>Contact Number</th>
                            <th>Added By</th>
                            <th>Last Updated By</th>
                            <th>Last Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($customers as $customer) { ?>
                            <tr>
                                <td>
                                    <?php echo htmlspecialchars($customer['customer_id']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($customer['customer_name']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($customer['contact_number']); ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $customer['added_by_username'] ?? 'Unknown'
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $customer['last_updated_by_username'] ?? 'Not updated'
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $customer['last_updated'] ?? 'Not updated'
                                    );
                                    ?>
                                </td>

                                <td class="actions">
                                    <a class="view-btn"
                                       href="viewrentals.php?customer_id=<?php echo urlencode($customer['customer_id']); ?>">
                                        View Rentals
                                    </a>

                                    <a class="edit-btn"
                                       href="editcustomer.php?customer_id=<?php echo urlencode($customer['customer_id']); ?>">
                                        Edit
                                    </a>

                                    <a class="delete-btn"
                                       href="deletecustomer.php?customer_id=<?php echo urlencode($customer['customer_id']); ?>">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

        <?php } else { ?>

            <p class="empty-message">
                No customers added yet. Add your first customer above.
            </p>

        <?php } ?>
    </section>
</div>

</body>
</html>
