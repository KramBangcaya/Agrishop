<?php
require_once('header.php');
require_once('api-config.php');
?>
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

<?php

if (!isset($_SESSION['customer'])) {
    header('location: ' . BASE_URL . 'logout.php');
    exit;
}
// Fetch data from the API
$api_url = API_BASE_URL . "/seller/all_buyer";
$response = file_get_contents($api_url);
$data = json_decode($response, true);

// Check if data is available
$options = "";
if (isset($data['data']) && is_array($data['data'])) {
    foreach ($data['data'] as $user) {
        // Display only sellers
        if ($user['role_name'] == 'Seller') {
            // Prepare the full name and role
            $user_name = $user['user_name'];
            $user_lastname = $user['user_lastname'] ? $user['user_lastname'] : '';
            $role_name = $user['role_name'];
            $full_name = $user_name . ' ' . $user_lastname; // Full name (first and last)

            // Generate the option for the dropdown using the full name
            $options .= "<option value='{$full_name}'>{$full_name} - {$role_name}</option>";
        }
    }
}
?>

<div class="page">
    <div class="container">
        <div class="row">

            <div class="col-md-12">
                <div class="user-content">
                    <h3>
                    <button class="btn" onclick="window.history.back()"><i class="fa fa-arrow-left" aria-hidden="true"></i></button>  Report Seller
                    </h3>
                    <?php

                    // var_dump($_SESSION['customer']['user_id']);
                    if($error_message != '') {
                        echo "<div class='error' style='padding: 10px;background:#f1f1f1;margin-bottom:20px;'>".$error_message."</div>";
                    }
                    if($success_message != '') {
                        echo "<div class='success' style='padding: 10px;background:#f1f1f1;margin-bottom:20px;'>".$success_message."</div>";
                    }
                    ?>
                    <form id="report-form" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="">Complainee's Name *</label>
                                <select class="form-control" name="reported_name" id="complainee-select">
                                    <option value="">Select Complainee</option>
                                    <?php echo $options; ?>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="">Attached File *</label>
                                <input type="file" class="form-control" name="proof[]" multiple>
                            </div>

                            <div class="col-md-12 form-group">
                                <label for="">Reason *</label>
                                <textarea name="reason" class="form-control" cols="30" rows="10" style="height:70px;"></textarea>
                            </div>
                        </div>
                        <input type="submit" class="btn btn-primary" value="Report">
                        <a href="reportseller.php" class="btn btn-primary">Cancel</a>
                    </form>
                    <div id="response-message"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>

<!-- Initialize Select2 -->
<script>
    $(document).ready(function() {
        $('#complainee-select').select2({
            placeholder: "Select Complainee",
            allowClear: true
        });

        // Handle form submission with AJAX
        $('#report-form').on('submit', function(e) {
            e.preventDefault(); // Prevent default form submission

            // Create FormData object
            var formData = new FormData(this);

            var userId = '<?php echo $_SESSION['customer']['user_id']; ?>';
            formData.append('userID', userId); // Append user_id to the form data

            var apiUrl = '<?php echo API_BASE_URL; ?>' + '/api/report/receive-report';
            $.ajax({
                url: apiUrl, // API endpoint
                type: 'POST',
                data: formData,
                processData: false,  // Important: prevent jQuery from processing the data
                contentType: false,  // Important: let the browser set the content type
                success: function(response) {
                    // Handle success
                    $('#response-message').html("<div class='success' style='padding: 10px;background:#f1f1f1;margin-bottom:20px;'>Report received successfully</div>");
                },
                error: function(xhr, status, error) {
                    // Handle error
                    $('#response-message').html("<div class='error' style='padding: 10px;background:#f1f1f1;margin-bottom:20px;'>Error: " + error + "</div>");
                }
            });
        });
    });
</script>
