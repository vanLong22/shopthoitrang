<?php 
$connect = mysqli_connect("localhost", "root", "", "shopthoitrang");

// Xử lý tìm kiếm hóa đơn
$searchKeyword = '';
if (isset($_POST['searchhoadon'])) {
    $searchKeyword = mysqli_real_escape_string($connect, $_POST['searchhoadon']);
    $billQuery = "SELECT * FROM bill WHERE name LIKE '%$searchKeyword%' OR phone_number LIKE '%$searchKeyword%' OR address LIKE '%$searchKeyword%'";
} else {
    $billQuery = "SELECT * FROM bill";
}

$resultBill = mysqli_query($connect, $billQuery);

$allBill = array();
while($row = mysqli_fetch_assoc($resultBill)){
    $allBill[] = $row;
}

// Xử lý sửa hóa đơn
if (isset($_POST['edit_bill'])) {
    $billId = $_POST['bill_id'];
    $newAddress = mysqli_real_escape_string($connect, $_POST['new_address']);
    $newTotalMoney = mysqli_real_escape_string($connect, $_POST['new_total_money']);
    $newStatus = mysqli_real_escape_string($connect, $_POST['new_status']);
    $updateQuery = "UPDATE bill SET address='$newAddress', total_money='$newTotalMoney', status='$newStatus' WHERE id='$billId'";
    mysqli_query($connect, $updateQuery);
    header("Location: ".$_SERVER['PHP_SELF']);
}

// Xử lý xóa hóa đơn
if (isset($_POST['delete_bill'])) {
    $billIdToDelete = $_POST['bill_id'];
    $deleteQuery = "DELETE FROM bill WHERE id='$billIdToDelete'";
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
    <div class="main">
      <h2>Hóa Đơn</h2>
      <div class="table-container">
        <div class="search-bar">
          <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <input type="text" name="searchhoadon" placeholder="Nhập tìm kiếm" value="<?php echo htmlspecialchars($searchKeyword); ?>">
            <button type="submit" class="button">Tìm Kiếm</button>
          </form>
        </div>
        <table>
          <thead>
            <tr>
              <th>Khách Hàng</th>
			  <th>Số điện thoại</th>
              <th>Địa chỉ</th>
              <th>Tổng Tiền</th>
              <th>Ngày Thanh Toán</th>
              <th>Trạng Thái</th>
              <th>Thực Hiện</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($allBill as $bill): ?>
            <tr>
              <td><?php echo htmlspecialchars($bill['name']); ?></td>
			  <td><?php echo htmlspecialchars($bill['phone_number']); ?></td>
              <td><?php echo htmlspecialchars($bill['address']); ?></td>
              <td><?php echo number_format($bill['total_money'], 0, ',', '.'); ?> ₫</td>
              <td><?php echo date("d/m/Y", strtotime($bill['create_at'])); ?></td>
              <td>
                <?php 
                  if($bill['status'] == '0') {
                    echo '<button class="btn btn-primary">Đang xử lý</button>';
                  } elseif($bill['status'] == '1') {
                    echo '<button class="btn btn-success">Đã giao hàng</button>';
                  } elseif($bill['status'] == '11') {
                    echo '<button class="btn btn-success" style="background-color: yellow;">Đang vận chuyển</button>';
				  } else {
                    echo '<button class="btn btn-danger" style="background-color: red;">Đã hủy đơn</button>';
                  }
                ?>
              </td>
              <td class="actions">
                <!-- Sửa hóa đơn -->
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display: inline;">
				<!--
                  <input type="hidden" name="bill_id" value="<?php echo $bill['id']; ?>">
                  <input type="text" name="new_address" placeholder="Địa chỉ mới" required>
                  <input type="number" name="new_total_money" placeholder="Tổng tiền mới" required>
                  <select name="new_status" required>
                    <option value="0" <?php echo ($bill['status'] == '0') ? 'selected' : ''; ?>>Đang vận chuyển</option>
                    <option value="Đã thanh toán" <?php echo ($bill['status'] == 'Đã thanh toán') ? 'selected' : ''; ?>>Đã giao hàng</option>
                    <option value="Đang xử lý" <?php echo ($bill['status'] == 'Đang xử lý') ? 'selected' : ''; ?>>Đang xử lý</option>
                  </select>
				-->
                  <button type="submit"  name="edit_bill" class="edit-btn"    >Sửa</button>
                </form>
                <!-- Xóa hóa đơn -->
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display: inline;">
                  <input type="hidden" name="bill_id" value="<?php echo $bill['user_id']; ?>">
                  <button type="submit" name="delete_bill" class="delete-btn"   onclick="return confirm('Bạn có chắc chắn muốn xóa hóa đơn này?');">Xóa</button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>
</html>
