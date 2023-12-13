<?php

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "event";

    // Kết nối đến MySQL
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Kiểm tra kết nối
    if ($conn->connect_error) {
        die("Connection failed:" . $conn->connect_error);
    }

    // Xử lý khi form được submit
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Hàm kiểm tra rỗng
        function isEmpty($value) {
            return empty($value) || trim($value) === '';
        }
        $marathon_ID = $_POST["marathon_ID"];
        $event_name = $_POST["event_name"];
        $registration_deadline = $_POST["registration_deadline"];
        $competition_day = $_POST["competition_day"];

        // Kiểm tra các trường không được để trống
        if (isEmpty($marathon_ID) || isEmpty($event_name) || isEmpty($registration_deadline) || isEmpty($competition_day)) {
            echo "Please complete all information!";
            exit();
        }

        // Kiểm tra xem marathon_ID đã tồn tại trong cơ sở dữ liệu chưa
        $check_marathon_id_sql = "SELECT * FROM participants WHERE marathon_ID = '$marathon_ID'";
        $result_marathon_id = $conn->query($check_marathon_id_sql);
        if ($result_marathon_id->num_rows > 0) {
            echo "Marathon ID already exists!";
            exit();
        }

        // Kiểm tra xem event_name đã tồn tại trong cơ sở dữ liệu chưa
        $check_event_name_sql = "SELECT * FROM participants WHERE event_name = '$event_name'";
        $result_event_name = $conn->query($check_event_name_sql);
        if ($result_event_name->num_rows > 0) {
            echo "Event Name already exists!";
            exit();
        }

        // Kiểm tra xem ngày kết thúc đăng ký lớn hơn ngày hôm nay
        if (strtotime($registration_deadline) <= time()) {
            echo "Registration deadline must be greater than today!";
            exit();
        }

        // Kiểm tra xem ngày kết thúc lớn hơn hoặc bằng ngày bắt đầu
        if (strtotime($competition_day) < strtotime($registration_deadline)) {
            echo "The exam date must be greater than the registration due date!";
            exit();
        }

        // Chuẩn bị và thực hiện truy vấn SQL để chèn dữ liệu vào bảng participants
        $sql = "INSERT INTO participants (marathon_ID, event_name, registration_deadline, competition_day)
                VALUES ('$marathon_ID', '$event_name', '$registration_deadline', '$competition_day')";

        if ($conn->query($sql) === TRUE) {
            header("Location: envent_management_admin.php");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }


    }
        // Lấy ngày hiện tại
        $current_date = date("Y-m-d");

        // Thực hiện truy vấn SQL để lấy thông tin của toàn bộ sự kiện
        $sql = "SELECT * FROM participants WHERE registration_deadline >= '$current_date' ORDER BY registration_deadline";
        $result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="/HoangTienTrungKien_21110088/CSS/envent_management_admin.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Event Management</title>
  
</head>
<body class="p-3 m-0 border-0 bd-example ">
    <nav class="navbar bg-body-tertiary fixed-top">
        <div class="container-fluid">
          <a class="navbar-brand" href="#"></a>
          <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
            <div class="offcanvas-header">
              <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Admin</h5>
              <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
              <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                <li class="nav-item">
                  <a class="nav-link active" aria-current="page" href="homefanpage.html">Home</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="homefanpage.html">Log Out</a>
                </li>
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Management
                  </a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="envent_management_admin.php">Event Management</a></li>
                    <li><a class="dropdown-item" href="#">Registration Management</a></li>
                    <li><a class="dropdown-item" href="#">User Management</a></li>
                  </ul>
                </li>
              </ul>
            </div>
          </div>
        </div>
    </nav>
    <br>
    <br>

    <header>
        <div class="navbar navbar-expand-lg ">
            <div class="container-fluid">
                <h2 class="navbar-brand mx-auto">Create a Marathon</h2>
                <button type="button" id="addButton" class="btn btn-success d-block mx-auto"> Add Event</button>
            </div>
        </div>

        <div id="addForm" class="container mx-auto" style="position: relative; display: none;">
            <form class="form_register mx-auto" action="envent_management_admin.php" method="post">
                <div class="row">
                  <div class="col">
                    <label for="marathon_ID">Marathon ID:</label><br>
                    <input type="number" class="form-control" name="marathon_ID" required>
                  </div>
                  <div class="col">
                    <label for="event_name">Event name:</label><br>
                    <input type="text" class="form-control" name="event_name" required>
                  </div>
                </div>           

                <div class="row">
                      <div class="col">
                        <label for="registration_deadline">Registration Deadline:</label><br>
                        <input type="date" class="form-control" name="registration_deadline" required>
                      </div>
                      <div class="col">
                        <label for="competition_day">Competition Day:</label><br>
                        <input type="date" class="form-control" name="competition_day" required>
                      </div>
                </div> 
                    <hr>
                    <div class="mx-auto">
                        <button type="submit" class="btn btn-primary d-block mx-auto">Create Event</button>
                    </div>
             </form>
        </div>

        <script>
            document.getElementById("addButton").addEventListener("click", function() {
                // Hiển thị hoặc ẩn form khi nút "Thêm" được nhấp
                var form = document.getElementById("addForm");
                form.style.display = (form.style.display === "none" || form.style.display === "") ? "block" : "none";
            });
        </script>

        <div class="container mx-auto">
            <?php
            if ($result->num_rows > 0) {
                echo "<table class='table' style='text-align: center;'>";
                echo "<thead><tr><th>Marathon ID</th><th>Event Name</th><th>Registration Deadline</th><th>Competition Day</th><th>Action</th></tr></thead>";
                echo "<tbody>";
                while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["marathon_ID"] . "</td>";
                echo "<td style='text-align: left; display: block;'>" . $row["event_name"] . "</td>";
                echo "<td>" . $row["registration_deadline"] . "</td>";
                echo "<td>" . $row["competition_day"] . "</td>";
                echo "<td>";
                echo "<a href='delete_event.php?id=" . $row["marathon_ID"] . "' onclick='return confirm(\"Are you sure?\");'><i class='bx bxs-trash'></i></a>";
                echo "</td>";
                echo "</tr>";
            }
                echo "</tbody></table>";
            } else {
                echo "There are no events.";
            }

            // Đóng kết nối
            $conn->close();
            ?>
        </div>
    </header>    
<!------------------------------------------------------------------------------------------------------->

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>   
</body>
</html>