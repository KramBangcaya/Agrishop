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

<<<<<<< HEAD
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
=======
>>>>>>> df4680cb15b87ee1ab2bb0cf3b0757b2e326d090

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
<<<<<<< HEAD
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
=======



                    <form action="" method="post">
                        <?php $csrf->echoInputField(); ?>
                        <div class="row">
                            <div class="col-md-2"></div>
                            <div class="col-md-8">

                                <?php
                                if($error_message != '') {
                                    echo "<div class='error' style='padding: 10px;background:#f1f1f1;margin-bottom:20px;'>".$error_message."</div>";
                                }
                                if($success_message != '') {
                                    echo "<div class='success' style='padding: 10px;background:#f1f1f1;margin-bottom:20px;'>".$success_message."</div>";
                                }
                                ?>

                                <div class="col-md-6 form-group">
                                    <label for="">First Name *</label>
                                    <input type="text" class="form-control" name="cust_name" value="<?php if(isset($_POST['cust_name'])){echo $_POST['cust_name'];} ?>">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="">Last Name *</label>
                                    <input type="text" class="form-control" name="cust_cname" value="<?php if(isset($_POST['cust_cname'])){echo $_POST['cust_cname'];} ?>">
                                    <label for="">Middle Initial *</label>
                                    <input type="text" class="form-control" name="cust_cname" value="<?php if(isset($_POST['cust_cname'])){echo $_POST['cust_cname'];} ?>">
                                </div>

                                <div class="col-md-6 form-group">
                                    <label for="">Telephone number *</label>
                                    <input type="text" class="form-control" name="cust_cname" value="<?php if(isset($_POST['cust_cname'])){echo $_POST['cust_cname'];} ?>">
                                </div>

                                <div class="col-md-6 form-group">
                                    <label for=""><?php echo LANG_VALUE_104; ?> *</label>
                                    <input type="text" class="form-control" name="cust_phone" value="<?php if(isset($_POST['cust_phone'])){echo $_POST['cust_phone'];} ?>">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for=""><?php echo LANG_VALUE_94; ?> *</label>
                                    <input type="email" class="form-control" name="cust_email" value="<?php if(isset($_POST['cust_email'])){echo $_POST['cust_email'];} ?>">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="">Date of birth *</label>
                                    <input type="date" class="form-control" name="cust_email" value="<?php if(isset($_POST['cust_email'])){echo $_POST['cust_email'];} ?>">
                                </div>
                                <div class="col-md-12 form-group">
                                    <label for=""><?php echo LANG_VALUE_105; ?> *</label>
                                    <textarea name="cust_address" class="form-control" cols="30" rows="10" style="height:70px;"><?php if(isset($_POST['cust_address'])){echo $_POST['cust_address'];} ?></textarea>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for=""><?php echo LANG_VALUE_96; ?> *</label>
                                    <input type="password" class="form-control" name="cust_password">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="">Confirm Password *</label>
                                    <input type="password" class="form-control" name="cust_re_password">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="">Upload Supporting Information *</label>
                                    <input type="file" class="form-control" name="cust_re_password">
                                </div>



                                <div style=" color: #ff0000;"class="col-md-6 form-group">
                                <label for="">* If You are a Buyer provide your Valid ID</label>   <br>
                                <label for="">* If You are a Seller provide your Business Permit</label><br>
                                <label for="">* If You are a Seller provide your QR Code</label>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for=""></label>
                                    <input type="submit" class="btn btn-danger" value="<?php echo LANG_VALUE_15; ?>" name="form1">
                                </div>


                            </div>
>>>>>>> df4680cb15b87ee1ab2bb0cf3b0757b2e326d090
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>
