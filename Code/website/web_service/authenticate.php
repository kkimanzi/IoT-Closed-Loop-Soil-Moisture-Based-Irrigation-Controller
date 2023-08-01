<?php
$clientNumber = $_POST["clientNumber"];
$serverNumber = $_POST["serverNumber"];

require_once "../config.php";
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id FROM servers WHERE cell_number='$serverNumber'";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) == 1){
    // server exists
    $serverId = mysqli_fetch_assoc($result)["id"];
    $clientQuery = "SELECT id, setting, date_begun from clients WHERE server_id='$serverId' AND cell_number='$clientNumber'";
    $clientResult = mysqli_query($conn, $clientQuery);
    
    if (mysqli_num_rows($clientResult) == 1){
        // Client exists
        $clientResult = mysqli_fetch_assoc($clientResult);
        // Get days difference between begining schedule and now
        $today = new DateTime('NOW');
        $dateBegun = new DateTime($clientResult["date_begun"]);
        $interval = $today->diff($dateBegun);
        $daysDifference = $interval->days;

        $setting = $clientResult["setting"];
        $xml = simplexml_load_file("../".$serverId."_files/watering_plans.xml") or die("Failed to open xml file");
        $settingNode = $xml->xpath("//Plant[@Variety='".$setting."']");
        
        if (count($settingNode[0]->Part) > 0){
            for ($i = 0; $i < count($settingNode[0]->Part); $i++){
                $part = $settingNode[0]->Part[$i];
                $days = $part->Day;
                $daysParams = explode('=', $days);
                $daysParams = explode('-',$daysParams[0]);

                // Check which part $dayDifference belongs to
                if ($daysDifference >= $daysParams[0] && $daysDifference < $daysParams[1]){
                    // Found match, emit moisture params
                    $moisture = $part->Moisture;
                    $expiry = $daysParams[1]-$daysDifference;
                    $returnString = "Moisture=$moisture;Expiry=$expiry";
                    echo($returnString);
                    exit();
                }

                //echo "Days = $part->Day , Moisture = $part->Moisture";
            }

            // If here it means days didn't match
            exit("Warning: Internal");
            
        } else {
            exit("Warning: Bad Schedule");
        }

        //echo json_encode($settingNode[0]->Part);
        //echo json_encode($settingNode);
    } else {
        // Client doesn't exist -> handle it
        exit("Warning: Bad Client");
    }
} else {
    // Server doesn't exist
    exit("Warning: Bad Server");
}

//$myJSON = json_encode($myArr);

//echo $phoneNumber;
?>