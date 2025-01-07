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
=======
<!-- Modal for displaying messages -->
<div id="messageModal" class="modal" tabindex="-1" role="dialog" style="display: none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Message</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="modalMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal CSS for styling -->
<style>
    .modal {
        display: none;
        position: fixed;
        z-index: 1050;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
    }
    .modal-dialog {
        position: relative;
        margin: 10% auto;
        max-width: 500px;
    }
    .modal-content {
        background-color: #fff;
        padding: 20px;
        border-radius: 4px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    }
</style>

>>>>>>> aa34e4fecb5c50a8a6538b9226766963f0402361
<?php
if (isset($_POST['form1'])) {

    // Check if passwords match
    if ($_POST['cust_password'] !== $_POST['cust_confirm_password']) {
        $error_message = "Passwords do not match. Please try again.";
        echo "<script>showModal('$error_message', false);</script>";
    } else {
        // Age validation: Check if the user is at least 18 years old
        $dob = $_POST['cust_dob'];
        $dob_date = new DateTime($dob);
        $current_date = new DateTime();
        $age = $current_date->diff($dob_date)->y;

        if ($age < 18) {
            $error_message = "You must be at least 18 years old to register.";
            echo "<script>showModal('$error_message', false);</script>";
        } else {
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
                echo "<script>showModal('$success_message', true);</script>";
            } else {
                $error_message = "Registration failed. Please try again.";
                echo "<script>showModal('$error_message', false);</script>";
            }
        }
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
                            <label for="">Confirm Password *</label>
                            <input type="password" class="form-control" name="cust_confirm_password" required>
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

<!-- JavaScript for Modal Functionality -->
<script>
function showModal(message, isSuccess) {
    document.getElementById('modalMessage').innerText = message;
    document.getElementById('modalTitle').innerText = isSuccess ? 'Success' : 'Error';
    document.getElementById('messageModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('messageModal').style.display = 'none';
}
</script>
