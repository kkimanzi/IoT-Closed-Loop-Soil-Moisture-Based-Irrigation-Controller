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
        <title>Web3 Ecommerce</title>
        <link rel="stylesheet" href="style.css"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>  
        <script src="https://npmcdn.com/js-alert/dist/jsalert.min.js"></script>
    </head>
    <body>

        <?php
            $clientId = $clientCell = $clientSetting = $clientDate = "";
            if ($_SERVER["REQUEST_METHOD"] == "POST"){
                //$clientId = sanitize($_POST["clientIdInput"]);
                $clientSetting = sanitize($_POST["clientSettingInput"]);
                $clientCell = sanitize($_POST["clientCellInput"]);
                $clientDate = sanitize($_POST["dateInput"]);
                $serverId = $_SESSION["id"];
                require_once "config.php";

                // Create connection
                $conn = new mysqli($servername, $username, $password, $dbname);
                // Check connection
                if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
                }
                

                // prepare and bind
                $stmt = $conn->prepare("INSERT INTO clients (server_id, cell_number, setting, date_begun) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $serverId, $clientCell, $clientSetting, $clientDate);
                $stmt->execute();

                // Redirect user to welcome page
                header("location: my_server.php");
            }

            function sanitize($data) {
                $data = trim($data);
                $data = stripslashes($data);
                $data = htmlspecialchars($data);
                return $data;
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
                <h5 class="text-center pb-4">Create client</h5>
                <div class="row mb-3">
                    <label for="clientCellInput" class="col-sm-3 col-form-label">Cell Phone</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="clientCellInput" id="clientCellInput">
                    </div>
                </div>
                <div class="row mb-3"> 
                    <label for="clientSettingInput" class="col-sm-3 col-form-label">Setting</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="clientSettingInput" id="clientSettingInput">
                    </div>
                </div>
                <div class="row mb-3"> 
                    <label for="dateInput" class="col-sm-3 col-form-label">Date Start</label>
                    <div class="col-sm-9">
                        <input type="date" class="form-control" name="dateInput" id="dateInput">
                    </div>
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