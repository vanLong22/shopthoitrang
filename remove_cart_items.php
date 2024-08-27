<?php
$connect = mysqli_connect("localhost", "root", "", "shopthoitrang");

if (!$connect) {
    die(json_encode(array('status' => 'error', 'message' => "Kết nối thất bại: " . mysqli_connect_error())));
}

// Nhận dữ liệu từ yêu cầu AJAX
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['order_ids']) || !is_array($data['order_ids'])) {
    echo json_encode(array('status' => 'error', 'message' => 'Không có đơn hàng nào được chọn.'));
    mysqli_close($connect);
    exit();
}

$order_ids = $data['order_ids'];

// Sử dụng prepared statements để xóa các đơn hàng
$stmt = $connect->prepare("DELETE FROM cart WHERE order_id = ?");
if (!$stmt) {
    echo json_encode(array('status' => 'error', 'message' => 'Lỗi khi chuẩn bị câu lệnh xóa.'));
    mysqli_close($connect);
    exit();
}

foreach ($order_ids as $order_id) {
    $stmt->bind_param("i", $order_id); // 'i' cho integer
    if (!$stmt->execute()) {
        echo json_encode(array('status' => 'error', 'message' => 'Lỗi khi xóa đơn hàng với ID: ' . $order_id));
        $stmt->close();
        mysqli_close($connect);
        exit();
    }
}

$stmt->close();
echo json_encode(array('status' => 'success', 'message' => 'Các sản phẩm đã được xóa khỏi giỏ hàng.'));
mysqli_close($connect);
?>
