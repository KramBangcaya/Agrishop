<?php
require_once('header.php');
require_once('api-config.php');
?>

<?php
// Initialize cURL to fetch data from the external API
$apiUrl = API_BASE_URL . "/seller/all";
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
                        const users = <?php echo json_encode($data['user']); ?>;

                        // Iterate over the users and add markers
                        users.forEach((user) => {
                            const latitude = parseFloat(user.latitude);
                            const longitude = parseFloat(user.longitude);

                            // Ensure latitude and longitude are valid numbers
                            if (!isNaN(latitude) && !isNaN(longitude)) {
                                const marker = new google.maps.Marker({
                                    position: { lat: latitude, lng: longitude },
                                    map: map,
                                    title: `${user.name} ${user.lastname}`
                                });

                                // marker.addListener('click', () => {
                                //     alert(`User ID: ${user.id}`);
                                // });

                                marker.addListener('click', () => {
                                    window.location.href = `sellerindex.php?user_id=${user.id}`;
                                });
                            }
                        });
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
