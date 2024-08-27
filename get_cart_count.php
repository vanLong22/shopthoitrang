<?php
session_start();
$connect = mysqli_connect("localhost", "root", "", "shopthoitrang");

// đếm số lượng sản phẩm trong giỏ hàng
$cartQuery = "SELECT COUNT(*) AS totalCart FROM cart";
$resultCart = mysqli_query($connect, $cartQuery);
$rowCart = mysqli_fetch_assoc($resultCart);

echo $rowCart['totalCart'];
//echo json_encode(['cart_count' => $data['cart_count']]);

// cần tính theo số lượng sp của từng người dùng
/*
<?php
session_start();
$connect = mysqli_connect("localhost", "root", "", "shopthoitrang");

// Kiểm tra kết nối
if (!$connect) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Giả sử bạn đã lưu user_id trong session sau khi người dùng đăng nhập
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// Nếu không có user_id trong session, trả về 0
if ($user_id == 0) {
    echo '0';
    exit;
}

// Đếm số lượng sản phẩm trong giỏ hàng của người dùng hiện tại
$cartQuery = "SELECT SUM(quantity) AS totalCart FROM cart WHERE user_id = ?";
$stmt = mysqli_prepare($connect, $cartQuery);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $totalCart);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

echo $totalCart ? $totalCart : '0'; // Nếu không có sản phẩm, trả về 0
*/
mysqli_close($connect);
?>


