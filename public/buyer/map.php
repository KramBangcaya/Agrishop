<?php
require_once('header.php');
require_once('api-config.php');

// Check if there's a search term in the query parameters
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Initialize cURL to fetch data from the new API with search term
$apiUrl = "http://192.168.1.9:8080/products/all_product?search=" . urlencode($searchTerm);
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

// Decode the JSON response from the API
$data = json_decode($response, true);

// Get the page details
$statement = $pdo->prepare("SELECT * FROM tbl_page WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row) {
    $faq_title = $row['faq_title'];
    $faq_banner = $row['faq_banner'];
}
?>

<div class="page-banner" style="background-image: url(assets/uploads/<?php echo $faq_banner; ?>);">
    <div class="inner">
        <h1>Near Me</h1>
    </div>
</div>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
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
                            zoom: 12,
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
                            window.location.href = `map.php?search=${encodeURIComponent(searchTerm)}`;
                        } else {
                            // If no search term, reload without the search query
                            window.location.href = `map.php`;
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

<?php require_once('footer.php'); ?>
