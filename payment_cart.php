<?php
/*
$connect = mysqli_connect("localhost", "root", "", "shopthoitrang");

if (!$connect) {
    die(json_encode(array('status' => 'error', 'message' => "Kết nối thất bại: " . mysqli_connect_error())));
}

// Nhận dữ liệu từ yêu cầu AJAX
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['cartItems']) || !isset($data['user_id']) || !isset($data['name']) || !isset($data['address']) || !isset($data['phone_number']) || !isset($data['total_money'])) {
    echo json_encode(array('status' => 'error', 'message' => 'Dữ liệu không đầy đủ.'));
    exit();
}

$cartItems = $data['cartItems'];
$user_id = intval($data['user_id']);
$name = mysqli_real_escape_string($connect, $data['name']);
$address = mysqli_real_escape_string($connect, $data['address']);
$phone_number = mysqli_real_escape_string($connect, $data['phone_number']);
$total_money = floatval($data['total_money']);
$status = 'pending'; // Hoặc giá trị trạng thái phù hợp
$status_recieve = 'pending'; // Hoặc giá trị trạng thái nhận hàng
$cancel_order = 'no'; // Hoặc giá trị trạng thái hủy đơn hàng
$delete_order = 'no'; // Hoặc giá trị trạng thái xóa đơn hàng
date_default_timezone_set('Asia/Ho_Chi_Minh'); // Đặt múi giờ chính xác
$create_at = date('Y-m-d H:i:s');

foreach ($cartItems as $order_id) {
    $order_id = intval($order_id);
    
    // Truy vấn thông tin sản phẩm từ bảng cart
    $query = "SELECT * FROM cart WHERE order_id = $order_id";
    $result = mysqli_query($connect, $query);
    $cartItem = mysqli_fetch_assoc($result);

    if ($cartItem) {
        $color = mysqli_real_escape_string($connect, $cartItem['color']);
        $size = mysqli_real_escape_string($connect, $cartItem['size']);
        $quantity_order = intval($cartItem['quantity']);
        $name_product = mysqli_real_escape_string($connect, $cartItem['name_product']);
        $product_id = intval($cartItem['product_id']);

        $insertQuery = "INSERT INTO bill (user_id, create_at, name, address, phone_number, status, total_money, status_recieve, cancel_order, delete_order, color, size, quantity_order, name_user, product_id) 
                        VALUES ($user_id, '$create_at', '$name_product', '$address', '$phone_number', '$status', $total_money, '$status_recieve', '$cancel_order', '$delete_order', '$color', '$size', $quantity_order, '$name', $product_id)";
        
        $updateProduct = "UPDATE product SET quantity = quantity - $quantity_order WHERE id = $product_id";
        mysqli_query($connect, $updateProduct);
        
        if (!mysqli_query($connect, $insertQuery)) {
            echo json_encode(array('status' => 'error', 'message' => 'Lỗi khi thêm dữ liệu vào bảng bill: ' . mysqli_error($connect)));
            mysqli_close($connect);
            exit();
        }

        // Xóa sản phẩm đã thanh toán khỏi bảng cart
		/*
        $deleteQuery = "DELETE FROM cart WHERE order_id = $order_id";
        if (!mysqli_query($connect, $deleteQuery)) {
            echo json_encode(array('status' => 'error', 'message' => 'Lỗi khi xóa sản phẩm khỏi giỏ hàng: ' . mysqli_error($connect)));
            mysqli_close($connect);
            exit();
        }
		*/
		/*
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Sản phẩm không tìm thấy trong giỏ hàng.'));
        mysqli_close($connect);
        exit();
    }
}

echo json_encode(array('status' => 'success', 'message' => 'Thanh toán thành công.'));
mysqli_close($connect);
*/
?>

<?php
$connect = mysqli_connect("localhost", "root", "", "shopthoitrang");

if (!$connect) {
    die(json_encode(array('status' => 'error', 'message' => "Kết nối thất bại: " . mysqli_connect_error())));
}

// Nhận dữ liệu từ yêu cầu AJAX
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['cartItems']) || !isset($data['user_id']) || !isset($data['name']) || !isset($data['address']) || !isset($data['phone_number']) || !isset($data['total_money'])) {
    echo json_encode(array('status' => 'error', 'message' => 'Dữ liệu không đầy đủ.'));
    exit();
}

$cartItems = $data['cartItems'];
$user_id = intval($data['user_id']);
$name = mysqli_real_escape_string($connect, $data['name']);
$address = mysqli_real_escape_string($connect, $data['address']);
$phone_number = mysqli_real_escape_string($connect, $data['phone_number']);
$total_money = floatval($data['total_money']);
$status = 'pending'; // Hoặc giá trị trạng thái phù hợp
$status_recieve = 'pending'; // Hoặc giá trị trạng thái nhận hàng
$cancel_order = 'no'; // Hoặc giá trị trạng thái hủy đơn hàng
$delete_order = 'no'; // Hoặc giá trị trạng thái xóa đơn hàng
date_default_timezone_set('Asia/Ho_Chi_Minh'); // Đặt múi giờ chính xác
$create_at = date('Y-m-d H:i:s');

// Tạo user_id mới
$maxOrderIdQuery = "SELECT MAX(CAST(user_id AS UNSIGNED)) AS max_order_id FROM bill";
$result = mysqli_query($connect, $maxOrderIdQuery);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $maxOrderId = $row['max_order_id'];
    $user_id = $maxOrderId ? $maxOrderId + 1 : 1;
} else {
    $errorMessage = mysqli_error($connect);
    error_log("Lỗi truy vấn max order_id: $errorMessage"); // Ghi log lỗi
    echo json_encode(['status' => 'error', 'message' => 'Lỗi truy vấn max order_id: ' . $errorMessage]);
    exit;
}

foreach ($cartItems as $item) {
    $order_id = intval($item['order_id']);
    $color = mysqli_real_escape_string($connect, $item['color']);
    $size = mysqli_real_escape_string($connect, $item['size']);
    $quantity_order = intval($item['quantity']);
    $name_product = mysqli_real_escape_string($connect, $item['name_product']);
    $product_id = intval($item['product_id']);

    $insertQuery = "INSERT INTO bill (user_id, create_at, name, address, phone_number, status, total_money, status_recieve, cancel_order, delete_order, color, size, quantity_order, name_user, product_id) 
                    VALUES ($user_id, '$create_at', '$name_product', '$address', '$phone_number', '$status', $total_money, '$status_recieve', '$cancel_order', '$delete_order', '$color', '$size', $quantity_order, '$name', $product_id)";
    
    $updateProduct = "UPDATE product SET quantity = quantity - $quantity_order WHERE id = $product_id";
    mysqli_query($connect, $updateProduct);
    
    if (!mysqli_query($connect, $insertQuery)) {
        echo json_encode(array('status' => 'error', 'message' => 'Lỗi khi thêm dữ liệu vào bảng bill: ' . mysqli_error($connect)));
        mysqli_close($connect);
        exit();
    }
	/*
    // Xóa sản phẩm đã thanh toán khỏi bảng cart
    $deleteQuery = "DELETE FROM cart WHERE order_id = $order_id";
    if (!mysqli_query($connect, $deleteQuery)) {
        echo json_encode(array('status' => 'error', 'message' => 'Lỗi khi xóa sản phẩm khỏi giỏ hàng: ' . mysqli_error($connect)));
        mysqli_close($connect);
        exit();
    }
	*/
}

echo json_encode(array('status' => 'success', 'message' => 'Thanh toán thành công.'));
mysqli_close($connect);
?>

