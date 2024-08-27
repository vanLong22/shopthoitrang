<?php 
$connect = mysqli_connect("localhost", "root", "", "shopthoitrang");

// Xử lý tìm kiếm người dùng theo tên
$searchKeyword = '';
if (isset($_POST['searchnguoidung'])) {
    $searchKeyword = mysqli_real_escape_string($connect, $_POST['searchnguoidung']);
    $userQuery = "SELECT * FROM user_account WHERE name LIKE '%$searchKeyword%'";
} else {
    $userQuery = "SELECT * FROM user_account";
}

$resultUser = mysqli_query($connect, $userQuery);

$allUser = array();
while($row = mysqli_fetch_assoc($resultUser)){
    $allUser[] = $row;
}

// Xử lý sửa thông tin người dùng
if (isset($_POST['edit_user'])) {
    $userId = $_POST['user_id'];
    $newName = mysqli_real_escape_string($connect, $_POST['new_name']);
    $newEmail = mysqli_real_escape_string($connect, $_POST['new_email']);
    $updateQuery = "UPDATE user_account SET name='$newName', email_account='$newEmail' WHERE id='$userId'";
    mysqli_query($connect, $updateQuery);
    header("Location: ".$_SERVER['PHP_SELF']);
}

// Xử lý xóa người dùng
if (isset($_POST['delete_user'])) {
    $userIdToDelete = $_POST['user_id'];
    $deleteQuery = "DELETE FROM user_account WHERE id='$userIdToDelete'";
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
            <div class="main-title">Người Dùng</div>
            <div class="main-form">
                <div class="main-form-title">Danh Sách Người Dùng</div>
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <div>
                        <label for="search-input">Tìm Kiếm:</label>
                        <input type="text" id="search-input" name="searchnguoidung" class="main-form-input" placeholder="Nhập tên người dùng" value="<?php echo htmlspecialchars($searchKeyword); ?>">
                    </div>
                    <button type="submit" class="main-form-button">Tìm Kiếm</button>
                </form>
                <table border="1">
                    <thead>
                        <tr>
                            <th>Tên Người Dùng</th>
                            <th>Gmail</th>
                            <th>Thực Hiện</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($allUser as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email_account']); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <!-- Sửa người dùng -->
                                    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display: inline;">
									<!--
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <input type="text" name="new_name" placeholder="Tên mới" required>
                                        <input type="email" name="new_email" placeholder="Email mới" required>
									-->
                                        <button type="submit" class="action-button action-button-edit" name="edit_user" class="action-button action-button-edit">Sửa</button>
                                    </form>
                                    <!-- Xóa người dùng -->
                                    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display: inline;">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" class="action-button action-button-delete" name="delete_user" class="action-button action-button-delete" onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này?');">Xóa</button>
                                    </form>
                                </div>
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
