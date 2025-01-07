<?php
require_once('header.php');
require_once('api-config.php');
?>

<?php
// Check if the customer is logged in or not
if(!isset($_SESSION['customer'])) {
    header('location: '.BASE_URL.'logout.php');
    exit;
} else {
    // Get the user ID from the session
    $cust_email = $_SESSION['user_id'];

    // Construct the API URL with the userID
    $api_url = API_BASE_URL . "/report/all_user?id=" . $cust_email;

    // Initialize cURL
    $ch = curl_init();

    // Set the cURL options
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute the cURL request and get the response
    $response = curl_exec($ch);

    // Close the cURL session
    curl_close($ch);

    // Decode the JSON response into an associative array
    $data = json_decode($response, true);

    // Check if the data exists and if there's a valid response
    if (isset($data['data']) && is_array($data['data'])) {
        $orders = $data['data'];
    } else {
        $orders = [];
    }
}
?>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="user-content">
                    <h1>Report History</h1><a style="float: right;" href="customer-billing-shipping-update.php"><button style="float: right;" class="btn btn-danger">Report Seller</button></a><br>
                    <h3 class="special"> </h3>
                    <div class="table-responsive">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="row" style="margin: 0 auto;"> <!-- Centering the inner row -->
                                    <div class="col-md-12 form-group">
                                        <?php foreach ($orders as $order): ?>
                                            <h4>Complainee's Name:</h4>
                                            <span><?php echo htmlspecialchars($order['buyer_name']); ?></span>

                                            <div style="margin-top: 10px; font-size: medium;">
                                                <label>Reason: </label>
                                                <span><?php echo htmlspecialchars($order['reason']); ?></span><br>

                                                <label>Reply: </label>
                                                <span><?php echo htmlspecialchars($order['reply'] ?? 'No reply'); ?></span><br>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>
