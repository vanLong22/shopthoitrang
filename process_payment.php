<?php
// Kết nối đến cơ sở dữ liệu
$connect = mysqli_connect("localhost", "root", "", "shopthoitrang");

// Nhận dữ liệu từ AJAX
$data = json_decode(file_get_contents("php://input"), true);

$user_id = isset($data['user_id']) ? intval($data['user_id']) : 0;
$name_user = isset($data['name']) ? $data['name'] : '';
$address = isset($data['address']) ? $data['address'] : '';
$phone_number = isset($data['phone_number']) ? $data['phone_number'] : '';
$color = isset($data['color']) ? $data['color'] : '';
$size = isset($data['size']) ? $data['size'] : '';
$quantity_order = isset($data['quantity_order']) ? intval($data['quantity_order']) : 0;
$product_id = isset($data['product_id']) ? intval($data['product_id']) : 0;
$price = isset($data['price']) ? floatval($data['price']) : 0;
$name_product = isset($data['name_product']) ? $data['name_product'] : ''; // New column

date_default_timezone_set('Asia/Ho_Chi_Minh'); // Đặt múi giờ chính xác
$create_at = date('Y-m-d H:i:s');
$status = 0; // 0: Chưa xử lý, 1: Đã xử lý
$total_money = $price * $quantity_order;
$status_recieve = 0; // 0: Chưa nhận, 1: Đã nhận
$cancel_order = 0; // 0: Không hủy, 1: Đã hủy
$delete_order = 0; // 0: Không xóa, 1: Đã xóa

// Bắt đầu giao dịch
mysqli_autocommit($connect, FALSE);
$response = ['success' => false];

try {
    // Thêm thông tin vào bảng 'bill'
    $query = "INSERT INTO bill (user_id, create_at, name, address, phone_number, status, total_money, status_recieve, cancel_order, delete_order, color, size, quantity_order, name_user, product_id) 
              VALUES ($user_id, '$create_at', '$name_user', '$address', '$phone_number', $status, $total_money, $status_recieve, $cancel_order, $delete_order, '$color', '$size', $quantity_order, '$name_product', $product_id)";
    if (mysqli_query($connect, $query)) {
        $bill_id = mysqli_insert_id($connect); // Lấy id của hóa đơn vừa tạo

        // Cập nhật số lượng sản phẩm
        $updateProductQuery = "UPDATE product SET quantity = quantity - $quantity_order WHERE id = $product_id";
        if (mysqli_query($connect, $updateProductQuery)) {
            // Xóa sản phẩm khỏi giỏ hàng
            $deleteCartQuery = "DELETE FROM cart WHERE product_id = $product_id";
            if (mysqli_query($connect, $deleteCartQuery)) {
                // Cam kết giao dịch
                mysqli_commit($connect);
                $response = ['success' => true];
            } else {
                throw new Exception('Không thể xóa sản phẩm khỏi giỏ hàng.');
            }
        } else {
            throw new Exception('Không thể cập nhật số lượng sản phẩm.');
        }
    } else {
        throw new Exception('Không thể tạo hóa đơn.');
    }
} catch (Exception $e) {
    // Hoàn tác giao dịch nếu có lỗi
    mysqli_rollback($connect);
    $response = ['success' => false, 'message' => $e->getMessage()];
}

// Đóng kết nối
mysqli_close($connect);

// Trả về kết quả dưới dạng JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
