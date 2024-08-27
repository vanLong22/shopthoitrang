<?php
$connect = mysqli_connect("localhost", "root", "", "shopthoitrang");

// Xác định số bài viết trên mỗi trang
$itemsPerPage = 8;

// Xác định trang hiện tại từ POST
$current_page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$start = ($current_page - 1) * $itemsPerPage;

// Tính tổng số bài viết
$totalQuery = "SELECT COUNT(*) AS total FROM content";
$totalResult = mysqli_query($connect, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalItems = $totalRow['total'];
$totalPages = ceil($totalItems / $itemsPerPage);

// Lấy bài viết cho trang hiện tại
$dataQuery = "SELECT * FROM content LIMIT $start, $itemsPerPage";
$resultQuery = mysqli_query($connect, $dataQuery);

// Lưu all sản phẩm
$allContents = array();
while ($row = mysqli_fetch_assoc($resultQuery)) {
    $allContents[] = $row;
}

mysqli_close($connect);
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bài Viết</title>
    <link rel="stylesheet" href="baiviet.css">
</head>
<body>
    <main>
        <section class="articles">
            <?php foreach ($allContents as $content): ?>
                <article>
                    <img src="<?php echo $content['img_content']; ?>" alt="<?php echo $content['description']; ?>">
                    <p class="date">Ngày <?php echo $content['create_at']; ?></p>
                    <p class="description"><?php echo $content['description']; ?></p>
                    <a href="<?php echo $content['link_content']; ?>" class="read-more">Đọc tiếp</a>
                </article>
            <?php endforeach; ?>
        </section>
        
        <!-- Phân trang -->
        <div class="pagination">
            <form action="" method="post" style="display: inline;">
                <?php if ($current_page > 1): ?>
                    <button type="submit" name="page" value="<?php echo $current_page - 1; ?>" class="page-link">« Previous</button>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <button type="submit" name="page" value="<?php echo $i; ?>" class="page-link <?php echo $i === $current_page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </button>
                <?php endfor; ?>

                <?php if ($current_page < $totalPages): ?>
                    <button type="submit" name="page" value="<?php echo $current_page + 1; ?>" class="page-link">Next »</button>
                <?php endif; ?>
            </form>
        </div>
    </main>
</body>
</html>
