<!-- This is main configuration File -->
<?php
ob_start();
session_start();
include("admin/inc/config.php");
include("admin/inc/functions.php");
include("admin/inc/CSRF_Protect.php");
$csrf = new CSRF_Protect();
$error_message = '';
$success_message = '';
$error_message1 = '';
$success_message1 = '';

// Getting all language variables into array as global variable
$i=1;
$statement = $pdo->prepare("SELECT * FROM tbl_language");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row) {
	define('LANG_VALUE_'.$i,$row['lang_value']);
	$i++;
}

$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row)
{
	$logo = $row['logo'];
	$favicon = $row['favicon'];
	$contact_email = $row['contact_email'];
	$contact_phone = $row['contact_phone'];
	$meta_title_home = $row['meta_title_home'];
    $meta_keyword_home = $row['meta_keyword_home'];
    $meta_description_home = $row['meta_description_home'];
    $before_head = $row['before_head'];
    $after_body = $row['after_body'];
}

// Checking the order table and removing the pending transaction that are 24 hours+ old. Very important
$current_date_time = date('Y-m-d H:i:s');
$statement = $pdo->prepare("SELECT * FROM tbl_payment WHERE payment_status=?");
$statement->execute(array('Pending'));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row) {
	$ts1 = strtotime($row['payment_date']);
	$ts2 = strtotime($current_date_time);
	$diff = $ts2 - $ts1;
	$time = $diff/(3600);
	if($time>24) {

		// Return back the stock amount
		$statement1 = $pdo->prepare("SELECT * FROM tbl_order WHERE payment_id=?");
		$statement1->execute(array($row['payment_id']));
		$result1 = $statement1->fetchAll(PDO::FETCH_ASSOC);
		foreach ($result1 as $row1) {
			$statement2 = $pdo->prepare("SELECT * FROM products WHERE id=?");
			$statement2->execute(array($row1['product_id']));
			$result2 = $statement2->fetchAll(PDO::FETCH_ASSOC);
			foreach ($result2 as $row2) {
				$p_qty = $row2['Quantity'];
			}
			$final = $p_qty+$row1['quantity'];

			$statement = $pdo->prepare("UPDATE products SET Quantity=? WHERE id=?");
			$statement->execute(array($final,$row1['product_id']));
		}

		// Deleting data from table
		$statement1 = $pdo->prepare("DELETE FROM tbl_order WHERE payment_id=?");
		$statement1->execute(array($row['payment_id']));

		$statement1 = $pdo->prepare("DELETE FROM tbl_payment WHERE id=?");
		$statement1->execute(array($row['id']));
	}
}
?>




<!DOCTYPE html>
<html lang="en">
<head>

	<!-- Meta Tags -->
	<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

	<!-- Favicon -->
	<link rel="icon" type="image/png" href="assets/uploads/agrishopfavicon.png">

	<!-- Stylesheets -->
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/font-awesome.min.css">
	<link rel="stylesheet" href="assets/css/owl.carousel.min.css">
	<link rel="stylesheet" href="assets/css/owl.theme.default.min.css">
	<link rel="stylesheet" href="assets/css/jquery.bxslider.min.css">
    <link rel="stylesheet" href="assets/css/magnific-popup.css">
    <link rel="stylesheet" href="assets/css/rating.css">
	<link rel="stylesheet" href="assets/css/spacing.css">
	<link rel="stylesheet" href="assets/css/bootstrap-touch-slider.css">
	<link rel="stylesheet" href="assets/css/animate.min.css">
	<link rel="stylesheet" href="assets/css/tree-menu.css">
	<link rel="stylesheet" href="assets/css/select2.min.css">
	<link rel="stylesheet" href="assets/css/main.css">
	<link rel="stylesheet" href="assets/css/responsive.css">

	<?php

	$statement = $pdo->prepare("SELECT * FROM tbl_page WHERE id=1");
	$statement->execute();
	$result = $statement->fetchAll(PDO::FETCH_ASSOC);
	foreach ($result as $row) {
		$about_meta_title = $row['about_meta_title'];
		$about_meta_keyword = $row['about_meta_keyword'];
		$about_meta_description = $row['about_meta_description'];
		$faq_meta_title = $row['faq_meta_title'];
		$faq_meta_keyword = $row['faq_meta_keyword'];
		$faq_meta_description = $row['faq_meta_description'];
		$blog_meta_title = $row['blog_meta_title'];
		$blog_meta_keyword = $row['blog_meta_keyword'];
		$blog_meta_description = $row['blog_meta_description'];
		$contact_meta_title = $row['contact_meta_title'];
		$contact_meta_keyword = $row['contact_meta_keyword'];
		$contact_meta_description = $row['contact_meta_description'];
		$pgallery_meta_title = $row['pgallery_meta_title'];
		$pgallery_meta_keyword = $row['pgallery_meta_keyword'];
		$pgallery_meta_description = $row['pgallery_meta_description'];
		$vgallery_meta_title = $row['vgallery_meta_title'];
		$vgallery_meta_keyword = $row['vgallery_meta_keyword'];
		$vgallery_meta_description = $row['vgallery_meta_description'];
	}

	$cur_page = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);

	if($cur_page == 'index.php' || $cur_page == '../resources/views/auth/registers.blade.php' || $cur_page == '../resources/views/auth/registers.blade.php' || $cur_page == 'cart.php' || $cur_page == 'checkout.php' || $cur_page == 'forget-password.php' || $cur_page == 'reset-password.php' || $cur_page == 'product-category.php' || $cur_page == 'product.php') {
		?>
		<title><?php echo "Agrishop"; ?></title>
		<meta name="keywords" content="<?php echo $meta_keyword_home; ?>">
		<meta name="description" content="<?php echo $meta_description_home; ?>">
		<?php
	}

	if($cur_page == 'about.php') {
		?>
		<title><?php echo $about_meta_title; ?></title>
		<meta name="keywords" content="<?php echo $about_meta_keyword; ?>">
		<meta name="description" content="<?php echo $about_meta_description; ?>">
		<?php
	}
	if($cur_page == 'faq.php') {
		?>
		<title><?php echo $faq_meta_title; ?></title>
		<meta name="keywords" content="<?php echo $faq_meta_keyword; ?>">
		<meta name="description" content="<?php echo $faq_meta_description; ?>">
		<?php
	}
	if($cur_page == 'contact.php') {
		?>
		<title><?php echo $contact_meta_title; ?></title>
		<meta name="keywords" content="<?php echo $contact_meta_keyword; ?>">
		<meta name="description" content="<?php echo $contact_meta_description; ?>">
		<?php
	}
	if($cur_page == 'product.php')
	{
		$statement = $pdo->prepare("SELECT * FROM products WHERE id=?");
		$statement->execute(array($_REQUEST['id']));
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
		foreach ($result as $row)
		{
		    $og_photo = $row['photos'];
		    $og_title = $row['Product_Name'];
		    $og_slug = 'product.php?id='.$_REQUEST['id'];
			$og_description = substr(strip_tags($row['Description']),0,200).'...';
		}
	}

	if($cur_page == 'dashboard.php') {
		?>
		<title>Dashboard - <?php echo $meta_title_home; ?></title>
		<meta name="keywords" content="<?php echo $meta_keyword_home; ?>">
		<meta name="description" content="<?php echo $meta_description_home; ?>">
		<?php
	}
	if($cur_page == 'customer-profile-update.php') {
		?>
		<title>Update Profile - <?php echo $meta_title_home; ?></title>
		<meta name="keywords" content="<?php echo $meta_keyword_home; ?>">
		<meta name="description" content="<?php echo $meta_description_home; ?>">
		<?php
	}
	if($cur_page == 'customer-billing-shipping-update.php') {
		?>
		<title>Update Billing and Shipping Info - <?php echo $meta_title_home; ?></title>
		<meta name="keywords" content="<?php echo $meta_keyword_home; ?>">
		<meta name="description" content="<?php echo $meta_description_home; ?>">
		<?php
	}
	if($cur_page == 'customer-password-update.php') {
		?>
		<title>Update Password - <?php echo $meta_title_home; ?></title>
		<meta name="keywords" content="<?php echo $meta_keyword_home; ?>">
		<meta name="description" content="<?php echo $meta_description_home; ?>">
		<?php
	}
	if($cur_page == 'place-order.php') {
		?>
		<title>Orders - <?php echo $meta_title_home; ?></title>
		<meta name="keywords" content="<?php echo $meta_keyword_home; ?>">
		<meta name="description" content="<?php echo $meta_description_home; ?>">
		<?php
	}
	?>

	<?php if($cur_page == 'blog-single.php'): ?>
		<meta property="og:title" content="<?php echo $og_title; ?>">
		<meta property="og:type" content="website">
		<meta property="og:url" content="<?php echo BASE_URL.$og_slug; ?>">
		<meta property="og:description" content="<?php echo $og_description; ?>">
		<meta property="og:image" content="assets/uploads/<?php echo $og_photo; ?>">
	<?php endif; ?>

	<?php if($cur_page == 'product.php'): ?>
		<meta property="og:title" content="<?php echo $og_title; ?>">
		<meta property="og:type" content="website">
		<meta property="og:url" content="<?php echo BASE_URL.$og_slug; ?>">
		<meta property="og:description" content="<?php echo $og_description; ?>">
		<meta property="og:image" content="assets/uploads/<?php echo $og_photo; ?>">
	<?php endif; ?>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>


<?php echo $before_head; ?>

</head>
<body>

<?php echo $after_body; ?>
<!--
<div id="preloader">
	<div id="status"></div>
</div>-->

<!-- top bar -->



<div class="header">
	<div class="container">
		<div class="row inner">
			<div class="col-md-4 logo" >
				<a href="index.php"><img src="assets/uploads/agrishoplogo.png" alt="logo image"></a>
			</div>

			<div class="col-md-5 right" >
				<ul>

					<?php
					if(isset($_SESSION['customer'])) {
                        // var_dump($_SESSION);
						?>
						<li><i class="fa fa-user"></i> User: <?php echo $_SESSION['customer']['name']; ?></li>



                        <li >
  <a href="#">
    <i class="fa fa-bell" aria-hidden="true"></i> Notifications
    <?php
    // Fetch count of cancelled orders
    $queryCount = "SELECT COUNT(*) AS total_cancelled FROM Orders WHERE buyer_id = ? AND order_status=? AND cancel_by=?";
    $stmtCount = $pdo->prepare($queryCount);
    $stmtCount->execute([$_SESSION['user_id'], "Cancelled Order", "seller"]);
    $countResult = $stmtCount->fetch(PDO::FETCH_ASSOC);

    // Display the count as a badge if it's greater than 0
    $totalCancelled = $countResult['total_cancelled'] ?? 0;
    if ($totalCancelled > 0) {
        echo "<span style='background: red; color: white; border-radius: 50%; padding: 3px 7px; margin-left: 5px;'>$totalCancelled</span>";
    }
    ?>
  </a>
</li>

<li><a href="logout.php"><i class="fa fa-sign-out" aria-hidden="true"></i> Logout</a></li>
						<!-- <li><a href="dashboard.php"><i class="fa fa-home"></i> <?php echo LANG_VALUE_89; ?></a></li> -->
						<?php
					} else {
						?>
						<li><a href="login.php"><i class="fa fa-sign-in"></i> <?php echo LANG_VALUE_9; ?></a></li>
						<li><a href="registration.php?id=buyer"><i class="fa fa-user-plus"></i> <?php echo LANG_VALUE_15; ?></a></li>

						<?php
					}
					?>

<li>
    <a href="cart.php">
        <i class="fa fa-shopping-cart"></i>
        <?php echo LANG_VALUE_18; ?> (<b><?php
        if (isset($_SESSION['cart_p_id']) && is_array($_SESSION['cart_p_id'])) {
            // Count the total number of unique product IDs
            $total_products = count($_SESSION['cart_p_id']);
            echo $total_products; // Display the total product count
            echo ' ';
        } else {
            echo '0';
        }

        if (isset($_SESSION['cart_p_qty']) && is_array($_SESSION['cart_p_qty'])) {
            $table_total_price = 0;
            $i = 0;

            // Loop through quantities and prices to calculate the total price
            foreach ($_SESSION['cart_p_qty'] as $key => $value) {
                $i++;
                $arr_cart_p_qty[$i] = $value;
            }

            $i = 0;
            foreach ($_SESSION['cart_p_current_price'] as $key => $value) {
                $i++;
                $arr_cart_p_current_price[$i] = $value;
            }

            for ($i = 1; $i <= count($arr_cart_p_qty); $i++) {
                if (isset($arr_cart_p_current_price[$i])) {
                    $row_total_price = $arr_cart_p_qty[$i] * $arr_cart_p_current_price[$i];
                    $table_total_price += $row_total_price;
                }
            }

           // echo number_format($table_total_price, 2); // Display total price formatted
        } else {
          //  echo '0.00'; // If no items, total price is 0
        }
        ?></b>)
    </a>
</li>

				</ul>
			</div>
			<div class="col-md-3 search-area">
				<form class="navbar-form navbar-left" role="search" action="search-result.php" method="get">
					<?php $csrf->echoInputField(); ?>

				</form>
			</div>
		</div>
	</div>
</div>



