<?php
// Kết nối đến cơ sở dữ liệu
$connect = mysqli_connect("localhost", "root", "", "shopthoitrang");

// Kiểm tra kết nối
if (!$connect) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Lấy dữ liệu từ yêu cầu POST
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$id_product = isset($_POST['id_product']) ? intval($_POST['id_product']) : 0;
$id_user = isset($_POST['id_user']) ? intval($_POST['id_user']) : 0;
$name_user = isset($_POST['name_user']) ? mysqli_real_escape_string($connect, $_POST['name_user']) : '';
$content = isset($_POST['content']) ? mysqli_real_escape_string($connect, $_POST['content']) : '';

// Kiểm tra dữ liệu hợp lệ
if ($id > 0 && $id_product > 0 && $id_user > 0 && !empty($name_user) && !empty($content)) {
    // Chèn đánh giá mới vào bảng comment_product với cột id
    $query = "INSERT INTO comment_product (id, id_product, id_user, name_user, content) 
              VALUES ('$id', '$id_product', '$id_user', '$name_user', '$content')";

    if (mysqli_query($connect, $query)) {
        echo 'success';  // Trả về phản hồi thành công
    } else {
        echo 'error';  // Trả về phản hồi lỗi nếu có vấn đề với câu truy vấn
    }
} else {
    echo 'error';  // Trả về phản hồi lỗi nếu dữ liệu không hợp lệ
}

// Đóng kết nối
mysqli_close($connect);
?>
