<?php
$connect = mysqli_connect("localhost", "root", "", "shopthoitrang");

if (!$connect) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Nhận dữ liệu từ yêu cầu AJAX
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['product_id'], $data['name_product'], $data['quantity_order'], $data['unit_price'], $data['color'], $data['size'])) {
    echo json_encode(['status' => 'error', 'message' => 'Thiếu thông tin cần thiết']);
    exit;
}

$productId = $data['product_id'];
$nameProduct = $data['name_product'];
$quantityOrder = $data['quantity_order'];
$unitPrice = $data['unit_price'];
$color = $data['color'];
$size = $data['size'];

// Đảm bảo kết nối đến cơ sở dữ liệu đã được thiết lập trước
if (!$connect) {
    error_log("Kết nối cơ sở dữ liệu thất bại");
    echo json_encode(['status' => 'error', 'message' => 'Kết nối cơ sở dữ liệu thất bại']);
    exit;
}

// Tạo order_id mới
$maxOrderIdQuery = "SELECT MAX(CAST(order_id AS UNSIGNED)) AS max_order_id FROM cart";
//$maxOrderIdQuery = "SELECT MAX(order_id) AS max_order_id FROM cart";  nếu là kiểu int
$result = mysqli_query($connect, $maxOrderIdQuery);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $maxOrderId = $row['max_order_id'];
    $orderId = $maxOrderId ? $maxOrderId + 1 : 1;
} else {
    $errorMessage = mysqli_error($connect);
    error_log("Lỗi truy vấn max order_id: $errorMessage"); // Ghi log lỗi
    echo json_encode(['status' => 'error', 'message' => 'Lỗi truy vấn max order_id: ' . $errorMessage]);
    exit;
}
/*
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $maxOrderId = $row['max_order_id'];
    $orderId = $maxOrderId ? $maxOrderId + 1 : 1;
    echo json_encode(['status' => 'success', 'order_id' => $orderId]);
} else {
    $errorMessage = mysqli_error($connect);
    error_log("Lỗi truy vấn max order_id: $errorMessage"); // Ghi log lỗi
    echo json_encode(['status' => 'error', 'message' => 'Lỗi truy vấn max order_id: ' . $errorMessage]);
}
*/


// Sử dụng prepared statement để tránh SQL Injection
$insertCartQuery = "INSERT INTO cart (order_id, name_product, product_id, quantity, unit_price, color, size)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($connect, $insertCartQuery);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "ssiidss", $orderId, $nameProduct, $productId, $quantityOrder, $unitPrice, $color, $size);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['status' => 'success']);
    } else {
        $errorMessage = mysqli_stmt_error($stmt);
        error_log("Lỗi thêm vào giỏ hàng: $errorMessage"); // Ghi log lỗi
        echo json_encode(['status' => 'error', 'message' => 'Lỗi thêm vào giỏ hàng: ' . $errorMessage]);
    }

    mysqli_stmt_close($stmt);
} else {
    $errorMessage = mysqli_error($connect);
    error_log("Lỗi chuẩn bị statement: $errorMessage"); // Ghi log lỗi
    echo json_encode(['status' => 'error', 'message' => 'Lỗi chuẩn bị statement: ' . $errorMessage]);
}

mysqli_close($connect);
?>
