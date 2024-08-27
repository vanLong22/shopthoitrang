<?php 
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['dangxuat'])) { // dang xuat
    session_unset(); // Clear all session variables
    session_destroy(); // Destroy the session
    header("Location: html.php"); // Redirect to the homepage
    exit();
}

$connect = mysqli_connect("localhost", "root", "", "shopthoitrang");
$dataQuery = "SELECT * FROM product";
$resultQuery = mysqli_query($connect, $dataQuery);

// đếm số lượng sản phẩm trong giỏ Hàng
$cartQuery = "SELECT COUNT(*) AS totalCart FROM cart";
$resultCart = mysqli_query($connect, $cartQuery);
$rowCart = mysqli_fetch_assoc($resultCart);
$_SESSION['totalCart'] = $rowCart['totalCart'];


// lưu all sản phẩm
$allProducts = array();
while ($row = mysqli_fetch_assoc($resultQuery)) {
	$allProducts[] = $row;
}

// chỉ định trang chủ là mặc định 
if(!isset($trangchu)){
	$trangchu = true;
}

if(!isset($_SESSION['category'])){
	$_SESSION['category'] = null;
}

if(!isset($_SESSION['search'])){
	$_SESSION['search'] = null;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" || isset($_GET['page'])) {
	// chuyển sang trang khác(ngoài trang chủ)
	$trangchu = false;
	if(isset($_GET['page'])){
		$trangchu = true;
	}
	
	// xóa chọn các danh mục sản phẩm
	unset($_SESSION['aosominam']);
	unset($_SESSION['aothunnam']);
	unset($_SESSION['quanjeannam']);
	unset($_SESSION['aothunnu']);
	unset($_SESSION['dogiadinh']);
	
	if(isset($_POST['trangchu'])){
		$trangchu = true;
		unset($_SESSION['category']);
		
	}
	
	if(isset($_POST['cuahang'])){
		if (!isset($cuahang)) {
			$cuahang = true;
		}
		unset($_SESSION['category']);
		
	}
	
	if(isset($_POST['gioithieu'])){
		if (!isset($gioithieu)) {
			$gioithieu = true;
		}
		unset($_SESSION['category']);
		
	}
	
	if(isset($_POST['baiviet'])){
		if (!isset($baiviet)) {
			$baiviet = true;
		}
		unset($_SESSION['category']);
		
	}
	
	if(isset($_POST['lienhe'])){
		if (!isset($lienhe)) {
			$lienhe = true;
		}
		unset($_SESSION['category']);
		
	}

	if(isset($_POST['giohang']) || isset($_POST['update']) || isset($_POST['delete'])){
		if (!isset($giohang)) {
			$giohang = true;
		}
		unset($_SESSION['category']);
		
	}
	
	if(isset($_POST['lichsudonhang'])){
		if (!isset($lichsudonhang)) {
			$lichsudonhang = true;
		}
		unset($_SESSION['category']);
		
	}
	
	if(isset($_POST['lichsudonhang'])){
		if (!isset($lichsudonhang)) {
			$lichsudonhang = true;
		}
		unset($_SESSION['category']);
		
	}
	
	// lấy thông tin từng danh mục sản phẩm
	if(isset($_POST['category']) || (isset($_SESSION['category']) && $_SESSION['category'] !== null)){
		
		
		$trangchu = true; // không chuyển sang trang khác
		$allProducts = array(); // Làm rỗng danh sách sản phẩm
		$_SESSION['category'] = isset($_POST['category']) ? $_POST['category'] : $_SESSION['category'];
		$category = mysqli_real_escape_string($connect, $_SESSION['category']);
		if ((isset($_POST['category']) && $_POST['category'] == 'Áo Thun Nữ') || (isset($_SESSION['category']) && $_SESSION['category'] == 'Áo Thun Nữ')) {
			$dataQuery = "SELECT * from product where name_category='$category' ";
			$resultQuery = mysqli_query($connect, $dataQuery);
			
			while ($row = mysqli_fetch_assoc($resultQuery)) {
				$allProducts[] = $row;
			}
			if(!isset($_SESSION['aothunnu'])){
				$_SESSION['aothunnu'] = true;
			}
		}
		
		if((isset($_POST['category']) && $_POST['category'] == 'Quần Jean Nam') || (isset($_SESSION['category']) && $_SESSION['category'] == 'Quần Jean Nam')){
			$dataQuery = "SELECT * from product where name_category='$category'";
			$resultQuery = mysqli_query($connect, $dataQuery);
			
			while ($row = mysqli_fetch_assoc($resultQuery)) {
				$allProducts[] = $row;
			}
			if(!isset($_SESSION['quanjeannam'])){
				$_SESSION['quanjeannam'] = true;
			}
		}
		
		if((isset($_POST['category']) && $_POST['category'] == 'Áo Thun Nam') || (isset($_SESSION['category']) && $_SESSION['category'] == 'Áo Thun Nam')){
			$dataQuery = "SELECT * from product where name_category='$category'";
			$resultQuery = mysqli_query($connect, $dataQuery);
			
			while ($row = mysqli_fetch_assoc($resultQuery)) {
				$allProducts[] = $row;
			}
			if(!isset($_SESSION['aothunnam'])){
				$_SESSION['aothunnam'] = true;
			}
		}
		
		if((isset($_POST['category']) && $_POST['category'] == 'Đồ Gia Đình') || (isset($_SESSION['category']) && $_SESSION['category'] == 'Đồ Gia Đình')){
			$dataQuery = "SELECT * from product where name_category='$category'";
			$resultQuery = mysqli_query($connect, $dataQuery);
			
			while ($row = mysqli_fetch_assoc($resultQuery)) {
				$allProducts[] = $row;
			}
			if(!isset($_SESSION['dogiadinh'])){
				$_SESSION['dogiadinh'] = true;
			}
		}
		
		if((isset($_POST['category']) &&$_POST['category'] == 'Áo Sơ Mi Nam') || (isset($_SESSION['category']) && $_SESSION['category'] == 'Áo Sơ Mi Nam')){
			$dataQuery = "SELECT * from product where name_category='$category'";
			$resultQuery = mysqli_query($connect, $dataQuery);
			
			while ($row = mysqli_fetch_assoc($resultQuery)) {
				$allProducts[] = $row;
			}
			if(!isset($_SESSION['aosominam'])){
				$_SESSION['aosominam'] = true;
			}
		}
	}
	
	// lấy thông tin từng danh mục sản phẩm
	if (isset($_POST['category']) || (isset($_SESSION['category']) && $_SESSION['category'] !== null) && !isset($search) ) {
		$trangchu = true; // không chuyển sang trang khác
		
		
		// Xác định trang hiện tại
		$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
		$productsPerPage = 4;
		$start = ($page - 1) * $productsPerPage;
		
		// Đếm tổng số sản phẩm trong danh mục đã chọn
		$countQuery = "SELECT COUNT(*) as total FROM product WHERE name_category='$category'";
		$countResult = mysqli_query($connect, $countQuery);
		$totalProducts = mysqli_fetch_assoc($countResult)['total'];
		// Tổng số trang
		$totalPages = ceil($totalProducts / $productsPerPage);
		
		// Lấy sản phẩm cho trang hiện tại
		$dataQuery = "SELECT * FROM product WHERE name_category='$category' LIMIT $start, $productsPerPage";
		$resultQuery = mysqli_query($connect, $dataQuery);
		
		// Lưu tất cả sản phẩm cho trang hiện tại
		$allProducts = array();
		while ($row = mysqli_fetch_assoc($resultQuery)) {
			$allProducts[] = $row;
		}
		// nhận biết rằng đang chọn 1 mục danh mục sản phẩm nhất định
		if(!isset($flagCategory) && isset($_SESSION['category']) && $_SESSION['category'] !== null){
			$flagCategory = true;
		}
	}
}


// TÌM KIẾM SẢN PHẨM
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['search'])) {
		$searchTerm = '';
		$allProducts = [];
		
        $searchTerm = mysqli_real_escape_string($connect, $_POST['search']);
        $dataQuery = "SELECT * FROM product WHERE name LIKE '%$searchTerm%'";
		// nhận biết rằng đang chọn 1 mục danh mục sản phẩm nhất định
		if(!isset($search)){
			$search = true;
		}
    } else {
        $dataQuery = "SELECT * FROM product";
    }
} else {
    $dataQuery = "SELECT * FROM product";
}
if(isset($search)){
	$resultQuery = mysqli_query($connect, $dataQuery);
	if (mysqli_num_rows($resultQuery) > 0) {
		while ($row = mysqli_fetch_assoc($resultQuery)) {
			$allProducts[] = $row;
		}
	}
}

// phân trang
if(!isset($flagCategory)){
	// Nếu không có category được chọn, phân trang mặc định cho tất cả sản phẩm
	$productsPerPage = 12;
	$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
	$start = ($page - 1) * $productsPerPage;
	
	// Đếm tổng số sản phẩm
	$countQuery = "SELECT COUNT(*) as total FROM product";
	$countResult = mysqli_query($connect, $countQuery);
	$totalProducts = mysqli_fetch_assoc($countResult)['total'];
	
	// Tổng số trang
	$totalPages = ceil($totalProducts / $productsPerPage);
	
	// Lấy sản phẩm cho trang hiện tại
	$dataQuery = "SELECT * FROM product LIMIT $start, $productsPerPage";
	$resultQuery = mysqli_query($connect, $dataQuery);
	
	// Lưu tất cả sản phẩm cho trang hiện tại
	$allProducts = array();
	while ($row = mysqli_fetch_assoc($resultQuery)) {
		$allProducts[] = $row;
	}
}




// đóng kn database
mysqli_close($connect);
?>



<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Thời Trang</title>
    <link rel="stylesheet" href="styles.css">
	<style>		
		 
	</style>
	
	<?php
    if (isset($trangchu) && $trangchu == true ) {
        echo '<link rel="stylesheet" href="trangchu.css">';
    } 
	if(isset($cuahang)){
        echo '<link rel="stylesheet" href="cuahang.css">';
    }
	if(isset($gioithieu)){
        echo '<link rel="stylesheet" href="gioithieu.css">';
    }
	if(isset($baiviet)){
        echo '<link rel="stylesheet" href="baiviet.css">';
    }
	if(isset($lienhe)){
        echo '<link rel="stylesheet" href="lienhe.css">';
    }
	if(isset($giohang)){
        echo '<link rel="stylesheet" href="giohang.css">';
    }
	if(isset($lichsudonhang)){
        echo '<link rel="stylesheet" href="lichsudonhang.css">';
    }
	if(isset($lichsudonhang)){
        echo '<link rel="stylesheet" href="lichsudonhang.css">';
    }
    ?>
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
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" >
					<button type="submit" name="trangchu" class="nav-button" style="<?php if (isset($trangchu) && $trangchu == true ) echo 'color:red;'; ?>">Trang Chủ</button>
					 <a href="cuahang.php" name="cuahang" class="nav-button" style="<?php if (isset($cuahang)) echo 'color:red;'; ?>">Cửa Hàng</a> 
																			 
					<!--<button type="submit" name="cuahang" class="nav-button" style="<?php //if (isset($cuahang)) echo 'color:red;'; ?>">Cửa Hàng</button>-->
					<button type="submit" name="gioithieu" class="nav-button" style="<?php if (isset($gioithieu)) echo 'color:red;'; ?>">Giới Thiệu</button>
					<button type="submit" name="baiviet" class="nav-button" style="<?php if (isset($baiviet)) echo 'color:red;'; ?>">Bài Viết</button>
					<button type="submit" name="lienhe" class="nav-button" style="<?php if (isset($lienhe)) echo 'color:red;'; ?>">Liên Hệ</button>
				</form>
			</nav>
			<div class="cart" name="giohang" type="submit">
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" >
					<button class="cart-icon" name="giohang">
						<img src="assets//logo//giohang.png" alt="Cart Icon"> <!-- Thay bằng hình ảnh giỏ hàng thực tế -->
						<div id="cart-count">
							<span class="cart-count"><?php echo $rowCart['totalCart']; ?></span>
						</div>
						<!-- <!-- Số lượng sản phẩm trong giỏ hàng -->
					</button>
					<div class="cart-dropdown">
						<button class="dropdown-item" name="lichsudonhang">Lịch Sử Đơn Hàng</button>
					</div>
				</form>
			</div>
        </div>
    </header>
	
	<?php if (isset($trangchu) && $trangchu == true): ?>
    <main>
	<!--
        <div class="breadcrumb">
			<a href="#">Trang Chủ</a> <span class="separator">></span> <span>Cửa Hàng</span>
        </div>
	-->
        <div class="shop-content">
            <aside>
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
					<div class="search-bar">
						<input type="text" name="search" placeholder="Tìm Kiếm...">
						<div id="suggestions" class="suggestions-box"></div>
					</div>
					
					<h3>DANH MỤC</h3>
					<ul>
						<li>
							<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
								<button type="submit" name="category" value="Áo Thun Nữ" style="background:none; border:none; <?php if(isset($_SESSION['aothunnu'])) { echo 'color:red;'; } ?>">Áo Thun Nữ</button>
							</form>																						

						</li>
						<li>
							<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
								<button type="submit" name="category" value="Quần Jean Nam" style="background:none; border:none;<?php if(isset($_SESSION['quanjeannam'])) { echo 'color:red;'; } ?>">Quần Jean Nam</button>
							</form>
						</li>
						<li>
							<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
								<button type="submit" name="category" value="Áo Thun Nam" style="background:none; border:none;<?php if(isset($_SESSION['aothunnam'])) { echo 'color:red;'; } ?>">Áo Thun Nam</button>
							</form>
						</li>
						<li>
							<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
								<button type="submit" name="category" value="Đồ Gia Đình" style="background:none; border:none;<?php if(isset($_SESSION['dogiadinh'])) { echo 'color:red;'; } ?>">Đồ Gia Đình</button>
							</form>
						</li>
						<li>
							<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
								<button type="submit" name="category" value="Áo Sơ Mi Nam" style="background:none; border:none;<?php if(isset($_SESSION['aosominam'])){ echo 'color:red;'; } ?>">Áo Sơ Mi Nam</button>
							</form>
						</li>
					</ul>
				</form>
            </aside>
            <section class="products">
				<?php if (empty($allProducts)): ?>
					<p>Không tìm thấy sản phẩm nào.</p>
				<?php else: ?>
					<?php foreach ($allProducts as $product): ?>
						<div class="product">
							<a href="cuahang.php?id=<?php echo $product['id']; ?>">
								<img src="<?php echo $product['img_product']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
							</a>
							<h4><?php echo htmlspecialchars($product['name']); ?></h4>
							<p><?php echo htmlspecialchars($product['price']); ?>đ</p>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</section>

			
        </div>
    </main>
	<div class="pagination">
		<?php if(!isset($search)): ?>
			<?php if ($page > 1): ?>
				<a href="?page=<?php echo $page - 1; ?>" class="page-link">« Previous</a>
			<?php endif; ?>

			<?php for ($i = 1; $i <= $totalPages; $i++): ?>
				<a href="?page=<?php echo $i; ?>" class="page-link <?php echo ($i == $page) ? 'active' : ''; ?>">
					<?php echo $i; ?>
				</a>
			<?php endfor; ?>

			<?php if ($page < $totalPages): ?>
				<a href="?page=<?php echo $page + 1; ?>" class="page-link">Next »</a>
			<?php endif; ?>
		<?php endif; ?>
	</div>
	<?php endif; ?>


	<?php if (isset($cuahang)): ?>
		<?php include 'cuahang.php'; ?>
	<?php endif; ?>
		
	<?php if (isset($gioithieu)): ?>
		<?php include 'gioithieu.php'; ?>
	<?php endif; ?>
	
	<?php if (isset($baiviet)): ?>
		<?php include 'baiviet.php'; ?>
	<?php endif; ?>
	
	<?php if (isset($lienhe)): ?>
		<?php include 'lienhe.php'; ?>
	<?php endif; ?>
		
	<?php if (isset($giohang)): ?>
		<?php include 'giohang.php'; ?>
	<?php endif; ?>

	<?php if (isset($lichsudonhang)): ?>
		<?php include 'lichsudonhang.php'; ?>
	<?php endif; ?>
	 
	
    <footer>
		<div class="footer-section">
			<h3>Danh mục</h3>
			<ul>
				<li><a href="#">Thời trang nữ</a></li>
				<li><a href="#">Thời trang nam</a></li>
				<li><a href="#">Đồ đôi nam nữ</a></li>
				<li><a href="#">Áo khoác</a></li>
				<li><a href="#">Sơ Mi Nam Nữ</a></li>
			</ul>
		</div>
		<div class="footer-section">
			<h3>Chính Sách</h3>
			<ul>
				<li><a href="#">Chính sách đổi trả</a></li>
				<li><a href="#">Chính sách bảo hành</a></li>
				<li><a href="#">Hướng dẫn chọn size</a></li>
				<li><a href="#">Chính sách bảo mật</a></li>
			</ul>
		</div>
		<div class="footer-section">
			<h3>Hỗ Trợ Khách Hàng</h3>
			<ul>
				<li><a href="#">Câu hỏi thường gặp</a></li>
				<li><a href="#">Hỗ trợ trực tuyến</a></li>
				<li><a href="#">Liên hệ chúng tôi</a></li>
				<li><a href="#">Góp ý & Khiếu nại</a></li>
			</ul>
		</div>
		<div class="footer-section">
			<h3>Thông Tin</h3>
			<ul>
				<li><a href="#">Về chúng tôi</a></li>
				<li><a href="#">Tuyển dụng</a></li>
				<li><a href="#">Tin tức</a></li>
				<li><a href="#">Chương trình khuyến mãi</a></li>
			</ul>
		</div>
	</footer>
	
	<script>
		document.querySelector('input[name="search"]').addEventListener('input', function() {
			let searchQuery = this.value;
			let suggestionsBox = document.getElementById('suggestions');
			
			if (searchQuery.length > 0) {
				let xhr = new XMLHttpRequest();
				xhr.open('POST', 'search_suggestions.php', true);
				xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
				xhr.onload = function() {
					if (this.status == 200) {
						suggestionsBox.innerHTML = this.responseText;
					}
				}
				xhr.send('query=' + encodeURIComponent(searchQuery));
			} else {
				suggestionsBox.innerHTML = '';
			}
		});
		
		
		/*
		$(document).ready(function() {
			// Cập nhật số lượng giỏ hàng từ server
			function updateCartCount() {
				$.ajax({
					url: 'get_cart_count.php',
					method: 'GET',
					success: function(response) {
						$('#cart-count').text(response.cart_count); // Giả định response.cart_count chứa số lượng sản phẩm
					},
					error: function() {
						alert('Không thể lấy số lượng giỏ hàng.');
					}
				});
			}

			updateCartCount();

			// Khi nhấn nút xóa sản phẩm
			$('.delete-btn').on('click', function() {
				var orderId = $(this).data('id'); // Lấy ID đơn hàng từ thuộc tính data

				if (confirm("Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng không?")) {
					$.ajax({
						url: 'delete_cart.php',
						method: 'POST',
						data: { order_id: orderId },
						success: function(response) {
							if (response === "Sản phẩm đã được xóa.") {
								updateCartCount(); // Cập nhật số lượng giỏ hàng
								$('tr[data-id="' + orderId + '"]').remove(); // Xóa sản phẩm khỏi bảng
							} else {
								alert('Lỗi: ' + response);
							}
						},
						error: function() {
							alert('Lỗi khi xóa sản phẩm.');
						}
					});
				}
			});
		});
*/

	</script>

</body>
</html>
