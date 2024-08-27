<?php
$connect = mysqli_connect("localhost", "root", "", "shopthoitrang");

if (!$connect) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Truy vấn dữ liệu đơn hàng
$dataQuery = "SELECT * FROM bill where status != '1'";
$resultQuery = mysqli_query($connect, $dataQuery);

$allBills = array();

while ($row = mysqli_fetch_assoc($resultQuery)) {
    $allBills[] = $row;
}

mysqli_close($connect);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LỊCH SỬ ĐƠN HÀNG</title>
    <link rel="stylesheet" href="trangthaidonhang.css">
</head>
<body>
    <div class="container">
        <nav>
            <!-- Navigation bar if needed -->
        </nav>
        <h1>Lịch sử đơn hàng</h1>

        <table>
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Địa chỉ</th>
                    <th>Số điện thoại</th>
                    <th>Ngày</th>
                    <th>Tổng tiền</th>
                    <th>Màu sắc</th>
                    <th>Kích thước</th>
                    <th>Số lượng</th>
                    <th>Tên sản phẩm</th>
                    <th>Trạng thái</th>
                    <th>Chi tiết</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allBills as $index => $bill): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($bill['address']); ?></td>
                        <td><?php echo htmlspecialchars($bill['phone_number']); ?></td>
                        <td><?php echo htmlspecialchars($bill['create_at']); ?></td>
                        <td><?php echo htmlspecialchars($bill['total_money']) . '₫'; ?></td>
                        <td><?php echo htmlspecialchars($bill['color']); ?></td>
                        <td><?php echo htmlspecialchars($bill['size']); ?></td>
                        <td><?php echo htmlspecialchars($bill['quantity_order']); ?></td>
                        <td><?php echo htmlspecialchars($bill['name_user']); ?></td>
                        <td><?php echo htmlspecialchars($bill['status']); ?></td>
                        <td>
                            <form action="html.php" method="POST">
                                <button type="submit" class="button" name="hoadon">Chi tiết</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
