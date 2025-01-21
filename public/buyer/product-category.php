<?php
require_once('api-config.php');
require_once('header.php');

$products = [];

$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
// Initialize cURL to fetch data from the new API with search term
$apiUrl =  API_BASE_URL . "/products/all_product?search=" . urlencode($searchTerm);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

// Execute and fetch response from the API
$response = curl_exec($ch);

// Check for errors
if (curl_errno($ch)) {
    $error = curl_error($ch);
    http_response_code(500); // Internal Server Error
    die(json_encode(["error" => $error]));
}

// Close the cURL session
curl_close($ch);

$data = json_decode($response, true);

// echo $apiUrl;
// echo $product;
if (isset($data['data']) && !empty($data['data'])) {
    $products = $data['data'];

}
// Fetch categories from API
$api_url = API_BASE_URL . '/categories/all';
$response = file_get_contents($api_url);
$categories = json_decode($response, true);

// Get filters from the request
$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : null;
$min_price = isset($_GET['min_price']) ? $_GET['min_price'] : null;
$max_price = isset($_GET['max_price']) ? $_GET['max_price'] : null;


// Build query parameters for filtering
$query_params = [];

// If category_id is set, apply category filter
if ($category_id && $category_id !== '0') {
    $query_params['category_id'] = $category_id;
}

// Apply price range filter if either min_price or max_price is set
if (!empty($min_price) && !empty($max_price) && empty($category_id)) {
        // Remove any spaces around the min and max prices
        $price_range_api_url = API_BASE_URL . "/products/price-range?min=" . urlencode($min_price) . "&max=" . urlencode($max_price);
        $price_range_response = file_get_contents($price_range_api_url);
        $products = json_decode($price_range_response, true);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $price_range_api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            // Execute and fetch response from the API
            $response = curl_exec($ch);

            // Check for errors
            if (curl_errno($ch)) {
                $error = curl_error($ch);
                http_response_code(500); // Internal Server Error
                die(json_encode(["error1" => $error]));
            }

        // Close the cURL session
        curl_close($ch);

        $data = json_decode($response, true);

    } else if(!empty($min_price) && !empty($max_price) && !empty($category_id)) {
        $price_range_api_url = API_BASE_URL . "/products/categories_price/{$category_id}?min=" . urlencode($min_price) . "&max=" . urlencode($max_price);


        // log($price_range_api_url);
        // echo $price_range_api_url;
        $price_range_response = file_get_contents($price_range_api_url);
        $products = json_decode($price_range_response, true);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $price_range_api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        // Execute and fetch response from the API
        $response = curl_exec($ch);

        // Check for errors
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            http_response_code(500); // Internal Server Error
            die(json_encode(["error1" => $error]));
        }

    // Close the cURL session
    curl_close($ch);

    $data = json_decode($response, true);
    } else if(!empty($searchTerm)){
        $apiUrl =  API_BASE_URL . "/products/all_product?search=" . urlencode($searchTerm);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        // Execute and fetch response from the API
        $response = curl_exec($ch);

        // Check for errors
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            http_response_code(500); // Internal Server Error
            die(json_encode(["error" => $error]));
        }

        // Close the cURL session
        curl_close($ch);

        $data = json_decode($response, true);

        // echo $data;

        $product_responses = file_get_contents($apiUrl);
        $products = json_decode($product_responses, true);

        // echo $product;
} else {
    // If no price range is set, fetch products based on category only
    if ($category_id && $category_id !== '0') {
        $product_api_url = API_BASE_URL . "/products/category/{$category_id}";
        // echo $product_api_url;
    } else {
        // If no category is selected, fetch all products
        $product_api_url = API_BASE_URL . "/products/all";
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $product_api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    // Execute and fetch response from the API
    $response = curl_exec($ch);

    // Check for errors
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        http_response_code(500); // Internal Server Error
        die(json_encode(["error" => $error]));
    }

// Close the cURL session
    curl_close($ch);

    $data = json_decode($response, true);
    // Fetch the filtered products
    $product_response = file_get_contents($product_api_url);
    // echo $product_response;
    $products = json_decode($product_response, true);
}
?>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h3><button class="btn" onclick="window.history.back()"><i class="fa fa-arrow-left" aria-hidden="true"></i></button></h3>
                <h2 style=" text-align: center;">Product Location</h2>
                    <div class="search-container" style="position: absolute; z-index: 1; width: 100%; margin-top: 10px; text-align: center;">
                        <form id="search-form" onsubmit="handleSearch(event)">
                            <input type="text" id="search-input" placeholder="Search for a product..." style="width: 50%; padding: 10px; font-size: 16px; border: 1px solid #ccc; border-radius: 4px;" value="<?php echo htmlspecialchars($searchTerm); ?>" />
                            <button type="submit" style="padding: 10px; font-size: 16px;">Search</button>
                        </form>
                    </div>

                    <div id="map" style="height: 450px; width: 100%;"></div>

                    <script>
                        function initMap() {
                            // Create a map centered at a default location
                            const defaultLocation = { lat: 7.448212, lng: 125.809425 }; // Example: Davao de Oro
                            const map = new google.maps.Map(document.getElementById("map"), {
                                zoom: 13,
                                center: defaultLocation
                            });

                            // Data from PHP (embedded as a JavaScript variable)
                            const products = <?php echo json_encode($data['data']); ?>;

                            // Remove existing markers before adding new ones
                            const markers = [];
                            function clearMarkers() {
                                markers.forEach(marker => marker.setMap(null));
                                markers.length = 0;
                            }

                            // Iterate over the products and add markers
                            products.forEach((product) => {
                                const latitude = parseFloat(product.latitude);
                                const longitude = parseFloat(product.longitude);

                                // Ensure latitude and longitude are valid numbers
                                if (!isNaN(latitude) && !isNaN(longitude)) {
                                    const marker = new google.maps.Marker({
                                        position: { lat: latitude, lng: longitude },
                                        map: map,
                                        title: `${product.Product_Name} by ${product.first_name} ${product.last_name}`
                                    });

                                    // Create an InfoWindow with product name and price
                                    const infoWindowContent = `
                                        <div>
                                            <h4>${product.Product_Name}</h4>
                                            <p>Price: ₱${product.price}</p>
                                        </div>
                                    `;
                                    const infoWindow = new google.maps.InfoWindow({
                                        content: infoWindowContent
                                    });

                                    marker.addListener('click', () => {
                                        window.location.href = `product.php?id=${product.id}`;
                                    });

                                    marker.addListener('mouseover', () => {
                    if (!isMobile()) { // Ensure it's not a mobile device
                        infoWindow.open(map, marker); // Open the InfoWindow automatically
                    }
                });

                // Add event listener to close InfoWindow on mouseout (desktop)
                marker.addListener('mouseout', () => {
                    if (!isMobile()) { // Ensure it's not a mobile device
                        infoWindow.close(); // Close the InfoWindow when mouse leaves the marker
                    }
                });

                // For mobile: open InfoWindow automatically on tap (touchstart)
                marker.addListener('touchstart', () => {
                    infoWindow.open(map, marker); // Open the InfoWindow automatically on tap
                });

                // For mobile: close InfoWindow on touchend (tap ends)
                marker.addListener('touchend', () => {
                    infoWindow.close(); // Close the InfoWindow after tap ends
                });

                                    // Add the marker to the markers array
                                    markers.push(marker);
                                }
                            });
                        }
                        // Function to detect if the user is on a mobile device
        function isMobile() {
            return /Mobi|Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
        }

                        // Function to handle search form submission
                        function handleSearch(event) {
                            event.preventDefault(); // Prevent form from submitting normally

                            const searchTerm = document.getElementById('search-input').value.trim();
                            if (searchTerm) {
                                // Redirect with the search query
                                window.location.href = `product-category.php?search=${encodeURIComponent(searchTerm)}`;
                            } else {
                                // If no search term, reload without the search query
                                window.location.href = `product-category.php`;
                            }
                        }

                        // Initialize the map after the page loads
                        window.onload = initMap;
                    </script>
                    <script async defer
                        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBa72Eer6ilUkPDSQn4ENOACV_oDYIpkOk&libraries=places&callback=initMap">
                    </script>
            </div>
        </div>
    </div>
</div>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <!-- Sidebar Category Display -->

                <form method="GET" action="product-category.php">
    <label for="category"><h3>Categories <i class="fa fa-sort"></i></h3></label><br>

    <!-- Category Dropdown -->
    <select name="category_id" class="btn btn-primary category-button" id="category" style="font-size: 18px; text-align: left;">
        <option value="" selected>Select a Category</option>

        <!-- Loop through categories and create options -->
        <?php if (isset($categories['data']) && count($categories['data']) > 0): ?>
            <?php foreach ($categories['data'] as $category): ?>
                <option value="<?php echo $category['id']; ?>"
                    <?php echo ($category['id'] == $category_id) ? 'selected' : ''; ?>>
                    <?php echo $category['category']; ?>
                </option>
            <?php endforeach; ?>
            <option value="0" <?php echo ($category_id == '0') ? 'selected' : ''; ?>>All</option>
        <?php else: ?>
            <p>No categories available.</p>
        <?php endif; ?>
    </select>
<br>
    <label for="price_range"><h3>Price Range (₱)</h3></label>
    <!-- Price Range Inputs -->
    <div>
    <label for="min_price" style="font-size: 18px;">Min:</label>
        <input type="number" name="min_price" placeholder="Min Price" value="<?php echo $min_price; ?>" class="form-control" style="font-size: 18px;">
        <label for="min_price" style="font-size: 18px;">Max:</label>
        <input type="number" name="max_price" placeholder="Max Price" value="<?php echo $max_price; ?>" class="form-control" style="font-size: 18px;">
    </div>
<br>
    <!-- Filter Button -->
    <button type="submit" class="btn btn-success" style="font-size: 18px;">Filter</button>
</form>

            </div>
            <br>
            <div class="col-md-9"><h2>Products</h2>
                <div class="product-list">
                    <?php
                    // var_dump($products['data']);
                    if (isset($products['data']) && count($products['data']) > 0): ?>
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

<!-- HTML Code -->

                                                <h6>Stock: <?php echo $product['Quantity']; ?></h6>
                                                <h5><?php echo $product['last_name']; ?> <?php echo $product['first_name']; ?></h5>
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
