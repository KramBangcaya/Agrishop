
<?php
// Access user_id
$user_id = $_SESSION['user_id'] ?? null;

// Handle cases where user_id is required
if (!$user_id) {
    die('User ID not found. Please log in again.');
}

// Use $user_id for your logic
?>


<div class="user-sidebar">
    <ul>

    <a href="customer-profile-update.php?id=<?php echo $user_id; ?>"><button class="btn btn-danger"><?php echo LANG_VALUE_117; ?></button></a>
        <a href="customer-billing-shipping-update.php"><button class="btn btn-danger">Report Seller</button></a>

        <a href="customer-order.php?id=<?php echo $user_id; ?>"><button class="btn btn-danger"><?php echo LANG_VALUE_24; ?></button></a>
        <a href="logout.php"><button class="btn btn-danger"><?php echo LANG_VALUE_14; ?></button></a>
    </ul>
</div>
