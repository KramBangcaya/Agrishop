<?php
require_once('header.php');
require_once('api-config.php');

// session_destroy();

// // Delete the session cookie
// if (ini_get("session.use_cookies")) {
//     $params = session_get_cookie_params();
//     setcookie(session_name(), '', time() - 42000,
//         $params["path"], $params["domain"],
//         $params["secure"], $params["httponly"]
//     );
// }

// // Optional: Redirect or display a confirmation
// echo "All sessions have been erased.";
?>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row) {
    $banner_cart = $row['banner_cart'];
}
?> <div id="toast" class="toast"></div>

<style>
    .toast {
        visibility: hidden;
        min-width: 250px;
        margin-left: -125px;
        background-color: #333;
        color: #fff;
        text-align: center;
        border-radius: 5px;
        padding: 15px;
        position: fixed;
        z-index: 1;
        left: 50%;
        bottom: 30px;
        font-size: 17px;
        opacity: 0;
        transition: opacity 0.5s, bottom 0.5s;
    }

    .toast.show {
        visibility: visible;
        opacity: 1;
        bottom: 50px;
    }
</style>
<script>
function showToast(message) {
    const toast = document.getElementById('toast');
    toast.innerText = message;
    toast.className = 'toast show';
    setTimeout(() => {
        toast.className = 'toast';
    }, 3000); // Toast disappears after 3 seconds
}</script>
<?php
$error_message = '';
if(isset($_POST['form1'])) {
//var_dump($_POST);



// Initialize arrays for product information

$i = 0;
$url = API_BASE_URL. '/products/all';
$json = file_get_contents($url);
$apiResponse = json_decode($json, true);

// Check if API response is valid
if (isset($apiResponse['data'])) {
    $result = $apiResponse['data'];
} else {
    $result = [];
}

foreach ($result as $row) {
    $i++;
    $table_product_id[$i] = $row['id'];
    $table_quantity[$i] = $row['Quantity'];

}


// Initialize arrays from POST request
$arr1 = $arr2 = $arr3 = [];
if (isset($_POST['product_id']) && is_array($_POST['product_id'])) {
    $arr1 = $_POST['product_id'];
    // var_dump($_POST['product_id']);
}
if (isset($_POST['quantity']) && is_array($_POST['quantity'])) {
    $arr2 = $_POST['quantity'];
}
if (isset($_POST['product_name']) && is_array($_POST['product_name'])) {
    $arr3 = $_POST['product_name'];
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




    $allow_update = 1;
    $error_message = '';
    // Loop through each item in the cart
    for ($i = 0; $i < count($arr1); $i++) {
        // var_dump(count($arr1));
        $temp_index = array_search($arr1[$i], $table_product_id);
        // var_dump($table_product_id);
        if ($temp_index !== false) {
            // Check stock availability
            if ($table_quantity[$temp_index] < $arr2[$i]) {
                $allow_update = 0;
                $error_message .= '"' . $arr2[$i] . '" items are not available for "' . $arr3[$i] . '"\n';
            } else {
                // Update quantity in session
                // $_SESSION['cart_p_qty'][$arr1[$i]] = $arr2[$i];


                $product_id = $arr1[$i]; // Product ID
                $_SESSION['cart_p_qty'][$i] = $arr2[$i];
            }
        }
    }
  //  $error_message .= '\nOther items quantity are updated successfully!';
    ?>

    <?php if($allow_update == 0): ?>
    	<script> showToast('<?php echo $error_message; ?>');</script>
	<?php else: ?>
		<script> showToast('All Items Quantity Update is Successful!');</script>
	<?php endif; ?>
    <?php

}
?>


    <div class="page-banner-inner" style="font-size:50px;">
    <h1 style="font-size:50px;text-align: center"> CART</h1>
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
    // Ensure session data is properly initialized
    $table_total_price = 0;

    $i=0;
    foreach($_SESSION['cart_p_qty'] as $key => $value)
    {
        $i++;
        $arr_cart_p_qty[$i] = $value;
    }

    $arr_cart_p_id = isset($_SESSION['cart_p_id']) ? array_values($_SESSION['cart_p_id']) : [];
    $arr_cart_p_qty = isset($_SESSION['cart_p_qty']) ? array_values($_SESSION['cart_p_qty']) : [];
    $arr_cart_p_current_price = isset($_SESSION['cart_p_current_price']) ? array_values($_SESSION['cart_p_current_price']) : [];
    $arr_cart_p_name = isset($_SESSION['cart_p_name']) ? array_values($_SESSION['cart_p_name']) : [];
    $arr_cart_p_featured_photo = isset($_SESSION['cart_p_featured_photo']) ? array_values($_SESSION['cart_p_featured_photo']) : [];

    if (empty($arr_cart_p_id)) {
        echo '<h2 class="text-center">Cart is Empty!!</h2><br>';
        echo '<h4 class="text-center">Add products to the cart to view them here.</h4>';
        return; // Stop execution if the cart is empty
    }
    ?>

    <h2 class="special" style="margin-left:10px;"><button class="btn" onclick="window.history.back()"><i class="fa fa-arrow-left" aria-hidden="true"></i></button> Order Details</h2>
    <h3 class="special"></h3>
    <?php for ($i = 0; $i < count($arr_cart_p_id); $i++): ?>
        <div class="row">
            <div class="col-md-4">
                <div class="row" style="margin: 0 auto;"> <!-- Centering the inner row -->
                    <div class="col-md-12 form-group">
                        <h2>
                            <?php echo htmlspecialchars($arr_cart_p_name[$i]); ?>&nbsp;
                            ₱<?php echo htmlspecialchars($arr_cart_p_current_price[$i]); ?>&nbsp;
                            <a href="cart-item-delete.php?id=<?php echo htmlspecialchars($arr_cart_p_id[$i]); ?>"
   class="trash"
   onclick="showToastAndRedirect(event, 'Product Removed!')">
    <i class="fa fa-trash" style="color:red;"></i>
</a>

<script>
    function showToastAndRedirect(event, message) {
        // Prevent the default behavior of the link
        event.preventDefault();

        // Show the toast notification
        showToast(message);

        // Delay the redirection to allow the toast to be visible
        setTimeout(() => {
            window.location.href = event.target.closest('a').href;
        }, 1500); // Adjust the delay if necessary
    }

    function showToast(message) {
        const toast = document.getElementById('toast');
        toast.innerText = message;
        toast.className = 'toast show';
        setTimeout(() => {
            toast.className = 'toast';
        }, 3000); // Toast disappears after 3 seconds
    }
</script>

<!-- Add the toast container if it's not already included -->
<div id="toast" class="toast"></div>
<style>
    .toast {
        visibility: hidden;
        min-width: 250px;
        margin-left: -125px;
        background-color: #333;
        color: #fff;
        text-align: center;
        border-radius: 5px;
        padding: 15px;
        position: fixed;
        z-index: 1;
        left: 50%;
        bottom: 30px;
        font-size: 17px;
        opacity: 0;
        transition: opacity 0.5s, bottom 0.5s;
    }

    .toast.show {
        visibility: visible;
        opacity: 1;
        bottom: 50px;
    }
</style>

                        </h2>

                        <!-- Product Image -->

                        <img src="http://192.168.1.9:8080/storage/<?php echo str_replace('\/', '/', trim($arr_cart_p_featured_photo[$i])); ?>"



                             alt="Product Image"
                             style="width: 100%; max-width: 250px; margin-top: 10px;"> <!-- Responsive and spaced -->
                        <input type="hidden" name="product_id[]" value="<?php echo htmlspecialchars($arr_cart_p_id[$i]); ?>">

                        <!-- Quantity and Total -->
                        <div style="margin-top: 10px; font-size: medium;">
                            <label>Quantity: </label>
                            <div class="quantity-container">
                                <button type="button" class="qty-btn qty-minus" data-index="<?php echo $i; ?>">-</button>
                                <input type="text" class="input-text qty text" name="quantity[]"
                                       value="<?php echo  $arr_cart_p_qty[$i]; ?>"
                                       title="Qty" size="4" pattern="[0-9]*" inputmode="numeric"
                                       id="quantityInput<?php echo $i; ?>"
                                       style="width: 60px; margin-right: 10px;">
                                <button type="button" class="qty-btn qty-plus" data-index="<?php echo $i; ?>">+</button>
                            </div>

                            <label>Total: </label>
                            <?php
                            $row_total_price = $arr_cart_p_current_price[$i] * $arr_cart_p_qty[$i];
                            echo '₱' . htmlspecialchars($row_total_price);
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <h3 class="special"></h3>
    <?php endfor; ?>
</div>

<script>
function showToast(message) {
    const toast = document.getElementById('toast');
    toast.innerText = message;
    toast.className = 'toast show';
    setTimeout(() => {
        toast.className = 'toast';
    }, 3000); // Toast disappears after 3 seconds
}

    // JavaScript to handle quantity updates
    document.addEventListener('DOMContentLoaded', function () {
        // Attach event listeners for all quantity buttons
        const minusButtons = document.querySelectorAll('.qty-minus');
        const plusButtons = document.querySelectorAll('.qty-plus');

        minusButtons.forEach(button => {
            button.addEventListener('click', function () {
                const index = this.getAttribute('data-index');
                const input = document.getElementById('quantityInput' + index);
                let value = parseInt(input.value, 10);
                if (!isNaN(value) && value > 1) {
                    input.value = value - 1;
                } else {
                    input.value = 1; // Prevent going below 1
                }
            });
        });

        plusButtons.forEach(button => {
            button.addEventListener('click', function () {
                const index = this.getAttribute('data-index');
                const input = document.getElementById('quantityInput' + index);
                let value = parseInt(input.value, 10);
                if (!isNaN(value)) {
                    input.value = value + 1;
                } else {
                    input.value = 1; // Default to 1 if the input is empty or invalid
                }
            });
        });

        // Ensure input fields accept only numeric values
        const quantityInputs = document.querySelectorAll('.input-text.qty');
        quantityInputs.forEach(input => {
            input.addEventListener('input', function (e) {
                this.value = this.value.replace(/[^0-9]/g, ''); // Strip non-numeric characters
            });
        });
    });
</script>

<style>
    .quantity-container {
        display: flex;
        align-items: center;
    }

    .qty-btn {
        width: 30px;
        height: 30px;
        background-color: #ddd;
        border: none;
        text-align: center;
        font-size: 18px;
        cursor: pointer;
    }

    .qty-btn:focus {
        outline: none;
    }

    .input-text.qty {
        width: 60px;
        text-align: center;
    }
</style>


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
                style="width:250px; height:50px; line-height:40px; text-align:center; display:inline-block;padding-bottom: 50px;">
                <?php echo LANG_VALUE_85; ?>
            </a>
        </li>
    </ul>
    <ul style="list-style:none; padding:0; display:inline-block; margin:5px;">
        <li>
            <a
                href="checkout.php"
                class="btn btn-primary"
                style="width:250px; height:50px; line-height:40px; text-align:center; display:inline-block; padding-bottom: 50px;">
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

    <div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <?php
                // Ensure the user_id is set in the session
                if (isset($_SESSION['user_id'])) {
                    $user_id = $_SESSION['user_id'];
                } else {
                    $user_id = null; // Handle this appropriately if user_id is not set
                }

                // Include the file without query string

                ?>
            </div>


            <?php
if (isset($_SESSION['customer'])) {
    ?>
    <div class="col-md-12">
        <div class="user-content" style="
            position: fixed; /* Keep it fixed in the viewport */
            bottom: 0; /* Position at the bottom */
            left: 0; /* Align to the left */
            width: 100%; /* Full width */
            z-index: 1000; /* Ensure it stays above other content */
            background-color: #fff; /* White background for contrast */
            padding: 10px; /* Add spacing inside */
            box-shadow: 0 -4px 6px rgba(0, 0, 0, 0.1); /* Add shadow above the bar */
        ">
            <div style="text-align: center; margin-right: 10px; margin-bottom: 10px;">
                <a href="index.php" class="btn btn-primary"><i class="fa fa-home" aria-hidden="true"></i> Home</a>
                <?php
                $statement = $pdo->prepare("SELECT * FROM tbl_page WHERE id=1");
                $statement->execute();
                $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                foreach ($result as $row) {
                    $about_title = $row['about_title'];
                    $faq_title = $row['faq_title'];
                    $blog_title = $row['blog_title'];
                    $contact_title = $row['contact_title'];
                    $pgallery_title = $row['pgallery_title'];
                    $vgallery_title = $row['vgallery_title'];
                }
                ?>
                <a href="customer-profile-update.php?id=<?php echo $user_id; ?>" class="btn btn-primary">
                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Profile
                </a>
                <a href="reportseller.php" class="btn btn-primary">
                    <i class="fa fa-flag" aria-hidden="true"></i> Report
                </a>
                <a href="customer-order.php?id=<?php echo $user_id; ?>" class="btn btn-primary">
                    <i class="fa fa-shopping-basket" aria-hidden="true"></i> Orders
                </a>
            </div>
        </div>
    </div>
    <?php
}
?>


        </div>
    </div>
</div>
</div>

