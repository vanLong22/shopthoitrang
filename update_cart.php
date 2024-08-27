<?php
$connect = mysqli_connect("localhost", "root", "", "shopthoitrang");

if (!$connect) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = $_POST['order_id'];
    $quantity = $_POST['quantity'];
    $color = $_POST['color'];
    $size = $_POST['size'];

    if (isset($order_id) && isset($quantity) && isset($color) && isset($size)) {
        $quantity = intval($quantity);
        $color = mysqli_real_escape_string($connect, $color);
        $size = mysqli_real_escape_string($connect, $size);

        if ($quantity > 0) {
            $updateQuery = "UPDATE cart 
                            SET quantity = $quantity, color = '$color', size = '$size' 
                            WHERE order_id = '$order_id'";
            if (mysqli_query($connect, $updateQuery)) {
                echo "Cập nhật thành công";
            } else {
                echo "Lỗi: " . mysqli_error($connect);
            }
        }
    } else {
        echo "Dữ liệu không đầy đủ";
    }
}

mysqli_close($connect);
?>
