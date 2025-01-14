<?php require_once('header.php');
require_once('api-config.php');

?>

<?php
if(!isset($_REQUEST['id'])) {
    header('location: index.php');
    exit;
} else {

    $product_id = $_REQUEST['id'];

    $api_url = API_BASE_URL . "/products/product/" . $product_id;

    // Use cURL to fetch data from API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    // var_dump($response);

    if (!$response) {
        echo "Error fetching data from API.";
        exit;
    }
     // Decode the JSON response
     $result = json_decode($response, true);

     // Check if decoding was successful and "data" key exists
     if (!$result || !isset($result['data'])) {
         echo "Invalid product data.";
         exit;
     }

     // Extract product details from the "data" key
     $product = $result['data'];

    //  var_dump($product);
     $p_name = $product['Product_Name'] ?? 'N/A';
     $p_current_price = $product['price'] ?? 'N/A';
     $p_qty = $product['Quantity'] ?? 'N/A';
     $p_measurement = $product['measurement'] ?? 'N/A';
     $p_featured_photo = $product['photos'][0] ?? 'N/A';
     $photo2 = $product['photos1'][0] ?? 'N/A';
     $photo3 = $product['photos2'][0] ?? 'N/A';
     $p_description = $product['Description'] ?? 'N/A';
     $s_name = $product['first_name'] ?? 'N/A';
     $s_last = $product['last_name'] ?? 'N/A';
     $s_address = $product['address'] ?? 'N/A';
     $s_id = $product['userID'] ?? 'N/A';
     $qrcode = $product['qrcode'][0] ?? 'N/A';
     $s_contact_number = $product['contact_number'] ?? 'N/A';

 }

    //var_dump($_SESSION);
if(isset($_POST['form_add_to_cart'])) {


    if(isset($_SESSION['cart_p_id']))
    {


        $arr_cart_p_id = array();
        $arr_cart_p_qty = array();
        $arr_cart_p_current_price = array();

        $i=0;
        foreach($_SESSION['cart_p_id'] as $key => $value)
        {
            $i++;
            $arr_cart_p_id[$i] = $value;
        }
        if($added == 1) {
           $error_message1 = 'This product is already added to the shopping cart.';
        } else {

            $i=0;
            foreach($_SESSION['cart_p_id'] as $key => $res)
            {
                $i++;
            }
            $new_key = $i+1;

            $_SESSION['cart_p_id'][$new_key] = $_REQUEST['id'];
            $_SESSION['cart_p_qty'][$new_key] = $_POST['p_qty'];
            $_SESSION['cart_p_current_price'][$new_key] = $_POST['p_current_price'];
            $_SESSION['cart_p_name'][$new_key] = $_POST['p_name'];
            $_SESSION['cart_s_name'][$new_key] = $_POST['s_name'];
            $_SESSION['cart_s_last'][$new_key] = $_POST['s_lastname'];
            $_SESSION['cart_qr'][$new_key] = $_POST['qrcode'];
            $_SESSION['s_id'][$new_key] = $_POST['s_id'];
            $_SESSION['s_contact_number'][$new_key] = $_POST['s_contact_number'];
            $_SESSION['s_address'][$new_key] = $_POST['s_address'];
            // $_SESSION['f_name'][$new_key] = $_REQUEST['fname'];
            // $_SESSION['l_name'][$new_key] = $_REQUEST['lname'];

            $_SESSION['cart_p_featured_photo'][$new_key] = $_POST['p_featured_photo'];

            $success_message1 = 'Product is added to the cart successfully!';
        }

    }
    else
    {

        $_SESSION['cart_p_id'][1] = $_REQUEST['id'];
        $_SESSION['cart_p_qty'][1] = $_POST['p_qty'];
        $_SESSION['cart_p_current_price'][1] = $_POST['p_current_price'];
        $_SESSION['cart_p_name'][1] = $_POST['p_name'];
        $_SESSION['cart_p_featured_photo'][1] = $_POST['p_featured_photo'];
        $_SESSION['cart_s_name'][1] = $_POST['s_name'];
        $_SESSION['cart_s_last'][1] = $_POST['s_lastname'];
        $_SESSION['s_id'][1] = $_POST['s_id'];
        $_SESSION['s_contact_number'][1] = $_POST['s_contact_number'];
        $_SESSION['s_address'][1] = $_POST['s_address'];
        $_SESSION['cart_qr'][1] = $_POST['qrcode'];
        // $_SESSION['f_name'][1] = $_REQUEST['fname'];
        // $_SESSION['l_name'][1] = $_REQUEST['lname'];

        $success_message1 = 'Product is added to the cart successfully!';
    }
}
?>

<?php
if($error_message1 != '') {
    echo "<script>alert('".$error_message1."')</script>";
}
if($success_message1 != '') {
    echo "<script>alert('".$success_message1."')</script>";
    header('location: product.php?id='.$_REQUEST['id']);
}
?>


<div class="page">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
                <div class="breadcrumb mb_30">
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li>></li>
                        <li><?php echo $p_name; ?></li>
                    </ul>
                </div>
                <h3><button class="btn" onclick="window.history.back()"><i class="fa fa-arrow-left" aria-hidden="true"></i></button> Product Details</h3>
			<div class="product">
				<div class="row">
                    <div class="col-md-5">
                    <ul class="prod-slider">
    <!-- Main Image -->
    <li
        id="main-image"
        style="background-image: url(<?php echo API_BASE_URL . '/storage/' . str_replace('\/', '/', trim($p_featured_photo, '[]"')); ?>);">
        <a
            class="popup"
            href="<?php echo API_BASE_URL . '/storage/' . str_replace('\/', '/', trim($p_featured_photo, '[]"')); ?>">
        </a>
    </li>
</ul>
<style>
/* Base Styles */
.prod-slider {
    list-style: none;
    padding: 0;
    margin: 0;
}

.prod-slider .main-image {
    style: "
        width: 100%;
        height: auto;
        background-size: cover;
        background-position: center center;
        background-repeat: no-repeat;
        aspect-ratio: 16 / 9;
    ";
}

/* Media Queries for Mobile */
@media (max-width: 768px) {
    .prod-slider .main-image {
        style: "
            aspect-ratio: 4 / 3;
        ";
    }
}

@media (max-width: 480px) {
    .prod-slider .main-image {
        style: "
            aspect-ratio: 1 / 1;
        ";
    }
}
</style>

                            <div id="prod-pager">
                                <?php
                                $product_id = $_REQUEST['id'];
                                $api_url =  API_BASE_URL . "/products/product/" . $product_id;

                                // Fetch product details from API
                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, $api_url);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                $response = curl_exec($ch);
                                curl_close($ch);

                                // Decode the JSON response
                                $product = json_decode($response, true);

                                // Check if the response is valid
                                if (!isset($product['data'])) {
                                    echo "Error fetching product details.";
                                    exit;
                                }

                                // Extract product data
                                $product_data = $product['data'];

                                // Parse images
                                $featured_photo = $product_data['photos'][0] ?? null;
                                $photo2 = $product_data['photos1'][0] ?? null;
                                $photo3 = $product_data['photos2'][0] ?? null;

                                $i = 1;

                                // Display images
                                if ($featured_photo) {
                                    echo '<a href="#" class="prod-thumb" data-image="' . API_BASE_URL . '/storage/' . htmlspecialchars($featured_photo) . '">
                                            <div class="prod-pager-thumb" style="background-image: url(' . API_BASE_URL . '/storage/' . htmlspecialchars($featured_photo) . ');"></div>
                                          </a>';
                                    $i++;
                                }
                                if ($photo2) {
                                    echo '<a href="#" class="prod-thumb" data-image="' . API_BASE_URL . '/storage/' . htmlspecialchars($photo2) . '">
                                            <div class="prod-pager-thumb" style="background-image: url(' . API_BASE_URL . '/storage/' . htmlspecialchars($photo2) . ');"></div>
                                        </a>';
                                    $i++;
                                }
                                if ($photo3) {
                                    echo '<a href="#" class="prod-thumb" data-image="' . API_BASE_URL . '/storage/' . htmlspecialchars($photo3) . '">
                                            <div class="prod-pager-thumb" style="background-image: url(' . API_BASE_URL . '/storage/' . htmlspecialchars($photo3) . ');"></div>
                                        </a>';
                                    $i++;
                                }
                                ?>
                            </div>
                        </div>

                        <div class="col-md-7">
							<!-- <div class="p-review">
								<div class="rating">

                                </div>
							</div> -->
                            <form action="" method="post" onsubmit="return checkStock();">


    <div class="p-price"><br><br><br><br>

        <h1><?php echo $p_name; ?></h1>
        <br>
        <span style="font-size:18px;">Product description</span>
        <div class="p-short-des" style="font-size:14px;font-weight: normal">
            <p>
                <?php echo $p_description; ?>
            </p>
        </div>

        <span style="font-size:14px;"><?php echo LANG_VALUE_54; ?></span><br>
        <span>
            <?php echo LANG_VALUE_1; ?><?php echo $p_current_price; ?>
        </span>
    </div>


    <div class="p-price">
        <span style="font-size:14px;">Measurement</span><br>
        <span>
            <?php echo $p_measurement; ?>
        </span>
    </div>
    <div class="p-price">
        <span style="font-size:14px;">Stock</span><br>
        <span>
            <?php echo $p_qty; ?>
        </span>
    </div>
    <input type="hidden" name="p_current_price" value="<?php echo $p_current_price; ?>">
    <input type="hidden" name="p_name" value="<?php echo $p_name; ?>">
    <input type="hidden" name="p_featured_photo" value="<?php echo $p_featured_photo; ?>">
    <input type="hidden" name="s_name" value="<?php echo $s_name; ?>">
    <input type="hidden" name="s_lastname" value="<?php echo $s_last; ?>">
    <input type="hidden" name="qrcode" value="<?php echo $qrcode; ?>">
    <input type="hidden" name="s_id" value="<?php echo $s_id; ?>">
    <input type="hidden" name="s_contact_number" value="<?php echo $s_contact_number; ?>">
    <input type="hidden" name="s_address" value="<?php echo $s_address; ?>">


    <div class="quantity-container">
    <button type="button" class="qty-btn qty-minus">-</button>
    <input
        type="text"
        class="input-text qty"
        step="1"
        min="1"
        max="<?php echo $p_qty; ?>"
        name="p_qty"
        value="1"
        title="Qty"
        size="4"
        pattern="[0-9]*"
        inputmode="numeric"
        id="quantityInput">
    <button type="button" class="qty-btn qty-plus">+</button>
</div>

<div id="stockErrorMessage" style="color: red; font-size: 14px; display: none;">
    Item is over the available stocks.
</div>




<div class="btn-cart btn-cart1" id="addToCartDiv">
    <input type="submit" value="<?php echo LANG_VALUE_154; ?>" name="form_add_to_cart" id="addToCartBtn">
</div>

<div id="successModal" class="modal" style="display: none;">
  <div class="modal-content">
    <span class="close" style="cursor: pointer;">&times;</span>
    <p style="font-size: 18px; font-weight: bold;">Item successfully added!</p>
  </div>
</div>


<style>
  /* Modal Styles */
  .modal {
    display: none; /* Hidden by default */
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
    z-index: 1000;
  }

  .modal-content {
    background-color: white;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 50%;
    text-align: center;
    border-radius: 8px;
  }

  .close {
    color: #aaa;
    font-size: 24px;
    font-weight: bold;
    float: right;
  }

  .close:hover,
  .close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
  }
</style>

<script>
  // Get elements
  const modal = document.getElementById('successModal');
  const showModalBtn = document.getElementById('addToCartBtn');
  const closeBtn = document.querySelector('.close');

  // Show the modal when the button is clicked
  showModalBtn.addEventListener('click', () => {
    modal.style.display = 'block';
  });

  // Close the modal when the close button is clicked
  closeBtn.addEventListener('click', () => {
    modal.style.display = 'none';
  });

  // Close the modal when clicking outside the modal content
  window.addEventListener('click', (event) => {
    if (event.target === modal) {
      modal.style.display = 'none';
    }
  });
</script>


<script>
    // Get elements
    const addToCartBtn = document.getElementById('addToCartBtn');
    const stockErrorMessage = document.getElementById('stockErrorMessage');
    const quantityInput = document.getElementById('quantityInput');
    const qtyPlusBtn = document.querySelector('.qty-plus');
    const qtyMinusBtn = document.querySelector('.qty-minus');
    const availableStock = <?php echo $p_qty; ?>;

    // Function to check stock and toggle UI
    function checkStock() {
        let enteredQty = parseInt(quantityInput.value, 10);

        // Ensure quantity is within valid range
        if (enteredQty > availableStock) {
            stockErrorMessage.style.display = 'block'; // Show error message
            quantityInput.value = availableStock; // Reset to max stock
            addToCartBtn.disabled = true; // Disable Add to Cart button
        } else if (enteredQty < 1 || isNaN(enteredQty)) {
            quantityInput.value = 1; // Reset to minimum
            stockErrorMessage.style.display = 'none';
            addToCartBtn.disabled = false;
        } else {
            stockErrorMessage.style.display = 'none'; // Hide error message
            addToCartBtn.disabled = false; // Enable Add to Cart button
        }
    }

    // Increment Quantity
    qtyPlusBtn.addEventListener('click', function () {
        let currentQty = parseInt(quantityInput.value, 10) || 1; // Default to 1 if NaN
        if (currentQty < availableStock) {
            quantityInput.value = currentQty + 1; // Increment by exactly 1
        }
        checkStock();
    });

    // Decrement Quantity
    qtyMinusBtn.addEventListener('click', function () {
        let currentQty = parseInt(quantityInput.value, 10) || 1; // Default to 1 if NaN
        if (currentQty > 1) {
            quantityInput.value = currentQty - 1; // Decrement by exactly 1
        }
        checkStock();
    });

    // Validate manually entered quantity
    quantityInput.addEventListener('input', function () {
        checkStock();
    });

</script>



</form>


<script>
    document.getElementById('quantityInput').addEventListener('input', function (e) {
        // Ensure the input is numeric
        var value = e.target.value;
        e.target.value = value.replace(/[^0-9]/g, ''); // Strip non-numeric characters
    });

    // Increment
    document.querySelector('.qty-plus').addEventListener('click', function () {
        var input = document.getElementById('quantityInput');
        var value = parseInt(input.value, 10);
        if (!isNaN(value)) {
            input.value = value + 1-1;
        } else {
            input.value = 1; // default to 1 if the input is empty or invalid
        }
    });

    // Decrement
    document.querySelector('.qty-minus').addEventListener('click', function () {
        var input = document.getElementById('quantityInput');
        var value = parseInt(input.value, 10);
        if (!isNaN(value) && value > 1) {
            input.value = value - 1+1;
        } else {
            input.value = 1; // prevent going below 1
        }
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
        width: 50px;
        text-align: center;
    }
</style>

<!-- Modal -->


<!-- Optional Styling -->
<style>
    /* Modal styling */
    .modal {
        display: none; /* Initially hidden */
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4); /* Black background with opacity */
    }

    .modal-content {
        background-color: #fff;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 400px;
        text-align: center;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        border-radius: 8px;
    }

    .close {
        color: #aaa;
        font-size: 28px;
        font-weight: bold;
        float: right;
    }

    .close:hover,
    .close:focus {
        color: #000;
        text-decoration: none;
        cursor: pointer;
    }
</style>


                            </form>

						</div>
                        </div>
				</div>
			</div>
		</div>
	</div>
</div>
    <div class="page" style="border-style: double; border-color: gray; border-width: 2px;">
        <div >
        <div class="container" style="padding-left:25px;">
            <div class="row">
                <div class="col-md-12">
                <div class="product">
                        <div class="row">
                            <h1><?php echo htmlspecialchars($s_name); ?> <?php echo htmlspecialchars($s_last); ?></h1>
                            <div class="btn-cart btn-cart1">
                                <form action="sellerindex.php" method="GET">
                                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($s_id); ?>">
                                    <input type="submit" value="Visit Shop">
                                </form>

                            </div>
                            <h4><?php  echo '<i class="fa fa-map-marker-alt"></i> '; echo $s_address;  ?></h4><br>
                        </div>
                        </div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
                    // Assuming your database connection is already established (e.g., using PDO or mysqli)
                    $product_id = $_REQUEST['id'];  // Get the product ID from the URL

                    // Fetch feedback from the database
                    $query = "SELECT * FROM feedback WHERE product_id = :product_id ORDER BY date DESC";
                    $stmt = $pdo->prepare($query);
                    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
                    $stmt->execute();

                    $feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    <div class="feedback">
                        <div class="row">
                            <h1 style="margin-left:15px;">Comments / Feedbacks</h1>
                            <br>

                            <?php

                            // var_dump($feedbacks);
                            if (count($feedbacks) > 0) {
                                foreach ($feedbacks as $feedback) {


                                    $date = $feedback['date']; // Original date
                                    $datetime = new DateTime($date); // Create DateTime object
                                    $formattedDate = $datetime->format('M. d, Y');
                                    echo '<div class="feedback-box">';
                                    echo '<div class="feedback-header">';
                                    echo '<h3>' . htmlspecialchars($feedback['buyer_name']) . '</h3>';
                                    echo '<h5>' . htmlspecialchars($formattedDate) . '</h5>';
                                    echo '<div class="rating">';

                                    // Display the rating as stars (you can adjust based on your rating system)
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $feedback['rating']) {
                                            echo '<i class="fa fa-star"></i>';
                                        } else {
                                            echo '<i class="fa fa-star-o"></i>';
                                        }
                                    }

                                    echo '</div>';
                                    echo '</div>'; // .feedback-header
                                    echo '<div class="feedback-content">';
                                    echo '<p>' . htmlspecialchars($feedback['feedback']) . '</p>';
                                    echo '</div>'; // .feedback-content
                                    echo '</div>'; // .feedback-box
                                }
                            } else {
                                echo '<p>No feedback available yet.</p>';
                            }
                            ?>
                        </div>
                    </div>

<div class="product bg-gray pt_70 pb_70">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="headline">
                    <h2><?php echo LANG_VALUE_155; ?></h2>
                    <h3><?php echo LANG_VALUE_156; ?></h3>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">

                <div class="product-carousel">

                    <?php
                    $current_product_id = $_REQUEST['id'];

                    // API URL to fetch products
                    $api_url = API_BASE_URL . "/products/all";

                    // Use cURL to fetch data from API
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $api_url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($ch);
                    curl_close($ch);

                    // Check if the response is not empty or false
                    if (!$response) {
                        echo "Error fetching products.";
                        exit;
                    }

                    // Decode the JSON response
                    $products = json_decode($response, true);

                    if (!isset($products['data']) || empty($products['data'])) {
                        echo "No products available.";
                        exit;
                    }
                    // Loop through products and exclude the current product
                    foreach ($products['data'] as $row) {
                        if ($row['id'] == $current_product_id) {
                            continue; // Skip the current product
                        }
                        ?>

                        <div class="item">
                            <div class="thumb">

                                <?php
                                // Decode the photos array and display the first photo
                                $photos = $row['photos'];
                                if (!empty($photos) && isset($photos[0])) {
                                    $photoUrl = str_replace('\/', '/', $photos[0]);
                                    echo '<div class="photo" style="background-image:url(' . API_BASE_URL . '/storage/' .  $photoUrl . ');"></div>';
                                } else {
                                    echo '<div class="photo" style="background-color: gray;">No photo available</div>';
                                }
                                ?>
                                <div class="overlay"></div>
                            </div>
                            <div class="text">
                                <h3><a href="product.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['Product_Name']); ?></a></h3>
                                <h4>
                                    <?php echo LANG_VALUE_1; ?><?php echo htmlspecialchars($row['price']); ?>
                                </h4>
                                <p><?php echo $row['first_name']?> <?php echo $row['last_name']?></p>
                                <p><a href="product.php?id=<?php echo $row['id']; ?>"
                                style="
        display: inline-block;
        padding: 10px 20px;
        background-color: #e7a340; /* Bootstrap primary color */
        color: #fff; /* White text */
        font-size: 16px;
        font-weight: bold;
        text-decoration: none;
        border-radius: 5px; /* Rounded corners */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Subtle shadow */
        transition: background-color 0.3s ease, transform 0.2s ease; /* Smooth hover effect */
    "
    onmouseover="this.style.backgroundColor='#0056b3'; this.style.transform='translateY(-2px)';"
    onmouseout="this.style.backgroundColor='#007bff'; this.style.transform='translateY(0)';"
                                >View</a></p>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>



<style>
    .feedback {
        font-family: Arial, sans-serif;
        margin-left: 25px; /* Adds margin to the left */
        margin-right: 25px;
    }
    .feedback-box {
        border-style: double;
        border-color: gray;
        border-width: 2px;
        padding: 15px;
        margin-bottom: 20px;
        background-color: #f9f9f9;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        margin-left: 10px; /* Adds some left margin for each feedback box */
        margin-right: 10px;
    }
    .feedback-header h3 {
        margin: 0;
        font-size: 18px;
        color: #333;
    }
    .rating {
        margin-top: 5px;
    }
    .rating i {
        color: gold;
    }
    .feedback-content p {
        font-size: 14px;
        color: #555;
        margin-top: 10px;
    }
    .feedback-box:hover {
        background-color: #f1f1f1;
    }
</style>



<script>
    // Select all thumbnail links
    const thumbnails = document.querySelectorAll('.prod-thumb');
    const mainImage = document.getElementById('main-image');

    thumbnails.forEach(thumb => {
        thumb.addEventListener('click', function (event) {
            event.preventDefault(); // Prevent default anchor behavior
            const imageUrl = this.getAttribute('data-image');
            mainImage.style.backgroundImage = `url(${imageUrl})`;
        });
    });


</script>

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
