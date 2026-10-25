
<?php
require_once 'models.php';

// Make sure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Get the ID of the logged-in user
$user_id = $_SESSION['user_id'];

// Add a new customer
if (isset($_POST['insertCustomerBtn'])) {
    $customer_name = trim($_POST['customer_name']);
    $contact_number = trim($_POST['contact_number']);

    if ($customer_name !== '' && $contact_number !== '') {
        insertCustomer(
            $pdo,
            $customer_name,
            $contact_number,
            $user_id
        );
    }

    header("Location: ../index.php");
    exit();
}

// Update a customer
if (isset($_POST['updateCustomerBtn'])) {
    $customer_id = $_POST['customer_id'];
    $customer_name = trim($_POST['customer_name']);
    $contact_number = trim($_POST['contact_number']);

    if ($customer_name !== '' && $contact_number !== '') {
        updateCustomer(
            $pdo,
            $customer_id,
            $customer_name,
            $contact_number,
            $user_id
        );
    }

    header("Location: ../index.php");
    exit();
}

// Delete a customer and record who deleted it
if (isset($_POST['deleteCustomerBtn'])) {
    $customer_id = (int) $_POST['customer_id'];

    deleteCustomer($pdo, $customer_id, $user_id);

    header("Location: ../index.php");
    exit();
}

// Add a rental record
if (isset($_POST['insertRentalBtn'])) {
    $customer_id = $_POST['customer_id'];
    $dress_name = trim($_POST['dress_name']);
    $rental_date = $_POST['rental_date'];
    $return_date = $_POST['return_date'];
    $rental_fee = $_POST['rental_fee'];
    $status = $_POST['status'];

    if (
        $dress_name !== '' &&
        $rental_date !== '' &&
        $return_date !== '' &&
        $rental_fee !== ''
    ) {
        insertRental(
            $pdo,
            $customer_id,
            $dress_name,
            $rental_date,
            $return_date,
            $rental_fee,
            $status,
            $user_id
        );
    }

    header("Location: ../viewrentals.php?customer_id=" . urlencode($customer_id));
    exit();
}

// Update a rental record
if (isset($_POST['updateRentalBtn'])) {
    $rental_id = $_POST['rental_id'];
    $customer_id = $_POST['customer_id'];
    $dress_name = trim($_POST['dress_name']);
    $rental_date = $_POST['rental_date'];
    $return_date = $_POST['return_date'];
    $rental_fee = $_POST['rental_fee'];
    $status = $_POST['status'];

    if (
        $dress_name !== '' &&
        $rental_date !== '' &&
        $return_date !== '' &&
        $rental_fee !== ''
    ) {
        updateRental(
            $pdo,
            $rental_id,
            $dress_name,
            $rental_date,
            $return_date,
            $rental_fee,
            $status,
            $user_id
        );
    }

    header("Location: ../viewrentals.php?customer_id=" . urlencode($customer_id));
    exit();
}


 // Delete a rental record and record who deleted it
if (isset($_POST['deleteRentalBtn'])) {
    $rental_id = (int) $_POST['rental_id'];
    $customer_id = (int) $_POST['customer_id'];

    deleteRental($pdo, $rental_id, $user_id);

    header("Location: ../viewrentals.php?customer_id=" . urlencode($customer_id));
    exit();
}

?>
