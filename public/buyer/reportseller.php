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
    require_once('admin/inc/config.php'); // Include your database configuration
// var_dump($_SESSION['user_id']);
    // Get the email from the session
    $cust_email = $_SESSION['user_id'];
    // $buyer_id = $_SESSION[];
    // Fetch orders from the database
    $query = "SELECT * FROM reports WHERE userID = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$cust_email]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // var_dump($orders);
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

                    <!-- Check if the user has already provided feedback for this product -->
                    <?php
                    $feedbackQuery = "SELECT * FROM reports WHERE userID = ??";
                    $stmt = $pdo->prepare($feedbackQuery);
                    $stmt->execute([$_SESSION['user_id']]);
                    $feedback = $stmt->fetch(PDO::FETCH_ASSOC);
                    ?>

                    <h2>Seller Name:</h2>
                    <span><?php echo htmlspecialchars($order['buyer_name']); ?></span>

                    <div style="margin-top: 10px; font-size: medium;">


                        <label>Report date time: </label>
                        <span><?php echo htmlspecialchars($order['created_at']); ?></span><br>



                        <label>Reason: </label>
                        <span><?php echo htmlspecialchars($order['reason']); ?></span><br>

                        <label>Reply: </label>
                        <span><?php echo htmlspecialchars($order['reply']); ?></span><br>
                        <label>Reply Report date time: </label>
                        <span><?php echo htmlspecialchars($order['updated_at']); ?></span><br>

                        <label>Proof/attached file: </label>
                        <span><?php echo htmlspecialchars($order['proof']); ?></span><br>
                        <br>
                        <h4>


                        <a style="float: right;" href="#"><button style="color:green;" class="btn btn-success">Confirm Report</button></a><br>
                        <a style="float: right;" href="customer-billing-shipping-update.php"><button style="color: red;" class="btn btn-danger">Cancel Report</button></a><br>





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

<?php require_once('footer.php'); ?>
