<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lag="en">
    <head>
        <meta charset="UTF-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <title>Create Crop</title>
        <link rel="stylesheet" href="style.css"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>  
        <script src="https://npmcdn.com/js-alert/dist/jsalert.min.js"></script>
    </head>
    <body>

        <?php
            $clientId = $clientCell = $clientSetting = $clientDate = "";
            if ($_SERVER["REQUEST_METHOD"] == "POST"){
                $cropName = sanitize($_POST["cropName"]);
                $serverId = $_SESSION["id"];
                require_once "config.php";

                // Create connection
                $conn = new mysqli($servername, $username, $password, $dbname);
                // Check connection
                if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
                }
                


                // Parse params data into array
                $paramsArray = getParamsArray();
                //Send array to XML writer
                printXML($paramsArray);

                
                // prepare and bind
                $stmt = $conn->prepare("INSERT INTO crops (server_id, name) VALUES (?, ?)");
                $stmt->bind_param("ss", $serverId, $cropName);
                $stmt->execute();

                // Redirect user to welcome page
                //header("location: my_server.php");
            }

            function sanitize($data) {
                $data = trim($data);
                $data = stripslashes($data);
                $data = htmlspecialchars($data);
                return $data;
            }

            function getParamsArray(){
                $paramsArr = [];
                for ($i = 0; isset($_POST["from".$i]) && $_POST["from".$i] != "";$i++){
                    $paramsSubset = [$_POST["from".$i],$_POST["to".$i],$_POST["ll".$i],$_POST["ul".$i]];
                    array_push($paramsArr,$paramsSubset);
                }
                return $paramsArr;
            }

            function printXML($paramsArr){
                $dirName = $_SESSION["id"].'_files';
                if (!file_exists($dirName)){
                    mkdir($dirName, 077, true);
                }
                if (!file_exists($dirName.'/watering_plans.xml')){
                    // Create new file
                    $dom = new DOMDocument();
                    $dom->encoding = 'utf-8';
		            $dom->xmlVersion = '1.0';
		            $dom->formatOutput = true;
                    
                    $xmlFileName = $dirName.'/watering_plans.xml';
                    $root = $dom->createElement('Watering_Plan');
                    $plantNode = $dom->createElement('Plant');
                    $plantNode->setAttributeNode(new DOMAttr('Variety', $_POST["cropName"]));

                    for ($i = 0; $i < count($paramsArr); $i++){
                        $partNode = $dom->createElement('Part');
                        
                        $dayVal = $paramsArr[$i][0].'-'.$paramsArr[$i][1];
                        $dayNode = $dom->createElement('Day',$dayVal);
                        $moistureVal = $paramsArr[$i][2].'-'.$paramsArr[$i][3];
                        $moistureNode = $dom->createElement('Moisture',$moistureVal);
                        $partNode->appendChild($dayNode);
                        $partNode->appendChild($moistureNode);

                        $plantNode->appendChild($partNode);

                    }
                    
                    $root->appendChild($plantNode);
                    $dom->appendChild($root);

                    $dom->save($xmlFileName);

                } else {
                    // file exists
                    $file = $_SESSION["id"].'_files/watering_plans.xml';
                    $xml = simplexml_load_file($file);
                    $wateringPlan = $xml;
                    $plantNode = $wateringPlan->addChild('Plant');
                    $plantNode->addAttribute('Variety', $_POST["cropName"]);

                    for ($i = 0; $i < count($paramsArr); $i++){
                        $partNode = $plantNode->addChild('Part');
                        
                        $dayVal = $paramsArr[$i][0].'-'.$paramsArr[$i][1];
                        $dayNode = $partNode->addChild('Day',$dayVal);
                        $moistureVal = $paramsArr[$i][2].'-'.$paramsArr[$i][3];
                        $moistureNode = $partNode->addChild('Moisture',$moistureVal);

                    }

                    $xml->asXML($file);
                }

                for ($i = 0; $i < count($paramsArr); $i++){
                    for($c = 0; $c < 4; $c++){
                        
                    }
                }
            }
        ?>

        <div class="shadow-sm p-2 mb-5 bg-body rounded">
            <div class="container" width="100%">
            <div class="nav justify-content-center" style="width:100%">
                    <h3><a href="my_server.php" class="btn text-center ml-3">Home</a></h3>
                </div>
                <div class="nav justify-content-center" style="width:100%">
                    <a href="logout.php" class="btn btn-danger text-center ml-3">Sign Out</a>
                </div>   
            </div>
        </div>

        <div class="container" class="mb-5" style="max-width: 650px; margin-top: 32px;">
        <form class="card p-5" method="post" action="<?php echo htmlSpecialChars($_SERVER['PHP_SELF']); ?>">
                <h5 class="text-center pb-4">Create Crop</h5>
                <div class="row mb-3"> 
                    <label for="cropName" class="col-sm-3 col-form-label">Crop Name</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="cropName" id="cropName">
                    </div>
                </div>

                <div class="text-center justify-content-center" style="display:flex; align-items:center; margin-top:16px" >
                    <table class="text-center table table-striped table-fit" style="">
                        <thead>
                            <tr>
                                <th scope="col" colspan="2" >Days</th>
                                <th scope="col" colspan="2">Moisture Level</th>
                            </tr>
                            <tr>
                                <th scope="col" >From</th>
                                <th scope="col">To</th>
                                <th scope="col">Lower Limit</th>
                                <th scope="col">Upper Limit</th>
                            </tr>
                        </thead>
                        <tbody id="myCropsBody">
                            <tr>
                                <td><input type="number" required class="form-control" name="from0"></td>
                                <td><input type="number" required class="form-control" name="to0"></td>
                                <td><input type="number" required step="0.01" class="form-control" name="ll0"></td>
                                <td><input type="number" required step="0.01" class="form-control" name="ul0"></td>
                            </tr>
                            <tr>
                                <td><input type="number" class="form-control" name="from1"></td>
                                <td><input type="number" class="form-control" name="to1"></td>
                                <td><input type="number" step="0.01" class="form-control" name="ll1"></td>
                                <td><input type="number" step="0.01" class="form-control" name="ul1"></td>
                            </tr>
                            <tr>
                                <td><input type="number" class="form-control" name="from2"></td>
                                <td><input type="number" class="form-control" name="to2"></td>
                                <td><input type="number" step="0.01" class="form-control" name="ll2"></td>
                                <td><input type="number" step="0.01" class="form-control" name="ul2"></td>
                            </tr>
                            <tr>
                                <td><input type="number" class="form-control" name="from3"></td>
                                <td><input type="number" class="form-control" name="to3"></td>
                                <td><input type="number" step="0.01" class="form-control" name="ll3"></td>
                                <td><input type="number" step="0.01" class="form-control" name="ul3"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="row mb-3" style="margin-top: 40px;"> 
                    <div class="col-sm-3"></div>
                    <div class="col-sm-6  position-relative">
                        <input id="createButton" type="submit" value="Create" class="position-absolute top-0 start-50 translate-middle btn btn-sm btn-primary" style="width: 4rem; height:2rem;">
                    </div>
                    <div class="col-sm-3"></div>
                    
                </div>
            </form>
        </div>
    </body>
</html>