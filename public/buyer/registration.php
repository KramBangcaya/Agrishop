<?php require_once('header.php');
require_once('api-config.php');
?>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row) {
    $banner_registration = $row['banner_registration'];
}
?>

<?php
if (isset($_POST['form1'])) {

    $url = API_BASE_URL . "/api/user/register"; // Laravel API endpoint

    // Prepare the data to send (excluding the photos for now)
    $postData = [
        'name' => $_POST['cust_name'],
        'lastname' => $_POST['cust_cname'],
        'middle_initial' => $_POST['cust_middle_initial'] ?? '',
        'date_of_birth' => $_POST['cust_dob'],
        'contact_number' => $_POST['cust_phone'],
        'telephone_number' => $_POST['cust_tel'] ?? '',
        'address' => $_POST['cust_address'],
        'email' => $_POST['cust_email'],
        'password' => $_POST['cust_password'],
    ];

    // Handle file uploads (multiple files for photos)
    if (isset($_FILES['photos']['tmp_name']) && !empty($_FILES['photos']['tmp_name'])) {
        $files = [];
        foreach ($_FILES['photos']['tmp_name'] as $key => $tmp_name) {
            $files['photos[' . $key . ']'] = new CURLFile($_FILES['photos']['tmp_name'][$key], $_FILES['photos']['type'][$key], $_FILES['photos']['name'][$key]);
        }
        // Merge file data into postData
        $postData = array_merge($postData, $files);
    }

    // cURL request to the API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Process the response
    if ($httpCode == 201) {
        $success_message = "User successfully registered.";
        // echo "<script>alert('$success_message');</script>";
    } else {
        $error_message = "Registration failed. Please try again.";
        // echo "<script>alert('$error_message');</script>";
    }
}
?>

<div class="page-banner" style="background-color:#444;background-image: url(assets/uploads/<?php echo $banner_registration; ?>);">
    <div class="inner">
        <h1>Registration</h1>
    </div>
</div>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="user-content">
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="col-md-6 form-group">
                            <label for="">First Name *</label>
                            <input type="text" class="form-control" name="cust_name" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="">Last Name *</label>
                            <input type="text" class="form-control" name="cust_cname" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="">Middle Initial</label>
                            <input type="text" class="form-control" name="cust_middle_initial">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="">Email *</label>
                            <input type="email" class="form-control" name="cust_email" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="">Password *</label>
                            <input type="password" class="form-control" name="cust_password" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="">Date of Birth *</label>
                            <input type="date" class="form-control" name="cust_dob" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="">Phone *</label>
                            <input type="text" class="form-control" name="cust_phone" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="">Address *</label>
                            <input type="text" class="form-control" name="cust_address" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="">Upload Photo *</label>
                            <input type="file" class="form-control" name="photos[]" required multiple>
                        </div>
                        <div style="color: #ff0000;" class="col-md-6 form-group">
                            <label for="">* If You are a Buyer, please provide your Valid ID</label>
                        </div>
                        <div class="col-md-6 form-group">
                            <input type="submit" class="btn btn-danger" name="form1" value="Register">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>
