<?php
    require_once "config.php";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $clientId = $_GET["clientId"];
    
    // prepare and bind
    $stmt = $conn->prepare("DELETE FROM clients WHERE id=?");
    $stmt->bind_param("s", $clientId);
    $stmt->execute();

    // Redirect user to welcome page
    header("location: my_server.php");

?>