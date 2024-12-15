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
        <h1>Products of Seller: <?php echo htmlspecialchars($seller_name); ?></h1>
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
                                        <p><a href="product.php?id=<?php echo $product['product_id']; ?>"><i class="fa fa-shopping-cart"></i> Add to Cart</a></p>
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

<?php require_once('footer.php'); ?>
