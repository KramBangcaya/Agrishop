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

                    // var_dump($_SESSION);?>

                    <h3 class="special"><button class="btn" onclick="window.history.back()"><i class="fa fa-arrow-left" aria-hidden="true"></i></button> <?php echo LANG_VALUE_26; ?></h3>
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
                                <select required name="payment_method[<?php echo $i; ?>]" placeholder="Select Payment Method" class="form-control payment-method" data-index="<?php echo $i; ?>">

                                    <option value="1">CASH ON DELIVERY (COD)</option>
                                    <option value="2">QR CODE</option>
                                </select>
                            </div>
                        </div>
                    </div>

    <!-- QR Code and Proof of Payment Section -->
    <div id="payment-details-<?php echo $i; ?>" class="payment-details" style="display:none;">
    <div class="col-md-12 form-groupser" >

        <img src="<?php echo API_BASE_URL . '/storage/' . str_replace('\/', '/', trim($_SESSION['cart_qr'][$i])); ?>"
             alt="QR Code" class="qr-code-img" style="padding: 0; margin: -90;">
    </div>

    <div class="col-md-12 form-groups" style="padding: 0; margin: 0;">
    <!-- <?php echo $_SESSION['cart_p_name'][$i]; ?> -->
    <br><label>Upload Proof of Payment</label><br>
        <p>- Must Downloaded QR Code</p>
        <p>- Must Clear Image & Not Screenshot</p>
        <input type="file" name="photo[<?php echo $i; ?>]" class="form-control">

    </div>
</div>

    <!-- Include hidden inputs to pass product data -->
    <input type="hidden" name="product_name[<?php echo $i; ?>]" value="<?php echo $_SESSION['cart_p_name'][$i]; ?>">
    <input type="hidden" name="product_quantity[<?php echo $i; ?>]" value="<?php echo $_SESSION['cart_p_qty'][$i]; ?>">
    <input type="hidden" name="product_price[<?php echo $i; ?>]" value="<?php echo $_SESSION['cart_p_current_price'][$i]; ?>">
    <input type="hidden" name="product_id[<?php echo $i; ?>]" value="<?php echo $_SESSION['cart_p_id'][$i]; ?>">
    <input type="hidden" name="seller_name[<?php echo $i; ?>]" value="<?php echo $_SESSION['cart_s_name'][$i]; ?>">
    <input type="hidden" name="seller_last[<?php echo $i; ?>]" value="<?php echo $_SESSION['cart_s_last'][$i]; ?>">
    <input type="hidden" name="seller_contact[<?php echo $i; ?>]" value="<?php echo $_SESSION['s_contact_number'][$i]; ?>">
    <input type="hidden" name="seller_address[<?php echo $i; ?>]" value="<?php echo $_SESSION['s_address'][$i]; ?>">
    <input type="hidden" name="seller_id[<?php echo $i; ?>]" value="<?php echo $_SESSION['s_id'][$i]; ?>">
<?php } ?>

<!-- Add CSS for Fixed Size and Responsive Design -->
<style>
    .qr-code-img {
        width: 550px; /* Fixed width */
        height: 550px; /* Fixed height */
        object-fit: contain; /* Ensures image scales within the fixed dimensions */
        display: block;
        padding: -50;
        padding-left: -50;
        margin: -50; /* Removes any space around the image */
    }


    @media (max-width: 550px) {
        .qr-code-img {
            width: 50px; /* Adjusted width for tablets */
            height: 420px; /* Adjusted height for tablets */
            padding: -50;
        padding-left: -50;
        margin: -50; /* Removes any space around the imag*/
        }
    }

    @media (max-width: 456px) {
        .qr-code-img {
            width: 380px; /* Adjusted width for smaller screens */
            height: 400px; /* Adjusted height for smaller screens */
            padding: -50;
        padding-left: -50;
        margin: -50; /* Removes any space around the imag*/
        }
    }
</style>

<!-- Add JavaScript to handle dropdown change -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const paymentDropdowns = document.querySelectorAll('.payment-method');

        paymentDropdowns.forEach(function (dropdown) {
            dropdown.addEventListener('change', function () {
                const index = this.getAttribute('data-index');
                const paymentDetails = document.getElementById('payment-details-' + index);

                if (this.value === "2") {
                    paymentDetails.style.display = 'block';
                } else {
                    paymentDetails.style.display = 'none';
                }
            });
        });
    });
</script>
                            <div class="cart-buttons">
                                <ul>

                                    <li>
                                    <!-- Place Order Button -->
<button
    type="button"
    class="btn btn-primary"
    style="background-color:#0d1452;"
    onclick="openConfirmationModal()">
    Place Order
</button>

<!-- Modal HTML -->
<div id="confirmationModal" class="modal">
    <div class="modal-content">
        <h3>Confirm Your Order</h3>
        <p>Are you sure you want to place the order?</p>
        <div class="modal-actions">
            <button class="btn btn-success" onclick="placeOrder()">Yes</button>
            <button class="btn btn-secondary" onclick="closeConfirmationModal()">No</button>
        </div>
    </div>
</div>

<!-- Modal Styles -->
<style>
    /* Modal container */
    .modal {
        display: none; /* Hidden by default */
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
        z-index: 1000; /* Ensure it appears above other content */
        justify-content: center;
        align-items: center;
    }

    /* Modal content box */
    .modal-content {
        background-color: #fff;
        padding: 20px;
        border-radius: 5px;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        width: 300px;
    }

    /* Modal actions */
    .modal-actions button {
        margin: 10px;
    }

    .btn {
        padding: 10px 20px;
        font-size: 16px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .btn-primary {
        background-color: #007bff;
        color: #fff;
    }

    .btn-success {
        background-color: #28a745;
        color: #fff;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: #fff;
    }

    .btn:hover {
        opacity: 0.9;
    }
</style>

<!-- Modal Script -->
<script>
    function openConfirmationModal() {
        // Show the modal
        document.getElementById('confirmationModal').style.display = 'flex';
    }

    function closeConfirmationModal() {
        // Hide the modal
        document.getElementById('confirmationModal').style.display = 'none';
    }

    function placeOrder() {
        // Show toast notification (optional)
        showToast('Order placed successfully!');

        // Submit the form or trigger any other action
        document.querySelector('form').submit();

        // Close the modal
        closeConfirmationModal();
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

<!-- Toast Notification Container -->
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
        z-index: 1001;
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


</li>

                                </ul>
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
</script>

<!-- Toast Notification Container -->
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

                        </form>
                    </div>

                <?php endif; ?>
            </div>

        </div>

    </div>

</div>

