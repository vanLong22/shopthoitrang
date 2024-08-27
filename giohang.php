<?php
$connect = mysqli_connect("localhost", "root", "", "shopthoitrang");

if (!$connect) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Truy vấn dữ liệu giỏ hàng
$dataQuery = "SELECT * FROM cart";
$resultQuery = mysqli_query($connect, $dataQuery);

$allCarts = array();
$totalAmount = 0; // Biến để lưu tổng tiền

while ($row = mysqli_fetch_assoc($resultQuery)) {
    $allCarts[] = $row;
    // Tính tổng tiền cho mỗi sản phẩm
    $totalAmount += $row['quantity'] * $row['unit_price'];
}
/*

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update'])) {
        $order_id = $_POST['order_id'];
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;

        if ($quantity > 0) {
            $updateQuery = "UPDATE cart SET quantity = $quantity WHERE order_id = $order_id";
            mysqli_query($connect, $updateQuery);
        }
    }

    if (isset($_POST['delete'])) {
        $order_id = $_POST['order_id'];
        $deleteQuery = "DELETE FROM cart WHERE order_id = $order_id";
        mysqli_query($connect, $deleteQuery);
    }
}
*/


mysqli_close($connect);
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ Hàng</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="cart-container">
        <h1>Giỏ hàng</h1>
        <table>
            <thead>
				<tr>
					<th>Chọn</th>
					<th>Sản phẩm</th>
					<th>Màu sắc</th>
					<th>Kích thước</th>
					<th>Số lượng</th>
					<th>Giá tiền</th>
					<th>Tổng tiền</th>
					<th>Thao tác</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($allCarts as $cart): ?>
					<tr data-id="<?php echo $cart['order_id']; ?>">
						<td><input type="checkbox" class="select-product" data-id="<?php echo $cart['order_id']; ?>" data-quantity="<?php echo $cart['quantity']; ?>" data-price="<?php echo $cart['unit_price']; ?>"></td>
						<td><?php echo htmlspecialchars($cart['name_product']); ?></td>
						<td>
							<select id="color" class="color" data-id="<?php echo $cart['order_id']; ?>">
								<option value="Beige" <?php echo ($cart['color'] == 'Beige') ? 'selected' : ''; ?>>Beige</option>
								<option value="Đen" <?php echo ($cart['color'] == 'Đen') ? 'selected' : ''; ?>>Đen</option>
								<option value="Trắng" <?php echo ($cart['color'] == 'Trắng') ? 'selected' : ''; ?>>Trắng</option>
								<option value="Xanh" <?php echo ($cart['color'] == 'Xanh') ? 'selected' : ''; ?>>Xanh</option>
							</select>
						</td>
						<td>
							<select id="size" class="size" data-id="<?php echo $cart['order_id']; ?>">
								<option value="S" <?php echo ($cart['size'] == 'S') ? 'selected' : ''; ?>>S</option>
								<option value="M" <?php echo ($cart['size'] == 'M') ? 'selected' : ''; ?>>M</option>
								<option value="L" <?php echo ($cart['size'] == 'L') ? 'selected' : ''; ?>>L</option>
								<option value="XL" <?php echo ($cart['size'] == 'XL') ? 'selected' : ''; ?>>XL</option>
							</select>
						</td>
						<td>
							<input type="number" class="quantity" data-id="<?php echo $cart['order_id']; ?>" data-price="<?php echo $cart['unit_price']; ?>" value="<?php echo htmlspecialchars($cart['quantity']); ?>">
						</td>
						<td><?php echo number_format($cart['unit_price'], 0, ',', '.'); ?>đ</td>
						<td class="total-price"><?php echo number_format($cart['quantity'] * $cart['unit_price'], 0, ',', '.'); ?>đ</td>
						<td>
							<button class="delete-btn" data-id="<?php echo $cart['order_id']; ?>">Xóa</button>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
        </table>
		
        <div class="subtotal">
            <span>Tổng cộng:</span>
            <span style="color: red;" id="subtotal"><?php echo number_format($totalAmount, 0, ',', '.'); ?>đ</span>
        </div>
		
        <div class="checkout-summary">
			<div class="summary-item">
				<span>Số lượng sản phẩm chọn thanh toán:</span>
				<span id="selected-quantity">0</span>
			</div>
			<div class="summary-item">
				<span>Tổng tiền chọn thanh toán:</span>
				<span id="selected-total">0đ</span>
			</div>
			<div class="button-container">
				<div class="select-buttons">
					<button id="select-all-btn">Chọn tất cả</button>
					<button id="deselect-all-btn">Bỏ chọn tất cả</button>
				</div>
				
				<button name="thanhtoan" id="checkout-btn">Thanh toán</button>
				
			</div>
		</div>
    </div>

    <script>
	/*
$(document).ready(function() {
    // Hàm để cập nhật số lượng sản phẩm trong biểu tượng giỏ hàng
    function updateCartCount(newCount) {
        $('#cart-count').text(newCount);
    }

    // Hàm để xử lý khi người dùng xóa sản phẩm
    $('.delete-btn').on('click', function() {
        let orderId = $(this).data('id');
        let confirmDelete = confirm("Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng không?");

        if (confirmDelete) {
            $.ajax({
                url: 'delete_cart.php',
                type: 'POST',
                data: {
                    order_id: orderId
                },
                success: function(response) {
                    if (response.status !== 'success') {
                        // Xóa sản phẩm khỏi bảng
                        $('tr[data-id="' + orderId + '"]').remove();

                        // Giảm số lượng sản phẩm trong giỏ hàng trên giao diện
                        let currentCount = parseInt($('#cart-count').text());
                        updateCartCount(currentCount - 1);

                        // Cập nhật lại tổng tiền sau khi xóa
                        updateTotal();

                        alert(response.message);
                    } else {
                        alert(response.message);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown); // Xem lỗi nếu có
                    alert("Đã xảy ra lỗi trong quá trình xóa sản phẩm.");
                }
            });
        }
    });

    // Hàm để cập nhật tổng tiền (được gọi lại sau khi xóa sản phẩm)
    function updateTotal() {
        let total = 0;

        $('.quantity').each(function() {
            let quantity = $(this).val();
            let price = $(this).data('price');
            let totalPrice = quantity * price;

            $(this).closest('tr').find('.total-price').text(totalPrice.toLocaleString('vi-VN') + 'đ');

            total += totalPrice;
        });

        $('#subtotal').text(total.toLocaleString('vi-VN') + 'đ');
        updateCheckoutSummary(); // Cập nhật thông tin thanh toán khi tổng cộng thay đổi
    }

    function updateCheckoutSummary() {
        let totalQuantity = 0;
        let totalAmount = 0;

        $('.select-product:checked').each(function() {
            let productId = $(this).data('id');
            let quantity = $(this).closest('tr').find('.quantity').val();
            let price = $(this).data('price');
            totalQuantity += parseInt(quantity);
            totalAmount += quantity * price;
        });

        $('#selected-quantity').text(totalQuantity);
        $('#selected-total').text(totalAmount.toLocaleString('vi-VN') + 'đ');
    }
});

*/

		$(document).ready(function() {
			function updateTotal() {
				let total = 0;

				$('.quantity').each(function() {
					let quantity = $(this).val();
					let price = $(this).data('price');
					let totalPrice = quantity * price;

					$(this).closest('tr').find('.total-price').text(totalPrice.toLocaleString('vi-VN') + 'đ');

					total += totalPrice;
				});

				$('#subtotal').text(total.toLocaleString('vi-VN') + 'đ');
				updateCheckoutSummary(); // Cập nhật thông tin thanh toán khi tổng cộng thay đổi
			}

			function updateCheckoutSummary() {
				let totalQuantity = 0;
				let totalAmount = 0;

				$('.select-product:checked').each(function() {
					let productId = $(this).data('id');
					let quantity = $(this).closest('tr').find('.quantity').val();
					let price = $(this).data('price');
					totalQuantity += parseInt(quantity);
					totalAmount += quantity * price;
				});

				$('#selected-quantity').text(totalQuantity);
				$('#selected-total').text(totalAmount.toLocaleString('vi-VN') + 'đ');
			}
			
			function updateCartInterface(selectedOrders) {
				selectedOrders.forEach(function(orderId) {
					$('tr[data-id="' + orderId + '"]').remove(); // Xóa hàng sản phẩm khỏi bảng
				});

				// Kiểm tra nếu giỏ hàng trống thì hiển thị thông báo hoặc ẩn bảng giỏ hàng
				if ($('tbody tr').length === 0) {
					$('.cart-container').html('<p>Giỏ hàng của bạn hiện tại trống.</p>');
				}

				// Cập nhật lại tổng tiền sau khi xóa các sản phẩm
				updateTotal();
				updateCheckoutSummary();
			}


			$('.quantity, .color, .size').on('change', function() {
				updateTotal();

				let quantity = $(this).closest('tr').find('.quantity').val();
				let color = $(this).closest('tr').find('.color').val();
				let size = $(this).closest('tr').find('.size').val();
				let orderId = $(this).closest('tr').data('id');

				$.ajax({
					url: 'update_cart.php',
					type: 'POST',
					data: {
						order_id: orderId,
						quantity: quantity,
						color: color,
						size: size
					},
					success: function(response) {
						console.log(response);
					}
				});
			});
/*
			$('.delete-btn').on('click', function() {
				let orderId = $(this).data('id');
				let confirmDelete = confirm("Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng không?");

				if (confirmDelete) {
					$.ajax({
						url: 'delete_cart.php',
						type: 'POST',
						data: {
							order_id: orderId
						},
						success: function(response) {
							console.log(response); // Xem phản hồi từ server
							$('tr[data-id="' + orderId + '"]').remove();
							updateTotal();
						},
						error: function(jqXHR, textStatus, errorThrown) {
							console.log(textStatus, errorThrown); // Xem lỗi nếu có
						}
					});
				}
			});
*/
			$('.delete-btn').on('click', function() {
				let orderId = $(this).data('id');
				let confirmDelete = confirm("Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng không?");

				if (confirmDelete) {
					$.ajax({
						url: 'delete_cart.php',
						type: 'POST',
						data: {
							order_id: orderId
						},
						success: function(response) {
							console.log(response); // Xem phản hồi từ server
							$('tr[data-id="' + orderId + '"]').remove();
							updateTotal();

							// Giảm số lượng sản phẩm trên biểu tượng giỏ hàng
							let cartCount = $('#cart-count .cart-count');
							let currentCount = parseInt(cartCount.text());

							if (currentCount > 0) {
								cartCount.text(currentCount - 1);
							}
						},
						error: function(jqXHR, textStatus, errorThrown) {
							console.log(textStatus, errorThrown); // Xem lỗi nếu có
						}
					});
				}
			});


			$('.select-product').on('change', function() {
				updateCheckoutSummary();
			});

			$('#select-all-btn').on('click', function() {
				$('.select-product').prop('checked', true).trigger('change');
			});

			$('#deselect-all-btn').on('click', function() {
				$('.select-product').prop('checked', false).trigger('change');
			});


			/* Thanh toán sản phẩm */
			$('#checkout-btn').on('click', function() {
				let selectedOrders = [];

				$('.select-product:checked').each(function() {
					let orderId = $(this).closest('tr').data('id');
					selectedOrders.push(orderId);
				});

				if (selectedOrders.length === 0) {
					alert("Vui lòng chọn ít nhất một sản phẩm để thanh toán.");
					return;
				}

				let userId = 1; // Thay đổi thành ID người dùng thực tế
				let name = 'Người Dùng'; // Thay đổi thành tên người dùng thực tế
				let address = 'Địa chỉ'; // Thay đổi thành địa chỉ thực tế
				let phoneNumber = 'Số điện thoại'; // Thay đổi thành số điện thoại thực tế
				let totalMoney = $('#selected-total').text().replace('đ', '').replace(/\./g, '').trim();

				$.ajax({
					url: 'payment_cart.php',
					type: 'POST',
					contentType: 'application/json',
					data: JSON.stringify({
						cartItems: selectedOrders,
						user_id: userId,
						name: name,
						address: address,
						phone_number: phoneNumber,
						total_money: totalMoney
					}),
					success: function(response) {
						if (response.status !== 'success') {
							alert("Thanh toán thành công, chờ giao hàng.");
							//updateCartInterface(selectedOrders); // Xóa các đơn hàng khỏi giỏ hàng
						}
					}
				});
			});
			
			
			
			
		});

    </script>
</body>
</html>



<!--
// tính năng xóa sản phẩm khỏi giỏ hàng sau khi thanh toán 
$('#checkout-btn').on('click', function() {
				let selectedOrders = [];

				$('.select-product:checked').each(function() {
					let orderId = $(this).closest('tr').data('id');
					selectedOrders.push(orderId);
				});

				if (selectedOrders.length === 0) {
					alert("Vui lòng chọn ít nhất một sản phẩm để thanh toán.");
					return;
				}

				let userId = 1; // Thay đổi thành ID người dùng thực tế
				let name = 'Người Dùng'; // Thay đổi thành tên người dùng thực tế
				let address = 'Địa chỉ'; // Thay đổi thành địa chỉ thực tế
				let phoneNumber = 'Số điện thoại'; // Thay đổi thành số điện thoại thực tế
				let totalMoney = $('#selected-total').text().replace('đ', '').replace(/\./g, '').trim();

				$.ajax({
					url: 'payment_cart.php',
					type: 'POST',
					contentType: 'application/json',
					data: JSON.stringify({
						cartItems: selectedOrders,
						user_id: userId,
						name: name,
						address: address,
						phone_number: phoneNumber,
						total_money: totalMoney
					}),
					success: function(response) {
						if (response.status !== 'success') {
							alert("Thanh toán thành công, chờ giao hàng.");
							updateCartInterface(selectedOrders); // Xóa các đơn hàng khỏi giỏ hàng
							// Xóa các đơn hàng khỏi giỏ hàng
							$.ajax({
								url: 'remove_cart_items.php',
								type: 'POST',
								contentType: 'application/json',
								data: JSON.stringify({
									order_ids: selectedOrders
								}),
								
								success: function(removeResponse) {
									if (removeResponse.status === 'success') {
										// Cập nhật giao diện sau khi xóa sản phẩm
										selectedOrders.forEach(function(orderId) {
											$('tr[data-id="' + orderId + '"]').remove();
										});
										console.log("Đơn hàng đã được xóa khỏi giỏ hàng.");
									} else {
										console.log("Lỗi khi xóa đơn hàng khỏi giỏ hàng: " + removeResponse.message);
									}
								}
							});
						}
					}
				});
			});
			
			///////////////////////////////


        $(document).ready(function() {
            function updateTotal() {
                let total = 0;

                $('.quantity').each(function() {
                    let quantity = $(this).val();
                    let price = $(this).data('price');
                    let totalPrice = quantity * price;

                    $(this).closest('tr').find('.total-price').text(totalPrice.toLocaleString('vi-VN') + 'đ');

                    total += totalPrice;
                });

                $('#subtotal').text(total.toLocaleString('vi-VN') + 'đ');
                updateCheckoutSummary(); // Cập nhật thông tin thanh toán khi tổng cộng thay đổi
            }

            function updateCheckoutSummary() {
                let totalQuantity = 0;
                let totalAmount = 0;

                $('.select-product:checked').each(function() {
                    let productId = $(this).data('id');
                    let quantity = $(this).closest('tr').find('.quantity').val();
                    let price = $(this).data('price');
                    totalQuantity += parseInt(quantity);
                    totalAmount += quantity * price;
                });

                $('#selected-quantity').text(totalQuantity);
                $('#selected-total').text(totalAmount.toLocaleString('vi-VN') + 'đ');
            }
			
			function updateCartInterface(selectedOrders) {
				selectedOrders.forEach(function(orderId) {
					$('tr[data-id="' + orderId + '"]').remove(); // Xóa hàng sản phẩm khỏi bảng
				});

				// Kiểm tra nếu giỏ hàng trống thì hiển thị thông báo hoặc ẩn bảng giỏ hàng
				if ($('tbody tr').length === 0) {
					$('.cart-container').html('<p>Giỏ hàng của bạn hiện tại trống.</p>');
				}

				// Cập nhật lại tổng tiền sau khi xóa các sản phẩm
				updateTotal();
				updateCheckoutSummary();
			}


            $('.quantity').on('input', function() {
				updateTotal();

				let quantity = $(this).val();
				let orderId = $(this).data('id');

				$.ajax({
					url: 'update_cart.php',
					type: 'POST',
					data: {
						order_id: orderId,
						quantity: quantity
					},
					success: function(response) {
						console.log(response);
					}
				});
			});


            $('.delete-btn').on('click', function() {
                let orderId = $(this).data('id');
                let confirmDelete = confirm("Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng không?");

                if (confirmDelete) {
                    $.ajax({
						url: 'delete_cart.php',
						type: 'POST',
						data: {
							order_id: orderId
						},
						success: function(response) {
							console.log(response); // Xem phản hồi từ server
							$('tr[data-id="' + orderId + '"]').remove();
							updateTotal();
						},
						error: function(jqXHR, textStatus, errorThrown) {
							console.log(textStatus, errorThrown); // Xem lỗi nếu có
						}
					});
                }
            });

            $('.select-product').on('change', function() {
                updateCheckoutSummary();
            });

            $('#select-all-btn').on('click', function() {
                $('.select-product').prop('checked', true).trigger('change');
            });

            $('#deselect-all-btn').on('click', function() {
                $('.select-product').prop('checked', false).trigger('change');
            });


			/* Thanh toán sản phẩm */
			$('#checkout-btn').on('click', function() {
				let selectedOrders = [];

				$('.select-product:checked').each(function() {
					let orderId = $(this).closest('tr').data('id');
					selectedOrders.push(orderId);
				});

				if (selectedOrders.length === 0) {
					alert("Vui lòng chọn ít nhất một sản phẩm để thanh toán.");
					return;
				}

				let userId = 1; // Thay đổi thành ID người dùng thực tế
				let name = 'Người Dùng'; // Thay đổi thành tên người dùng thực tế
				let address = 'Địa chỉ'; // Thay đổi thành địa chỉ thực tế
				let phoneNumber = 'Số điện thoại'; // Thay đổi thành số điện thoại thực tế
				let totalMoney = $('#selected-total').text().replace('đ', '').replace(/\./g, '').trim();

				$.ajax({
					url: 'payment_cart.php',
					type: 'POST',
					contentType: 'application/json',
					data: JSON.stringify({
						cartItems: selectedOrders,
						user_id: userId,
						name: name,
						address: address,
						phone_number: phoneNumber,
						total_money: totalMoney
					}),
					success: function(response) {
						if (response.status !== 'success') {
							alert("Thanh toán thành công, chờ giao hàng.");
							updateCartInterface(selectedOrders); // Xóa các đơn hàng khỏi giỏ hàng
							// Xóa các đơn hàng khỏi giỏ hàng
							$.ajax({
								url: 'remove_cart_items.php',
								type: 'POST',
								contentType: 'application/json',
								data: JSON.stringify({
									order_ids: selectedOrders
								}),
								
								success: function(removeResponse) {
									if (removeResponse.status === 'success') {
										// Cập nhật giao diện sau khi xóa sản phẩm
										selectedOrders.forEach(function(orderId) {
											$('tr[data-id="' + orderId + '"]').remove();
										});
										console.log("Đơn hàng đã được xóa khỏi giỏ hàng.");
									} else {
										console.log("Lỗi khi xóa đơn hàng khỏi giỏ hàng: " + removeResponse.message);
									}
								}
							});
						}
					}
				});
			});



        });
		 		
-->