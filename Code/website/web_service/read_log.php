<?php
header('Content-Type: application/json');

require_once "../config.php";
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
die("Connection failed: " . $conn->connect_error);
}

$clientId = $_GET["clientId"];
$serverId = $_GET["serverId"];

$sqlQuery = "SELECT id, timestamp, moisture, battery FROM logs WHERE server_id='$serverId' AND client_id='$clientId'  ORDER BY timestamp";

$result = mysqli_query($conn,$sqlQuery);

$data = array();
foreach ($result as $row) {
	$data[] = $row;
}

mysqli_close($conn);

echo json_encode($data);
?>