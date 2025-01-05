<?php require_once('header.php'); ?>

<?php
// Check if the customer is logged in or not
if(!isset($_SESSION['customer'])) {
    header('location: '.BASE_URL.'logout.php');
    exit;
} else {
    // If customer is logged in, but admin make him inactive, then force logout this user.

    $statement = $pdo->prepare("SELECT * FROM users WHERE id=?");
    $statement->execute(array($_SESSION['customer']['user_id']));
    $total = $statement->rowCount();
    if($total) {
        header('location: '.BASE_URL.'logout.php');
        exit;
    }
}
?>

<div class="page">
    <div class="container">
        <div class="row">

            <div class="col-md-12">
                <div class="user-content">
                    <h3>
                        <?php echo LANG_VALUE_117; ?>
                    </h3>
                    <?php
                    if($error_message != '') {
                        echo "<div class='error' style='padding: 10px;background:#f1f1f1;margin-bottom:20px;'>".$error_message."</div>";
                    }
                    if($success_message != '') {
                        echo "<div class='success' style='padding: 10px;background:#f1f1f1;margin-bottom:20px;'>".$success_message."</div>";
                    }
                    ?>
                    <form action="" method="post">
                        <?php $csrf->echoInputField();

                        var_dump ($_SESSION);
                        ?>
                        <div class="row">


                            <div class="col-md-6 form-group">
                                <label for="">First Name *</label>
                                <input type="text" class="form-control" name="cust_name" value="<?php echo $_SESSION['customer']['name']; ?>">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="">Last Name</label>
                                <input type="text" class="form-control" name="cust_cname" value="<?php echo $_SESSION['customer']['lastname']; ?>">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for=""><?php echo LANG_VALUE_94; ?> *</label>
                                <input type="text" class="form-control" name="" value="<?php echo $_SESSION['customer']['email']; ?>" disabled>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="">Phone Number *</label>
                                <input type="text" class="form-control" name="cust_phone" value="<?php echo $_SESSION['customer']['contact_number']; ?>">
                            </div>
                            <div class="col-md-12 form-group">
                                <label for=""><?php echo LANG_VALUE_105; ?> *</label>
                                <textarea name="cust_address" class="form-control" cols="30" rows="10" style="height:70px;"><?php echo $_SESSION['customer']['address']; ?></textarea>
                            </div>


                            <div class="col-md-6 form-group">
                                <label for="">Password *</label>
                                <input type="password" class="form-control" name="cust_city" value="">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="">Profile Picture *</label>
                                <input type="file" class="form-control" name="cust_state" value="">
                            </div>

                        </div>
                        <input type="submit" class="btn btn-primary" value="<?php echo LANG_VALUE_5; ?>" name="form1">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<?php require_once('footer.php'); ?>
