<?php
/*
Lý do nút <a> không chuyển sang màu đỏ khi click vào có thể liên quan đến cách bạn sử dụng và xử lý các biến PHP và HTML. Trong mã của bạn, <a> được sử dụng để tạo một liên kết đến cuahang.php, 
nhưng nó không phải là một phần của form, nên khi bạn nhấn vào nó, trang không được cập nhật với giá trị của các biến PHP.

if(!isset($cuahang)){
	$cuahang = null;
}
*/
// Kiểm tra trạng thái của biến cuahang
$cuahang = isset($_GET['cuahang']) ? true : false;


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Thời Trang</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function updateCartCount() {
            $.ajax({
                url: 'get_cart_count.php',
                method: 'GET',
                success: function(data) {
                    $('.cart-count').text(data);
                },
                error: function() {
                    $('.cart-count').text('0'); // Xử lý lỗi nếu có
                }
            });
        }

        $(document).ready(function() {
            updateCartCount();

            // Khi nhấn nút thêm vào giỏ hàng
            $('.add-to-cart-button').on('click', function() {
                var productId = $(this).data('product-id'); // Lấy ID sản phẩm từ thuộc tính data
                var quantity = 1; // Hoặc lấy từ input số lượng nếu cần

                $.ajax({
                    url: 'add_to_cart.php',
                    method: 'POST',
                    data: { product_id: productId, quantity: quantity },
                    success: function() {
                        updateCartCount(); // Cập nhật số lượng giỏ hàng
                    },
                    error: function() {
                        alert('Không thể thêm sản phẩm vào giỏ hàng.');
                    }
                });
            });
        });
    </script>
</head>
<body>
    <header>
        <div class="header-top">
            <span>KhaiTK</span>
            <a href="#" class="login">Đăng Nhập</a>
        </div>
        <div class="header-main">
            <h1>SHOP THỜI TRANG</h1>
            <nav>
                <form action="html.php" method="POST">
                    <button type="submit" name="trangchu" class="nav-button" style="<?php if (isset($trangchu) && $trangchu == true) echo 'color:red;'; ?>">Trang Chủ</button>
                    <!--<button type="submit" name="cuahang" class="nav-button" style="<?php //if (isset($cuahang)) echo 'color:red;'; ?>">Cửa Hàng</button>-->
					<a href="cuahang.php" name="cuahang" class="nav-button" style="<?php if (isset($cuahang)) echo 'color:red;'; ?>">Cửa Hàng</a> 
                    <button type="submit" name="gioithieu" class="nav-button" style="<?php if (isset($gioithieu)) echo 'color:red;'; ?>">Giới Thiệu</button>
                    <button type="submit" name="baiviet" class="nav-button" style="<?php if (isset($baiviet)) echo 'color:red;'; ?>">Bài Viết</button>
                    <button type="submit" name="lienhe" class="nav-button" style="<?php if (isset($lienhe)) echo 'color:red;'; ?>">Liên Hệ</button>
                </form>
            </nav>
            <div class="cart" name="giohang" type="submit">
                <form action="html.php" method="POST">
                    <button class="cart-icon" name="giohang">
                        <img src="assets/logo/giohang.png" alt="Cart Icon">
                        <span class="cart-count">0</span>
                    </button>
                    <div class="cart-dropdown">
                        <button class="dropdown-item" name="lichsudonhang">Lịch Sử Đơn Hàng</button>
                    </div>
                </form>
            </div>
        </div>
    </header>
</body>
</html>
