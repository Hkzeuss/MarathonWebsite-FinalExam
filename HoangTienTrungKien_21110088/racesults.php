<?php
// Kết nối đến cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "achievements";

$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối đến cơ sở dữ liệu thất bại: " . $conn->connect_error);
}

// Truy vấn SQL để lấy toàn bộ dữ liệu từ bảng participants
$sql = "SELECT * FROM participants";
$result = $conn->query($sql);
// Đóng kết nối
$conn->close();
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
    <link rel="stylesheet" href="/HoangTienTrungKien_21110088/CSS/racesults.css">
    <title>RACE RESULTS</title>
  
</head>
<body class="p-3 m-0 border-0 bd-example ">
  <nav class="navbar navbar-expand-lg ">
    <div class="container-fluid">
      <h2 class="navbar-brand ms-5 font-weight01"> <a class="no-underline" style="color: black;" href="/HoangTienTrungKien_21110088/home.html">MARATHON HANOI</a></h2>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav offset-4 mb-2 mb-lg-0 font-weight01 ">

          <li class="nav-item">
            <a class="nav-link active " aria-current="page" href="homefanpage.html">Home</a>
          </li>

          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle show" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="true">Event Information</a>
            <ul class="dropdown-menu show" data-bs-popper="static">
              <li><a class="dropdown-item" href="/HoangTienTrungKien_21110088/racesults.html">RACE RESULTS</a></li>
              <li><a class="dropdown-item" href="#">BECOME EXHIBITION REGIONAL PARTNER</a></li>
              <li><a class="dropdown-item" href="#">EVENT SCHEDULE</a></li>
              <li><a class="dropdown-item" href="#">RACE KIT COLLECTION PROCEDURE</a></li>
              <li><a class="dropdown-item" href="#">RULES & REGULATIONS</a></li>
            </ul>
          </li>

          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle show" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="true">Sponsors&Partners</a>
            <ul class="dropdown-menu show" data-bs-popper="static">
              <li><a class="dropdown-item" href="#">PARTNER</a></li>
              <li><a class="dropdown-item" href="#">BECOME EXHIBITION REGIONAL PARTNER</a></li>
            </ul>
          </li>

      </div>
    </div>
  </nav>

<!------------------------------------------------------------------------------------------------------->
<div class="container mx-auto" style ="border: 5px solid black;">
            <?php
                if ($result->num_rows > 0) {
                    // Create an array to store unique marathon IDs
                    $uniqueevent_names = array();

                    // Group rows by event_name
                    while ($row = $result->fetch_assoc()) {
                        $event_name = $row["event_name"];
                        $uniqueevent_names[$event_name][] = $row;
                    }

                    // Iterate through unique event_name and display tables
                    foreach ($uniqueevent_names as $event_name => $rows) {
                        $standing = 1;
                        echo "<h2 style='text-align: center; color: white; background-color: #3498db;'>Marathon Name: $event_name</h2>";
                        echo "<table class='table table-bordered border-primary table-group-divider table-striped table-hover' style='text-align: center;'>";
                        echo "<thead >
                                <tr>
                                    <th>Rank</th>
                                    <th>Full Name</th>
                                    <th>Time ReCord</th>
                                </tr>
                             </thead>";
                        echo "<tbody class='border-primary table-group-divider'>";

                        foreach ($rows as $row) {
                            echo "<tr>";
                            echo "<td>" . $standing . "</td>";
                            echo "<td>" . $row["fullName"] . "</td>";
                            echo "<td>" . $row["time_record"] . "</td>";
                            echo "<a href='delete_achievements.php?id=" . $row["time_record"] . "' onclick='return confirm(\"Are you sure?\");' style='text-align: center; display: block;'>
                                    <i class='bx bxs-trash'></i>
                                </a>";
                            echo "</td>";
                            echo "</tr>";
                            $standing++;
                        }

                        echo "</tbody></table>";
                        echo "<br><hr><br>";
                        
                    }
                    
                } else {
                    echo "There are no recorded achievements yet.";
                }
            ?>
        </div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>   
</body>
</html>