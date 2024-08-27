<?php 
$connect = mysqli_connect("localhost", "root", "", "shopthoitrang");

// Tìm kiếm
$searchKeyword = '';
if (isset($_POST['searchdanhmuc'])) {
    $searchKeyword = mysqli_real_escape_string($connect, $_POST['searchdanhmuc']);
    $dataQuery = "SELECT * FROM product WHERE name_category LIKE '%$searchKeyword%'";
} else {
    $dataQuery = "SELECT * FROM product";
}

$resultQuery = mysqli_query($connect, $dataQuery);

$displayedCategories = []; // Mảng để lưu trữ các tên danh mục đã hiển thị

// Xử lý chỉnh sửa tên danh mục
if (isset($_POST['edit_category'])) {
    $newCategoryName = mysqli_real_escape_string($connect, $_POST['new_name']);
    $oldCategoryName = mysqli_real_escape_string($connect, $_POST['old_name']);
    $updateQuery = "UPDATE product SET name_category='$newCategoryName' WHERE name_category='$oldCategoryName'";
    mysqli_query($connect, $updateQuery);
    header("Location: ".$_SERVER['PHP_SELF']);
}

// Xử lý xóa danh mục
if (isset($_POST['delete_category'])) {
    $categoryToDelete = mysqli_real_escape_string($connect, $_POST['category_to_delete']);
    $deleteQuery = "DELETE FROM product WHERE name_category='$categoryToDelete'";
    mysqli_query($connect, $deleteQuery);
    header("Location: ".$_SERVER['PHP_SELF']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Thời Trang</title>
    <style>
        /* Thêm style của bạn ở đây nếu cần */
    </style>
</head>
<body>
    <div class="container">
        <div class="main-content">
            <h2>Danh Sách Danh Mục</h2>
            <div class="form-group">
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <label for="search">Tìm Kiếm:</label>
                    <input type="text" id="search" name="searchdanhmuc" placeholder="Nhập tên danh mục" value="<?php echo htmlspecialchars($searchKeyword); ?>">
                    <button type="submit">Tìm Kiếm</button>
                </form>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Hình Ảnh</th>
                        <th>Tên Danh Mục</th>
                        <th>Ngày tạo</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($resultQuery)) { ?>
                        <?php 
                            if (!in_array($row['name_category'], $displayedCategories)) {
                                $displayedCategories[] = $row['name_category']; 
                        ?>
                        <tr>
                            <td><img src="<?php echo $row['img_product']; ?>" alt="<?php echo $row['name']; ?>" width="50"></td>
                            <td><?php echo $row['name_category']; ?></td> <!-- Hiển thị tên danh mục -->
                            <td><?php echo $row['create_at']; ?></td>
                            <td class="actions">
                                <!-- Sửa danh mục -->
								<button type="submit" class="edit-btn" name="edit_category">Sửa</button>
                                <!-- Xóa danh mục -->
                                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display: inline;">
                                    <input type="hidden" name="category_to_delete" value="<?php echo $row['name_category']; ?>">
                                    <button type="submit" class="delete-btn" name="delete_category" onclick="return confirm('Bạn có chắc chắn muốn xóa toàn bộ sản phẩm trong danh mục này?');">Xóa</button>
                                </form>
                            </td>
                        </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
