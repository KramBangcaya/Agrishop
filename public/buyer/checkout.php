<?php require_once('header.php'); ?>

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

                <?php if(!isset($_SESSION['customer'])): ?>

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
                        foreach($_SESSION['cart_size_id'] as $key => $value)
                        {
                            $i++;
                            $arr_cart_size_id[$i] = $value;
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
                        ?>


                        <?php for($i=1;$i<=count($arr_cart_p_id);$i++): ?>

                            <div class="clear"></div>

                            <?php
                                $row_total_price = $arr_cart_p_current_price[$i]*$arr_cart_p_qty[$i];
                                $table_total_price = $table_total_price + $row_total_price;
                                ?>

                <h3 class="special"><?php echo LANG_VALUE_33; ?> for <?php echo $arr_cart_p_name[$i]; ?>  Total Cost: <?php echo LANG_VALUE_1; ?><?php echo $row_total_price; ?></h3>


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


                <form action="payment/bank/init.php" method="post" id="bank_form">
                                     <div class="col-md-12 form-group">
                                         <label for=""><?php echo "Seller Name"; ?> <br>
                                         <span style="font-size:12px;font-weight:normal;">(<?php echo "Scan The QR Here to Pay"; ?>)</span></label>
                                         <img src="http://192.168.1.9:8080/storage/<?php echo str_replace('\/', '/', trim($arr_cart_p_featured_photo[$i])); ?>" alt="">
                                     </div>


                                     <div class="col-md-12 form-group">
                                     <label for=""><?php echo "Upload here the Proof of Payment"; ?> <br>
                                     <span style="font-size:12px;font-weight:normal;">(<?php echo "Supporting Documents"; ?>)</span></label>
                                         <input type="file" class="btn btn-primary" value="Upload" name="form3">
                                         <!-- <input type="submit" class="btn btn-primary" value="<?php echo LANG_VALUE_46; ?>" name="form3"> -->
                                     </div>
                                 </form>

                        <?php endfor; ?>




                </div>





                <div class="cart-buttons">
                    <ul>

                        <li><a href="cart.php" class="btn btn-primary"><?php echo LANG_VALUE_21; ?></a></li>
                        <li><a href="#" class="btn btn-primary"><?php echo "Proceed"; ?></a></li>
                    </ul>
                </div>



                <?php endif; ?>

            </div>
        </div>
    </div>
</div>


<?php require_once('footer.php'); ?>
