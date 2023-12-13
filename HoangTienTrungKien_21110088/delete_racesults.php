<?php
    session_start();

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "achievements";

    // Kết nối đến MySQL
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Kiểm tra kết nối
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Kiểm tra xác nhận xóa
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $time_record_to_delete = $_GET['id'];

        // Chuẩn bị và thực hiện truy vấn SQL để xóa dữ liệu từ bảng participants
        $delete_sql = "DELETE FROM participants WHERE time_record = '$time_record_to_delete'";

        if ($conn->query($delete_sql) === TRUE) {
            // Xóa thành công, chuyển hướng về trang user.php
            header("Location: racesults_management_admin.php");
            exit();
        } else {
            echo "Error deleting record: " . $conn->error;
        }
    }
    // Đóng kết nối
    $conn->close();
?>