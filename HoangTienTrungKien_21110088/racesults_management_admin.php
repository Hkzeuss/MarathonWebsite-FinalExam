<?php
    session_start();

    // Check if the logout button is clicked
    if (isset($_POST["logout"])) {
        // Destroy the session
        session_destroy();

        // Redirect to the login page
        header("Location: admin.php");
        exit();
    }

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

    // event
    $servername_event = "localhost";
    $username_event = "root";
    $password_event = "";
    $dbname_event = "event";

    // Kết nối đến MySQL - Database event
    $conn_event = new mysqli($servername_event, $username_event, $password_event, $dbname_event);

    // Kiểm tra kết nối
    if ($conn_event->connect_error) {
        die("Connection failed:" . $conn_event->connect_error);
    }

    // Truy vấn danh sách sự kiện từ Database event
    $query_events = "SELECT event_name FROM participants";
    $result_events = $conn_event->query($query_events);

    // Mảng để lưu trữ danh sách sự kiện
    $events = array();

    // Lặp qua kết quả và lưu vào mảng
    while ($row = $result_events->fetch_assoc()) {
        $events[] = $row['event_name'];
    }

    // Xử lý khi form được submit
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Hàm kiểm tra rỗng
        function isEmpty($value) {
            return empty($value) || trim($value) === '';
        }

        $fullName = $_POST["fullName"];
        $event_name = $_POST["event_name"];
        $time_record = $_POST["time_record"];
        $nationality = $_POST["nationality"];

        // Kiểm tra các trường không được để trống
        if ( isEmpty($fullName) ||isEmpty($event_name) || isEmpty($time_record) || isEmpty($nationality)) {
            echo "Please complete all information!";
            exit();
        }

        // Kiểm tra xem thông tin đăng ký đã tồn tại hay chưa
        $checkQuery = "SELECT * FROM participants WHERE
                        fullName = '$fullName' AND
                        nationality = '$nationality' AND
                        event_name = '$event_name'";

        $checkResult = $conn->query($checkQuery);

        if ($checkResult->num_rows > 0) {
            // Thông tin đăng ký đã tồn tại
            echo "You have registered for the contest!";
            exit();
        }

         // Thực hiện truy vấn để xác định rank
         $rankQuery = "SELECT COUNT(*) + 1 AS ranks FROM participants 
         WHERE event_name = '$event_name' AND time_record <= '$time_record'
         ORDER BY time_record ASC";
        $rankResult = $conn->query($rankQuery);
 
     if ($rankResult === FALSE) {
         echo "Error in rank query: " . $conn->error;
         exit();
     }
 
     if ($rankResult->num_rows > 0) {
         $rankRow = $rankResult->fetch_assoc();
         $ranks = $rankRow['ranks'];
     
         // Cập nhật ranks cho tất cả các người tham gia có thời gian nhỏ hơn hoặc bằng thời gian mới
         $updateranksQuery = "UPDATE participants SET ranks = ranks + 1 
             WHERE event_name = '$event_name' AND time_record <= '$time_record'";
         $conn->query($updateranksQuery);
     } else {
         $ranks = 1;
     } 

        // Chuẩn bị và thực hiện truy vấn SQL để chèn dữ liệu vào bảng participants
        $sql = "INSERT INTO participants (event_name, fullName, time_record, nationality, ranks)
                VALUES ('$event_name', '$fullName', '$time_record', '$nationality' , '$ranks')";

        if ($conn->query($sql) === TRUE) {
            // Đăng ký thành công, chuyển hướng về trang achievements.php
            header("Location: racesults_management_admin.php");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    // Truy vấn SQL để lấy toàn bộ dữ liệu từ bảng participants
    $sql = "SELECT * FROM participants ORDER BY time_record ASC";
    $result = $conn->query($sql);

    // Đóng kết nối
    $conn->close();
    $conn_event->close();
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
    <title>Racesults Management</title>
  
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
                  <a class="nav-link active" aria-current="page" href="dashboard.php">Home</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#">Log Out</a>
                </li>
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Management
                  </a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">Event Management</a></li>
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
                <h2 class="navbar-brand mx-auto">Enter and Display Race Results List.</h2>
                
                <button type="button" id="addButton" class="btn btn-success d-block mx-auto">Add Race Results</button>
            </div>
        </div>
        
        <div id="addForm" class="container mx-auto"  style="position: relative; display: none;">
            <form class="form-register-3 mx-auto" action="racesults_management_admin.php" method="post">


                <div class="row">
                    <div class="col">
                    <label for="event_name">Select Event:</label>
                    <select name="event_name" required>
                        <option value="" disabled selected>Select Event</option>
                        <?php
                            foreach ($events as $event) {
                                echo "<option value=\"$event\">$event</option>";
                            }
                        ?>
                        </select>
                    </div>
                    
                   
                </div>

                <div class="row">
                  <div class="col">
                    <label for="fullName">Enter Full Name:</label><br>
                    <input type="fullName" class="form-control" name="fullName" required>
                  </div>
                  <div class="col">
                    <label for="time_record">Enter Time Record:</label><br>
                    <input type="text" class="form-control" name="time_record" pattern="([0-9]+):([0-5][0-9]):[0-5][0-9].[0-9]{1,3}" placeholder="--:--:--.---"required>
                  </div>
                </div>

                <div class="row">
                  <div class="col">
                  <label for="nationality">Quốc tịch / Nationality *</label><br>
                  <select class="form-select" id="nationalitySelect" name="nationality" required></select>
                  </div>
                  <div class="col">
                  </div>
                </div>


                <hr>
                <div class="mx-auto">
                    <button type="submit" class="btn btn-primary d-block mx-auto">Add Race Results</button>
                </div>
            </form>
        </div>
<br>
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            var nationalitySelect = document.getElementById("nationalitySelect");
            var sexSelect = document.getElementById("sexSelect");
        
            // Danh sách mã quốc gia ISO 3166-1 alpha-2 và alpha-3
            var countries = [
            { name: "Afghanistan", alpha2: "AF", alpha3: "AFG" },
            { name: "Albania", alpha2: "AL", alpha3: "ALB" },
            { name: "Algeria", alpha2: "DZ", alpha3: "DZA" },
            { name: "Andorra", alpha2: "AD", alpha3: "AND" },
            { name: "Angola", alpha2: "AO", alpha3: "AGO" },
            { name: "Antigua and Barbuda", alpha2: "AG", alpha3: "ATG" },
            { name: "Argentina", alpha2: "AR", alpha3: "ARG" },
            { name: "Armenia", alpha2: "AM", alpha3: "ARM" },
            { name: "Australia", alpha2: "AU", alpha3: "AUS" },
            { name: "Austria", alpha2: "AT", alpha3: "AUT" },
            { name: "Azerbaijan", alpha2: "AZ", alpha3: "AZE" },
            { name: "Bahamas", alpha2: "BS", alpha3: "BHS" },
            { name: "Bahrain", alpha2: "BH", alpha3: "BHR" },
            { name: "Bangladesh", alpha2: "BD", alpha3: "BGD" },
            { name: "Barbados", alpha2: "BB", alpha3: "BRB" },
            { name: "Belarus", alpha2: "BY", alpha3: "BLR" },
            { name: "Belgium", alpha2: "BE", alpha3: "BEL" },
            { name: "Belize", alpha2: "BZ", alpha3: "BLZ" },
            { name: "Benin", alpha2: "BJ", alpha3: "BEN" },
            { name: "Bhutan", alpha2: "BT", alpha3: "BTN" },
            { name: "Bolivia", alpha2: "BO", alpha3: "BOL" },
            { name: "Bosnia and Herzegovina", alpha2: "BA", alpha3: "BIH" },
            { name: "Botswana", alpha2: "BW", alpha3: "BWA" },
            { name: "Brazil", alpha2: "BR", alpha3: "BRA" },
            { name: "Brunei", alpha2: "BN", alpha3: "BRN" },
            { name: "Bulgaria", alpha2: "BG", alpha3: "BGR" },
            { name: "Burkina Faso", alpha2: "BF", alpha3: "BFA" },
            { name: "Burundi", alpha2: "BI", alpha3: "BDI" },
            { name: "Cabo Verde", alpha2: "CV", alpha3: "CPV" },
            { name: "Cambodia", alpha2: "KH", alpha3: "KHM" },
            { name: "Cameroon", alpha2: "CM", alpha3: "CMR" },
            { name: "Canada", alpha2: "CA", alpha3: "CAN" },
            { name: "Central African Republic", alpha2: "CF", alpha3: "CAF" },
            { name: "Chad", alpha2: "TD", alpha3: "TCD" },
            { name: "Chile", alpha2: "CL", alpha3: "CHL" },
            { name: "China", alpha2: "CN", alpha3: "CHN" },
            { name: "Colombia", alpha2: "CO", alpha3: "COL" },
            { name: "Comoros", alpha2: "KM", alpha3: "COM" },
            { name: "Congo", alpha2: "CG", alpha3: "COG" },
            { name: "Costa Rica", alpha2: "CR", alpha3: "CRI" },
            { name: "Côte d'Ivoire", alpha2: "CI", alpha3: "CIV" },
            { name: "Croatia", alpha2: "HR", alpha3: "HRV" },
            { name: "Cuba", alpha2: "CU", alpha3: "CUB" },
            { name: "Cyprus", alpha2: "CY", alpha3: "CYP" },
            { name: "Czech Republic", alpha2: "CZ", alpha3: "CZE" },
            { name: "Denmark", alpha2: "DK", alpha3: "DNK" },
            { name: "Djibouti", alpha2: "DJ", alpha3: "DJI" },
            { name: "Dominica", alpha2: "DM", alpha3: "DMA" },
            { name: "Dominican Republic", alpha2: "DO", alpha3: "DOM" },
            { name: "Ecuador", alpha2: "EC", alpha3: "ECU" },
            { name: "Egypt", alpha2: "EG", alpha3: "EGY" },
            { name: "El Salvador", alpha2: "SV", alpha3: "SLV" },
            { name: "Equatorial Guinea", alpha2: "GQ", alpha3: "GNQ" },
            { name: "Eritrea", alpha2: "ER", alpha3: "ERI" },
            { name: "Estonia", alpha2: "EE", alpha3: "EST" },
            { name: "Eswatini", alpha2: "SZ", alpha3: "SWZ" },
            { name: "Ethiopia", alpha2: "ET", alpha3: "ETH" },
            { name: "Fiji", alpha2: "FJ", alpha3: "FJI" },
            { name: "Finland", alpha2: "FI", alpha3: "FIN" },
            { name: "France", alpha2: "FR", alpha3: "FRA" },
            { name: "Gabon", alpha2: "GA", alpha3: "GAB" },
            { name: "Gambia", alpha2: "GM", alpha3: "GMB" },
            { name: "Georgia", alpha2: "GE", alpha3: "GEO" },
            { name: "Germany", alpha2: "DE", alpha3: "DEU" },
            { name: "Ghana", alpha2: "GH", alpha3: "GHA" },
            { name: "Greece", alpha2: "GR", alpha3: "GRC" },
            { name: "Grenada", alpha2: "GD", alpha3: "GRD" },
            { name: "Guatemala", alpha2: "GT", alpha3: "GTM" },
            { name: "Guinea", alpha2: "GN", alpha3: "GIN" },
            { name: "Guinea-Bissau", alpha2: "GW", alpha3: "GNB" },
            { name: "Guyana", alpha2: "GY", alpha3: "GUY" },
            { name: "Haiti", alpha2: "HT", alpha3: "HTI" },
            { name: "Honduras", alpha2: "HN", alpha3: "HND" },
            { name: "Hungary", alpha2: "HU", alpha3: "HUN" },
            { name: "Iceland", alpha2: "IS", alpha3: "ISL" },
            { name: "India", alpha2: "IN", alpha3: "IND" },
            { name: "Indonesia", alpha2: "ID", alpha3: "IDN" },
            { name: "Iran", alpha2: "IR", alpha3: "IRN" },
            { name: "Iraq", alpha2: "IQ", alpha3: "IRQ" },
            { name: "Ireland", alpha2: "IE", alpha3: "IRL" },
            { name: "Israel", alpha2: "IL", alpha3: "ISR" },
            { name: "Italy", alpha2: "IT", alpha3: "ITA" },
            { name: "Jamaica", alpha2: "JM", alpha3: "JAM" },
            { name: "Japan", alpha2: "JP", alpha3: "JPN" },
            { name: "Jordan", alpha2: "JO", alpha3: "JOR" },
            { name: "Kazakhstan", alpha2: "KZ", alpha3: "KAZ" },
            { name: "Kenya", alpha2: "KE", alpha3: "KEN" },
            { name: "Kiribati", alpha2: "KI", alpha3: "KIR" },
            { name: "Kuwait", alpha2: "KW", alpha3: "KWT" },
            { name: "Kyrgyzstan", alpha2: "KG", alpha3: "KGZ" },
            { name: "Laos", alpha2: "LA", alpha3: "LAO" },
            { name: "Latvia", alpha2: "LV", alpha3: "LVA" },
            { name: "Lebanon", alpha2: "LB", alpha3: "LBN" },
            { name: "Lesotho", alpha2: "LS", alpha3: "LSO" },
            { name: "Liberia", alpha2: "LR", alpha3: "LBR" },
            { name: "Libya", alpha2: "LY", alpha3: "LBY" },
            { name: "Liechtenstein", alpha2: "LI", alpha3: "LIE" },
            { name: "Lithuania", alpha2: "LT", alpha3: "LTU" },
            { name: "Luxembourg", alpha2: "LU", alpha3: "LUX" },
            { name: "Madagascar", alpha2: "MG", alpha3: "MDG" },
            { name: "Malawi", alpha2: "MW", alpha3: "MWI" },
            { name: "Malaysia", alpha2: "MY", alpha3: "MYS" },
            { name: "Maldives", alpha2: "MV", alpha3: "MDV" },
            { name: "Mali", alpha2: "ML", alpha3: "MLI" },
            { name: "Malta", alpha2: "MT", alpha3: "MLT" },
            { name: "Marshall Islands", alpha2: "MH", alpha3: "MHL" },
            { name: "Mauritania", alpha2: "MR", alpha3: "MRT" },
            { name: "Mauritius", alpha2: "MU", alpha3: "MUS" },
            { name: "Mexico", alpha2: "MX", alpha3: "MEX" },
            { name: "Micronesia", alpha2: "FM", alpha3: "FSM" },
            { name: "Moldova", alpha2: "MD", alpha3: "MDA" },
            { name: "Monaco", alpha2: "MC", alpha3: "MCO" },
            { name: "Mongolia", alpha2: "MN", alpha3: "MNG" },
            { name: "Montenegro", alpha2: "ME", alpha3: "MNE" },
            { name: "Morocco", alpha2: "MA", alpha3: "MAR" },
            { name: "Mozambique", alpha2: "MZ", alpha3: "MOZ" },
            { name: "Myanmar", alpha2: "MM", alpha3: "MMR" },
            { name: "Namibia", alpha2: "NA", alpha3: "NAM" },
            { name: "Nauru", alpha2: "NR", alpha3: "NRU" },
            { name: "Nepal", alpha2: "NP", alpha3: "NPL" },
            { name: "Netherlands", alpha2: "NL", alpha3: "NLD" },
            { name: "New Zealand", alpha2: "NZ", alpha3: "NZL" },
            { name: "Nicaragua", alpha2: "NI", alpha3: "NIC" },
            { name: "Niger", alpha2: "NE", alpha3: "NER" },
            { name: "Nigeria", alpha2: "NG", alpha3: "NGA" },
            { name: "North Korea", alpha2: "KP", alpha3: "PRK" },
            { name: "North Macedonia", alpha2: "MK", alpha3: "MKD" },
            { name: "Norway", alpha2: "NO", alpha3: "NOR" },
            { name: "Oman", alpha2: "OM", alpha3: "OMN" },
            { name: "Pakistan", alpha2: "PK", alpha3: "PAK" },
            { name: "Palau", alpha2: "PW", alpha3: "PLW" },
            { name: "Panama", alpha2: "PA", alpha3: "PAN" },
            { name: "Papua New Guinea", alpha2: "PG", alpha3: "PNG" },
            { name: "Paraguay", alpha2: "PY", alpha3: "PRY" },
            { name: "Peru", alpha2: "PE", alpha3: "PER" },
            { name: "Philippines", alpha2: "PH", alpha3: "PHL" },
            { name: "Poland", alpha2: "PL", alpha3: "POL" },
            { name: "Portugal", alpha2: "PT", alpha3: "PRT" },
            { name: "Qatar", alpha2: "QA", alpha3: "QAT" },
            { name: "Romania", alpha2: "RO", alpha3: "ROU" },
            { name: "Russia", alpha2: "RU", alpha3: "RUS" },
            { name: "Rwanda", alpha2: "RW", alpha3: "RWA" },
            { name: "Saint Kitts and Nevis", alpha2: "KN", alpha3: "KNA" },
            { name: "Saint Lucia", alpha2: "LC", alpha3: "LCA" },
            { name: "Saint Vincent and the Grenadines", alpha2: "VC", alpha3: "VCT" },
            { name: "Samoa", alpha2: "WS", alpha3: "WSM" },
            { name: "San Marino", alpha2: "SM", alpha3: "SMR" },
            { name: "Sao Tome and Principe", alpha2: "ST", alpha3: "STP" },
            { name: "Saudi Arabia", alpha2: "SA", alpha3: "SAU" },
            { name: "Senegal", alpha2: "SN", alpha3: "SEN" },
            { name: "Serbia", alpha2: "RS", alpha3: "SRB" },
            { name: "Seychelles", alpha2: "SC", alpha3: "SYC" },
            { name: "Sierra Leone", alpha2: "SL", alpha3: "SLE" },
            { name: "Singapore", alpha2: "SG", alpha3: "SGP" },
            { name: "Slovakia", alpha2: "SK", alpha3: "SVK" },
            { name: "Slovenia", alpha2: "SI", alpha3: "SVN" },
            { name: "Solomon Islands", alpha2: "SB", alpha3: "SLB" },
            { name: "Somalia", alpha2: "SO", alpha3: "SOM" },
            { name: "South Africa", alpha2: "ZA", alpha3: "ZAF" },
            { name: "South Korea", alpha2: "KR", alpha3: "KOR" },
            { name: "South Sudan", alpha2: "SS", alpha3: "SSD" },
            { name: "Spain", alpha2: "ES", alpha3: "ESP" },
            { name: "Sri Lanka", alpha2: "LK", alpha3: "LKA" },
            { name: "Sudan", alpha2: "SD", alpha3: "SDN" },
            { name: "Suriname", alpha2: "SR", alpha3: "SUR" },
            { name: "Sweden", alpha2: "SE", alpha3: "SWE" },
            { name: "Switzerland", alpha2: "CH", alpha3: "CHE" },
            { name: "Syria", alpha2: "SY", alpha3: "SYR" },
            { name: "Taiwan", alpha2: "TW", alpha3: "TWN" },
            { name: "Tajikistan", alpha2: "TJ", alpha3: "TJK" },
            { name: "Tanzania", alpha2: "TZ", alpha3: "TZA" },
            { name: "Thailand", alpha2: "TH", alpha3: "THA" },
            { name: "Togo", alpha2: "TG", alpha3: "TGO" },
            { name: "Tonga", alpha2: "TO", alpha3: "TON" },
            { name: "Trinidad and Tobago", alpha2: "TT", alpha3: "TTO" },
            { name: "Tunisia", alpha2: "TN", alpha3: "TUN" },
            { name: "Turkey", alpha2: "TR", alpha3: "TUR" },
            { name: "Turkmenistan", alpha2: "TM", alpha3: "TKM" },
            { name: "Tuvalu", alpha2: "TV", alpha3: "TUV" },
            { name: "Uganda", alpha2: "UG", alpha3: "UGA" },
            { name: "Ukraine", alpha2: "UA", alpha3: "UKR" },
            { name: "United Arab Emirates", alpha2: "AE", alpha3: "ARE" },
            { name: "United Kingdom", alpha2: "GB", alpha3: "GBR" },
            { name: "United States", alpha2: "US", alpha3: "USA" },
            { name: "Uruguay", alpha2: "UY", alpha3: "URY" },
            { name: "Uzbekistan", alpha2: "UZ", alpha3: "UZB" },
            { name: "Vanuatu", alpha2: "VU", alpha3: "VUT" },
            { name: "Vatican City", alpha2: "VA", alpha3: "VAT" },
            { name: "Venezuela", alpha2: "VE", alpha3: "VEN" },
            { name: "Vietnam", alpha2: "VN", alpha3: "VNM" },
            { name: "Yemen", alpha2: "YE", alpha3: "YEM" },
            { name: "Zambia", alpha2: "ZM", alpha3: "ZMB" },
            { name: "Zimbabwe", alpha2: "ZW", alpha3: "ZWE" },
            ];
        
            // Sắp xếp theo tên quốc gia
            countries.sort(function(a, b) {
            return a.name.localeCompare(b.name);
            });
        
            // Thêm các tùy chọn vào danh sách chọn quốc tịch
            countries.forEach(function(country) {
            var option = document.createElement("option");
            option.value = country.alpha2;
            option.text = country.name;
            nationalitySelect.add(option);
            });
        });
        </script>

        <script>
            document.getElementById("addButton").addEventListener("click", function() {
                // Hiển thị hoặc ẩn form khi nút "Thêm" được nhấp
                var form = document.getElementById("addForm");
                form.style.display = (form.style.display === "none" || form.style.display === "") ? "block" : "none";
            });
        </script>

        <div class=" mx-auto" style ="border: 5px solid black;">
            <?php
            
                if ($result->num_rows > 0) {
                    // Create an array to store unique event_name
                    $uniqueevent_name = array();

                    // Group rows by event_name
                    while ($row = $result->fetch_assoc()) {
                        $event_name = $row["event_name"];
                        $uniqueevent_name[$event_name][] = $row;
                    }

                    // Iterate through unique event_name and display tables
                    foreach ($uniqueevent_name as $event_name => $rows) {
                        $rank = 1;
                        echo "<h2 style='text-align: center; color: white; background-color: #3498db;'>Event Name: $event_name</h2>";
                        echo "<table class='table' style='text-align: center;'>";
                        echo "<thead ><tr><th>Ranks</th><th>Full Name</th><th>Time Cord</th><th>Nationality</th><th>Action</th></tr></thead>";
                        echo "<tbody>";

                        foreach ($rows as $row) {
                            echo "<tr>";
                            echo "<td>" . $rank . "</td>";
                            echo "<td>" . $row["fullName"] . "</td>";
                            echo "<td>" . $row["time_record"] . "</td>";
                            echo "<td>" . $row["nationality"] . "</td>"; 
                            echo "<td>";
                            echo "<a href='delete_racesults.php?id=" . $row["time_record"] . "' onclick='return confirm(\"Are you sure?\");' style='text-align: center; display: block;'>
                                    <i class='bx bxs-trash'></i>
                                </a>";
                            echo "</td>";
                            echo "</tr>";
                            $rank++;
                        }

                        echo "</tbody></table>";
                        echo "<br><hr><br>";
                        
                    }
                    
                } else {
                    echo "There are no recorded achievements yet.";
                }
            ?>
        </div>
    </header>

<!------------------------------------------------------------------------------------------------------->

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>   
</body>
</html>