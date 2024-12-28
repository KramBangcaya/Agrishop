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

<!--div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <!?php if(!isset($_SESSION['customer'])):
                    ?>

                    <p>
                        <a href="login.php" class="btn btn-md btn-danger"><!?php echo LANG_VALUE_160; ?></a>
                    </p>
                <!?php else: ?>

                <h3 class="special"><!?php echo LANG_VALUE_26; ?></h3>
                <div class="cart">


                         <!?php
                        var_dump($_SESSION);
                        $table_total_price = 0;

                        $i=0;
                        foreach($_SESSION['cart_p_id'] as $key => $value)
                        {
                            $i++;
                            $arr_cart_p_id[$i] = $value;
                        }

                        $i=0;
                        foreach($_SESSION['cart_p_qty'] as $key => $value)
                        {
                            $i++;
                            $arr_cart_p_qty[$i] = $value;
                        }

                        $i=0;
                        foreach($_SESSION['cart_p_current_price'] as $key => $value)
                        {
                            $i++;
                            $arr_cart_p_current_price[$i] = $value;
                        }

                        $i=0;
                        foreach($_SESSION['cart_p_name'] as $key => $value)
                        {
                            $i++;
                            $arr_cart_p_name[$i] = $value;
                        }

                        $i=0;
                        foreach($_SESSION['cart_p_featured_photo'] as $key => $value)
                        {
                            $i++;
                            $arr_cart_p_featured_photo[$i] = $value;
                        }
                        $i=0;
                        foreach($_SESSION['cart_s_name'] as $key => $value)
                        {
                            $i++;
                            $cart_s_name[$i] = $value;
                        }
                        $i=0;
                        foreach($_SESSION['cart_s_last'] as $key => $value)
                        {
                            $i++;
                            $cart_s_last[$i] = $value;
                        }
                        $i=0;
                        foreach($_SESSION['cart_qr'] as $key => $value)
                        {
                            $i++;
                            $cart_qr[$i] = $value;
                        }
                        ?>
<<<<<<< HEAD







<?php
// Grouping products by seller (using first and last name as the key)
$sellers = [];
for ($i = 1; $i <= count($arr_cart_p_id); $i++) {
    $seller = $cart_s_name[$i] . ' ' . $cart_s_last[$i];
    if (!isset($sellers[$seller])) {
        $sellers[$seller] = [
            'products' => [],
            'qr_code' => $cart_qr[$i],
            'total_cost' => 0,
            'first_name' => $cart_s_name[$i],
            'last_name' => $cart_s_last[$i],
        ];
    }
    $row_total_price = $arr_cart_p_current_price[$i] * $arr_cart_p_qty[$i];
    $sellers[$seller]['products'][] = [
        'name' => $arr_cart_p_name[$i],
        'price' => $arr_cart_p_current_price[$i],
        'quantity' => $arr_cart_p_qty[$i],
        'total' => $row_total_price,
    ];
    $sellers[$seller]['total_cost'] += $row_total_price;
}
?>

<?php foreach ($sellers as $seller_key => $seller_data): ?>
    <div class="clear"></div>

    <!-- Seller Information -->
    <h3 class="special">
        <?php echo LANG_VALUE_33; ?> for <?php echo $seller_data['first_name']; ?> <?php echo $seller_data['last_name']; ?>
        Total Cost: ₱<?php echo $seller_data['total_cost']; ?>
    </h3>

    <!-- Payment Method -->
    <div class="row">
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-12 form-group">
                    <label for=""><?php echo LANG_VALUE_34; ?> *</label>
                    <select name="payment_method" class="form-control select2" id="advFieldsStatus">
                        <option value=""><?php echo LANG_VALUE_35; ?></option>
                        <option value="PayPal">CASH ON DELIVERY</option>
                        <option value="Bank Deposit">QR CODE</option>
                    </select>
=======
                        <!?php for($i=1;$i<=count($arr_cart_p_id);$i++): ?>
                            <div class="clear"></div>
                            <!?php
                                $row_total_price = $arr_cart_p_current_price[$i]*$arr_cart_p_qty[$i];
                                $table_total_price = $table_total_price + $row_total_price;
                                ?>
                <h3 class="special"><!?php echo LANG_VALUE_33; ?> for <!?php echo $arr_cart_p_name[$i]; ?>  Total Cost: <!?php echo LANG_VALUE_1; ?>
                <!?php echo $row_total_price; ?></h3>
                <div class="row">
                <div class="col-md-4">
	                            <div class="row">

	                                <div class="col-md-12 form-group">
	                                    <label for=""><!?php echo LANG_VALUE_34; ?> *</label>
	                                    <select name="payment_method" class="form-control select2" id="advFieldsStatus">
	                                        <option value=""><!?php echo LANG_VALUE_35; ?></option>
	                                        <option value="PayPal">CASH ON DELIVERY</option>
	                                        <option value="Bank Deposit">QR CODE</option>
	                                    </select>
	                                </div>
	                            </div>
		                    </div>
>>>>>>> 3120d23dc2f1c3a4efecb44bc524880e78ca78f3
                </div>
            </div>
        </div>
    </div>

    <!-- QR Code -->
    <form  method="post" id="bank_form">
        <div class="col-md-12 form-group">
            <label for="">
                <?php echo $seller_data['first_name']; ?> <?php echo $seller_data['last_name']; ?><br>
                <span style="font-size:12px; font-weight:normal;">(<?php echo "Scan The QR Here to Pay"; ?>)</span>
            </label>
            <img
                src="<?php echo API_BASE_URL . '/storage/' . str_replace('\/', '/', trim($seller_data['qr_code'])); ?>"
                alt="QR Code"
                style="max-width:100%; height:auto;">
        </div>

        <!-- Proof of Payment Upload -->
        <div class="col-md-12 form-group">
            <label for="">
                <?php echo "Upload here the Proof of Payment"; ?><br>
                <span style="font-size:12px; font-weight:normal;">(<?php echo "Supporting Documents"; ?>)</span>
            </label>
            <input type="file" class="btn btn-primary" value="Upload" name="form3">
        </div>
    </form>

<?php endforeach; ?>




                <form action="place-order.php" method="post" enctype="multipart/form-data" id="order_form">
                            <div class="col-md-12 form-group">
                                <label for=""><!?php echo $cart_s_name[$i]; ?> <!?php echo $cart_s_last[$i]; ?><br>
                                    <span style="font-size:12px;font-weight:normal;">(<!?php echo "Scan The QR Here to Pay"; ?>)</span></label>
                                <img src="<!?php echo API_BASE_URL . '/storage/' . str_replace('\/', '/', trim($cart_qr[$i])); ?>" alt="">
                            </div>

<<<<<<< HEAD





=======
                            <div class="col-md-12 form-group">
                                <label for=""><!?php echo "Upload here the Proof of Payment"; ?> <br>
                                    <span style="font-size:12px;font-weight:normal;">(<!?php echo "Supporting Documents"; ?>)</span></label>
                                <input type="file" class="btn btn-primary" value="Upload" name="photo" required>
                            </div>
>>>>>>> 3120d23dc2f1c3a4efecb44bc524880e78ca78f3

                            <input type="submit" class="btn btn-primary" value="Place Order">
                        </form>

                        <!?php endfor; ?>
                </div>
                <div class="cart-buttons">
                    <ul>

<<<<<<< HEAD
                        <li><a href="cart.php" class="btn btn-primary"><?php echo LANG_VALUE_21; ?></a></li>
                        <li><a href="customer-order.php" class="btn btn-primary"><?php echo "Place Order"; ?></a></li>
=======
                        <li><a href="cart.php" class="btn btn-primary"><!?php echo LANG_VALUE_21; ?></a></li>
                        <li><a href="#" class="btn btn-primary"><!?php echo "Proceed"; ?></a></li>
>>>>>>> 3120d23dc2f1c3a4efecb44bc524880e78ca78f3
                    </ul>
                </div>
                <!?php endif; ?>
            </div>
        </div>
    </div>
</div-->


<?php require_once('footer.php'); ?>

