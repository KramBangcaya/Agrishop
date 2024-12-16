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

                <?php if(!isset($_SESSION['customer'])):
                    ?>

                    <p>
                        <a href="login.php" class="btn btn-md btn-danger"><?php echo LANG_VALUE_160; ?></a>
                    </p>
                <?php else: ?>

                <h3 class="special"><?php echo LANG_VALUE_26; ?></h3>
                <div class="cart">


                         <?php
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












                </div>





                <div class="cart-buttons">
                    <ul>

                        <li><a href="cart.php" class="btn btn-primary"><?php echo LANG_VALUE_21; ?></a></li>
                        <li><a href="customer-order.php" class="btn btn-primary"><?php echo "Place Order"; ?></a></li>
                    </ul>
                </div>



                <?php endif; ?>

            </div>
        </div>
    </div>
</div>


<?php require_once('footer.php'); ?>
