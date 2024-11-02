<?php
session_start();
require 'functions.php';

$message = '';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_contact'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    if (empty($name) || empty($email) || empty($phone) || empty($address)) {
        $message = 'Please fill in all fields.';
    } else {
        // Insert contact into PostgreSQL
        $insertedId = insertContact($name, $email, $phone, $address, $_SESSION['username']);

        if ($insertedId) {
            $message = 'Contact added successfully!';
            header('Location: contacts.php');
            exit;
        } else {
            $message = 'Failed to add contact. Please try again.';
        }
    }
}

// Handle delete contact form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_contact'])) {
    $contactId = $_POST['delete_contact'];

    // Delete contact from PostgreSQL
    $deleteCount = deleteContact($contactId);

    if ($deleteCount > 0) {
        $message = 'Contact deleted successfully!';
        header('Location: contacts.php');
        exit;
    } else {
        $message = 'Failed to delete contact. Please try again.';
    }
}

// Fetch contacts of the logged-in user
$contacts = getContacts($_SESSION['username']);

// Handle logout
if (isset($_POST['logout'])) {
    // Unset all session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    // Redirect to index.php
    header('Location: index.php');
    exit;
}

?>


<!DOCTYPE html>
<html>
<head>
    <title>Contacts</title>
    <link rel="stylesheet" href="./styles.css">
</head>
<body>
    <h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2>
    <p>This is the contacts page. You can add, edit, and delete your contacts here.</p>
    
    <!-- Display success or error message -->
    <?php if ($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>
    
    <!-- Contact Form -->
    <form method="POST" action="contacts.php">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
        <br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <br>
        <label for="phone">Phone Number:</label>
        <input type="text" id="phone" name="phone" required>
        <br>
        <label for="address">Address:</label>
        <textarea id="address" name="address" rows="4" required></textarea>
        <br>
        <button type="submit" name="add_contact">Add Contact</button>
    </form>
    
    <!-- Display existing contacts -->
    <h3>Your Contacts:</h3>
    <ul>
        <?php foreach ($contacts as $contact): ?>
            <li class="contact-item">
                <strong>Name:</strong> <?php echo $contact['name']; ?><br>
                <strong>Email:</strong> <?php echo $contact['email']; ?><br>
                <strong>Phone:</strong> <?php echo $contact['phone']; ?><br>
                <strong>Address:</strong> <?php echo $contact['address']; ?><br>
                <!-- Edit and Delete Buttons -->
                <form method="POST" action="edit_contact.php" style="display:inline;">
                    <input type="hidden" name="contact_id" value="<?php echo $contact['id']; ?>">
                    <button type="submit" class="edit-btn">Edit</button>
                </form>
                <form method="POST" action="contacts.php" style="display:inline;">
                    <input type="hidden" name="delete_contact" value="<?php echo $contact['id']; ?>">
                    <button type="submit" class="delete-btn">Delete</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
    <form method="POST" action="contacts.php">
        <button type="submit" name="logout">Logout</button>
    </form>
</body>
</html>
