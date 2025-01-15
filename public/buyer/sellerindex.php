<?php
require_once('header.php');
require_once('api-config.php');
?>

<?php
// Get the user_id from the query string
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

// Validate the user_id
if ($user_id <= 0) {
    die("Invalid user ID.");
}

// Fetch product data from the API for the specific seller
$apiUrl = API_BASE_URL ."/products/product/seller/$user_id";
$json = @file_get_contents($apiUrl);

if ($json === false) {
    die("Failed to fetch data from API.");
}

$products = json_decode($json, true)['data'];
// Display seller products

if (!empty($products)) {
    // Extract seller's name from the first product
    $seller_name = $products[0]['name'] . ' ' . $products[0]['lastname'];
} else {
    $seller_name = "Unknown Seller"; // Fallback if no products are found
}
?>
<div class="product pt_70 pb_70">
    <div class="container">
        <div class="headline">
        <h1><button class="btn" onclick="window.history.back()"><i class="fa fa-arrow-left" aria-hidden="true"></i></button> Products of Seller: <?php echo htmlspecialchars($seller_name); ?></h1>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="product-carousel">
                    <?php
                    if ($products) {
                        foreach ($products as $product) {

                            $photos = json_decode($product['photos'], true);

                            // Ensure the array contains at least one photo
                            $photo_url = !empty($photos) ? $photos[0] : '';

                            ?>
                            <div class="item">
                                <div class="thumb">
                                <div class="photo" style="background-image:url(<?php echo API_BASE_URL . '/storage/' . htmlspecialchars($photo_url); ?>);"></div>

                                <div class="overlay"></div>
                                </div>
                                <div class="text">
                                    <h3><a href="product.php?id=<?php echo $product['id']; ?>"><?php echo $product['Product_Name']; ?></a></h3>
                                    <h4>₱<?php echo $product['price']; ?></h4>
                                    <div class="rating">
                                        <?php for ($i = 1; $i <= 5; $i++) echo '<i class="fa fa-star"></i>'; ?>
                                    </div>
                                    <h3><a href="product.php?id=<?php echo $product['id']; ?>"><?php echo $product['name'] . ' ' . $product['lastname']; ?></a></h3>
                                    <?php if ($product['Quantity'] == 0): ?>
                                        <div class="out-of-stock"><div class="inner">Out Of Stock</div></div>
                                    <?php else: ?>
                                        <p><a href="product.php?id=<?php echo $product['product_id']; ?>"
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

                                        ></i>View</a></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<p>No products found for this seller.</p>';
                    }
                    ?>
                </div>
            </div>
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
