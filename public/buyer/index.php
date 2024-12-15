<?php
require_once('header.php');
require_once('api-config.php');

?>




<div id="bootstrap-touch-slider" class="carousel bs-slider fade control-round indicators-line" data-ride="carousel" data-pause="hover" data-interval="false" >

    <!-- Indicators -->


    <!-- Wrapper For Slides -->
    <div class="carousel-inner"  role="listbox">

        <?php
        $i=0;
        $statement = $pdo->prepare("SELECT * FROM tbl_slider");
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $row) {
            ?>


            <div class="item <?php
            if($i==0) {echo 'active';} ?>" style="background-image:url(assets/uploads/agribackground.jpg">


                <div class="container">
                    <div class="row">

                    <div class="slide-text
                        <?php

                            if($row['position'] == 'Center') {echo 'slide_style_center';}
                            ?>">

                            <h1 style="font-size : clamp(2rem, 8vw, 5rem);" data-animation="animated <?php

                            if($row['position'] == 'Center') {echo 'fadeInDown';}
                            ?>">Welcome to AgriShop</h1>
                            <p data-animation="animated <?php

                            if($row['position'] == 'Center') {echo 'fadeInDown';}
                            ?>">One-Stop Shop for Quality Agricultural Products</p>


                        </div>
                    </div>

                </div>

            </div>




            <?php
            $i++;
        }
        ?>

    </div>

</div>

<div class="product pt_70 pb_70">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="headline" >
                    <h2>List of Products</h2>
                    <div class="btn-cart btn-cart1"><a href="../buyer/product-category.php" class="btn btn-primary">Browse Products <i class="fa fa-search"></i></a></div>

                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="product-carousel">
                    <?php

                    // Fetch product data from the API
                    $apiUrl = API_BASE_URL . '/products/all';
                    $json = @file_get_contents($apiUrl); // Suppress warnings with @

                    $products = json_decode($json, true)['data']; // Decode the JSON response to PHP array

                    // Check if products are fetched

                    if ($products) {
                        $i = 0;
                        foreach ($products as $product) {
                            ?>
                            <div class="item">
                                <div class="thumb">
                                <div class="photo" style="background-image:url(<?php echo API_BASE_URL . '/storage/' . $product['photos'][0]; ?>);"></div>



                                </div>
                                <div class="text">
                                    <h3><a href="product.php?id=<?php echo $product['id']; ?>&fname=<?php echo $product['first_name']; ?>&lname=<?php echo $product['last_name']; ?>"></a><?php echo $product['Product_Name']; ?></a></h3>

                                    <h4>
                                    ₱<?php echo $product['price']; ?>
                                    </h4>
                                    <div class="rating">
                                        <?php
                                        // Assuming no rating system from API. If rating system exists, you can implement it here.
                                        // Example: Displaying full stars for simplicity as there's no rating data in the API response.
                                        for ($i = 1; $i <= 5; $i++) {
                                            echo '<i class="fa fa-star"></i>';
                                        }
                                        ?>
                                    </div>
                                    <h3><a href="product.php?id=<?php echo $product['id']; ?>&fname=<?php echo $product['first_name']; ?>&lname=<?php echo $product['last_name']; ?>"><?php echo $product['first_name']; ?> <?php echo $product['last_name']; ?></a></h3>
                                        <?php if($product['Quantity'] == 0): ?>
                                            <div class="out-of-stock">
                                                <div class="inner">
                                                    Out Of Stock
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <p><a href="product.php?id=<?php echo $product['id']; ?>&fname=<?php echo $product['first_name']; ?>&lname=<?php echo $product['last_name']; ?>"><i class="fa fa-shopping-cart"></i> Add to Cart</a></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php
                                $i++;
                            }

                        } else {
                            echo '<p>No popular products found.</p>';
                        }

                    ?>
                </div>
            </div>
        </div>
    </div>
</div>


<?php require_once('footer.php'); ?>
