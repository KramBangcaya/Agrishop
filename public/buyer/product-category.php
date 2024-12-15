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

// Prepare the product API URL based on category
$product_api_url = API_BASE_URL . "/products";

// Prepare the query parameters for price range and category
$query_params = [];

// Apply category filter if category_id is set
if ($category_id && $category_id !== '0') {
    $query_params['category_id'] = $category_id;
}

// Apply price range filters if set
if ($min_price !== null && $max_price !== null) {
    // If price range is set, use the price-range API
    $price_range_api_url = API_BASE_URL . "/products/price-range?min={$min_price}&max={$max_price}";

    // Fetch the filtered products based on the price range
    $price_range_response = file_get_contents($price_range_api_url);
    $products = json_decode($price_range_response, true);

    // Filter the products by category if a category is set
    if (isset($products['data']) && is_array($products['data'])) {
        // Filter the products by category if a category is set
        if ($category_id && $category_id !== '0') {
            $products['data'] = array_filter($products['data'], function($product) use ($category_id) {
                return $product['idCategory'] == $category_id;
            });
        }
    } else {
        $products['data'] = []; // Set it to an empty array if 'data' is not set or not an array
    }
} else {
    // If no price range is set, fetch all products in the selected category
    if ($category_id && $category_id !== '0') {
        $product_api_url .= "/category/{$category_id}";
    } else {
        $product_api_url .= "/all";
    }

    // Fetch the filtered products
    $product_response = file_get_contents($product_api_url);
    $products = json_decode($product_response, true);
}

?>

<?php require_once('header.php'); ?>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <!-- Sidebar Category Display -->
                <div class="sidebar-category">
                    <label for="category"><h3>Categories</h3></label>
                    <select name="category" class="btn btn-primary category-button" id="category" onchange="location = this.value;">
                        <?php if (isset($categories['data']) && count($categories['data']) > 0): ?>
                            <option value="" selected>Select a Category</option>
                            <?php foreach ($categories['data'] as $category): ?>
                                <option value="product-category.php?category_id=<?php echo $category['id']; ?>&min_price=<?php echo isset($_GET['min_price']) ? $_GET['min_price'] : ''; ?>&max_price=<?php echo isset($_GET['max_price']) ? $_GET['max_price'] : ''; ?>">
                                    <?php echo $category['category']; ?>
                                </option>
                            <?php endforeach; ?>
                            <option value="product-category.php?category_id=0&min_price=<?php echo isset($_GET['min_price']) ? $_GET['min_price'] : ''; ?>&max_price=<?php echo isset($_GET['max_price']) ? $_GET['max_price'] : ''; ?>">All</option>
                        <?php else: ?>
                            <p>No categories available.</p>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Sidebar Price Range Filter -->
                <div class="sidebar-category">
                    <label for="price_range"><h3>Price Range</h3></label>
                    <form method="GET" action="product-category.php">
                        <label for="min_price">Min:</label>
                        <input type="number" class="btn btn-primary category-button" name="min_price" value="<?php echo isset($_GET['min_price']) ? $_GET['min_price'] : ''; ?>"> <br><br>
                        <label for="max_price">Max:</label>
                        <input type="number" class="btn btn-primary category-button" name="max_price" value="<?php echo isset($_GET['max_price']) ? $_GET['max_price'] : ''; ?>"> <br><br>
                        <input type="hidden" name="category_id" value="<?php echo isset($category_id) ? $category_id : ''; ?>">
                        <button type="submit" class="btn btn-success">Filter</button>
                    </form>
                </div>
            </div>

            <div class="col-md-9">
                <h3>Products</h3>
                <div class="product-list">
                    <?php if (isset($products['data']) && count($products['data']) > 0): ?>
                        <div class="row">
                            <?php foreach ($products['data'] as $product): ?>
                                <?php if (isset($product['id'], $product['Product_Name'], $product['price'], $product['photos'])): ?>
                                    <div class="col-md-4 item item-product-cat">
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
                                                    <p><a href="product.php?id=<?php echo $product['id']; ?>"><i class="fa fa-shopping-cart"></i> Add to Cart</a></p>
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

<?php require_once('footer.php'); ?>
