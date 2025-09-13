<?php
include("includes/db.php");
include("functions/functions.php");

if (!isset($_GET['c_id'])) {
  echo "<script>window.open('index.php','_self')</script>";
  exit();
}

$customer_id = intval($_GET['c_id']);
$ip_add = getRealUserIp();
$status = "complete";
$invoice_no = mt_rand();

$select_cart = "SELECT * FROM cart WHERE ip_add=?";
$stmt_cart = mysqli_prepare($con, $select_cart);
mysqli_stmt_bind_param($stmt_cart, 's', $ip_add);
mysqli_stmt_execute($stmt_cart);
$run_cart = mysqli_stmt_get_result($stmt_cart);

while ($row_cart = mysqli_fetch_array($run_cart)) {
  $pro_id = $row_cart['p_id'];
  $pro_size = $row_cart['size'];
  $pro_qty = $row_cart['qty'];
  $sub_total = $row_cart['p_price'] * $pro_qty;

  $insert_customer_order = "INSERT INTO customer_orders (customer_id, due_amount, invoice_no, qty, size, order_date, order_status) VALUES (?, ?, ?, ?, ?, NOW(), ?)";
  $stmt_order = mysqli_prepare($con, $insert_customer_order);
  mysqli_stmt_bind_param($stmt_order, 'idisss', $customer_id, $sub_total, $invoice_no, $pro_qty, $pro_size, $status);
  mysqli_stmt_execute($stmt_order);

  $insert_pending_order = "INSERT INTO pending_orders (customer_id, invoice_no, product_id, qty, size, order_status) VALUES (?, ?, ?, ?, ?, ?)";
  $stmt_pending = mysqli_prepare($con, $insert_pending_order);
  mysqli_stmt_bind_param($stmt_pending, 'iiisss', $customer_id, $invoice_no, $pro_id, $pro_qty, $pro_size, $status);
  mysqli_stmt_execute($stmt_pending);
}

// Clear cart
$delete_cart = "DELETE FROM cart WHERE ip_add=?";
$stmt_del = mysqli_prepare($con, $delete_cart);
mysqli_stmt_bind_param($stmt_del, 's', $ip_add);
mysqli_stmt_execute($stmt_del);

echo "<script>alert('Your order has been submitted. Thank you!')</script>";
echo "<script>window.open('customer/my_account.php?my_orders','_self')</script>";
?>
