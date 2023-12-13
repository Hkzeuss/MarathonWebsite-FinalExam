<?php
    session_start();

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "user";

    // Kết nối đến MySQL
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Kiểm tra kết nối
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Kiểm tra xác nhận xóa
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $user_id_to_delete = $_GET['id'];

        // Chuẩn bị và thực hiện truy vấn SQL để xóa dữ liệu từ bảng participants
        $delete_sql = "DELETE FROM participants WHERE passport = '$user_id_to_delete'";

        if ($conn->query($delete_sql) === TRUE) {
            // Xóa thành công, chuyển hướng về trang user.php
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Error deleting record: " . $conn->error;
        }
    }

    // Đóng kết nối
    $conn->close();
?>