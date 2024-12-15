<?php
require_once('header.php');
require_once('api-config.php');
?>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row) {
    $banner_cart = $row['banner_cart'];
}
?>

<?php
$error_message = '';
if(isset($_POST['form1'])) {

    $i = 0;
    $statement = $pdo->prepare("SELECT * FROM products");
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result as $row) {
        $i++;
        $table_product_id[$i] = $row['id'];
        $table_quantity[$i] = $row['Quantity'];
    }

    $i=0;
    foreach($_POST['product_id'] as $val) {
        $i++;
        $arr1[$i] = $val;
    }
    $i=0;
    foreach($_POST['quantity'] as $val) {
        $i++;
        $arr2[$i] = $val;

    }
    $i=0;
    foreach($_POST['product_name'] as $val) {
        $i++;
        $arr3[$i] = $val;

    }

    $allow_update = 1;
    for($i=1;$i<=count($arr1);$i++) {
        for($j=1;$j<=count($table_product_id);$j++) {
            if($arr1[$i] == $table_product_id[$j]) {
                $temp_index = $j;
                break;
            }
        }
        if($table_quantity[$temp_index] < $arr2[$i]) {
        	$allow_update = 0;
            $error_message .= '"'.$arr2[$i].'" items are not available for "'.$arr3[$i].'"\n';
        } else {
            $_SESSION['cart_p_qty'][$i] = $arr2[$i];
        }
    }
    $error_message .= '\nOther items quantity are updated successfully!';
    ?>

    <?php if($allow_update == 0): ?>
    	<script>alert('<?php echo $error_message; ?>');</script>
	<?php else: ?>
		<script>alert('All Items Quantity Update is Successful!');</script>
	<?php endif; ?>
    <?php

}
?>


    <div class="page-banner-inner" style="font-size:50px;">
    <h1 style="font-size:50px;text-align: center">CART</h1>
</div>


<div class="page">
	<div class="container">
		<div class="row">
			<div class="col-md-12">


                <?php if(!isset($_SESSION['cart_p_id'])): ?>
                    <?php echo '<h2 class="text-center">Cart is Empty!!</h2></br>'; ?>
                    <?php echo '<h4 class="text-center">Add products to the cart in order to view it here.</h4>'; ?>
                <?php else: ?>



                <form action="" method="post">
                    <?php $csrf->echoInputField(); ?>

                    <div class="table-responsive">

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
                        foreach($_SESSION['cart_color_name'] as $key => $value)
                        {
                            $i++;
                            $arr_cart_color_name[$i] = $value;
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

<h2 class="special" style="margin-left:10px;">Order Details</h2><h3 class="special"> </h3>

                        <?php for($i=1;$i<=count($arr_cart_p_id);$i++): ?>
<<<<<<< HEAD
                        <tr>

                            <td><?php echo $i; ?></td>
                            <!-- <td><?php echo $_SESSION['f_name']; ?></td>
                            <td><?php echo $_SESSION['l_name']; ?></td> -->
                            <td>
                            <img src="<?php echo API_BASE_URL . '/storage/' . str_replace('\/', '/', trim($arr_cart_p_featured_photo[$i], '[]"')); ?>" alt="">
                            </td>
                            <td><?php echo $arr_cart_p_name[$i]; ?></td>
                            <td><?php echo LANG_VALUE_1; ?><?php echo $arr_cart_p_current_price[$i]; ?></td>
                            <td>
                                <input type="hidden" name="product_id[]" value="<?php echo $arr_cart_p_id[$i]; ?>">
                                <input type="hidden" name="product_name[]" value="<?php echo $arr_cart_p_name[$i]; ?>">
                                <input type="number" class="input-text qty text" step="1" min="1" max="" name="quantity[]" value="<?php echo $arr_cart_p_qty[$i]; ?>" title="Qty" size="4" pattern="[0-9]*" inputmode="numeric">
                            </td>
                            <td class="text-right">
                                <?php
                                $row_total_price = $arr_cart_p_current_price[$i]*$arr_cart_p_qty[$i];
                                $table_total_price = $table_total_price + $row_total_price;
                                ?>
                                <?php echo LANG_VALUE_1; ?><?php echo $row_total_price; ?>
                            </td>
                            <td class="text-center">
                                <a  onclick="return confirmDelete();" href="cart-item-delete.php?id=<?php echo $arr_cart_p_id[$i]; ?>" class="trash"><i class="fa fa-trash" style="color:red;"></i></a>
                            </td>
                        </tr>
                        <?php endfor; ?>
                        <tr>
                            <th colspan="5" class="total-text">Total</th>
                            <th class="total-amount"><?php echo LANG_VALUE_1; ?><?php echo $table_total_price; ?></th>
                            <th></th>
                        </tr>
                    </table>
=======




                            <div class="row">
    <div class="col-md-4">
        <div class="row" style="margin: 0 auto;"> <!-- Centering the inner row -->
            <div class="col-md-12 form-group">
                <h2>
                    <?php echo $arr_cart_p_name[$i]; ?>&nbsp;
                    ₱<?php echo $arr_cart_p_current_price[$i]; ?>&nbsp;
                    <a onclick="return confirmDelete();"
                       href="cart-item-delete.php?id=<?php echo $arr_cart_p_id[$i]; ?>"
                       class="trash">
                        <i class="fa fa-trash" style="color:red;"></i>
                    </a>
                </h2>

                <!-- Product Image -->
                <img src="http://192.168.1.9:8080/storage/<?php echo str_replace('\/', '/', trim($arr_cart_p_featured_photo[$i])); ?>"
                     alt="Product Image"
                     style="width: 100%; max-width: 250px; margin-top: 10px;"> <!-- Responsive and spaced -->

                <!-- Quantity and Total -->
                <div style="margin-top: 10px; font-size: medium;">
                    <label>Quantity: </label>
                    <input type="number"
                           class="input-text qty text"
                           step="1"
                           min="1"
                           max=""
                           name="quantity[]"
                           value="<?php echo $arr_cart_p_qty[$i]; ?>"
                           title="Qty"
                           size="4"
                           pattern="[0-9]*"
                           inputmode="numeric"
                           style="width: 60px; margin-right: 10px;">

                    <label>Total: </label>
                    <?php $row_total_price = $arr_cart_p_current_price[$i] * $arr_cart_p_qty[$i]; ?>
                    ₱<?php echo $row_total_price; ?>
>>>>>>> c149625e19c8f21819769bac5f7c5911db5a978e
                </div>
            </div>
        </div>
    </div>
</div>

                <h3 class="special"> </h3>



                        <?php endfor; ?>





                </div>

              <div class="cart-buttons" style="text-align:center; margin-right:10px; margin-bottom:10px;">
    <ul style="list-style:none; padding:0; display:inline-block; margin:5px;">
        <li>
            <input
                type="submit"
                value="<?php echo LANG_VALUE_20; ?>"
                class="btn btn-secondary"
                name="form1"
                style="width:250px; height:50px; text-align:center; display:inline-block;">
        </li>
    </ul>
    <ul style="list-style:none; padding:0; display:inline-block; margin:5px;">
        <li>
            <a
                href="index.php"
                class="btn btn-primary"
                style="width:250px; height:50px; line-height:40px; text-align:center; display:inline-block;">
                <?php echo LANG_VALUE_85; ?>
            </a>
        </li>
    </ul>
    <ul style="list-style:none; padding:0; display:inline-block; margin:5px;">
        <li>
            <a
                href="checkout.php"
                class="btn btn-primary"
                style="width:250px; height:50px; line-height:40px; text-align:center; display:inline-block;">
                <?php echo LANG_VALUE_23; ?>
            </a>
        </li>
    </ul>
</div>



                </form>
                <?php endif; ?>



			</div>
		</div>
	</div>
</div>


<?php require_once('footer.php'); ?>
