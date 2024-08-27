<?php
// Kết nối đến cơ sở dữ liệu
$connect = mysqli_connect("localhost", "root", "", "shopthoitrang");

if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

// Lấy ID sản phẩm và ID đánh giá cuối cùng từ yêu cầu POST
$id_product = isset($_POST['id_product']) ? intval($_POST['id_product']) : 0;
$last_review_id = isset($_POST['last_review_id']) ? intval($_POST['last_review_id']) : 0;

// Giới hạn số lượng đánh giá cần tải thêm (có thể thay đổi số lượng này)
$limit = 5;

// Truy vấn để lấy thêm đánh giá, chỉ lấy các đánh giá có ID nhỏ hơn ID cuối cùng đã tải
$query = "SELECT * FROM comment_product 
          WHERE id_product = $id_product 
          ORDER BY id DESC 
          LIMIT $limit";

$result = mysqli_query($connect, $query);

// Kiểm tra và trả về kết quả
if (mysqli_num_rows($result) > 0) {
    while ($review = mysqli_fetch_assoc($result)) {
        ?>
        <div class="review-item" data-review-id="<?php echo $review['id']; ?>">
            <p><strong><?php echo htmlspecialchars($review['name_user']); ?>:</strong> <?php echo htmlspecialchars($review['content']); ?></p>
        </div>
        <?php
    }
} else {
    echo "No more reviews.";
}

// Đóng kết nối
mysqli_close($connect);
?>
