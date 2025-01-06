<?php require_once('header.php');
require_once('api-config.php');
?>

<?php
// Check if the customer is logged in or not
if (!isset($_SESSION['customer'])) {
    header('location: ' . BASE_URL . 'logout.php');
    exit;
} else {
    // Fetch user data from the external API
    $email = $_SESSION['customer']['email'];
    $apiUrl = API_BASE_URL ."/login/submit?email=" . urlencode($email);
    $userData = [];

    try {
        $response = file_get_contents($apiUrl);

        if ($response === FALSE) {
            throw new Exception("Unable to fetch data from API");
        }

        $responseData = json_decode($response, true);

        // Check if the user array exists and is not empty
        if (isset($responseData['user']) && count($responseData['user']) > 0) {
            $userData = $responseData['user'][0]; // Get the first user's data
        } else {
            throw new Exception("No user data found in the API response");
        }
    } catch (Exception $e) {
        // Redirect to logout or handle error gracefully
        header('location: ' . BASE_URL . 'logout.php');
        exit;
    }
}
?>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="user-content">
                    <h3><?php echo LANG_VALUE_117; ?></h3>
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
                        //var_dump($_SESSION);
                        ?>
                        <div class="row">
                            <input type="hidden" name="user_id" value="<?php echo $_SESSION['customer']['user_id']; ?>">

                            <div class="col-md-6 form-group">
                                <label for="">First Name *</label>
                                <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($userData['name'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="">Last Name</label>
                                <input type="text" class="form-control" name="lastname" value="<?php echo htmlspecialchars($userData['lastname'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for=""><?php echo LANG_VALUE_94; ?> *</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>" disabled>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="">Phone Number *</label>
                                <input type="text" class="form-control" name="contact_number" value="<?php echo htmlspecialchars($userData['contact_number'] ?? ''); ?>">
                            </div>
                            <div class="col-md-12 form-group">
                                <label for=""><?php echo LANG_VALUE_105; ?> *</label>
                                <textarea name="address" class="form-control" cols="30" rows="10" style="height:70px;"><?php echo htmlspecialchars($userData['address'] ?? ''); ?></textarea>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="">Password *</label>
                                <input type="password" class="form-control" name="cust_password" value="">
                            </div>
                        </div>
                        <input type="submit" class="btn btn-primary" value="<?php echo LANG_VALUE_5; ?>" name="form1">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelector('#profile-form').addEventListener('submit', function(event) {
        event.preventDefault();

        // Get form data
        var formData = new FormData(this);

        // Send data via AJAX
        fetch('http://192.168.1.9:8080/buyer/update-user.php', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.message === 'User updated successfully.') {
                // Show success message
                alert('Profile updated successfully!');
            } else {
                // Show error message
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
</script>

<?php require_once('footer.php'); ?>
