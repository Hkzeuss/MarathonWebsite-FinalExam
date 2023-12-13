<?php
// Kết nối đến cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "marathon_registration";

$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối đến cơ sở dữ liệu thất bại: " . $conn->connect_error);
}

// Tạo cơ sở dữ liệu chính
$sql = "CREATE DATABASE IF NOT EXISTS marathon_registration";
if ($conn->query($sql) === TRUE) {
    echo "Primary database created successfully!\n";
} else {
    echo "Error creating main database! " . $conn->error . "\n";
}

// Chọn cơ sở dữ liệu chính
$conn->select_db("marathon_registration");
//-----------------------------------------------------user------------------------------------------------------------
$sql = "CREATE DATABASE IF NOT EXISTS user";
if ($conn->query($sql) === TRUE) {
    echo "Created child database (user) successfully\n";
} else {
    echo "Error creating child database! " . $conn->error . "\n";
}

// Chọn cơ sở dữ liệu con
$conn->select_db("user");
// Xử lý dữ liệu từ biểu mẫu khi biểu mẫu được submit
$sql = "CREATE TABLE IF NOT EXISTS participants (

    fullName VARCHAR(255) NOT NULL,
    nationality VARCHAR(255) NOT NULL,
    sex VARCHAR(10) NOT NULL,
    dob INT NOT NULL,
    email VARCHAR(100) NOT NULL,
    phoneNumber INT NOT NULL,
    address TEXT NOT NULL,
    event_name VARCHAR(100) NOT NULL,
    passport VARCHAR(100) NOT NULL
)";
if ($conn->query($sql) === TRUE) {
    echo "Successfully created participants table\n";
} else {
    echo "Error when creating participants table!" . $conn->error . "\n";
}
// ------------------------------------------------------------------------------------------------------------------
$sql = "CREATE DATABASE IF NOT EXISTS event";
if ($conn->query($sql) === TRUE) {
    echo "Created child database (event) successfully\n";
} else {
    echo "Error creating child database! " . $conn->error . "\n";
}

// Chọn cơ sở dữ liệu con
$conn->select_db("event");

// Tạo bảng participants trong cơ sở dữ liệu con
$sql = "CREATE TABLE IF NOT EXISTS participants (
    marathon_ID INT NOT NULL,
    event_name VARCHAR(255) NOT NULL,
    registration_deadline DATE NOT NULL, 
    competition_day DATE NOT NULL
)";
if ($conn->query($sql) === TRUE) {
    echo "Successfully created participants table\n";
} else {
    echo "Error creating participants table: " . $conn->error . "\n";}

//----------------------------------------------------------------------------------------------------------------------------
$sql = "CREATE DATABASE IF NOT EXISTS achievements";
if ($conn->query($sql) === TRUE) {
    echo "Created child database (achievements) successfully\n";
} else {
    echo "Error creating child database! " . $conn->error . "\n";
}

// Chọn cơ sở dữ liệu con
$conn->select_db("achievements");

// Tạo bảng participants trong cơ sở dữ liệu con
$sql = "CREATE TABLE IF NOT EXISTS participants (
    ranks INT NOT NULL,
    event_name VARCHAR(255) NOT NULL,
    fullName VARCHAR(255) NOT NULL, 
    time_record VARCHAR(255) NOT NULL,
    nationality VARCHAR(255) NOT NULL

)";
if ($conn->query($sql) === TRUE) {
    echo "Successfully created participants table\n";
} else {
    echo "Error creating participants table: " . $conn->error . "\n";}
    
// Đóng kết nối
$conn->close();
?>
