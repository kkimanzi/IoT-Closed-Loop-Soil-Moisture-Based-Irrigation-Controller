<?php
    require_once "../config.php";
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    }

    
    $clientNumber = $_POST["clientNumber"];
    $serverNumber = $_POST["serverNumber"];;
    $moisture = $_POST["moisture"];
    $battery = $_POST["battery"];
    
    $sql = "SELECT id FROM servers WHERE cell_number='$serverNumber'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1){
        // server exists
        $serverId = mysqli_fetch_assoc($result)["id"];
        $clientQuery = "SELECT id from clients WHERE server_id='$serverId' AND cell_number='$clientNumber'";
        $clientResult = mysqli_query($conn, $clientQuery);
        if (mysqli_num_rows($clientResult) == 1){
            // client exists
            $clientId = mysqli_fetch_assoc($clientResult)["id"];

            // prepare and bind
            $stmt = $conn->prepare("INSERT INTO logs (server_id, client_id, moisture, battery) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $serverId, $clientId, $moisture, $battery);
            $stmt->execute();

            echo "Success";
            exit();
        } else {
            exit("Warning: Bad Client");
        }
    } else {
        exit("Warning: Bad Server");
    }
    
?>