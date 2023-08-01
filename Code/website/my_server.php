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
<html lang="en">

    <head>
        <meta charset="UTF-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <title>Home</title>
        <link rel="stylesheet" href="style.css"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>  
        <script src="https://npmcdn.com/js-alert/dist/jsalert.min.js"></script>
        
        <style>
            body{ font: 14px sans-serif; }
            .wrapper{ width: 360px; padding: 20px; }
            table.table-fit {
                width: auto !important;
                table-layout: auto !important;
                padding-left: 30px;
                padding-right: 30px;
            }
            table.table-fit thead th, table.table-fit tfoot th {
                width: auto !important;
                padding-left: 30px;
                padding-right: 30px;
            }
            table.table-fit tbody td, table.table-fit tfoot td {
                width: auto !important;
                padding-left: 30px;
                padding-right: 30px;
            }
        </style>

    </head>
        
    <body>
        <div class="shadow-sm p-2 mb-5 bg-body rounded">
            <div class="container" width="100%">
                <h3 class="text-center p-2">Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to our site.</h3>
                <div class="nav justify-content-center" style="width:100%">
                    <a href="logout.php" class="btn btn-danger text-center ml-3">Sign Out</a>
                </div>   
            </div>
        </div>

        <?php
            require_once "config.php";

            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);
            // Check connection
            if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
            }
        ?>

        <div class="container">
            <!-- My Clients -->
            <div class="p-3">
                <h4 class="text-center">My Clients</h4>
                <div class="text-center justify-content-center" style="display:flex; align-items:center">
                    <table class="text-center table table-fit" style="">
                        <thead>
                            <tr>
                                <th scope="col">id</th>
                                <th scope="col">Cell Number</th>
                                <th scope="col">Setting</th>
                                <th scope="col">Start Date</th>
                                <th scope="col">Edit</th>
                            </tr>
                        </thead>
                        <tbody id="myClientsBody">
                            <?php                               
                               $sql = "SELECT id, cell_number, setting, date_begun FROM clients WHERE server_id='$_SESSION[id]'";
                               $result = mysqli_query($conn, $sql);

                               if (mysqli_num_rows($result) > 0){
                                   while($row = mysqli_fetch_assoc($result)){
                                       echo "<tr>";
                                       echo "<th scope='row'>".$row["id"]."</th>"; 
                                       echo "<td>".$row["cell_number"]."</td>"; 
                                       echo "<td>".$row["setting"]."</td>";
                                       echo "<td>".$row["date_begun"]."</td>";
                                       echo "<td><a href='edit_client.php?serverId=".$_SESSION["id"]."&clientId=".$row["id"]."' class='btn btn-primary text-center ml-3' style='height:2rem'>Edit</a></td>";
                                       echo "</tr>";
                                    }
                                }
                            ?>  
                        </tbody>
                    </table>
                </div>
                <div class="nav justify-content-center" style="width:100%">
                    <a href="create_client.php" class="btn btn-warning text-center ml-3">New Client</a>           
                </div>
            </div>

            <!-- My Crops -->
            <div class="p-3">
                <h4 class="text-center">My Crops</h4>
                <div class="text-center justify-content-center" style="display:flex; align-items:center">
                    <table class="text-center table table-fit" style="">
                        <thead>
                            <tr>
                                <th scope="col">id</th>
                                <th scope="col">Name</th>
                                <th scope="col">Edit</th>
                            </tr>
                        </thead>
                        <tbody id="myCropsBody">
                            <?php
                                $sql = "SELECT id, name FROM crops WHERE server_id='$_SESSION[id]'";
                                $result = mysqli_query($conn, $sql);

                                if (mysqli_num_rows($result) > 0){
                                    while($row = mysqli_fetch_assoc($result)){
                                        echo "<tr>";
                                        echo "<th scope='row'>".$row["id"]."</th>";
                                        echo "<td>".$row["name"]."</td>";
                                        echo "<td><a href='edit_crop.php?cropId=".$row["id"]."' class='btn btn-primary text-center ml-3' style='height:2rem'>Edit</a></td>";
                                        echo "</tr>";
                                    }
                                }
                            
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="nav justify-content-center" style="width:100%">
                    <a href="create_crop.php" class="btn btn-warning text-center ml-3">New Crop</a>           
                </div>
            </div>
        </div>
    </body>
</html>