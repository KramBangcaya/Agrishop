<?php require_once('header.php');
require_once('api-config.php');?>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row) {
    $banner_checkout = $row['banner_checkout'];
}
?>

<?php
if(!isset($_SESSION['cart_p_id'])) {
    header('location: cart.php');
    exit;
}
?>

<div class="page-banner" style="background-image: url(assets/uploads/<?php echo $banner_checkout; ?>)">
    <div class="overlay"></div>
    <div class="page-banner-inner">
        <h1><?php echo LANG_VALUE_22; ?></h1>
    </div>
</div>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <?php if (!isset($_SESSION['customer'])): ?>
                    <p>
                        <a href="login.php" class="btn btn-md btn-danger"><?php echo LANG_VALUE_160; ?></a>
                    </p>
                <?php else:

                    var_dump($_SESSION);?>

                    <h3 class="special"><?php echo LANG_VALUE_26; ?></h3>
                    <div class="cart">
                        <form action="place-order.php" method="post" enctype="multipart/form-data" id="order_form">

                            <?php
                            $table_total_price = 0;

                            for ($i = 1; $i <= count($_SESSION['cart_p_id']); $i++) {
                                $row_total_price = $_SESSION['cart_p_current_price'][$i] * $_SESSION['cart_p_qty'][$i];
                                $table_total_price += $row_total_price;
                            ?>

                                <h3 class="special">
                                    Payment Section for <?php echo $_SESSION['cart_p_name'][$i]; ?>
                                    Total Cost: <?php echo LANG_VALUE_1; ?><?php echo $row_total_price; ?>
                                </h3>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label><?php echo LANG_VALUE_34; ?> *</label>
                                            <select name="payment_method[<?php echo $i; ?>]" class="form-control">
                                                <option value="">Select Payment Method</option>
                                                <option value="COD">CASH ON DELIVERY</option>
                                                <option value="QR">QR CODE</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 form-group">
                                    <label for=""><?php echo $_SESSION['cart_s_name'][$i]; ?> <?php echo $_SESSION['cart_s_last'][$i]; ?><br>
                                        <span style="font-size:12px;font-weight:normal;">(Scan The QR Here to Pay)</span>
                                    </label>
                                    <img src="<?php echo API_BASE_URL . '/storage/' . str_replace('\/', '/', trim($_SESSION['cart_qr'][$i])); ?>" alt="">
                                </div>

                                <div class="col-md-12 form-group">
                                    <label>Upload Proof of Payment</label>
                                    <input type="file" name="photo[<?php echo $i; ?>]" class="form-control" required>
                                </div>














                                <!-- Include hidden inputs to pass product data -->
                                <input type="hidden" name="product_name[<?php echo $i; ?>]" value="<?php echo $_SESSION['cart_p_name'][$i]; ?>">
                                <input type="hidden" name="product_quantity[<?php echo $i; ?>]" value="<?php echo $_SESSION['cart_p_qty'][$i]; ?>">
                                <input type="hidden" name="product_price[<?php echo $i; ?>]" value="<?php echo $_SESSION['cart_p_current_price'][$i]; ?>">
                                <input type="hidden" name="seller_name[<?php echo $i; ?>]" value="<?php echo $_SESSION['cart_s_name'][$i]; ?>">
                                <input type="hidden" name="seller_last[<?php echo $i; ?>]" value="<?php echo $_SESSION['cart_s_last'][$i]; ?>">
                                <input type="hidden" name="seller_contact[<?php echo $i; ?>]" value="<?php echo $_SESSION['s_contact_number'][$i]; ?>">
                                <input type="hidden" name="seller_address[<?php echo $i; ?>]" value="<?php echo $_SESSION['s_address'][$i]; ?>">
                                <input type="hidden" name="seller_id[<?php echo $i; ?>]" value="<?php echo $_SESSION['s_id'][$i]; ?>">




                            <?php } ?>

                            <div class="cart-buttons">
                                <ul>
                                    <li><a href="cart.php" class="btn btn-primary"><?php echo LANG_VALUE_21; ?></a></li>

                                    <li><button type="submit" class="btn btn-primary">Proceed</button></li>
                                </ul>
                            </div>
                        </form>
                    </div>

                <?php endif; ?>
            </div>
        </div>
    </div>
</div>




<?php require_once('footer.php'); ?>

