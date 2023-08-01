<?php
    require_once "config.php";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $cropId = $_GET["cropId"];
    
    // prepare and bind
    $stmt = $conn->prepare("DELETE FROM crops WHERE id=?");
    $stmt->bind_param("s", $cropId);
    $stmt->execute();

    // Redirect user to welcome page
    header("location: my_server.php");

?>