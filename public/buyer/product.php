<?php require_once('header.php'); ?>

<?php
if(!isset($_REQUEST['id'])) {
    header('location: index.php');
    exit;
} else {

    $product_id = $_REQUEST['id'];

    $api_url = "http://192.168.1.9:8080/products/product/" . $product_id;

    // Use cURL to fetch data from API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    // var_dump($response);

    if (!$response) {
        echo "Error fetching data from API.";
        exit;
    }
     // Decode the JSON response
     $result = json_decode($response, true);

     // Check if decoding was successful and "data" key exists
     if (!$result || !isset($result['data'])) {
         echo "Invalid product data.";
         exit;
     }

     // Extract product details from the "data" key
     $product = $result['data'];

     $p_name = $product['Product_Name'] ?? 'N/A';
     $p_current_price = $product['price'] ?? 'N/A';
     $p_qty = $product['Quantity'] ?? 'N/A';
     $p_measurement = $product['measurement'] ?? 'N/A';
     $p_featured_photo = $product['photos'][0] ?? 'N/A';
     $photo2 = $product['photos1'][0] ?? 'N/A';
     $photo3 = $product['photos2'][0] ?? 'N/A';
     $p_description = $product['Description'] ?? 'N/A';
     $s_name = $product['first_name'] ?? 'N/A';
     $s_last = $product['last_name'] ?? 'N/A';
     $s_address = $product['address'] ?? 'N/A';
     $s_id = $product['userID'] ?? 'N/A';

 }

if(isset($_POST['form_add_to_cart'])) {

	// getting the currect stock of this product
	$statement = $pdo->prepare("SELECT * FROM products WHERE id=?");
	$statement->execute(array($_REQUEST['id']));
	$result = $statement->fetchAll(PDO::FETCH_ASSOC);
	foreach ($result as $row) {
		$current_p_qty = $row['Quantity'];
	}
	if($_POST['p_Quantityqty'] > $current_p_qty):
		$temp_msg = 'Sorry! There are only '.$current_p_qty.' item(s) in stock';
		?>
		<script type="text/javascript">alert('<?php echo $temp_msg; ?>');</script>
		<?php
	else:
    if(isset($_SESSION['cart_p_id']))
    {

        $arr_cart_p_id = array();
        $arr_cart_size_id = array();
        $arr_cart_color_id = array();
        $arr_cart_p_qty = array();
        $arr_cart_p_current_price = array();

        $i=0;
        foreach($_SESSION['cart_p_id'] as $key => $value)
        {
            $i++;
            $arr_cart_p_id[$i] = $value;
        }

        $i=0;
        foreach($_SESSION['cart_size_id'] as $key => $value)
        {
            $i++;
            $arr_cart_size_id[$i] = $value;
        }

        $i=0;
        foreach($_SESSION['cart_color_id'] as $key => $value)
        {
            $i++;
            $arr_cart_color_id[$i] = $value;
        }


        $added = 0;
        if(!isset($_POST['size_id'])) {
            $size_id = 0;
        } else {
            $size_id = $_POST['size_id'];
        }
        if(!isset($_POST['color_id'])) {
            $color_id = 0;
        } else {
            $color_id = $_POST['color_id'];
        }
        for($i=1;$i<=count($arr_cart_p_id);$i++) {
            if( ($arr_cart_p_id[$i]==$_REQUEST['id']) && ($arr_cart_size_id[$i]==$size_id) && ($arr_cart_color_id[$i]==$color_id) ) {
                $added = 1;
                break;
            }
        }
        if($added == 1) {
           $error_message1 = 'This product is already added to the shopping cart.';
        } else {

            $i=0;
            foreach($_SESSION['cart_p_id'] as $key => $res)
            {
                $i++;
            }
            $new_key = $i+1;

            if(isset($_POST['size_id'])) {

                $size_id = $_POST['size_id'];

                $statement = $pdo->prepare("SELECT * FROM tbl_size WHERE size_id=?");
                $statement->execute(array($size_id));
                $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                foreach ($result as $row) {
                    $size_name = $row['size_name'];
                }
            } else {
                $size_id = 0;
                $size_name = '';
            }

            if(isset($_POST['color_id'])) {
                $color_id = $_POST['color_id'];
                $statement = $pdo->prepare("SELECT * FROM tbl_color WHERE color_id=?");
                $statement->execute(array($color_id));
                $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                foreach ($result as $row) {
                    $color_name = $row['color_name'];
                }
            } else {
                $color_id = 0;
                $color_name = '';
            }


            $_SESSION['cart_p_id'][$new_key] = $_REQUEST['id'];
            $_SESSION['cart_size_id'][$new_key] = $size_id;
            $_SESSION['cart_size_name'][$new_key] = $size_name;
            $_SESSION['cart_color_id'][$new_key] = $color_id;
            $_SESSION['cart_color_name'][$new_key] = $color_name;
            $_SESSION['cart_p_qty'][$new_key] = $_POST['p_qty'];
            $_SESSION['cart_p_current_price'][$new_key] = $_POST['p_current_price'];
            $_SESSION['cart_p_name'][$new_key] = $_POST['p_name'];
            // $_SESSION['f_name'][$new_key] = $_REQUEST['fname'];
            // $_SESSION['l_name'][$new_key] = $_REQUEST['lname'];

            $_SESSION['cart_p_featured_photo'][$new_key] = $_POST['p_featured_photo'];

            $success_message1 = 'Product is added to the cart successfully!';
        }

    }
    else
    {

        if(isset($_POST['size_id'])) {

            $size_id = $_POST['size_id'];

            $statement = $pdo->prepare("SELECT * FROM tbl_size WHERE size_id=?");
            $statement->execute(array($size_id));
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $row) {
                $size_name = $row['size_name'];
            }
        } else {
            $size_id = 0;
            $size_name = '';
        }

        if(isset($_POST['color_id'])) {
            $color_id = $_POST['color_id'];
            $statement = $pdo->prepare("SELECT * FROM tbl_color WHERE color_id=?");
            $statement->execute(array($color_id));
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $row) {
                $color_name = $row['color_name'];
            }
        } else {
            $color_id = 0;
            $color_name = '';
        }


        $_SESSION['cart_p_id'][1] = $_REQUEST['id'];
        $_SESSION['cart_size_id'][1] = $size_id;
        $_SESSION['cart_size_name'][1] = $size_name;
        $_SESSION['cart_color_id'][1] = $color_id;
        $_SESSION['cart_color_name'][1] = $color_name;
        $_SESSION['cart_p_qty'][1] = $_POST['p_qty'];
        $_SESSION['cart_p_current_price'][1] = $_POST['p_current_price'];
        $_SESSION['cart_p_name'][1] = $_POST['p_name'];
        $_SESSION['cart_p_featured_photo'][1] = $_POST['p_featured_photo'];
        // $_SESSION['f_name'][1] = $_REQUEST['fname'];
        // $_SESSION['l_name'][1] = $_REQUEST['lname'];

        $success_message1 = 'Product is added to the cart successfully!';
    }
	endif;
}
?>

<?php
if($error_message1 != '') {
    echo "<script>alert('".$error_message1."')</script>";
}
if($success_message1 != '') {
    echo "<script>alert('".$success_message1."')</script>";
    header('location: product.php?id='.$_REQUEST['id']);
}
?>


<div class="page">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
                <div class="breadcrumb mb_30">
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li>></li>
                        <li><?php echo $p_name; ?></li>
                    </ul>
                </div>

				<div class="product">
					<div class="row">
                    <div class="col-md-5">
                            <ul class="prod-slider">
                                <!-- Main Image -->
                                <li id="main-image" style="background-image: url(http://192.168.1.9:8080/storage/<?php echo str_replace('\/', '/', trim($p_featured_photo, '[]"')); ?>);">
                                    <a class="popup" href="http://192.168.1.9:8080/storage/<?php echo str_replace('\/', '/', trim($p_featured_photo, '[]"')); ?>"></a>
                                </li>
                            </ul>
                            <div id="prod-pager">
                                <?php
                                $product_id = $_REQUEST['id'];
                                $api_url = "http://192.168.1.9:8080/products/product/" . $product_id;

                                // Fetch product details from API
                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, $api_url);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                $response = curl_exec($ch);
                                curl_close($ch);

                                // Decode the JSON response
                                $product = json_decode($response, true);

                                // Check if the response is valid
                                if (!isset($product['data'])) {
                                    echo "Error fetching product details.";
                                    exit;
                                }

                                // Extract product data
                                $product_data = $product['data'];

                                // Parse images
                                $featured_photo = $product_data['photos'][0] ?? null;
                                $photo2 = $product_data['photos1'][0] ?? null;
                                $photo3 = $product_data['photos2'][0] ?? null;

                                $i = 1;

                                // Display images
                                if ($featured_photo) {
                                    echo '<a href="#" class="prod-thumb" data-image="http://192.168.1.9:8080/storage/' . htmlspecialchars($featured_photo) . '">
                                            <div class="prod-pager-thumb" style="background-image: url(http://192.168.1.9:8080/storage/' . htmlspecialchars($featured_photo) . ');"></div>
                                        </a>';
                                    $i++;
                                }
                                if ($photo2) {
                                    echo '<a href="#" class="prod-thumb" data-image="http://192.168.1.9:8080/storage/' . htmlspecialchars($photo2) . '">
                                            <div class="prod-pager-thumb" style="background-image: url(http://192.168.1.9:8080/storage/' . htmlspecialchars($photo2) . ');"></div>
                                        </a>';
                                    $i++;
                                }
                                if ($photo3) {
                                    echo '<a href="#" class="prod-thumb" data-image="http://192.168.1.9:8080/storage/' . htmlspecialchars($photo3) . '">
                                            <div class="prod-pager-thumb" style="background-image: url(http://192.168.1.9:8080/storage/' . htmlspecialchars($photo3) . ');"></div>
                                        </a>';
                                    $i++;
                                }
                                ?>
                            </div>
                        </div>

						<div class="col-md-7">

							<!-- <div class="p-review">
								<div class="rating">

                                </div>
							</div> -->


                            <form action="" method="post">
                            <div class="p-quantity">
                                <div class="row">
                                    <?php if(isset($size)): ?>
                                    <div class="col-md-12 mb_20">
                                        <?php echo LANG_VALUE_52; ?> <br>
                                        <select name="size_id" class="form-control select2" style="width:auto;">
                                            <?php
                                            $statement = $pdo->prepare("SELECT * FROM tbl_size");
                                            $statement->execute();
                                            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($result as $row) {
                                                if(in_array($row['size_id'],$size)) {
                                                    ?>
                                                    <option value="<?php echo $row['size_id']; ?>"><?php echo $row['size_name']; ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <?php endif; ?>

                                    <?php if(isset($color)): ?>
                                    <div class="col-md-12">
                                        <?php echo LANG_VALUE_53; ?> <br>
                                        <select name="color_id" class="form-control select2" style="width:auto;">
                                            <?php
                                            $statement = $pdo->prepare("SELECT * FROM tbl_color");
                                            $statement->execute();
                                            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($result as $row) {
                                                if(in_array($row['color_id'],$color)) {
                                                    ?>
                                                    <option value="<?php echo $row['color_id']; ?>"><?php echo $row['color_name']; ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <?php endif; ?>

                                </div>

                            </div>
							<div class="p-price">
                            <div class="p-title"><h2><?php echo $p_name; ?></h2></div>
                            <div class="p-short-des" style="font-size:14px;">
                                <p>
                                    <?php echo $p_description; ?>
                                </p>
                            </div>
                                <span style="font-size:14px;"><?php echo LANG_VALUE_54; ?></span><br>
                                <span>
                                        <?php echo LANG_VALUE_1; ?><?php echo $p_current_price; ?>
                                </span>
                            </div>
                            <div class="p-price">
                                <span style="font-size:14px;">Measurement</span><br>
                                <span>
                                        <?php echo $p_measurement; ?>
                                </span>
                            </div>
                            <div class="p-price">
                                <span style="font-size:14px;">Stock</span><br>
                                <span>
                                        <?php echo $p_qty; ?>
                                </span>
                            </div>
                            <input type="hidden" name="p_current_price" value="<?php echo $p_current_price; ?>">
                            <input type="hidden" name="p_name" value="<?php echo $p_name; ?>">
                            <input type="hidden" name="p_featured_photo" value="<?php echo $p_featured_photo; ?>">

							<div class="p-quantity">
                                <?php echo LANG_VALUE_55; ?> <br>
								<input type="number" class="input-text qty" step="1" min="1" max="" name="p_qty" value="1" title="Qty" size="4" pattern="[0-9]*" inputmode="numeric">
							</div>

							<div class="btn-cart btn-cart1">
                                <input type="submit" value="<?php echo LANG_VALUE_154; ?>" name="form_add_to_cart">
							</div>

                            </form>

						</div>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>





    <div class="page">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                <div class="product">
                        <div class="row">
                            <h1><?php echo htmlspecialchars($s_name); ?> <?php echo htmlspecialchars($s_last); ?></h1>
                            <div class="btn-cart btn-cart1">
                                <form action="sellerindex.php" method="GET">
                                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($s_id); ?>">
                                    <input type="submit" value="Visit Shop">
                                </form>
                            </div>
                            <?php echo $s_address; ?>
                        </div>



				<div class="product">


					<div class="row">
                    <h1><?php echo $_GET['fname']; ?> <?php echo $_GET['lname']; ?></h1>
                                    <div class="btn-cart btn-cart1">
                                <input type="submit" value="<?php echo "Visit Shop"; ?>" name="form_add_to_cart"></div><?php echo $s_address; ?>
						</div>

					</div>

				</div>

			</div>
		</div>
	</div>
</div>






<div class="product bg-gray pt_70 pb_70">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="headline">
                    <h2><?php echo LANG_VALUE_155; ?></h2>
                    <h3><?php echo LANG_VALUE_156; ?></h3>
                </div>
            </div>
        </div>



        <div class="row">
            <div class="col-md-12">

                <div class="product-carousel">

                    <?php
                    $current_product_id = $_REQUEST['id'];

                    // API URL to fetch products
                    $api_url = "http://192.168.1.9:8080/products/all";

                    // Use cURL to fetch data from API
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $api_url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($ch);
                    curl_close($ch);

                    // Check if the response is not empty or false
                    if (!$response) {
                        echo "Error fetching products.";
                        exit;
                    }

                    // Decode the JSON response
                    $products = json_decode($response, true);

                    if (!isset($products['data']) || empty($products['data'])) {
                        echo "No products available.";
                        exit;
                    }

                    // Loop through products and exclude the current product
                    foreach ($products['data'] as $row) {
                        if ($row['id'] == $current_product_id) {
                            continue; // Skip the current product
                        }
                        ?>



                        <div class="item">
                            <div class="thumb">

                                <?php
                                // Decode the photos array and display the first photo
                                $photos = $row['photos'];
                                if (!empty($photos) && isset($photos[0])) {
                                    $photoUrl = str_replace('\/', '/', $photos[0]);
                                    echo '<div class="photo" style="background-image:url(http://192.168.1.9:8080/storage/' . $photoUrl . ');"></div>';
                                } else {
                                    echo '<div class="photo" style="background-color: gray;">No photo available</div>';
                                }
                                ?>
                                <div class="overlay"></div>
                            </div>
                            <div class="text">
                                <h3><a href="product.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['Product_Name']); ?></a></h3>
                                <h4>
                                    <?php echo LANG_VALUE_1; ?><?php echo htmlspecialchars($row['price']); ?>
                                </h4>
                                <p><?php echo $row['first_name']?> <?php echo $row['last_name']?></p>
                                <p><a href="product.php?id=<?php echo $row['id']; ?>"><?php echo LANG_VALUE_154; ?></a></p>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Select all thumbnail links
    const thumbnails = document.querySelectorAll('.prod-thumb');
    const mainImage = document.getElementById('main-image');

    thumbnails.forEach(thumb => {
        thumb.addEventListener('click', function (event) {
            event.preventDefault(); // Prevent default anchor behavior
            const imageUrl = this.getAttribute('data-image');
            mainImage.style.backgroundImage = `url(${imageUrl})`;
        });
    });
</script>


<?php require_once('footer.php'); ?>
