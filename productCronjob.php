<?php
$servername = "localhost";
$username = "prime";
$password = "Awosys123+-";
$dbname = "dtx_oms";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}


$sql = "SELECT * FROM tbl_Products WHERE active=1 AND product_monthly_qty > 0 AND product_daily_limit > product_daily_qty;";
$result1 = mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($result1))
{
  $id         = $row['id'];
  $dailyLimit = $row['product_daily_limit'];
  $dailyQty   = $row['product_daily_qty'];
  $monthlyQty = $row['product_monthly_qty'];

  if($monthlyQty < $dailyLimit){
    $toAddQty = $monthlyQty - $dailyQty;
  }else{
    $toAddQty = $dailyLimit - $dailyQty;
  }

  $sqlUpdaProduct = "UPDATE tbl_Products set product_daily_qty = product_daily_qty+$toAddQty,product_monthly_qty = product_monthly_qty-$toAddQty WHERE id=$id LIMIT 1;";
  mysqli_query($conn,$sqlUpdaProduct);


}

mysqli_close($conn);

?>
