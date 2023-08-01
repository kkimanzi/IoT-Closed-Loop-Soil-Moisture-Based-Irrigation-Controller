
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
            
            $serverId = $serverName = $serverCell = "";
            if ($_SERVER["REQUEST_METHOD"] == "POST"){
                //$serverId = sanitize($_POST["serverIdInput"]);
                $serverName = $_POST["serverNameInput"];
                $serverCell = sanitize($_POST["serverCellInput"]);
                $serverPassword = $_POST["serverPasswordInput"];

                require_once "config.php";

                // Create connection
                $conn = new mysqli($servername, $username, $password, $dbname);
                // Check connection
                if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
                }
                

                // prepare and bind
                $stmt = $conn->prepare("INSERT INTO servers (server_name, cell_number, server_password) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $serverName, $serverCell, $serverPassword);
                $stmt->execute();

                // Redirect user to welcome page
                header("location: login.php");

            }

            function sanitize($data) {
                $data = trim($data);
                $data = stripslashes($data);
                $data = htmlspecialchars($data);
                return $data;
            }

        ?>

        <div class="container" class="mb-5" style="max-width: 650px; margin-top: 32px;">
        <form class="card p-5" method="post" action="<?php echo htmlSpecialChars($_SERVER['PHP_SELF']); ?>">
                <h5 class="text-center pb-4">Create Server</h5>
                <div class="row mb-3"> 
                    <label for="serverNameInput" class="col-sm-3 col-form-label">Name</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="serverNameInput" id="serverNameInput">
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="serverCellInput" class="col-sm-3 col-form-label">Cell Phone</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="serverCellInput" id="serverCellInput">
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="serverPasswordInput" class="col-sm-3 col-form-label">Password</label>
                    <div class="col-sm-9">
                        <input type="password" class="form-control" name="serverPasswordInput" id="serverPasswordInput">
                    </div>
                </div>
                <div class="row mb-3" style="margin-top: 40px;"> 
                    <div class="col-sm-3"></div>
                    <div class="col-sm-12  position-relative">
                        <input id="createButton" type="submit" value="create" class="position-absolute top-0 start-50 translate-middle btn btn-sm btn-primary" style="width: 4rem; height:2rem;">
                    </div>
                    <div class="col-sm-3"></div> 
                </div>
            </form>
        </div>

    </body>
</html>