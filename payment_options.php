<div class="box"><!-- box Starts --> 

<?php
$session_email = $_SESSION['customer_email'];
$select_customer = "SELECT * FROM customers WHERE customer_email=?";
$stmt = mysqli_prepare($con, $select_customer);
mysqli_stmt_bind_param($stmt, 's', $session_email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row_customer = mysqli_fetch_array($result);
$customer_id = $row_customer['customer_id'];
?>

<h1 class="text-center">Payment Options For You</h1>

<p class="lead text-center">
  <a href="order.php?c_id=<?php echo $customer_id; ?>">Pay Offline</a>
</p>

<center>
  <form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post" target="_top">

    <!-- PayPal Required Fields -->
    <input type="hidden" name="cmd" value="_cart">
    <input type="hidden" name="upload" value="1">
    <input type="hidden" name="business" value="sb-5ekge30669664@business.example.com">
    <input type="hidden" name="currency_code" value="USD">

    <!-- Return URLs -->
    <input type="hidden" name="return" value="http://yourdomain.com/paypal_order.php?c_id=<?php echo $customer_id; ?>">
    <input type="hidden" name="cancel_return" value="http://localhost/ecommerce/checkout.php">

    <?php
    $i = 0;
    $ip_add = getRealUserIp();
    $get_cart = "SELECT * FROM cart WHERE ip_add=?";
    $stmt_cart = mysqli_prepare($con, $get_cart);
    mysqli_stmt_bind_param($stmt_cart, 's', $ip_add);
    mysqli_stmt_execute($stmt_cart);
    $run_cart = mysqli_stmt_get_result($stmt_cart);

    while ($row_cart = mysqli_fetch_array($run_cart)) {
        $pro_id = $row_cart['p_id'];
        $pro_qty = $row_cart['qty'];
        $pro_price = $row_cart['p_price'];

        $get_products = "SELECT * FROM products WHERE product_id=?";
        $stmt_pro = mysqli_prepare($con, $get_products);
        mysqli_stmt_bind_param($stmt_pro, 'i', $pro_id);
        mysqli_stmt_execute($stmt_pro);
        $result_pro = mysqli_stmt_get_result($stmt_pro);
        $row_products = mysqli_fetch_array($result_pro);

        $product_title = $row_products['product_title'];
        $i++;
    ?>
      <input type="hidden" name="item_name_<?php echo $i; ?>" value="<?php echo htmlspecialchars($product_title); ?>">
      <input type="hidden" name="item_number_<?php echo $i; ?>" value="<?php echo $i; ?>">
      <input type="hidden" name="amount_<?php echo $i; ?>" value="<?php echo $pro_price; ?>">
      <input type="hidden" name="quantity_<?php echo $i; ?>" value="<?php echo $pro_qty; ?>">
    <?php } ?>

    <input type="image" name="submit" width="250" height="100" src="images/paypal.png" alt="Pay with PayPal">
  </form>
</center>

</div><!-- box Ends -->
