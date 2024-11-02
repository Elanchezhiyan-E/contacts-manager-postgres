<?php
session_start();
require 'functions.php';

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

$message = '';

// Check if contact ID is provided
if (isset($_POST['contact_id'])) {
    $contactId = $_POST['contact_id'];
    $contact = getContactById($contactId);

    if ($contact) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_contact'])) {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $address = $_POST['address'];

            if (empty($name) || empty($email) || empty($phone) || empty($address)) {
                $message = 'Please fill in all fields.';
            } else {
                $updated = updateContact($contactId, $name, $email, $phone, $address);

                if ($updated) {
                    $message = 'Contact updated successfully!';
                    header('Location: contacts.php');
                    exit;
                } else {
                    $message = 'Failed to update contact. Please try again.';
                }
            }
        }
    } else {
        $message = 'Contact not found.';
    }
} else {
    header('Location: contacts.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Contact</title>
    <link rel="stylesheet" href="./styles.css">
</head>
<body>
    <h2>Edit Contact</h2>
    <p><?php echo $message; ?></p>
    <?php if ($contact): ?>
        <form method="POST" action="edit_contact.php">
            <input type="hidden" name="contact_id" value="<?php echo $contact['id']; ?>">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo $contact['name']; ?>" required>
            <br>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo $contact['email']; ?>" required>
            <br>
            <label for="phone">Phone Number:</label>
            <input type="text" id="phone" name="phone" value="<?php echo $contact['phone']; ?>" required>
            <br>
            <label for="address">Address:</label>
            <textarea id="address" name="address" rows="4" required><?php echo $contact['address']; ?></textarea>
            <br>
            <button type="submit" name="update_contact">Update Contact</button>
            <button type="button" onclick="window.location.href='contacts.php'">Cancel</button>
        </form>
    <?php endif; ?>
</body>
</html>
