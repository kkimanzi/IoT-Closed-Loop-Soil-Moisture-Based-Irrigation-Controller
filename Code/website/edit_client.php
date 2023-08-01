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
        
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>  
        <script src="https://npmcdn.com/js-alert/dist/jsalert.min.js"></script>
    
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.27.0/moment.min.js" integrity="sha512-rmZcZsyhe0/MAjquhTgiUcb4d9knaFc7b5xAfju483gbEXTkeJRUMIPk6s3ySZMYUHEcjKbjLjyddGWMrNEvZg==" crossorigin="anonymous"></script>
        <script src='https://cdn.jsdelivr.net/npm/chart.js@2.9.3/dist/Chart.min.js'></script>

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
                $clientId = sanitize($_POST["clientIdInput"]);
                $clientSetting = sanitize($_POST["clientSettingInput"]);
                $clientCell = sanitize($_POST["clientCellInput"]);
                $clientDate = sanitize($_POST["dateInput"]);
                $serverId = $_SESSION["id"];
              
                echo $clientId;
                echo $clientSetting;
                echo $clientCell;
                echo $clientDate;
                echo $serverId;

                // prepare and bind
                $stmt = $conn->prepare("UPDATE clients SET cell_number=?, setting=?, date_begun=? WHERE server_id=? AND id=?");
                $stmt->bind_param("sssss", $clientCell, $clientSetting, $clientDate, $serverId, $clientId);
                $stmt->execute();

                // Redirect user to welcome page
                header("location: my_server.php");
            } else {
                $clientId = $_GET["clientId"];
                // prepare and bind
                $sql = "SELECT cell_number, setting, date_begun FROM clients WHERE server_id=".$_SESSION["id"]." AND id=".$clientId."";
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
                <input type="text" style="display:none" id="clientIdInput" name="clientIdInput" value="<?php if($_SERVER["REQUEST_METHOD"] != "POST"){echo $_GET["clientId"];}?>">
                <div class="row mb-3">
                    <label for="clientCellInput" class="col-sm-3 col-form-label">Cell Phone</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="clientCellInput" id="clientCellInput" value="<?php echo $row["cell_number"] ?>">
                    </div>
                </div>
                <div class="row mb-3"> 
                    <label for="clientSettingInput" class="col-sm-3 col-form-label">Setting</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="clientSettingInput" id="clientSettingInput"value="<?php echo $row["setting"] ?>">
                    </div>
                </div>
                <div class="row mb-3"> 
                    <label for="dateInput" class="col-sm-3 col-form-label">Date Start</label>
                    <div class="col-sm-9">
                        <input type="date" class="form-control" name="dateInput" id="dateInput"value="<?php echo $row["date_begun"] ?>">
                    </div>
                </div>
                <div class="row mb-3" style="margin-top: 40px;"> 
                    <div class="col-sm-3"></div>
                    <div class="col-sm-6  position-relative">
                        <input id="update" type="submit" class="position-absolute top-0 start-0 translate-middle btn btn-sm btn-warning" style="width: 5rem; height:2rem;" value="Update">
                        <a href="delete_client.php?clientId=<?php if($_SERVER["REQUEST_METHOD"] != "POST"){echo $_GET["clientId"];} ?>" class="position-absolute top-0 start-100 translate-middle btn btn-sm btn-danger" style="height:2rem">Delete</a>
                    </div>
                    <div class="col-sm-3"></div>
                    
                </div>
            </form>

            <!-- Data Log -->
            <div style="margin-top:32px">
                <h4 class="text-center">Moisture Log</h4>
                    <div class="text-center justify-content-center" style="display:flex; align-items:center">
                        <canvas id="moistureCanvas" class="mx-auto p-2" style="width:100%;max-width:600px"></canvas>
                    </div>
                </div>
            </div>
            <div style="margin-top:32px">
                <h4 class="text-center">Moisture Log</h4>
                    <div class="text-center justify-content-center" style="display:flex; align-items:center">
                        <canvas id="batteryCanvas" class="mx-auto p-2" style="width:100%;max-width:600px"></canvas>
                    </div>
                </div>
            </div>
        </div>


        <script>
            $(document).ready(function () {
                showGraph();
            });
            
            getParameter = (key) => {
                address = window.location.search
                parameterList = new URLSearchParams(address)
                return parameterList.get(key)
            }

            function showGraph()
            {
                {
                    var url = "web_service/read_log.php?serverId="+getParameter("serverId") +"&clientId="+getParameter("clientId") ;
                    $.post(url,
                    function (data)
                    {   
                        data = JSON.parse(data);
                        console.log(data);

                        var xValues = [];
                        var moistureArr = [];
                        var batteryArr = [];

                        //console.log("Length of data = ",data.length);
                        //console.log("Data is ", data[0]);
                        
                        for (var i in data) {
                            xValues.push(data[i].timestamp);
                            moistureArr.push(data[i].moisture);
                            batteryArr.push(data[i].battery);
                        }

                        console.log(xValues);
                        console.log(moistureArr);

                        // For moisture logging
                        new Chart("moistureCanvas", {
                            type: "line",
                            data: {
                            labels: xValues,
                            datasets: [{
                                fill: false,
                                data: moistureArr,
                                borderColor: "#bae755",
                                backgroundColor: "#e755ba",
                                pointBackgroundColor: "#55bae7",
                                pointBorderColor: "#55bae7",
                                pointHoverBackgroundColor: "#55bae7",
                                pointHoverBorderColor: "#55bae7"
                            }]
                            },
                            options: {
                                scales: {
                                xAxes: [{
                                    type: 'time',
                                    time: {
                                    unit: 'hour'
                                    },
                                    scaleLabel: {
                                        display: true,
                                        labelString: 'Date'
                                    }
                                }],
                                yAxes: [{
                                    max: 0.9,
                                    min: 0.1,
                                    scaleLabel: {
                                        display: true,
                                        labelString: 'Moisture Level'
                                    },
                                    ticks: {
                                        beginAtZero: true,
                                        steps: 10,
                                        stepValue: 5,
                                        max: 1
                                    }
                                }]
                                }, 
                                legend: {display: false},
                                title: {
                                    display: false,
                                    text: ""
                                }
                            }
                        });

                        // For battery logging
                        new Chart("batteryCanvas", {
                            type: "line",
                            data: {
                            labels: xValues,
                            datasets: [{
                                data: batteryArr,
                                fill: false,
                                borderColor: "#bae755",
                                backgroundColor: "#e755ba",
                                pointBackgroundColor: "#55bae7",
                                pointBorderColor: "#55bae7",
                                pointHoverBackgroundColor: "#55bae7",
                                pointHoverBorderColor: "#55bae7"
                            }]
                            },
                            options: {
                                scales: {
                                xAxes: [{
                                    type: 'time',
                                    time: {
                                    unit: 'hour'
                                    },
                                    scaleLabel: {
                                        display: true,
                                        labelString: 'Date'
                                    }
                                }],
                                yAxes: [{
                                    scaleLabel: {
                                        display: true,
                                        labelString: 'Battery Level'
                                    },
                                    ticks: {
                                        beginAtZero: true,
                                        steps: 10,
                                        stepValue: 5,
                                        max: 100
                                    }
                                }]
                                }, 
                                legend: {display: false},
                                title: {
                                    display: false,
                                    text: ""
                                }
                            }
                        });

                    });
                }
            }
        </script>

    </body>
</html>