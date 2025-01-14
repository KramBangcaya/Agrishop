<?php
require_once('header.php');
require_once('api-config.php');

?>




<div id="bootstrap-touch-slider" class="carousel bs-slider fade control-round indicators-line" data-ride="carousel" data-pause="hover" data-interval="false" >

    <!-- Indicators -->


    <!-- Wrapper For Slides -->
    <div class="carousel-inner"  role="listbox">

        <?php
        $i=0;
        $statement = $pdo->prepare("SELECT * FROM tbl_slider");
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $row) {
            ?>


            <div class="item <?php
            if($i==0) {echo 'active';} ?>" style="background-image:url(assets/uploads/agribackground.jpg">


                <div class="container">
                    <div class="row">

                    <div class="slide-text
                        <?php

                            if($row['position'] == 'Center') {echo 'slide_style_center';}
                            ?>">

                            <h1 style="font-size : clamp(2rem, 8vw, 5rem);" data-animation="animated <?php

                            if($row['position'] == 'Center') {echo 'fadeInDown';}
                            ?>">Welcome to AgriShop</h1>
                            <p data-animation="animated <?php

                            if($row['position'] == 'Center') {echo 'fadeInDown';}
                            ?>">One-Stop Shop for Quality Agricultural Products</p>


                        </div>
                    </div>

                </div>

            </div>




            <?php
            $i++;
        }
        ?>

    </div>

</div>

<div class="product pt_70 pb_70">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="headline" >
                    <h2>List of Products</h2>
                    <div class="btn-cart btn-cart1"><a href="../buyer/product-category.php" class="btn btn-primary">Browse Products <i class="fa fa-search"></i></a></div>

                </div>
            </div>
        </div>




        <div class="row">
    <?php

    // Fetch product data from the API
    $apiUrl = API_BASE_URL . '/products/all';
    $json = @file_get_contents($apiUrl); // Suppress warnings with @

    $products = json_decode($json, true)['data']; // Decode the JSON response to PHP array

    // Check if products are fetched
    if ($products) {
        $i = 0;
        foreach ($products as $product) {
            ?>
            <div class="item"
            style="
    border: 2px solid #f0f0f0; /* Light gray border */
    padding: 15px; /* Spacing inside the container */
    margin: 15px; /* Spacing between products */
    border-radius: 10px; /* Rounded corners for a modern look */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
    background-color: #fff; /* White background to stand out */
    overflow: hidden; /* Ensures content stays within the box */
    display: flex; /* Flexbox for responsive layout */
    flex-direction: column; /* Stack content vertically */
    text-align: center; /* Center-align text content */
"

onmouseover="this.style.transform='scale(1.05)'; this.style.boxShadow='0 6px 12px rgba(0, 0, 0, 0.2)';"
onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 4px 8px rgba(0, 0, 0, 0.1)';"
>
                <div class="thumb">
                    <div class="photo" style="background-image:url(<?php echo API_BASE_URL . '/storage/' . $product['photos'][0]; ?>);"></div>
                </div>
                <div class="text">
                    <h3>
                        <a href="product.php?id=<?php echo $product['id']; ?>&fname=<?php echo $product['first_name']; ?>&lname=<?php echo $product['last_name']; ?>">
                            <?php echo $product['Product_Name']; ?>
                        </a>
                    </h3>
                    <h4>₱<?php echo $product['price']; ?></h4>
                    <div class="rating">
                        <?php
                        // Example: Displaying full stars for simplicity
                        for ($i = 1; $i <= 5; $i++) {
                            echo '<i class="fa fa-star"></i>';
                        }
                        ?>
                    </div>
                    <h3>
                        <a href="product.php?id=<?php echo $product['id']; ?>&fname=<?php echo $product['first_name']; ?>&lname=<?php echo $product['last_name']; ?>">
                            <?php echo $product['first_name']; ?> <?php echo $product['last_name']; ?>
                        </a>
                    </h3>
                    <?php if ($product['Quantity'] == 0): ?>
                        <div class="out-of-stock">
                            <div class="inner">Out Of Stock</div>
                        </div>
                    <?php else: ?>
                        <p>
                            <a href="product.php?id=<?php echo $product['id']; ?>&fname=<?php echo $product['first_name']; ?>&lname=<?php echo $product['last_name']; ?>"
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
                            >View</a>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
            <?php
            $i++;
        }
    } else {
        echo '<p>No popular products found.</p>';
    }
    ?>
</div>

<?php
// Check if the customer is logged in or not
if(!isset($_SESSION['customer'])) {
    // header('location: '.BASE_URL.'logout.php');
    // exit;
} else {

    $cust_email = $_SESSION['customer']['email'];

    // var_dump($_SESSION);// Get email from session

    $api_url = API_BASE_URL . "/login/submit?email=" . urlencode($cust_email);

    // Initialize cURL session
    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification if needed
    $api_response = curl_exec($ch);
    curl_close($ch);

    $response_data = json_decode($api_response, true);


    if ($response_data && isset($response_data['user'][0])) {
        // Get the user's data from the API response
        $user = $response_data['user'][0];// Assuming 'status' field exists in the API response
        $_SESSION['user_id'] = $user['user_id'];

        // If the user is inactive, log them out

    } else {
        // If no data is returned from the API, force logout or show an error
        header('location: '.BASE_URL.'logout.php');
        exit;
    }
    // If customer is logged in, but admin make him inactive, then force


}
?>

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









							</div>



