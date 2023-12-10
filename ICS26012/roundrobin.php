<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@200;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="design.css" />
    <title>Round Robin Scheduling</title>
    </style>
    <script>
        function submitForm() {
            var formData = new FormData(document.getElementById("schedulingForm"));
            var xhr = new XMLHttpRequest();

            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.getElementById("resultContainer").innerHTML = xhr.responseText;
                }
            };

            xhr.open("POST", window.location.href, true);
            xhr.send(formData);
        }
    </script>
</head>

<body>
    <div class="container-fluid">
        <!--Navbar-->
        <nav class="navbar navbarr">
            <div class="container-fluid">
                <a href="#" class="navbar-brand page-title">Round-Robin</a>
                <div class="d-flex">
                    <a href="main.html" class="nav-link me-4 navi">Home</a>
                    <a href="fcfs.php" class="nav-link me-4 navi">FCFS</a>
                    <a href="roundrobin.php" class="nav-link me-4 navi">Round Robin</a>
                    <a href="scan.php" class="nav-link me-4 navi">Scan</a>
                </div>
            </div>
        </nav>
        <div class="row">
            <div class="margin-text"></div>
            <div class="col-md-5">

                <h1 class="title-margin"><b>Round-Robin</b></h1>
                <form id="schedulingForm" method="post">
                    <label for="num_processes" class="form-label"><b>Enter the number of processes:</b></label>
                    <input type="number" class="form-control contF" name="num_processes" value="<?php echo isset($_POST['num_processes']) ? htmlspecialchars($_POST['num_processes']) : ''; ?>" required>
                    <br>
                    <label for="quantum" class="form-label"><b>Enter the quantum:</b></label>
                    <input type="number" class="form-control contF" name="quantum" value="<?php echo isset($_POST['quantum']) ? htmlspecialchars($_POST['quantum']) : ''; ?>" required>
                    <br>
                    <label for="process_numbers" class="form-label"><b>Enter the process numbers (comma-separated):</b></label>
                    <input type="text" class="form-control contF" name="pos" value="<?php echo isset($_POST['pos']) ? htmlspecialchars($_POST['pos']) : ''; ?>" required>
                    <br>
                    <label for="arrival_times" class="form-label"><b>Enter the arrival times of processes (comma-separated):</b></label>
                    <input type="text" class="form-control contF" name="arrival_time" value="<?php echo isset($_POST['arrival_time']) ? htmlspecialchars($_POST['arrival_time']) : ''; ?>" required>
                    <br>
                    <label for="burst_times" class="form-label"><b>Enter the burst times of processes (comma-separated):</b></label>
                    <input type="text" class="form-control contF" name="burst_time" value="<?php echo isset($_POST['burst_time']) ? htmlspecialchars($_POST['burst_time']) : ''; ?>" required>
                    <br>
                    <button class="btn btn-enter" name="enterButton" value="Enter">Enter</button>
                </form>
            </div>


            <div class="col-md-7 results-margin">
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $n = $_POST["num_processes"];
                    $quant = $_POST["quantum"];
                    $processes = [];

                    // Split comma-separated values into arrays
                    $process_numbers = explode(',', $_POST["pos"]);
                    $arrival_times = explode(',', $_POST["arrival_time"]);
                    $burst_times = explode(',', $_POST["burst_time"]);

                    // Populate the processes array
                    for ($i = 0; $i < $n; $i++) {
                        $processes[$i]["pos"] = $process_numbers[$i];
                        $processes[$i]["AT"] = $arrival_times[$i];
                        $processes[$i]["BT"] = $burst_times[$i];
                    }

                    $c = $n;
                    $s = array_fill(0, $n, array_fill(0, 20, -1));
                    $time = 0;
                    $mini = PHP_INT_MAX;
                    $b = $a = [];

                    for ($i = 0; $i < $n; $i++) {
                        $b[$i] = $processes[$i]["BT"];
                        $a[$i] = $processes[$i]["AT"];
                    }

                    $tot_wt = $tot_tat = 0;
                    $flag = false;

                    while ($c != 0) {
                        $mini = PHP_INT_MAX;
                        $flag = false;

                        for ($i = 0; $i < $n; $i++) {
                            $p = $time + 0.1;
                            if ($a[$i] <= $p && $mini > $a[$i] && $b[$i] > 0) {
                                $index = $i;
                                $mini = $a[$i];
                                $flag = true;
                            }
                        }

                        if (!$flag) {
                            $time++;
                            continue;
                        }

                        $j = 0;
                        while ($s[$index][$j] != -1) {
                            $j++;
                        }

                        if ($s[$index][$j] == -1) {
                            $s[$index][$j] = $time;
                            $processes[$index]["ST"][$j] = $time;
                        }

                        if ($b[$index] <= $quant) {
                            $time += $b[$index];
                            $b[$index] = 0;
                        } else {
                            $time += $quant;
                            $b[$index] -= $quant;
                        }

                        if ($b[$index] > 0) {
                            $a[$index] = $time + 0.1;
                        }

                        if ($b[$index] == 0) {
                            $c--;
                            $processes[$index]["FT"] = $time;
                            $processes[$index]["WT"] = $processes[$index]["FT"] - $processes[$index]["AT"] - $processes[$index]["BT"];
                            $tot_wt += $processes[$index]["WT"];
                            $processes[$index]["TAT"] = $processes[$index]["BT"] + $processes[$index]["WT"];
                            $tot_tat += $processes[$index]["TAT"];
                        }
                    }
                }

                ?>

                <?php if ($_SERVER["REQUEST_METHOD"] == "POST") : ?>

                    <table class="table">
                        <tr>
                            <th class="table-width">Process number</th>
                            <th class="table-width">Arrival time</th>
                            <th class="table-width">Burst time</th>
                            <th class="table-width">Final time</th>
                            <th class="table-width">Wait Time</th>
                            <th class="table-width">TurnAround Time</th>
                        </tr>
                        <?php
                        for ($i = 0; $i < $n; $i++) {
                            echo "<tr>";
                            echo "<td class='table-width'>{$processes[$i]["pos"]}</td>";
                            echo "<td class='table-width'>{$processes[$i]["AT"]}</td>";
                            echo "<td class='table-width'>{$processes[$i]["BT"]}</td>";

                            echo "<td class='table-width'>{$processes[$i]["FT"]}</td>";
                            echo "<td class='table-width'>{$processes[$i]["WT"]}</td>";
                            echo "<td class='table-width'>{$processes[$i]["TAT"]}</td>";
                            echo "</tr>";
                        }
                        ?>
                    </table>

                    <?php
                    $avg_wt = $tot_wt / (float)$n;
                    $avg_tat = $tot_tat / (float)$n;
                    ?>

                    <p><b>The average wait time is: <?php echo $avg_wt; ?></b></p>
                    <p><b>The average TurnAround time is: <?php echo $avg_tat; ?></b></p>
                    <a href="roundrobin.php" class="btn btn-enter">Reset Values</a>

                <?php endif; ?>
            </div>
        </div>
    </div>

</body>

</html>