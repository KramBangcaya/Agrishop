<?php
require_once('header.php');

if (isset($_POST['verify_otp'])) {
    $entered_otp = $_POST['otp'];

    // Check if the entered OTP matches the session OTP
    if ($entered_otp == $_SESSION['otp']) {
        $user = $_SESSION['temp_user'];
        $user_id = $user['user_id'];

        // Call the API to update date_login
        $api_url = "http://192.168.1.9:8080/api/user/login_otp/$user_id";

        // echo $api_url;
        $ch = curl_init($api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); // Set the request to POST
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Optional: disable SSL verification
        $api_response = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        echo $http_status;
        // Check the API response
        if ($http_status == 201) {
            $_SESSION['customer'] = $user; // Save user data in session
            unset($_SESSION['otp']); // Clear OTP from session
            unset($_SESSION['temp_user']); // Clear temporary user data from session

            header("location: " . BASE_URL . "dashboard.php?id=" . $user_id);
            exit;
        } else {
            $error_message = "Failed to update login information. Please try again later.";
        }
    } else {
        $error_message = "Invalid OTP. Please try again.";
    }
}
?>

<div class="container">
    <div class="row">
        <div class="col-md-4 offset-md-4">
            <h3>OTP Verification</h3>
            <?php
            if (!empty($error_message)) {
                echo "<div class='error' style='padding: 10px;background:#f1f1f1;margin-bottom:20px;'>$error_message</div>";
            }
            ?>
            <form method="post" action="">
                <div class="form-group">
                    <label for="otp">Enter OTP</label>
                    <input type="text" name="otp" class="form-control" required>
                </div>
                <div class="form-group">
                    <button type="submit" name="verify_otp" class="btn btn-success">Verify</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>
