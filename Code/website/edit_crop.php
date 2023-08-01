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
            require_once "config.php";
            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);
            // Check connection
            if ($conn->connect_error) {
               die("Connection failed: " . $conn->connect_error);
            }

            
            if($_SERVER["REQUEST_METHOD"] == "POST"){
                // Update the record
                $cropId = sanitize($_POST["cropIdInput"]);
                $cropName = sanitize($_POST["cropName"]);
                $serverId = $_SESSION["id"];

                // prepare and bind
                $stmt = $conn->prepare("UPDATE crops SET name=? WHERE id=?");
                $stmt->bind_param("ss", $cropName, $cropId);
                $stmt->execute();

                // Redirect user to welcome page
                header("location: my_server.php");
            } else {
                $cropId = $_GET["cropId"];
                // prepare and bind
                $sql = "SELECT name FROM crops WHERE id=".$cropId."";
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_assoc($result);
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
                <input type="text" style="display:none" id="cropIdInput" name="cropIdInput" value="<?php if($_SERVER["REQUEST_METHOD"] != "POST"){echo $_GET["cropId"];}?>">
                <div class="row mb-3"> 
                    <label for="cropName" class="col-sm-3 col-form-label">Crop Name</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="cropName" id="cropName" value="<?php echo $row["name"] ?>">
                    </div>
                </div>
                <div class="row mb-3" style="margin-top: 40px;"> 
                    <div class="col-sm-3"></div>
                    <div class="col-sm-6  position-relative">
                        <input id="update" type="submit" class="position-absolute top-0 start-0 translate-middle btn btn-sm btn-warning" style="width: 5rem; height:2rem;" value="Update">
                        <a href="delete_crop.php?cropId=<?php if($_SERVER["REQUEST_METHOD"] != "POST"){echo $_GET["cropId"];} ?>" class="position-absolute top-0 start-100 translate-middle btn btn-sm btn-danger" style="height:2rem">Delete</a>
                    </div>
                    <div class="col-sm-3"></div>
                    
                </div>
            </form>
        </div>
    </body>
</html>