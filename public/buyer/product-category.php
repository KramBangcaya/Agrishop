<?php
// Fetch categories from API
$api_url = 'http://192.168.1.9:8080/categories/all';
$response = file_get_contents($api_url);
$categories = json_decode($response, true);

$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : null;

$products = [];

if ($category_id) {
    $product_api_url = "http://192.168.1.9:8080/products/category/{$category_id}";
    $product_response = file_get_contents($product_api_url);
    $products = json_decode($product_response, true);  // Decode the product JSON response
} else {
    // If no category is selected, fetch and display all products
    $product_api_url = "http://192.168.1.9:8080/products/all";
    $product_response = file_get_contents($product_api_url);
    $products = json_decode($product_response, true);  // Decode the product JSON response
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

                    <select name="payment_method" class="btn btn-primary category-button" id="category" onchange="location = this.value;">
                        <?php if (isset($categories['data']) && count($categories['data']) > 0): ?>
                            <option value="" selected>Select a Category</option>
                            <?php foreach ($categories['data'] as $category): ?>

                                    <option value="product-category.php?category_id=<?php echo $category['id']; ?>" style="text-align: left;" >
                                        <?php echo $category['category']; ?>

                                    </option>


                            <?php endforeach; ?>
                            <option value="product-category.php?category_id=0" style="text-align: left;"><a href="product-category.php?category_id=0">All</a></option>
                        <?php else: ?>
                            <p>No categories available.</p>
                        <?php endif; ?>

	                                    </select>




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
                                        // Decode the string into an array
                                        $photosArray = json_decode($product['photos'], true);
                                    } else {
                                        // Use the photos array directly
                                        $photosArray = $product['photos'];
                                    }

                                    // Get the first photo
                                    $photoPath = isset($photosArray[0]) ? $photosArray[0] : 'default.jpg'; // Use the first photo or a default image if unavailable


                                    ?>
                                <div class="photo" style="background-image:url(http://192.168.1.9:8080/storage/<?php echo $photoPath; ?>); width: 100%; height: 200px; background-size: cover;"></div>
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
            <p>No products available for this category.</p>
        <?php endif; ?>
    </div>
</div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>
