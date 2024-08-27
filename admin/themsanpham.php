<?php
// Kết nối đến cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "shopthoitrang"; // Thay thế bằng tên cơ sở dữ liệu của bạn

$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Thiết lập múi giờ Hồ Chí Minh
date_default_timezone_set('Asia/Ho_Chi_Minh');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['them'])) {
    $danh_muc = $_POST['danh_muc'];
    $ten_san_pham = $_POST['ten_san_pham'];
    $gia_san_pham = $_POST['gia_san_pham'];
    $so_luong = $_POST['so_luong'];
    $mo_ta = $_POST['mo_ta'];
    $production_company = "Your Company"; // Thay thế bằng giá trị thực tế
    $create_at = date('Y-m-d H:i:s');
    $update_at = $create_at;

    // Tạo ID mới (id = id lớn nhất hiện có + 1)
    $sql_max_id = "SELECT MAX(CAST(id AS UNSIGNED)) AS max_id FROM product";
    $result = $conn->query($sql_max_id);
    $row = $result->fetch_assoc();
    $max_id = $row['max_id'];
    $new_id = ($max_id + 1); 
	 

    // Xử lý tải tệp ảnh lên
    $target_dir = "assets/img_product/";

    // Kiểm tra và tạo thư mục nếu chưa tồn tại
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $target_file = $target_dir . basename($_FILES["anh_dai_dien"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Kiểm tra xem tệp có phải là ảnh không
    if(!empty($_FILES["anh_dai_dien"]["tmp_name"])) {
        $check = getimagesize($_FILES["anh_dai_dien"]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        } else {
            $error_message = "File không phải là ảnh.";
            $uploadOk = 0;
        }
    } else {
        $error_message = "Không có tệp nào được tải lên hoặc đường dẫn tệp trống.";
        $uploadOk = 0;
    }

    // Kiểm tra xem tệp đã tồn tại chưa
    if (file_exists($target_file)) {
        $error_message = "Rất tiếc, tệp đã tồn tại.";
        $uploadOk = 0;
    }

    // Kiểm tra kích thước tệp
    if ($_FILES["anh_dai_dien"]["size"] > 500000) {
        $error_message = "Rất tiếc, tệp của bạn quá lớn.";
        $uploadOk = 0;
    }

    // Cho phép các định dạng tệp nhất định
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" 
    && $imageFileType != "gif" ) {
        $error_message = "Chỉ cho phép các tệp JPG, JPEG, PNG & GIF.";
        $uploadOk = 0;
    }

    // Kiểm tra và thực hiện tải lên tệp nếu không có lỗi
    if ($uploadOk && empty($error_message)) {
        if (move_uploaded_file($_FILES["anh_dai_dien"]["tmp_name"], $target_file)) {
            $anh_dai_dien = $target_file;
        } else {
            $error_message = "Rất tiếc, có lỗi xảy ra khi tải tệp của bạn.";
        }
    }

    // Nếu tải lên thành công và không có lỗi, thực hiện chèn vào cơ sở dữ liệu
    if ($uploadOk && empty($error_message)) {
        $sql = "INSERT INTO product (id, name, price, img_product, quantity, descrip, production_company, create_at, update_at, category_id, name_category) 
                VALUES ('$new_id', '$ten_san_pham', '$gia_san_pham', '$anh_dai_dien', '$so_luong', '$mo_ta', '$production_company', '$create_at', '$update_at', '$danh_muc', '$danh_muc')";

        if ($conn->query($sql) === TRUE) {
            $success_message = "Sản phẩm đã được thêm thành công!";
        } else {
            $error_message = "Lỗi: " . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Thời Trang</title>
    <link rel="stylesheet" href="themsanpham.css">
</head>
<body>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
    <?php
    if (isset($success_message)) {
        echo '<p style="color: green;" class="success-message">' . $success_message . '</p>';
        unset($success_message); 
    }
    if (isset($error_message)) {
        echo '<p style="color: green;" class="error-message">' . $error_message . '</p>';
        unset($error_message);  
    }
    ?>
        <div class="main">
            <h2>Sản Phẩm</h2>
            <div class="form-container">
                <div class="form-group">
                    <label for="danh-muc">Tên Danh Mục:</label>
                    <select id="danh-muc" name="danh_muc">
                        <option value="0">Chọn danh mục</option>
                        <option value="117">Đồ Gia Đình</option>
                        <option value="112">Quần Jean Nam</option>
                        <option value="114">Áo Sơ Mi Nam</option>
                        <option value="113">Áo Thun Nam</option>
                        <option value="123">Áo Ấm</option>
                        <option value="120">Đầm, Váy</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="anh-dai-dien">Ảnh Đại Diện:</label>
                    <input type="file" id="anh-dai-dien" name="anh_dai_dien">
                </div>
                <div class="form-group">
                    <label for="ten-san-pham">Tên Sản Phẩm:</label>
                    <input type="text" id="ten-san-pham" name="ten_san_pham" placeholder="Nhập Sản Phẩm">
                </div>
                <div class="form-group">
                    <label for="gia-san-pham">Giá Sản Phẩm:</label>
                    <input type="number" id="gia-san-pham" name="gia_san_pham" placeholder="Nhập Giá">
                </div>
                <div class="form-group">
                    <label for="so-luong">Số Lượng:</label>
                    <input type="number" id="so-luong" name="so_luong" value="0">
                </div>
            </div>
            <div class="description">
                <label for="mo-ta">Mô tả thêm:</label>
                <textarea id="mo-ta" name="mo_ta" placeholder="Nhập mô tả"></textarea>
                <button type="submit" name="them">Thêm</button>
            </div>
        </div>
    </form>
</body>
</html>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Thời Trang</title>
    <link rel="stylesheet" href="themsanpham.css">
</head>
<body>
	
	 
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
	<?php
    if (isset($success_message)) {
        echo '<p style="color: green;" class="success-message">' . $success_message . '</p>';
        unset($success_message); 
    }
    if (isset($error_message)) {
        echo '<p style="color: green;" class="error-message">' . $error_message . '</p>';
        unset($error_message);  
    }
    ?>
		<div class="main">
			<h2>Sản Phẩm</h2>
			<div class="form-container">
				<div class="form-group">
					<label for="danh-muc">Tên Danh Mục:</label>
					<select id="danh-muc" name="danh_muc">
						<option value="0">Chọn danh mục</option>
						<option value="117">Đồ Gia Đình</option>
						<option value="112">Quần Jean Nam</option>
						<option value="114">Áo Sơ Mi Nam</option>
						<option value="113">Áo Thun Nam</option>
						<option value="123">Áo Ấm</option>
						<option value="120">Đầm, Váy</option>
					</select>
				</div>
				<div class="form-group">
					<label for="anh-dai-dien">Ảnh Đại Diện:</label>
					<input type="file" id="anh-dai-dien" name="anh_dai_dien">
				</div>
				<div class="form-group">
					<label for="ten-san-pham">Tên Sản Phẩm:</label>
					<input type="text" id="ten-san-pham" name="ten_san_pham" placeholder="Nhập Sản Phẩm">
				</div>
				<div class="form-group">
					<label for="gia-san-pham">Giá Sản Phẩm:</label>
					<input type="number" id="gia-san-pham" name="gia_san_pham" placeholder="Nhập Giá">
				</div>
				<div class="form-group">
					<label for="so-luong">Số Lượng:</label>
					<input type="number" id="so-luong" name="so_luong" value="0">
				</div>
			</div>
			<div class="description">
				<label for="mo-ta">Mô tả thêm:</label>
				<textarea id="mo-ta" name="mo_ta" placeholder="Nhập mô tả"></textarea>
				<button type="submit" name="them">Thêm</button>
			</div>
		</div>
	</form>
 
</body>
</html>
