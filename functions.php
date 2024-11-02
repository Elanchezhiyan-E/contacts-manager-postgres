<?php
// Function to establish PostgreSQL connection
function connectPostgreSQL() {
    $host = 'localhost';
    $port = '5432';
    $dbname = 'contacts_manager';
    $user = 'wrath';
    $password = '1234';

    $connString = "host={$host} dbname={$dbname} user={$user} password={$password}";
    $conn = pg_connect($connString);

    if (!$conn) {
        die("Connection failed: " . pg_last_error());
    }

    return $conn;
}

// Function to authenticate user
function authenticateUser($username, $password) {
    $conn = connectPostgreSQL();

    $username = pg_escape_string($username);
    $password = pg_escape_string($password);

    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = pg_query($conn, $query);

    if (!$result) {
        die("Error in SQL query: " . pg_last_error($conn));
    }

    $user = pg_fetch_assoc($result);

    return $user ? true : false;
}

// Function to register user
function registerUser($username, $email, $password) {
    $conn = connectPostgreSQL();

    $username = pg_escape_string($username);
    $email = pg_escape_string($email);
    $password = pg_escape_string($password);

    // Check if username or email already exists
    $query = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
    $result = pg_query($conn, $query);

    if (!$result) {
        die("Error in SQL query: " . pg_last_error($conn));
    }

    $existingUser = pg_fetch_assoc($result);

    if ($existingUser) {
        return false; // User already exists
    }

    // Insert new user
    $query = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";
    $result = pg_query($conn, $query);

    if (!$result) {
        die("Error in SQL query: " . pg_last_error($conn));
    }

    return true;
}

// Function to insert contact
function insertContact($name, $email, $phone, $address, $username) {
    $conn = connectPostgreSQL();

    $name = pg_escape_string($name);
    $email = pg_escape_string($email);
    $phone = pg_escape_string($phone);
    $address = pg_escape_string($address);
    $username = pg_escape_string($username);

    $query = "INSERT INTO contacts (name, email, phone, address, user_id) VALUES ('$name', '$email', '$phone', '$address', '$username')";
    $result = pg_query($conn, $query);

    if (!$result) {
        die("Error in SQL query: " . pg_last_error($conn));
    }

    return pg_last_oid($result);
}

// Function to retrieve contacts
function getContacts($username) {
    $conn = connectPostgreSQL();

    $username = pg_escape_string($username);

    $query = "SELECT * FROM contacts WHERE user_id = '$username'";
    $result = pg_query($conn, $query);

    if (!$result) {
        die("Error in SQL query: " . pg_last_error($conn));
    }

    $contacts = pg_fetch_all($result);

    return $contacts;
}

// Function to delete contact
function deleteContact($contactId) {
    $conn = connectPostgreSQL();

    $contactId = pg_escape_string($contactId);

    $query = "DELETE FROM contacts WHERE id = '$contactId'";
    $result = pg_query($conn, $query);

    if (!$result) {
        die("Error in SQL query: " . pg_last_error($conn));
    }

    return pg_affected_rows($result);
}

// Function to get contact by ID
function getContactById($contactId) {
    $conn = connectPostgreSQL();

    $contactId = pg_escape_string($contactId);

    $query = "SELECT * FROM contacts WHERE id = '$contactId'";
    $result = pg_query($conn, $query);

    if (!$result) {
        die("Error in SQL query: " . pg_last_error($conn));
    }

    $contact = pg_fetch_assoc($result);

    return $contact;
}

// Function to update contact
function updateContact($contactId, $name, $email, $phone, $address) {
    $conn = connectPostgreSQL();

    $contactId = pg_escape_string($contactId);
    $name = pg_escape_string($name);
    $email = pg_escape_string($email);
    $phone = pg_escape_string($phone);
    $address = pg_escape_string($address);

    $query = "UPDATE contacts SET name = '$name', email = '$email', phone = '$phone', address = '$address' WHERE id = '$contactId'";
    $result = pg_query($conn, $query);

    if (!$result) {
        die("Error in SQL query: " . pg_last_error($conn));
    }

    return pg_affected_rows($result) > 0;
}
?>
