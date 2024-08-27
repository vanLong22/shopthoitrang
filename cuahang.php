<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Kết nối đến cơ sở dữ liệu
$connect = mysqli_connect("localhost", "root", "", "shopthoitrang");

if(!isset($cuahang)){
	$cuahang = null;
}

// Lấy id sản phẩm hiện tại từ query string
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 51;

// Lấy thông tin sản phẩm hiện tại
$productQuery = "SELECT * FROM product WHERE id = $product_id";
$productResult = mysqli_query($connect, $productQuery);
$product = mysqli_fetch_assoc($productResult);

// Nếu sản phẩm không tồn tại, có thể chuyển hướng hoặc hiển thị thông báo lỗi
if (!$product) {
    echo "Sản phẩm không tồn tại.";
    exit;
}

// Lấy thông tin sản phẩm liên quan dựa trên danh mục
$category_id = $product['category_id'];
$relatedProductsQuery = "SELECT * FROM product WHERE category_id = $category_id AND id != $product_id LIMIT 4";
$relatedProductsResult = mysqli_query($connect, $relatedProductsQuery);

// Lấy danh sách đánh giá cho sản phẩm (giới hạn 5 đánh giá đầu tiên)
$limit = 5;
$reviewsQuery = "SELECT * FROM comment_product WHERE id_product = $product_id ORDER BY id DESC LIMIT $limit";
$reviewsResult = mysqli_query($connect, $reviewsQuery);

// phân trang
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 12; // Số sản phẩm trên mỗi trang
$offset = ($page - 1) * $limit;

// TÌM KIẾM SẢN PHẨM
$searchTerm = ''; // giá trị mà người dùng nhập vào ô tìm kiếm 
$allProducts = [];


if ($_SERVER["REQUEST_METHOD"] == "POST" || isset($_GET['search']) || isset($_GET['page'])) {
	/*
	if(isset($_POST['cuahang'])){
		if (!isset($cuahang)) {
			$cuahang = true;
		}
	}
	*/	
	// lưu tên sản phẩm mà người dùng tìm kiếm
    if(isset($_GET['search'])) {
        $searchTerm = mysqli_real_escape_string($connect, $_GET['search']);
    } elseif (isset($_POST['search'])) {
        $searchTerm = mysqli_real_escape_string($connect, $_POST['search']);
    }
    
    $dataQuery = "SELECT * FROM product WHERE name LIKE '%$searchTerm%' LIMIT $limit OFFSET $offset";
    // nhận biết rằng đang chọn 1 mục danh mục sản phẩm nhất định
    if(!isset($search)){
        $search = true;
    }
} else {
    $dataQuery = "SELECT * FROM product";
}


$resultQuery = mysqli_query($connect, $dataQuery);

if (mysqli_num_rows($resultQuery) > 0) {
    while ($row = mysqli_fetch_assoc($resultQuery)) {
        $allProducts[] = $row;
    }
}

if(isset($search)) {
	$totalQuery = "SELECT COUNT(*) as total FROM product WHERE name LIKE '%$searchTerm%'";
	$totalResult = mysqli_query($connect, $totalQuery);
	$totalRow = mysqli_fetch_assoc($totalResult);
	$totalProducts = $totalRow['total'];
	$totalPages = ceil($totalProducts / $limit);
}


// Đóng kết nối
mysqli_close($connect);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?></title>
    <link rel="stylesheet" href="styles.css">
	<link rel="stylesheet" href="cuahang.css">
	<style>				
		/* Container của thanh tìm kiếm */
		.search-bar {
			position: relative; /* Để căn chỉnh hộp gợi ý tìm kiếm dựa trên thanh tìm kiếm */
			display: flex;
			justify-content: center;
			align-items: center;
			background-color: #fff;
			border-radius: 50px;
			padding: 5px 20px;
			max-width: 300px;
			width: 100%;
		}

		/* Form */
		.search-bar form {
			display: flex;
			width: 100%;
		}

		/* Input tìm kiếm */
		.search-bar input[type="text"] {
			border: none;
			outline: none;
			font-size: 16px;
			padding: 10px 15px;
			width: 100%;
			border-radius: 50px;
			transition: box-shadow 0.3s ease;
		}

		/* Khi người dùng focus vào input */
		.search-bar input[type="text"]:focus {
			box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.2);
		}

		/* Button tìm kiếm */
		.search-bar button {
			background-color: #007bff;
			border: none;
			color: white;
			padding: 10px 20px;
			margin-left: 10px;
			font-size: 16px;
			border-radius: 50px;
			cursor: pointer;
			transition: background-color 0.3s;
		}

		/* Hiệu ứng khi hover vào button */
		.search-bar button:hover {
			background-color: #0056b3;
		}

		/* Hiển thị hộp gợi ý khi có kết quả tìm kiếm */
		.search-bar input[type="text"]:not(:placeholder-shown) + .suggestions-box {
			display: block; /* Hiển thị hộp gợi ý khi có nội dung tìm kiếm */
		}
		
		/* Hộp gợi ý tìm kiếm */
		.suggestions-box {
			display: none; /* Ẩn hộp gợi ý */
			border: 1px solid #ddd;
			border-radius: 4px;
			background-color: #fff;
			position: absolute;
			top: 100%; /* Đặt ngay bên dưới thanh tìm kiếm */
			left: 0;
			width: 100%;
			max-height: 200px; /* Chiều cao tối đa */
			overflow-y: auto;
			z-index: 1000;
			box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Đổ bóng để tạo chiều sâu */
			padding: 5px 0; /* Padding cho không gian bên trong */
		}

		/* Mục gợi ý tìm kiếm */
		.suggestion-item {
			padding: 12px 15px; /* Padding cho mục gợi ý */
			cursor: pointer;
			transition: background-color 0.2s ease, color 0.2s ease; /* Hiệu ứng chuyển tiếp */
		}

		.suggestion-item:hover {
			background-color: #f5f5f5; /* Màu nền khi di chuột qua */
			color: #333; /* Màu chữ khi di chuột qua */
		}

		.suggestion-item:active {
			background-color: #e0e0e0; /* Màu nền khi nhấp chuột */
		}
		
		/* Container kết quả tìm kiếm */
		.search-results {
			margin-top: 30px;
			padding: 20px;
			background-color: #f9f9f9;
			border-radius: 4px;
			box-shadow: 0 2px 5px rgba(0,0,0,0.1);
		}

		/* Tiêu đề kết quả tìm kiếm */
		.search-results h2 {
			font-size: 24px;
			margin-bottom: 20px;
			color: #333;
		}

		/* Container các sản phẩm trong kết quả tìm kiếm */
		.search-results .products {
			display: flex;
			flex-wrap: wrap;
			gap: 40px;
			flex-grow: 1;
		}

		/* Từng sản phẩm */
		.search-results .product-item {
			background-color: white;
			padding: 20px;
			width: calc(20% - 20px);
			box-shadow: 0 2px 5px rgba(0,0,0,0.1);
			text-align: center;
			border-radius: 4px;
			transition: transform 0.3s ease, box-shadow 0.3s ease, filter 0.3s ease;
		}

		/* Hình ảnh sản phẩm */
		.search-results .product-item img {
			width: 100%;
			height: auto;
			border-bottom: 1px solid #ddd;
			padding-bottom: 15px;
			transition: transform 0.3s ease;
		}

		/* Tên sản phẩm */
		.search-results .product-item p {
			font-size: 16px;
			margin: 10px 0;
			color: #555;
		}

		/* Hiệu ứng khi di chuột vào sản phẩm */
		.search-results .product-item:hover {
			transform: scale(1.05) translateY(-8px);
			box-shadow: 0 10px 15px rgba(0, 0, 0, 0.15);
		}

		/* Hiệu ứng khi di chuột vào hình ảnh sản phẩm */
		.search-results .product-item:hover img {
			transform: scale(1.1);
		}
		
		/* Phân trang */
		.pagination {
			text-align: center;
			margin: 20px 0;
		}

		.page-link {
			display: inline-block;
			padding: 10px 15px;
			margin: 0 5px;
			background-color: #f0f0f0;
			color: #333;
			text-decoration: none;
			border-radius: 5px;
		}

		.page-link.active {
			background-color: #333;
			color: #fff;
		}

		.page-link:hover {
			background-color: #ddd;
		}
	</style>
	<script>
        document.addEventListener("DOMContentLoaded", function() {
            var quantityInput = document.getElementById('quantity');
            var stockMessage = document.getElementById('stock-message');
            var maxQuantity = <?php echo $product['quantity']; ?>; // Giá trị số lượng còn lại từ PHP

            // Thiết lập giá trị max cho input
            quantityInput.setAttribute('max', maxQuantity);

            // Hiển thị thông báo nếu sản phẩm hết hàng
            if (maxQuantity === 0) {
                stockMessage.textContent = 'Sản phẩm đã hết hàng';
                quantityInput.disabled = true; // Vô hiệu hóa input nếu hết hàng
            }
			
			// Xử lý nhấp vào nút "Xem thêm"
            document.getElementById('load-more-reviews').addEventListener('click', function() {
                var lastReviewId = document.querySelector('.review-item:last-child') ? document.querySelector('.review-item:last-child').dataset.reviewId : 0;
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'load_more_reviews.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        document.getElementById('review-list').innerHTML += xhr.responseText;
                    }
                };
                xhr.send('id_product=' + <?php echo $product_id; ?> + '&last_review_id=' + lastReviewId);
            });

        });
    </script>
</head>
<body>
	<?php 
	if (isset($cuahang)) {
		
	} else if (isset($_GET['id']) || isset($allProducts)) {
		include 'header.php'; 
	} 
	?>
	
    <div class="container">
		<!-- Thanh tìm kiếm -->
        <div class="search-bar">
            <form action="cuahang.php" method="POST">
				<!--<button type="submit">Tìm kiếm</button>-->
				<input type="text" name="search" placeholder="Tìm Kiếm...">
				<div id="suggestions" class="suggestions-box"></div>
            </form>
        </div>
		
		<!-- Kiểm tra xem có kết quả tìm kiếm hay không -->
        <?php if (isset($search)) { ?>
            <!-- Hiển thị sản phẩm sau khi tìm kiếm -->
            <div class="search-results">
                <h2>Kết Quả Tìm Kiếm</h2>
                <div class="products">
                    <?php foreach ($allProducts as $product) { ?>
                        <div class="product-item">
                            <a href="cuahang.php?id=<?php echo $product['id']; ?>">
                                <img src="<?php echo htmlspecialchars($product['img_product']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            </a>
                            <p><?php echo htmlspecialchars($product['name']); ?> - <?php echo number_format($product['price'], 0, ',', '.'); ?>đ</p>
                        </div>
                    <?php } ?>
                </div>
				
				<!-- Phân trang -->
				<div class="pagination">
					<?php if(isset($search)): ?>
						<?php if ($page > 1): ?>
							<a href="?search=<?php echo urlencode($searchTerm); ?>&page=<?php echo $page - 1; ?>" class="page-link">« Previous</a>
							<!-- <a href="?page=<?php echo $page - 1; ?>" class="page-link">« Previous</a> -->
						<?php endif; ?>

						<?php for ($i = 1; $i <= $totalPages; $i++): ?>
							<a href="?search=<?php echo urlencode($searchTerm); ?>&page=<?php echo $i; ?>" class="page-link <?php echo ($i == $page) ? 'active' : ''; ?>">
								<?php echo $i; ?>
							</a>
							<!--
							<a href="?page=<?php echo $i; ?>" class="page-link <?php echo ($i == $page) ? 'active' : ''; ?>">
								<?php echo $i; ?>
							</a>
							-->
						<?php endfor; ?>

						<?php if ($page < $totalPages): ?>
							<a href="?search=<?php echo urlencode($searchTerm); ?>&page=<?php echo $page + 1; ?>" class="page-link">Next »</a>
						<?php endif; ?>
					<?php endif; ?>
				</div>
				
            </div>
        <?php } else { ?>
            <!-- Nếu không có kết quả tìm kiếm, hiển thị sản phẩm được chọn -->
            <div class="product-image">
                <img src="<?php echo htmlspecialchars($product['img_product']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
			
            <div class="product-details">
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                <p><?php echo number_format($product['price'], 0, ',', '.'); ?>đ</p>
            </div>
			
            <div class="options">
                <div>
					<label for="color">Màu sắc</label>
					<select id="color">
						<option>Beige</option>
						<option>Đen</option>
						<option>Trắng</option>
						<option>Xanh</option>
					</select>
				</div>
				<div>
					<label for="size">Kích thước</label>
					<select id="size">
						<option>S</option>
						<option>M</option>
						<option>L</option>
						<option>XL</option>
					</select>
				</div>
				<div>
					<label for="quantity">Số lượng</label>
					<input type="number" id="quantity" value="1" min="1" max="<?php echo $product['quantity']; ?>">
					<span id="stock-message" style="color: red;"></span>
				</div>
            </div>
			
            <div class="add-to-cart">
                <button <?php if($product['quantity'] <= 0) echo "disabled" ; ?>>THÊM VÀO GIỎ HÀNG</button>
                <button <?php if($product['quantity'] <= 0) echo "disabled" ; ?>>THANH TOÁN</button>
            </div>
			
            <div class="related-products">
                <h2>Sản Phẩm Liên Quan</h2>
                <?php while ($relatedProduct = mysqli_fetch_assoc($relatedProductsResult)) { ?>
                    <div class="product">
                        <a href="cuahang.php?id=<?php echo $relatedProduct['id']; ?>">
                            <img src="<?php echo htmlspecialchars($relatedProduct['img_product']); ?>" alt="<?php echo htmlspecialchars($relatedProduct['name']); ?>">
                        </a>
                        <p><?php echo htmlspecialchars($relatedProduct['name']); ?> - <?php echo number_format($relatedProduct['price'], 0, ',', '.'); ?>đ</p>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
		
		<?php if (!isset($search)) { ?>
			<div class="reviews">
				<h2>Đánh Giá Sản Phẩm</h2>
				<div class="review-form">
					<label for="review">Viết đánh giá của bạn:</label>
					<textarea id="review" rows="4" placeholder="Nhập đánh giá của bạn ở đây..."></textarea>
					<button id="submit-review">Gửi Đánh Giá</button>
				</div>
				<div id="review-response" style="color: green; display: flex; justify-content: center; align-items: center; "></div>
				<div id="review-response" style="color: green;"></div>
				<div class="review-list">
					<?php while ($review = mysqli_fetch_assoc($reviewsResult)) { ?>
						<div class="review-item">
							<p><strong><?php echo htmlspecialchars($review['name_user']); ?>:</strong> <?php echo htmlspecialchars($review['content']); ?></p>
						</div>
					<?php } ?>
				</div>
				<?php if (mysqli_num_rows($reviewsResult) == $limit) { ?>
					<div style="display: flex; justify-content: center; align-items: center;">
						<button id="load-more-reviews" data-product-id="<?php echo $product_id; ?>">Xem thêm</button>
					</div>
				<?php } ?>
			</div>
		<?php } ?>
    </div>
	  
	<?php 
	if (isset($cuahang)) {
		
	} else if (isset($_GET['id']) || isset($allProducts)) {
		include 'footer.php'; 
	} 
	?>


	<script>
		document.addEventListener("DOMContentLoaded", function() {
			document.querySelector('.add-to-cart button:nth-child(1)').addEventListener('click', function() {
				var quantity = document.getElementById('quantity').value;
				var color = document.getElementById('color').value;
				var size = document.getElementById('size').value;

				var data = {
					product_id: <?php echo $product_id; ?>,
					name_product: "<?php echo addslashes($product['name']); ?>",
					quantity_order: quantity,
					unit_price: <?php echo $product['price']; ?>,
					color: color,
					size: size
				};

				fetch('add_to_cart.php', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json'
					},
					body: JSON.stringify(data)
				})
				.then(response => response.json())
				.then(result => {
					if (result.status === 'success') {
						alert('Đã thêm vào giỏ hàng thành công!');

						// Cập nhật số lượng trên biểu tượng giỏ hàng
						var cartCount = document.querySelector('.cart-count');
						var currentCount = parseInt(cartCount.textContent);
						cartCount.textContent = currentCount + 1;
					} else {
						alert('Đã xảy ra lỗi. Vui lòng thử lại.');
					}
				})
				.catch(error => console.error('Error:', error));
			});
		});
	</script>
	<script> // gợi ý tìm kiếm
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
	</script>
	<script>
		document.addEventListener("DOMContentLoaded", function() {
			document.getElementById('submit-review').addEventListener('click', function() {
				var reviewContent = document.getElementById('review').value.trim();
				
				// Ràng buộc yêu cầu nhập nội dung đánh giá
				if (reviewContent === "") {
					alert("Vui lòng nhập nội dung đánh giá.");
					return;
				}
				
				var idProduct = <?php echo $product_id; ?>; // ID sản phẩm hiện tại
				var idUser = 1; // ID người dùng (Cần thay đổi cho phù hợp với ứng dụng của bạn)
				var nameUser = "Người dùng"; // Tên người dùng (Cần thay đổi cho phù hợp với ứng dụng của bạn)
				var id = Date.now(); // Hoặc bạn có thể sử dụng một cách khác để tạo giá trị id duy nhất

				// Gửi đánh giá đến máy chủ
				fetch('submit_review.php', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/x-www-form-urlencoded'
					},
					body: 'id=' + id + '&id_product=' + idProduct + '&id_user=' + idUser + '&name_user=' + encodeURIComponent(nameUser) + '&content=' + encodeURIComponent(reviewContent)
				})
				.then(response => response.text())
				.then(result => {
					console.log(result);  // Kiểm tra phản hồi từ server
					if (result.trim() === 'success') {
						// Hiển thị thông báo thành công
						document.getElementById('review-response').textContent = 'Đánh giá đã được gửi thành công!';

						// Làm trống ô nhập hiện tại và tạo ô nhập mới
						document.getElementById('review').value = ''; // Xóa nội dung cũ
					} else {
						alert("Đã xảy ra lỗi: " + result);  // Hiển thị lỗi từ server
					}
				})
				.catch(error => console.error('Error:', error));
			});
		});
	</script>
	

	<script>
		document.addEventListener("DOMContentLoaded", function() {
			var quantityInput = document.getElementById('quantity');
			var maxQuantity = <?php echo $product['quantity']; ?>;

			quantityInput.addEventListener('input', function() {
				var currentQuantity = parseInt(quantityInput.value);

				if (currentQuantity > maxQuantity) {
					alert('Số lượng bạn nhập lớn hơn số lượng sản phẩm hiện có. Vui lòng nhập số lượng nhỏ hơn hoặc bằng ' + maxQuantity);
					quantityInput.value = maxQuantity; // Đặt lại giá trị về maxQuantity
				}
			});
		});
	</script>
	<script>
		document.addEventListener("DOMContentLoaded", function() {
			var quantityInput = document.getElementById('quantity');
			var stockMessage = document.getElementById('stock-message');
			var maxQuantity = <?php echo $product['quantity']; ?>;
			var userId = 1; // Giá trị user_id giả định, bạn cần lấy giá trị này từ phiên người dùng

			quantityInput.setAttribute('max', maxQuantity);

			if (maxQuantity <= 0) {
				stockMessage.textContent = 'Sản phẩm đã hết hàng';
				quantityInput.disabled = true;
			}

			document.querySelector('.add-to-cart button:nth-child(2)').addEventListener('click', function() {
				var color = document.getElementById('color').value;
				var size = document.getElementById('size').value;
				var quantity = quantityInput.value;

				var data = {
					product_id: <?php echo $product_id; ?>,
					user_id: userId,
					name: 'Người dùng',
					name_product: "<?php echo addslashes($product['name']); ?>",
					price: <?php echo $product['price']; ?>,
					color: color,
					size: size,
					quantity_order: quantity,
					address: 'Địa chỉ người dùng',  // Bạn cần lấy từ phiên hoặc yêu cầu người dùng nhập
					phone_number: 'Số điện thoại', // Bạn cần lấy từ phiên hoặc yêu cầu người dùng nhập
				};

				fetch('process_payment.php', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json'
					},
					body: JSON.stringify(data)
				})
				.then(response => response.json())
				.then(result => {
					if (result.success) {
						alert('Thanh toán thành công!');
						// Xóa sản phẩm khỏi giao diện mà không cần tải lại trang
						//document.querySelector('.container').style.display = 'none';
					} else {
						alert('Đã xảy ra lỗi. Vui lòng thử lại.');
					}
				})
				.catch(error => console.error('Error:', error));
			});
		});
	</script>
	 


</body>
</html>



