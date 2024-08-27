<?php

$connect = mysqli_connect("localhost", "root", "", "shopthoitrang");
$dataQuery = "SELECT * FROM product";
$resultQuery = mysqli_query($connect, $dataQuery);

$allProducts = array();
$totalPrice = 0; // tổng tiền của các sản Phẩm
// Lấy tất cả các sản phẩm và tính tổng tiền
while ($row = mysqli_fetch_assoc($resultQuery)) {
    $allProducts[] = $row;
    $totalPrice += $row['price'];
}

// đếm số lượng sản phẩm 
$categoryNames = array_column($allProducts, 'name_category');
$uniqueCategories = array_unique($categoryNames);
$totalUniqueCategories = count($uniqueCategories);

// đếm số lượng người Dùng
$userQuery = "SELECT count(*) as totalUser FROM user_account";
$resultUser = mysqli_query($connect, $userQuery);
$row = mysqli_fetch_assoc($resultUser);
$totalUser = $row['totalUser'];

// đếm số lượng sản phẩm đang xử lý
$billQuery = "SELECT count(*) as billing FROM bill where status = '0'";
$resultBill = mysqli_query($connect, $billQuery);
$row = mysqli_fetch_assoc($resultBill);
$totalBilling = $row['billing'];


// Truy vấn để lấy các hóa đơn có status = 0
$billQuery = "SELECT * FROM bill WHERE status = '0'";
$resultBill = mysqli_query($connect, $billQuery);
// Lấy tất cả các hóa đơn có status = 0
$invoices = array();
while ($row = mysqli_fetch_assoc($resultBill)) {
    $invoices[] = $row;
}


// mặc định hiển thị giao diện trang chủ
$trangchu = true;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$trangchu = false; // chuyển sang giao diện khác 
	
	if(isset($_POST['trangchu'])){
		$trangchu = true;
	}
	
	if(isset($_POST['danhmuc']) || isset($_POST['delete_category']) || isset($_POST['searchdanhmuc']) ){
		$danhmuc = true;
	}
	
	if(isset($_POST['themdanhmuc']) || isset($_POST['searchthemdanhmuc'])){
		$themdanhmuc = true;
	}
	
	if(isset($_POST['themsanpham']) || isset($_POST['them']) ){
		$themsanpham = true;
	}
	
	if(isset($_POST['sanpham']) || isset($_POST['searchsanpham']) ||  isset($_POST['xemthemsanpham'])){
		$sanpham = true;
	}
	
	if(isset($_POST['nguoidung']) || isset($_POST['searchnguoidung'])){
		$nguoidung = true;
	}
		        
	if(isset($_POST['hoadon']) || isset($_POST['searchhoadon']) || isset($_POST['xemthemhoadon'])){
		$hoadon = true;
	}
		
	if(isset($_POST['thongke'])){
		$thongke = true;
	}
		
	if(isset($_POST['baiviet'])){
		$baiviet = true;
	}
	
	
}



?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Thời Trang</title>
    <link rel="stylesheet" href="admin.css">
    <script>
		document.addEventListener('DOMContentLoaded', () => {
			document.querySelectorAll('.sidebar .dropdown-toggle').forEach(link => {
				link.addEventListener('click', function(event) {
					event.preventDefault();
					const parentLi = this.parentElement;

					// Toggle the 'open' class
					parentLi.classList.toggle('open');

					// Close other open dropdowns
					document.querySelectorAll('.sidebar .dropdown').forEach(li => {
						if (li !== parentLi) {
							li.classList.remove('open');
						}
					});
				});
			});
		});
		/*
		document.addEventListener('DOMContentLoaded', () => {
			const sidebarLinks = document.querySelectorAll('.sidebar ul li a');

			sidebarLinks.forEach(link => {
				link.addEventListener('click', function() {
					// Xóa class active khỏi tất cả các mục
					sidebarLinks.forEach(l => l.parentElement.classList.remove('active'));

					// Thêm class active vào mục được chọn
					this.parentElement.classList.add('active');
				});
			});
		});
		*/
		
		document.addEventListener('DOMContentLoaded', () => {
			const sidebarLinks = document.querySelectorAll('.sidebar ul li a');

			sidebarLinks.forEach(link => {
				link.addEventListener('click', function() {
					// Xóa class active khỏi tất cả các mục và mục con
					sidebarLinks.forEach(l => l.parentElement.classList.remove('active'));

					// Thêm class active vào mục được chọn
					this.parentElement.classList.add('active');
					
					// Kiểm tra nếu mục này là mục con, thì làm mờ các mục con khác trong cùng nhóm
					const parentLi = this.closest('.dropdown');
					if (parentLi) {
						const siblingLinks = parentLi.querySelectorAll('ul li');
						siblingLinks.forEach(sibling => {
							if (sibling !== this.parentElement) {
								sibling.classList.remove('active');
							}
						});
					}
				});
			});
		});
		
		
		document.addEventListener('DOMContentLoaded', () => {
    const buttons = document.querySelectorAll('.sidebar .nav-button');

    buttons.forEach(button => {
        button.addEventListener('click', function() {
            // Xóa class active khỏi tất cả các nút
            buttons.forEach(btn => btn.classList.remove('active'));
            // Thêm class active vào nút được click
            this.classList.add('active');
        });
    });
});

    </script>
	<?php
    if (isset($danhmuc)) {
        echo '<link rel="stylesheet" href="danhmucsanpham.css">';
    } 
	if(isset($themdanhmuc)){
        echo '<link rel="stylesheet" href="themdanhmuc.css">';
    }
	if(isset($sanpham)){
        echo '<link rel="stylesheet" href="danhsachsanpham.css">';
    }
	if(isset($themsanpham)){
        echo '<link rel="stylesheet" href="themsanpham.css">';
    }
	if(isset($hoadon)){
        echo '<link rel="stylesheet" href="hoadon.css">';
    }
	if(isset($nguoidung)){
        echo '<link rel="stylesheet" href="nguoidung.css">';
    }
    ?>
</head>
<body>
    <!-- Header -->
    <div class="header">
        Shop Thời Trang
    </div>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Sidebar -->
		<div class="sidebar">
			<div class="home-container" style="display: flex; align-items: center; color: black;">
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
					<button type="submit" name="trangchu" class="home-button">
						<img style="width: 30px; height: 30px;" src="../assets/logo/trangchu.png" alt="Home Icon" class="home-icon">Trang Chủ
					</button>
				</form>
			</div>
			<ul>
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
					<li class="dropdown <?php echo isset($danhmuc) || isset($themdanhmuc) ? 'open active' : ''; ?>">
						<a href="#" class="dropdown-toggle">Danh Mục</a>
						<ul>
							<li>
								<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
									<button type="submit" name="themdanhmuc" class=" <?php echo isset($themdanhmuc) ? 'active' : ''; ?>">Thêm Danh Mục</button>
								</form>
							</li>
							<li>
								<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
									<button type="submit" name="danhmuc" class=" <?php echo isset($danhmuc) ? 'active' : ''; ?>">Danh Sách Danh Mục</button>
								</form>
							</li>
						</ul>
					</li>
					<li class="dropdown <?php echo isset($sanpham) || isset($themsanpham) ? 'open active' : ''; ?>">
						<a href="#" class="dropdown-toggle">Sản Phẩm</a>
						<ul>
							<li>
								<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
									<button type="submit" name="themsanpham" class="nav-button <?php echo isset($themsanpham) ? 'active' : ''; ?>">Thêm Sản Phẩm</button>
								</form>
							</li>
							<li>
								<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
									<button type="submit" name="sanpham" class="nav-button <?php echo isset($sanpham) ? 'active' : ''; ?>">Danh Sách Sản Phẩm</button>
								</form>
							</li>
						</ul>
					</li>
					<li>
						<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
							<button type="submit" name="nguoidung" class="nav-button <?php echo isset($nguoidung) ? 'active' : ''; ?>">Người Dùng</button>
						</form>
					</li>
					<li>
						<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
							<button type="submit" name="baiviet" class="nav-button <?php echo isset($baiviet) ? 'active' : ''; ?>">Bài Viết</button>
						</form>
					</li>
					<li>
						<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
							<button type="submit" name="hoadon" class="nav-button <?php echo isset($hoadon) ? 'active' : ''; ?>">Hóa Đơn</button>
						</form>
					</li>
					<li>
						<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
							<button type="submit" name="thongke" class="nav-button <?php echo isset($thongke) ? 'active' : ''; ?>">Thống Kê</button>
						</form>
					</li>
				</form>
			</ul>
			<ul class="logout">
				<li><a href="#">Đăng Xuất</a></li>
			</ul>
			<span class="version">Version 1.1</span>
		</div>


        <!-- Main Content -->
        <div class="main-content">
			<?php if(isset($trangchu) && $trangchu == true): ?>
				<div class="stats">
					<div class="stat-item">
						<span>Tổng Tất Cả</span>
						<h3><?php echo number_format($totalPrice, 0, ',', '.'); ?> đ</h3>
					</div>
					<div class="stat-item">
						<span>Số Lượng Sản Phẩm</span>
						<h3><?php echo $totalUniqueCategories; ?></h3>
					</div>
					<div class="stat-item">
						<span>Số Lượng Người Dùng</span>
						<h3><?php echo $totalUser; ?></h3>
					</div>
					<div class="stat-item">
						<span>Yêu Cầu Đang Xử Lý</span>
						<h3><?php echo $totalBilling; ?></h3>
					</div>
				</div>
				<div class="content">
					<div class="product-status">
						<h2>Trạng thái sản phẩm</h2>
						<table>
							<tr>
								<th>Sản Phẩm</th>
								<th>Giá</th>
							</tr>
							<?php $productCount = 0; ?>
							<?php foreach ($allProducts as $product): ?>
								<?php if ($productCount < 10): ?>
								<tr>
									<td><?php echo htmlspecialchars($product['name']); ?></td>
									<td><?php echo number_format($product['price'], 0, ',', '.'); ?> đ</td>
								</tr>
								<?php $productCount++; ?>
								<?php else: ?>
									<?php break; // Đoạn này chỉ cần nếu dùng break, còn nếu không dùng break thì cần có điều kiện này ?>
								<?php endif; ?>
							<?php endforeach; ?>
						</table>
						<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
							<button class="more" name="xemthemsanpham" >Xem thêm</button>
						</form>
					</div>


					<div class="invoices">
						<h2>Hóa Đơn</h2>
						<table>
							<thead>
								<tr>
									<th>STT</th>
									<th>Khách Hàng</th>
									<th>Tổng Tiền</th>
									<th>Trạng Thái</th>
									<th>Thực Hiện</th>
								</tr>
							</thead>
							<tbody>
								<?php if (!empty($invoices)): ?>
									<?php foreach ($invoices as $index => $invoice): ?>
										<tr>
											<td><?php echo $index + 1; ?></td>
											<td><?php echo htmlspecialchars($invoice['name']); ?></td>
											<td><?php echo number_format($invoice['total_money'], 0, ',', '.'); ?> đ</td>
											<td><span class="status processing">Đang xử lý</span></td>
											<td><button>Chi tiết</button></td>
										</tr>
									<?php endforeach; ?>
								<?php else: ?>
									<tr>
										<td colspan="5">Không có hóa đơn nào.</td>
									</tr>
								<?php endif; ?>
							</tbody>
						</table>
						<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
							<button class="more" name="xemthemhoadon" >Xem thêm</button>
						</form>
					</div>
				</div>
			<?php endif; ?>
			
			<?php if(isset($danhmuc)): ?>
				<?php include 'danhmucsanpham.php' ?>
			<?php endif; ?>
			
			<?php if(isset($themdanhmuc)): ?>
				<?php include 'themdanhmuc.php' ?>
			<?php endif; ?>
			
			<?php if(isset($sanpham)): ?>
				<?php include 'danhsachsanpham.php' ?>
			<?php endif; ?>
			
			<?php if(isset($themsanpham)): ?>
				<?php include 'themsanpham.php' ?>
			<?php endif; ?>
			
			<?php if(isset($nguoidung)): ?>
				<?php include 'nguoidung.php' ?>
			<?php endif; ?>
			
			<?php if(isset($hoadon)): ?>
				<?php include 'hoadon.php' ?>
			<?php endif; ?>
			
        </div>
    </div>
</body>
</html>