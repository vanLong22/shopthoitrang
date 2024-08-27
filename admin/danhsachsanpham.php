<?php 
$connect = mysqli_connect("localhost", "root", "", "shopthoitrang");

// Xử lý tìm kiếm sản phẩm
$searchKeyword = '';
if (isset($_POST['searchsanpham'])) {
    $searchKeyword = mysqli_real_escape_string($connect, $_POST['searchsanpham']);
    $dataQuery = "SELECT * FROM product WHERE name LIKE '%$searchKeyword%' OR name_category LIKE '%$searchKeyword%'";
} else {
    $dataQuery = "SELECT * FROM product";
}

$resultQuery = mysqli_query($connect, $dataQuery);

// Xử lý sửa sản phẩm
if (isset($_POST['edit_product'])) {
    $productId = $_POST['product_id'];
    $newName = mysqli_real_escape_string($connect, $_POST['new_name']);
    $newPrice = mysqli_real_escape_string($connect, $_POST['new_price']);
    $newQuantity = mysqli_real_escape_string($connect, $_POST['new_quantity']);
    $updateQuery = "UPDATE product SET name='$newName', price='$newPrice', quantity='$newQuantity' WHERE id='$productId'";
    mysqli_query($connect, $updateQuery);
    header("Location: ".$_SERVER['PHP_SELF']);
}

// Xử lý xóa sản phẩm
if (isset($_POST['delete_product'])) {
    $productIdToDelete = $_POST['product_id'];
    $deleteQuery = "DELETE FROM product WHERE id='$productIdToDelete'";
    mysqli_query($connect, $deleteQuery);
    header("Location: ".$_SERVER['PHP_SELF']);
}

mysqli_close($connect);
?>

<!DOCTYPE html>
<html lang="vi">
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
            <h1>Danh Sách Sản Phẩm</h1>
            <div class="search-container">
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="text" name="searchsanpham" placeholder="Nhập tên sản phẩm" value="<?php echo htmlspecialchars($searchKeyword); ?>">
                    <button type="submit">Tìm Kiếm</button>
                </form>
            </div>
            <div class="table-container">
                <h2>Danh Sách Sản Phẩm</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Tên Danh Mục</th>
                            <th>Tên Sản Phẩm</th>
                            <th>Giá</th>
                            <th>Hình Ảnh</th>
                            <th>Số Lượng</th>
                            <th>Trạng Thái</th>
                            <th>Thực Hiện</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Vòng lặp để hiển thị dữ liệu từ cơ sở dữ liệu
                        while ($row = mysqli_fetch_assoc($resultQuery)) {
                            // Kiểm tra trạng thái hàng tồn kho
                            $status = ($row['quantity'] > 0) ? "Còn Hàng" : "Hết Hàng";
                            echo "<tr>";
                            echo "<td>{$row['name_category']}</td>";
                            echo "<td>{$row['name']}</td>";
                            echo "<td>" . number_format($row['price'], 0, ',', '.') . " ₫</td>";
                            echo "<td><img src='../{$row['img_product']}' alt='Sản phẩm' class='product-image' width='50'></td>";
                            echo "<td>{$row['quantity']}</td>";
                            echo "<td>{$status}</td>";
                            echo "<td class='actions'>
                                    <!-- Form sửa sản phẩm -->
                                    <form method='POST' action='" . htmlspecialchars($_SERVER['PHP_SELF']) . "' style='display:inline-block;'>
										
                                        <button type='submit' name='edit_product' class='edit-btn'>Sửa</button>
                                    </form>
                                    <!-- Form xóa sản phẩm -->
                                    <form method='POST' action='" . htmlspecialchars($_SERVER['PHP_SELF']) . "' style='display:inline-block;'>
                                        <input type='hidden' name='product_id' value='{$row['id']}'>
                                        <button type='submit' name='delete_product' class='delete-btn' onclick='return confirm(\"Bạn có chắc chắn muốn xóa sản phẩm này?\");'>Xóa</button>
                                    </form>
                                  </td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
