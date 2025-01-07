<?php
require_once('header.php');
require_once('api-config.php');

// Check if the customer is logged in
if (!isset($_SESSION['customer'])) {
    header('location: ' . BASE_URL . 'logout.php');
    exit;
}

$error_message = '';
$success_message = '';

// Fetch user data using the GET request to the API
$userData = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $email = $_SESSION['customer']['email'];
    $apiUrl = API_BASE_URL . "/login/submit?email=" . urlencode($email);

    try {
        // Make the GET request to fetch user data
        $response = file_get_contents($apiUrl);

        // Check if the API request was successful
        if ($response === FALSE) {
            throw new Exception("Unable to fetch data from API");
        }

        // Decode the JSON response into an array
        $responseData = json_decode($response, true);

        // var_dump($responseData);
        // Check if the response contains user data
        if (isset($responseData['user']) && count($responseData['user']) > 0) {
            $userData = $responseData['user'][0]; // Get the first user's data
        } else {
            throw new Exception("No user data found in the API response");
        }
    } catch (Exception $e) {
        // Handle error gracefully and redirect to logout if necessary
        header('location: ' . BASE_URL . 'logout.php');
        exit;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form1'])) {
    $postData = [
        'user_id' => $_POST['user_id'],
        'name' => $_POST['name'],
        'lastname' => $_POST['lastname'],
        'contact_number' => $_POST['contact_number'],
        'address' => $_POST['address'],
    ];

    // Send the POST request to the API
    $apiUrl = API_BASE_URL . "/buyer/update-profile-api.php";
    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($postData)
        ]
    ];
    $context = stream_context_create($options);
    $response = file_get_contents($apiUrl, false, $context);

    // Handle the API response
    $result = json_decode($response, true);
    if ($result['status'] === 'success') {
        $success_message = $result['message'];
        header("Refresh:0");
    } else {
        $error_message = $result['message'];
    }
}
?>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="user-content">
                    <h3>Update Profile</h3>
                    <?php
                    if (!empty($error_message)) {
                        echo "<div class='error' style='padding: 10px;background:#f1f1f1;margin-bottom:20px;'>" . $error_message . "</div>";
                    }
                    if (!empty($success_message)) {
                        echo "<div class='success' style='padding: 10px;background:#f1f1f1;margin-bottom:20px;'>" . $success_message . "</div>";
                    }
                    ?>
                    <form id="profile-form" method="post" enctype="multipart/form-data">
                        <?php $csrf->echoInputField();
                            // var_dump($userData);
                        ?>
                        <div class="row">
                            <input type="hidden" name="user_id" value="<?php echo $userData['user_id'] ?? ''; ?>">


                            <div class="col-md-6 form-group">
                                <label for="">First Name *</label>
                                <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($userData['name'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="">Last Name</label>
                                <input type="text" class="form-control" name="lastname" value="<?php echo htmlspecialchars($userData['lastname'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="">Email *</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>" disabled>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="">Phone Number *</label>
                                <input type="text" class="form-control" name="contact_number" value="<?php echo htmlspecialchars($userData['contact_number'] ?? ''); ?>">
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="">Address *</label>
                                <textarea name="address" class="form-control" cols="30" rows="10" style="height:70px;"><?php echo htmlspecialchars($userData['address'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        <input type="submit" class="btn btn-primary" value="Update Profile" name="form1">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>



<?php require_once('footer.php'); ?>
