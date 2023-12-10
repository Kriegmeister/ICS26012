<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@200;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="design.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Scan</title>
</head>

<body>
    <div class="container-fluid">
        <!--Navbar-->
        <nav class="navbar navbarr">
            <div class="container-fluid">
                <a href="#" class="navbar-brand page-title">Scan</a>
                <div class="d-flex">
                    <a href="main.html" class="nav-link me-4 navi">Home</a>
                    <a href="fcfs.php" class="nav-link me-4 navi">FCFS</a>
                    <a href="roundrobin.php" class="nav-link me-4 navi">Round Robin</a>
                    <a href="scan.php" class="nav-link me-4 navi">Scan</a>
                </div>
            </div>
        </nav>
        <div class="margin-text"></div>
        <div class="row">
            <!-- First Column - Input Form -->
            <div class="col-md-5">
                <form method="post">
                    <h1 class="title-margin"><b>Scan</b></h1>
                    <br>
                    <!--Input the starting head position-->
                    <label class="form-label" for="starting_position"><b>Enter the starting position:</b></label>
                    <input class="form-control contF" type="number" id="starting_position" name="starting_position" required min="0" step="1" value="<?php echo isset($_POST['starting_position']) ? $_POST['starting_position'] : ''; ?>"><br>

                    <!--Input the direction-->
                    <label for="direction"><b>Select the direction:</b></label>
                    <select class="btn btn-secondary btn-sm dropdown-toggle" id="direction" name="direction" value="<?php echo isset($_POST['direction']) ? $_POST['direction'] : ''; ?>" required>
                        <option value="left">Left</option>
                        <option value="right">Right</option>
                    </select><br>

                    <!-- Input Number of Processes -->
                    <label for="numProcesses" class="form-label"><b>Input size of queue [2-9]:</label></b>
                    <input type="number" class="form-control contF" id="numProcesses" name="numProcesses" min="2" max="9" value="<?php echo isset($_POST['numProcesses']) ? $_POST['numProcesses'] : ''; ?>" required>

                    <button class="btn btn-enter" name="enterButton" value="Enter">Enter</button>
                    <br><br>

                    <?php
                    // Retrieve the numProcess input
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $numProcesses = isset($_POST["numProcesses"]) ? (int)$_POST["numProcesses"] : 0;

                        echo "<b>Input Locations:</b><br><br>";
                        // Loop for every number of processes
                        for ($i = 0; $i < $numProcesses; $i++) {
                            $burstTimeValue = isset($_POST['burstTimes'][$i]) ? $_POST['burstTimes'][$i] : '';
                            echo "<label for='burstTime{$i}' class='form-label contF'><b>Location</b> " . ($i + 1) . ": </label>";
                            echo "<input type='number' name='burstTimes[]' id='burstTime{$i}' required class='form-control contF' value='{$burstTimeValue}'>";
                        }
                        echo "<input type='submit' class='btn btn-enter' value='Calculate'>";
                    }
                    ?>
                </form>
            </div>

            <!-- Second Column - Results -->
            <div class="col-md-7 results-margin">
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["burstTimes"])) {
                    $burstTimes = array_map('intval', $_POST["burstTimes"]);
                    $startingPosition = isset($_POST["starting_position"]) ? $_POST["starting_position"] : "";
                    $direction = isset($_POST["direction"]) ? $_POST["direction"] : "";

                    // PHP program to demonstrate
                    // SCAN Disk Scheduling algorithm
                    $disk_size = 200;

                    function SCAN($arr, $head, $direction)
                    {
                        $seek_count = 0;

                        $left = [];
                        $right = [];
                        $seek_sequence = [];



                        for ($i = 0; $i < count($arr); $i++) { 
                            if ($arr[$i] < $head) {
                                array_push($left, $arr[$i]);
                            }
                            if ($arr[$i] > $head) {
                                array_push($right, $arr[$i]);
                            }
                        }

                        // sorting left and right vectors
                        sort($left);
                        sort($right);

                        // run the while loop two times.
                        // one by one scanning right
                        // and left of the head
                        $run = 2;
                        while ($run-- > 0) {
                            if ($direction == "left") {
                                for ($i = count($left) - 1; $i >= 0; $i--) { 
                                    $cur_track = $left[$i];

                                    // appending current track to seek sequence
                                    array_push($seek_sequence, $cur_track);

                                    // calculate absolute distance
                                    $distance = abs($cur_track - $head);

                                    // increase the total count
                                    $seek_count += $distance;

                                    // accessed track is now the new head
                                    $head = $cur_track;
                                }
                                $direction = "right";
                            } elseif ($direction == "right") {
                                for ($i = 0; $i < count($right); $i++) {
                                    $cur_track = $right[$i];

                                    // appending current track to seek sequence
                                    array_push($seek_sequence, $cur_track);

                                    // calculate absolute distance
                                    $distance = abs($cur_track - $head);

                                    // increase the total count
                                    $seek_count += $distance;

                                    // accessed track is now new head
                                    $head = $cur_track;
                                }
                                $direction = "left";
                            }
                        }

                        echo "<b>Total number of seek operations = </b>" . $seek_count . "</br>"; //formatting for the output of the total seek operations
                        echo "<b>Seek Sequence is</b>" . "</br>";

                        // Output the sequence as a list
                        echo implode(" -> ", $seek_sequence) . "</br>";

                        echo " <a href='scan.php' class='btn btn-enter'>Reset Values</a>";

                        // Prepare data for line chart
                        $dataPoints = json_encode(range(1, count($seek_sequence)));  // Swap data with labels
                        $labels = json_encode(array_map('intval', $seek_sequence));  // Swap labels with data

                        echo "<canvas id='lineChart' width='5' height='5'></canvas>";

                        // JavaScript for line chart
                        echo "<script>";
                        echo "var ctx = document.getElementById('lineChart').getContext('2d');";
                        echo "var chart = new Chart(ctx, {";
                        echo "type: 'line',";
                        echo "data: {";
                        echo "labels: " . $labels . ",";
                        echo "datasets: [{";
                        echo "label: 'Seek Sequence',";
                        echo "data: " . $dataPoints . ",";
                        echo "borderColor: 'rgba(75, 192, 192, 1)',";
                        echo "borderWidth: 2,";
                        echo "fill: false,";  // Remove fill
                        echo "backgroundColor: 'rgba(75, 192, 192, 0.2)'";
                        echo "}],";
                        echo "},";
                        echo "options: {";
                        echo "scales: {";
                        echo "x: {";
                        echo "position: 'top',";  // X-axis on top
                        echo "type: 'linear',"; // Sort x-axis labels in increasing order
                        echo "},";
                        echo "y: {";
                        echo "position: 'left',";  // Y-axis on the left
                        echo "reverse: true,";
                        echo "ticks: { stepSize: 1, beginAtZero: true }"; // Set the interval on the y-axis and start from zero
                        echo "}";
                        echo "}";
                        echo "}";
                        echo "});";
                        echo "</script>";
                    }

                    // request array
                    // everything you need to manipulate is here
                    $arr =  $burstTimes;   //this is the queue
                    $head = $startingPosition;                                   //the head starting position
                    

                    SCAN($arr, $head, $direction);
                }
                ?>
            </div>
        </div>
    </div>
</body>

</html>