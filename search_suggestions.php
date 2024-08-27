<?php
$connect = mysqli_connect("localhost", "root", "", "shopthoitrang");

if (isset($_POST['query'])) {
    $searchQuery = mysqli_real_escape_string($connect, $_POST['query']);
    $query = "SELECT name FROM product WHERE name LIKE '%$searchQuery%' LIMIT 5";
    $result = mysqli_query($connect, $query);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<p class="suggestion-item">' . htmlspecialchars($row['name']) . '</p>';
        }
    } else {
        echo '<p class="suggestion-item">Không tìm thấy kết quả</p>';
    }
}
mysqli_close($connect);
?>
