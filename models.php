<?php
require_once 'dbConfig.php';


// ============================================
// CUSTOMER FUNCTIONS
// ============================================

// Get all customers and their tracking information
function getAllCustomers($pdo) {
    $sql = "SELECT
                customers.*,
                added_user.username AS added_by_username,
                updated_user.username AS last_updated_by_username
            FROM customers
            LEFT JOIN users AS added_user
                ON customers.added_by = added_user.user_id
            LEFT JOIN users AS updated_user
                ON customers.last_updated_by = updated_user.user_id
            ORDER BY customers.customer_id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// Get one customer using their ID
function getCustomerByID($pdo, $customer_id) {
    $sql = "SELECT * FROM customers WHERE customer_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$customer_id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}


// Add a customer and record who added it
function insertCustomer($pdo, $customer_name, $contact_number, $user_id) {
    $sql = "INSERT INTO customers
            (customer_name, contact_number, added_by)
            VALUES (?, ?, ?)";

    $stmt = $pdo->prepare($sql);

    return $stmt->execute([
        $customer_name,
        $contact_number,
        $user_id
    ]);
}


// Update a customer and record who updated it
function updateCustomer(
    $pdo,
    $customer_id,
    $customer_name,
    $contact_number,
    $user_id
) {
    $sql = "UPDATE customers
            SET customer_name = ?,
                contact_number = ?,
                last_updated_by = ?,
                last_updated = NOW()
            WHERE customer_id = ?";

    $stmt = $pdo->prepare($sql);

    return $stmt->execute([
        $customer_name,
        $contact_number,
        $user_id,
        $customer_id
    ]);
}


// Delete a customer and preserve deletion history
function deleteCustomer($pdo, $customer_id, $user_id) {
    try {
        // Start a transaction
        $pdo->beginTransaction();

        // Get the customer details before deleting
        $sql = "SELECT customer_id, customer_name, contact_number
                FROM customers
                WHERE customer_id = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$customer_id]);

        $customer = $stmt->fetch(PDO::FETCH_ASSOC);

        // Stop if the customer does not exist
        if (!$customer) {
            $pdo->rollBack();
            return false;
        }

        // Get all rental records belonging to this customer
        $sql = "SELECT rental_id, customer_id, dress_name
                FROM rental_records
                WHERE customer_id = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$customer_id]);

        $rentals = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Save the deletion history of each rental
        foreach ($rentals as $rental) {
            $sql = "INSERT INTO rental_deletion_logs
                    (rental_id, customer_id, dress_name, deleted_by)
                    VALUES (?, ?, ?, ?)";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                $rental['rental_id'],
                $rental['customer_id'],
                $rental['dress_name'],
                $user_id
            ]);
        }

        // Save the customer's deletion history
        $sql = "INSERT INTO customer_deletion_logs
                (customer_id, customer_name, contact_number, deleted_by)
                VALUES (?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            $customer['customer_id'],
            $customer['customer_name'],
            $customer['contact_number'],
            $user_id
        ]);

        // Delete the customer's rental records
        $sql = "DELETE FROM rental_records
                WHERE customer_id = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$customer_id]);

        // Delete the customer
        $sql = "DELETE FROM customers
                WHERE customer_id = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$customer_id]);

        // Commit the transaction
        $pdo->commit();

        return true;

    } catch (PDOException $e) {
        // Roll back all changes if something fails
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $e;
    }
}


// ============================================
// RENTAL FUNCTIONS
// ============================================

// Get rental records for a selected customer,
// including tracking usernames
function getRentalsByCustomer($pdo, $customer_id) {
    $sql = "SELECT
                rental_records.*,
                added_user.username AS added_by_username,
                updated_user.username AS last_updated_by_username
            FROM rental_records
            LEFT JOIN users AS added_user
                ON rental_records.added_by = added_user.user_id
            LEFT JOIN users AS updated_user
                ON rental_records.last_updated_by = updated_user.user_id
            WHERE rental_records.customer_id = ?
            ORDER BY rental_records.rental_id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$customer_id]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// Get one rental record
function getRentalByID($pdo, $rental_id) {
    $sql = "SELECT * FROM rental_records WHERE rental_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$rental_id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}


// Add a rental record and record who added it
function insertRental(
    $pdo,
    $customer_id,
    $dress_name,
    $rental_date,
    $return_date,
    $rental_fee,
    $status,
    $user_id
) {
    $sql = "INSERT INTO rental_records
            (customer_id, dress_name, rental_date, return_date,
             rental_fee, status, added_by)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);

    return $stmt->execute([
        $customer_id,
        $dress_name,
        $rental_date,
        $return_date,
        $rental_fee,
        $status,
        $user_id
    ]);
}


// Update a rental record and record who updated it
function updateRental(
    $pdo,
    $rental_id,
    $dress_name,
    $rental_date,
    $return_date,
    $rental_fee,
    $status,
    $user_id
) {
    $sql = "UPDATE rental_records
            SET dress_name = ?,
                rental_date = ?,
                return_date = ?,
                rental_fee = ?,
                status = ?,
                last_updated_by = ?,
                last_updated = NOW()
            WHERE rental_id = ?";

    $stmt = $pdo->prepare($sql);

    return $stmt->execute([
        $dress_name,
        $rental_date,
        $return_date,
        $rental_fee,
        $status,
        $user_id,
        $rental_id
    ]);
}


// Delete a rental record and save its deletion history
function deleteRental($pdo, $rental_id, $user_id) {
    try {
        // Start a transaction
        $pdo->beginTransaction();

        // Get rental details before deleting
        $sql = "SELECT rental_id, customer_id, dress_name
                FROM rental_records
                WHERE rental_id = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$rental_id]);

        $rental = $stmt->fetch(PDO::FETCH_ASSOC);

        // Stop if the rental does not exist
        if (!$rental) {
            $pdo->rollBack();
            return false;
        }

        // Save the deletion history
        $sql = "INSERT INTO rental_deletion_logs
                (rental_id, customer_id, dress_name, deleted_by)
                VALUES (?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            $rental['rental_id'],
            $rental['customer_id'],
            $rental['dress_name'],
            $user_id
        ]);

        // Delete the rental record
        $sql = "DELETE FROM rental_records
                WHERE rental_id = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$rental_id]);

        // Commit the transaction
        $pdo->commit();

        return true;

    } catch (PDOException $e) {
        // Roll back if something fails
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $e;
    }
}


// ============================================
// USER REGISTRATION AND LOGIN FUNCTIONS
// ============================================

// Register a new user with a hashed password
function registerUser(
    $pdo,
    $first_name,
    $last_name,
    $address,
    $age,
    $username,
    $password
) {
    $hashed_password = password_hash(
        $password,
        PASSWORD_DEFAULT
    );

    $sql = "INSERT INTO users
            (first_name, last_name, address, age, username, password)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);

    return $stmt->execute([
        $first_name,
        $last_name,
        $address,
        $age,
        $username,
        $hashed_password
    ]);
}


// Find a user by username
function getUserByUsername($pdo, $username) {
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}


// Check username and password during login
function loginUser($pdo, $username, $password) {
    $user = getUserByUsername($pdo, $username);

    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }

    return false;
}

?>