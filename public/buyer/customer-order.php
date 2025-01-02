<?php
require_once('header.php');
require_once('api-config.php');
?>

<?php
// Check if the customer is logged in or not
if(!isset($_SESSION['customer'])) {
    header('location: '.BASE_URL.'logout.php');
    exit;
} else {
    // Get the email from the session
    $cust_email = $_SESSION['customer']['email'];

    // API URL with the email as a parameter
    $api_url = API_BASE_URL . "/login/submit?email=" . urlencode($cust_email);

    // Initialize cURL session
    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification if needed
    $api_response = curl_exec($ch);
    curl_close($ch);

    // Decode the API response
    $response_data = json_decode($api_response, true);

    if ($response_data && isset($response_data['user'][0])) {
        // Get the user's data from the API response
        $user = $response_data['user'][0]; // Assuming 'status' field exists in the API response


    } else {
        // If no data is returned from the API, force logout or show an error
        header('location: '.BASE_URL.'logout.php');
        exit;
    }
}
?>


<div class="page">
    <div class="container">


        <div class="row">
            <div class="col-md-12">
                <?php require_once('customer-sidebar.php'); ?>
            </div>



            <div class="col-md-12">
                <div class="user-content">
                    <h1><?php echo LANG_VALUE_25; ?></h1>
                    <h3 class="special"> </h3>
                    <div class="table-responsive">


                    <div class="row">
    <div class="col-md-4">
        <div class="row" style="margin: 0 auto;"> <!-- Centering the inner row -->
            <div class="col-md-12 form-group">
                <h2>
                    Apple&nbsp;
                    ₱30.00&nbsp;

                </h2>


                <!-- Product Image -->
            <!-- Responsive and spaced -->

                <!-- Quantity and Total -->
                <div style="margin-top: 10px; font-size: medium;">
                    <label>Quantity: </label>
                    <input type="number"
                           class="input-text qty text"
                           step="1"
                           min="1"
                           max=""
                           name="quantity[]"
                           value="5"
                           title="Qty"
                           size="4"
                           pattern="[0-9]*"
                           inputmode="numeric"
                           style="width: 60px; margin-right: 10px;">

                    <label>Total: </label>

                    ₱200.00<br><br>
                    <label>Payment date time: 10/20/2024</label><br>
                    <label>Transaction ID: wenrjwjno3423</label><br>
                    <label>Payment ID: 213123214</label><br>
                    <label>Seller Name: Hazel Tomol</label><br>
                    <label>Seller Number: 09092930003</label><br>
                    <label>Seller Address: Tagum, Davao del Norte.</label><br>
                    <label>Order Status:  </label><p style="color:green;">Delivered</p>
                    <br><h4> <a onclick="return confirmDelte();"
                       href="cart-item-delete.php?id=<?php echo $arr_cart_p_id[$i]; ?>"
                       class="trash">
                       Cancel Order <i class="fa fa-ban" style="color:red;"></i>
                    </a></h4>
                    <h4 onclick="toggleFeedbackForm(event)" style="cursor: pointer;">
    Feedback <i class="fa fa-comments" style="color:green;"></i>
</h4>
<div id="feedback-form" style="display: none; margin-top: 10px;">
    <textarea id="feedback-text" placeholder="Enter your feedback here..." rows="4" cols="50"></textarea>
    <br>
    <button onclick="submitFeedback()">Submit</button>
    <button onclick="cancelFeedback()">Cancel</button>
</div>

                </div>


            </div>
        </div>
    </div>
</div>
<script>
function toggleFeedbackForm(event) {
    event.preventDefault(); // Prevent default behavior (if needed)
    const feedbackForm = document.getElementById('feedback-form');
    if (feedbackForm) {
        feedbackForm.style.display = feedbackForm.style.display === 'none' ? 'block' : 'none';
    } else {
        console.error('Feedback form not found.');
    }
}

function submitFeedback() {
    const textarea = document.getElementById('feedback-text');
    if (textarea) {
        const feedback = textarea.value.trim();
        if (feedback) {
            console.log('Feedback:', feedback);
            alert(`Feedback submitted: ${feedback}`);
            document.getElementById('feedback-form').style.display = 'none'; // Hide the form after submission
        } else {
            alert('Please enter your feedback before submitting.');
        }
    } else {
        console.error('Feedback textarea not found.');
    }
}

function cancelFeedback() {
    const feedbackForm = document.getElementById('feedback-form');
    if (feedbackForm) {
        feedbackForm.style.display = 'none'; // Hide the feedback form
    } else {
        console.error('Feedback form not found.');
    }
}
</script>
<h3 class="special"> </h3>



            <?php
            /* ===================== Pagination Code Starts ================== */
            $adjacents = 5;

            $statement = $pdo->prepare("SELECT * FROM tbl_payment WHERE customer_email=? ORDER BY id DESC");
            //$statement->execute(array($_SESSION['customer']['cust_email']));
            $total_pages = $statement->rowCount();

            $targetpage = BASE_URL.'customer-order.php';
            $limit = 10;
            $page = @$_GET['page'];
            if($page)
                $start = ($page - 1) * $limit;
            else
                $start = 0;


            $statement = $pdo->prepare("SELECT * FROM tbl_payment WHERE customer_email=? ORDER BY id DESC LIMIT $start, $limit");
            //$statement->execute(array($_SESSION['customer']['cust_email']));
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);


            if ($page == 0) $page = 1;
            $prev = $page - 1;
            $next = $page + 1;
            $lastpage = ceil($total_pages/$limit);
            $lpm1 = $lastpage - 1;
            $pagination = "";
            if($lastpage > 1)
            {
                $pagination .= "<div class=\"pagination\">";
                if ($page > 1)
                    $pagination.= "<a href=\"$targetpage?page=$prev\">&#171; previous</a>";
                else
                    $pagination.= "<span class=\"disabled\">&#171; previous</span>";
                if ($lastpage < 7 + ($adjacents * 2))
                {
                    for ($counter = 1; $counter <= $lastpage; $counter++)
                    {
                        if ($counter == $page)
                            $pagination.= "<span class=\"current\">$counter</span>";
                        else
                            $pagination.= "<a href=\"$targetpage?page=$counter\">$counter</a>";
                    }
                }
                elseif($lastpage > 5 + ($adjacents * 2))
                {
                    if($page < 1 + ($adjacents * 2))
                    {
                        for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
                        {
                            if ($counter == $page)
                                $pagination.= "<span class=\"current\">$counter</span>";
                            else
                                $pagination.= "<a href=\"$targetpage?page=$counter\">$counter</a>";
                        }
                        $pagination.= "...";
                        $pagination.= "<a href=\"$targetpage?page=$lpm1\">$lpm1</a>";
                        $pagination.= "<a href=\"$targetpage?page=$lastpage\">$lastpage</a>";
                    }
                    elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
                    {
                        $pagination.= "<a href=\"$targetpage?page=1\">1</a>";
                        $pagination.= "<a href=\"$targetpage?page=2\">2</a>";
                        $pagination.= "...";
                        for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
                        {
                            if ($counter == $page)
                                $pagination.= "<span class=\"current\">$counter</span>";
                            else
                                $pagination.= "<a href=\"$targetpage?page=$counter\">$counter</a>";
                        }
                        $pagination.= "...";
                        $pagination.= "<a href=\"$targetpage?page=$lpm1\">$lpm1</a>";
                        $pagination.= "<a href=\"$targetpage?page=$lastpage\">$lastpage</a>";
                    }
                    else
                    {
                        $pagination.= "<a href=\"$targetpage?page=1\">1</a>";
                        $pagination.= "<a href=\"$targetpage?page=2\">2</a>";
                        $pagination.= "...";
                        for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
                        {
                            if ($counter == $page)
                                $pagination.= "<span class=\"current\">$counter</span>";
                            else
                                $pagination.= "<a href=\"$targetpage?page=$counter\">$counter</a>";
                        }
                    }
                }
                if ($page < $counter - 1)
                    $pagination.= "<a href=\"$targetpage?page=$next\">next &#187;</a>";
                else
                    $pagination.= "<span class=\"disabled\">next &#187;</span>";
                $pagination.= "</div>\n";
            }
            /* ===================== Pagination Code Ends ================== */
            ?>


                                <?php
                                $tip = $page*10-10;
                                foreach ($result as $row) {
                                    $tip++;
                                    ?>




                                    <?php
                                }
                                ?>





                        <div class="pagination" style="overflow: hidden;">
                        <?php
                            echo $pagination;
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>
