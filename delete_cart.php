<?php
/*
$connect = mysqli_connect("localhost", "root", "", "shopthoitrang");

if (!$connect) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['order_id'])) {
        $order_id = intval($_POST['order_id']);
        
        $deleteQuery = "DELETE FROM cart WHERE order_id = $order_id";
        
        if (mysqli_query($connect, $deleteQuery)) {
            echo "Sản phẩm đã được xóa.";
        } else {
            echo "Lỗi: " . mysqli_error($connect);
        }
    } else {
        echo "ID đơn hàng không được xác định.";
    }
}

mysqli_close($connect);
*/

?>
<?php
$connect = mysqli_connect("localhost", "root", "", "shopthoitrang");

if (!$connect) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['order_id'])) {
        $order_id = intval($_POST['order_id']);
        
        $deleteQuery = "DELETE FROM cart WHERE order_id = $order_id";
        
        if (mysqli_query($connect, $deleteQuery)) {
            if (mysqli_affected_rows($connect) > 0) {
                $response['status'] = 'success';
                $response['message'] = 'Sản phẩm đã được xóa.';
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Không tìm thấy sản phẩm với ID này.';
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Lỗi: ' . mysqli_error($connect);
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'ID đơn hàng không được xác định.';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Phương thức yêu cầu không hợp lệ.';
}

mysqli_close($connect);

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
