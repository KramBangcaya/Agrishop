<?php
require_once('api-config.php');
// Fetch categories from API
$api_url = API_BASE_URL . '/categories/all';
$response = file_get_contents($api_url);
$categories = json_decode($response, true);

// Get filters from the request
$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : null;
$min_price = isset($_GET['min_price']) ? $_GET['min_price'] : null;
$max_price = isset($_GET['max_price']) ? $_GET['max_price'] : null;

$products = [];

// Build query parameters for filtering
$query_params = [];

// If category_id is set, apply category filter
if ($category_id && $category_id !== '0') {
    $query_params['category_id'] = $category_id;
}

// Apply price range filter if either min_price or max_price is set
if (!empty($min_price) || !empty($max_price)) {
    // If min_price and max_price are both provided
    if (!empty($min_price) && !empty($max_price)) {
        $price_range_api_url = API_BASE_URL . "/products/price-range?min={$min_price}&max={$max_price}";
        $price_range_response = file_get_contents($price_range_api_url);
        $products = json_decode($price_range_response, true);
    } else {
        // If only min_price or max_price is set
        if (!empty($min_price)) {
            $query_params['min_price'] = $min_price;
        }
        if (!empty($max_price)) {
            $query_params['max_price'] = $max_price;
        }

        // Build product API URL with price range filter
        $product_api_url = API_BASE_URL . "/products?";
        $product_api_url .= http_build_query($query_params);

        // Fetch the filtered products
        $product_response = file_get_contents($product_api_url);
        $products = json_decode($product_response, true);
    }
} else {
    // If no price range is set, fetch products based on category only
    if ($category_id && $category_id !== '0') {
        $product_api_url = API_BASE_URL . "/products/category/{$category_id}";
    } else {
        // If no category is selected, fetch all products
        $product_api_url = API_BASE_URL . "/products/all";
    }

    // Fetch the filtered products
    $product_response = file_get_contents($product_api_url);
    $products = json_decode($product_response, true);
}
?>

<?php require_once('header.php'); ?>
<?php require_once('map.php'); ?>
<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <!-- Sidebar Category Display -->
                <div class="sidebar-category">
                    <label for="category"><h3>Categories <i class="fa fa-sort"></i></h3></><br>
                    <select name="category" class="btn btn-primary category-button" id="category" onchange="location = this.value;" style="font-size: 18px; text-align: left;">
                        <?php if (isset($categories['data']) && count($categories['data']) > 0): ?>
                            <option value="" selected>Select a Category</option>
                            <?php foreach ($categories['data'] as $category): ?>
                                <option value="product-category.php?category_id=<?php echo $category['id']; ?>
                                    <?php echo !empty($min_price) ? '&min_price=' . $min_price : ''; ?>
                                    <?php echo !empty($max_price) ? '&max_price=' . $max_price : ''; ?>">
                                    <?php echo $category['category']; ?>
                                </option>
                            <?php endforeach; ?>
                            <option value="product-category.php?category_id=0
                                <?php echo !empty($min_price) ? '&min_price=' . $min_price : ''; ?>
                                <?php echo !empty($max_price) ? '&max_price=' . $max_price : ''; ?>">All</option>
                        <?php else: ?>
                            <p>No categories available.</p>
                        <?php endif; ?>
                    </select>
                </div>



                <!-- Sidebar Price Range Filter -->
                <div class="sidebar-category">
                    <label for="price_range"><h3>Price Range (₱)</h3></label>
                    <form method="GET" action="product-category.php">
                        <label for="min_price" style="font-size: 18px;">Min:</label>
                        <input type="number" style="font-size: 18px;" name="min_price" value="<?php echo isset($_GET['min_price']) ? $_GET['min_price'] : ''; ?>"> <br><br>
                        <label for="max_price" style="font-size: 18px;">Max:</label>
                        <input type="number" style="font-size: 18px;" name="max_price" value="<?php echo isset($_GET['max_price']) ? $_GET['max_price'] : ''; ?>"> <br><br>
                        <input type="hidden" name="category_id" value="<?php echo isset($category_id) ? $category_id : ''; ?>">
                        <button type="submit" style="font-size: 18px;" class="btn btn-success">Filter</button>
                    </form>
                </div>
            </div>
<br>
            <div class="col-md-9"><h3>Products</h3>
                <div class="product-list">
                    <?php if (isset($products['data']) && count($products['data']) > 0): ?>
                        <div class="row">
                            <?php foreach ($products['data'] as $product): ?>
                                <?php if (isset($product['id'], $product['Product_Name'], $product['price'], $product['photos'])): ?>
                                    <div class="col-md-4 item item-product-cat" style="
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
onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 4px 8px rgba(0, 0, 0, 0.1)';">
                                        <div class="inner">
                                            <div class="thumb">
                                                <?php
                                                if (is_string($product['photos'])) {
                                                    $photosArray = json_decode($product['photos'], true);
                                                } else {
                                                    $photosArray = $product['photos'];
                                                }
                                                $photoPath = isset($photosArray[0]) ? $photosArray[0] : 'default.jpg';
                                                ?>
                                                <div class="photo" style="background-image:url(<?php echo API_BASE_URL . '/storage/' . $photoPath; ?>); width: 100%; height: 200px; background-size: cover;"></div>
                                                <div class="overlay"></div>
                                            </div>
                                            <div class="text">
                                                <h3><a href="product.php?id=<?php echo $product['id']; ?>"><?php echo $product['Product_Name']; ?></a></h3>
                                                <h4>₱<?php echo $product['price']; ?></h4>
                                                <div class="rating">
                                                    <?php
                                                    $rating = isset($product['rating']) ? $product['rating'] : 0;
                                                    for ($i = 1; $i <= 5; $i++) {
                                                        echo $i <= $rating ? '<i class="fa fa-star"></i>' : '<i class="fa fa-star-o"></i>';
                                                    }
                                                    ?>
                                                </div>
                                                <?php if (isset($product['Quantity']) && $product['Quantity'] == 0): ?>
                                                    <div class="out-of-stock">
                                                        <div class="inner">Out Of Stock</div>
                                                    </div>
                                                <?php else: ?>
                                                    <p>
    <a href="product.php?id=<?php echo $product['id']; ?>" style="
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
    onmouseout="this.style.backgroundColor='#007bff'; this.style.transform='translateY(0)';">
        View
    </a>
</p>


                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <p>Product information missing.</p>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>No products available for this category or price range.</p>
                    <?php endif; ?>
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



        </div>
    </div>
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
