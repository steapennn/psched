<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Process Scheduling Calculator</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='../css/style.css'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />


</head>
<body>
    <div class="container">
        <div class="inputbox">
            <h2>Process Scheduling Calculator</h1>
	        <form method="POST">
                <label for="algo">Select Algorithm:</label> <br>
                <select name="algo">
                    <option disabled selected hidden>-- Select Algorithm --</option>
                    <option value="fcfs">First Come First Serve</option>
                    <option value="sjfnon">Shortest Job First (Non-preemptive)</option>
                    <option value="sjf">Shortest Job First (Preemptive)</option>
                    <option value="prionon">Priority (Non-preemptive)</option>
                    <option value="prio">Priority (Preemptive)</option>
                    <option value="round">Round Robin</option>
                </select> <br><br>
                
                <label for="AT">Arrival Time:</label> <br>
                <input type="text" name="AT" placeholder="0 1 2 3 4" required> <br><br>
                
                <label for="BT">Burst Time:</label> <br>
                <input type="text" name="BT" placeholder="0 1 2 3 4" required> <br><br>
                
                <input type="submit" value="Submit">
             </form>
             <?php
             if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST['algo']) && isset($_POST['AT']) && isset($_POST['BT'])) {
                    $algorithm = $_POST['algo'];
                    $at = $_POST['AT'];
                    $bt = $_POST['BT'];

                    $atArray = explode(' ', $at);
                    $btArray = explode(' ', $bt);
                    
                    if (count($atArray) === count($btArray)) {
                        switch ($algorithm) {

                             case 'fcfs':

                                $n = count($atArray);
                                $tfArray = array();
                                $ttArray = array();
                                $wtArray = array();
           
                                $tfArray[0] = $atArray[0] + $btArray[0];
                                $wtArray[0] = 0;
                                $wtTotal = 0;
                                $ttTotal = 0;
            
                                for ($i = 1; $i < $n; $i++) {
                                    $tfArray[$i] = max($tfArray[$i - 1] + $btArray[$i], $atArray[$i] + $btArray[$i]);
                                    $wtArray[$i] = $tfArray[$i - 1] - $atArray[$i];
                                    $wtTotal += $wtArray[$i];
                                }

                                for ($i = 0; $i < $n; $i++) {
                                    $ttArray[$i] = $tfArray[$i] - $atArray[$i];
                                    $ttTotal += $ttArray[$i];
                                }
                                $avgWt = $wtTotal / $n;
                                $avgTt = $ttTotal / $n;
                                ?><div class="echo-message"><?php echo '<br><i class="fas fa-angle-double-right"></i> First Come First Serve algorithm selected.<br><br>';?>
                                <br><p>Gantt Chart</p>
                                
                                <div class="gantt-chart">
                                    <?php
                                    $processes = $atArray;
                                    $timeIntervals = $tfArray;
                                    array_unshift($timeIntervals, 0);
                                    $totalTime = end($timeIntervals);
                                    
  
                                    for ($i = 0; $i < count($processes); $i++) {
                                        $width = (($timeIntervals[$i + 1] - $timeIntervals[$i]) / $totalTime) * 100;
                                        echo '<div class="process-bar p' . ($i + 1) . '" style="width: ' . $width .  '%;">'. $processes[$i].'</div>';
                                    }
                                    ?></div>
                                    <div class="timeline">
                                        <?php
                                        $count = count($timeIntervals);
                                        foreach ($timeIntervals as $index => $time) {
                                            $width = ($time / $totalTime) * 100;
                                            $positionClass = ($index === 0) ? 'position-left' : (($index === $count - 1) ? 'position-right' : '');
                                            echo '<div class="timeline-number ' . $positionClass . '" style="left: ' . $width . '%;">' . $time . '</div>';
                                        }
                                        ?>
                                        </div>
                                    </div>




                                </p><?php
                                ?>
                                <div class="table-container"><?php
                                echo "<table class='custom-table'>";
                                echo "<tr>";
                                echo "<th>Process</th>";
                                echo "<th>Arrival Time</th>";
                                echo "<th>Burst Time</th>";
                                echo "<th>Time Finished</th>";
                                echo "<th>Turnaround Time</th>";
                                echo "<th>Waiting Time</th>";
                                echo "</tr>";
            
                                for ($i = 0; $i < $n; $i++) {
                                    echo "<tr>";
                                    echo "<td>P" . ($i + 1) . "</td>";
                                    echo "<td>" . $atArray[$i] . "</td>";
                                    echo "<td>" . $btArray[$i] . "</td>";
                                    echo "<td>" . $tfArray[$i] . "</td>";
                                    echo "<td>" . $ttArray[$i] . "</td>";
                                    echo "<td>" . $wtArray[$i] . "</td>";
                                    echo "</tr>";
                                }
                                echo "<tr>";
                                echo "<td colspan='4'>Average:</td>";
                                echo "<td>" . $avgTt . "</td>";
                                echo "<td>" . $avgWt . "</td>";
                                echo "</tr>";

                                echo "</table>";
                                ?></div><?php
                                break;
                                
                            case 'sjfnon':        
                                $n = count($btArray);
                            $visited = array_fill(0, $n, false);
                            $ct = array_fill(0, $n, 0);
                            $tat = array_fill(0, $n, 0);
                            $wt = array_fill(0, $n, 0);
                            $totalTime = 0;
                            $completed = 0;
                            
                            while ($completed < $n) {
                                $shortestJobIndex = -1;
                                $shortestJobTime = PHP_INT_MAX;
                                
                                for ($i = 0; $i < $n; $i++) {
                                    if ($btArray[$i] < $shortestJobTime && !$visited[$i] && $atArray[$i] <= $totalTime) {
                                        $shortestJobIndex = $i;
                                        $shortestJobTime = $btArray[$i];
                                    }
                                }
                                
                                if ($shortestJobIndex != -1) {
                                    $visited[$shortestJobIndex] = true;
                                    $totalTime += $btArray[$shortestJobIndex];
                                    $ct[$shortestJobIndex] = $totalTime;
                                    $tat[$shortestJobIndex] = $ct[$shortestJobIndex] - $atArray[$shortestJobIndex];
                                    $wt[$shortestJobIndex] = $tat[$shortestJobIndex] - $btArray[$shortestJobIndex];
                                    $completed++;
                                } else {
                                    $totalTime++;
                                }
                            }
                            
                            $avgTAT = 0;
                            $avgWT = 0;
                            
                            for ($i = 0; $i < $n; $i++) {
                                $avgTAT += $tat[$i];
                                $avgWT += $wt[$i];
                            }
                            
                            $avgTAT /= $n;
                            $avgWT /= $n; ?>
                            
                            <div class="echo-message"><?php echo '<br><i class="fas fa-sort-amount-down"></i> Shortest Job First (Non-Preemptive) algorithm selected.<br><br>';?><?php
                                ?>
<br><p>Gantt Chart</p>
<div class="gantt-chart">
    <?php
    $processes = $atArray;
    $timeIntervals = $ct;
    $totalTime = end($timeIntervals);

    $previousTime = 0; // Track the previous time interval

    // Sort processes based on their finish time
    $processOrder = array_keys($atArray);
    array_multisort($timeIntervals, $processOrder);

    for ($i = 0; $i < count($processes); $i++) {
        $width = (($timeIntervals[$i] - $previousTime) / $totalTime) * 100;
        echo '<div class="process-bar p' . ($processOrder[$i] + 1) . '" style="width: ' . $width . '%;">' . $processes[$processOrder[$i]] . '</div>';
        $previousTime = $timeIntervals[$i]; // Update the previous time interval
    }
    ?>
</div>
<div class="timeline">
    <?php
    $count = count($timeIntervals);
    $previousTime = 0; // Track the previous time interval
    $leftOffset = 0; // Initial left offset

    $displayTime = ($processes[0] === 1 || $processes[0] === "1") ? 1 : 0;
    array_unshift($timeIntervals, $displayTime); // Add 0 or 1 to the beginning of the array


    foreach ($timeIntervals as $index => $time) {
        $widthh = ($time / $totalTime) * 100;
        $positionClass = ($index === 0) ? 'position-left' : (($index === $count - 1) ? 'position-right' : '');
        echo '<div class="timeline-number ' . $positionClass . '" style="left: ' . $widthh . '%;">' . $time . '</div>';
    }
    ?>
</div>







                                </p>

                                                <div class="table-container"><?php
                                                echo "<table class='custom-table'>";
                                                echo "<tr>";
                                                echo "<th>Process</th>";
                                                echo "<th>Arrival Time</th>";
                                                echo "<th>Burst Time</th>";
                                                echo "<th>Time Finished</th>";
                                                echo "<th>Turnaround Time</th>";
                                                echo "<th>Waiting Time</th>";
                                                echo "</tr>";

                                                for ($i = 0; $i < $n; $i++) {
                                                     echo "<tr>";
                                                     echo "<td>P" . ($i + 1) . "</td>";
                                                     echo "<td>" . $atArray[$i] . "</td>";
                                                     echo "<td>" . $btArray[$i] . "</td>";
                                                     echo "<td>" . $ct[$i] . "</td>";
                                                     echo "<td>" . $tat[$i] . "</td>";
                                                     echo "<td>" . $wt[$i] . "</td>";
                                                     echo "</tr>";
                                                    }
                                                    echo "<tr>";
                                echo "<td colspan='4'>Average:</td>";
                                echo "<td>" . $avgTAT . "</td>";
                                echo "<td>" . $avgWT . "</td>";
                                echo "</tr>";

                                                    echo "</table>";
                                                    ?></div><?php
                        
                            break;

                            case 'sjf':
                                ?><p class="echo-message"><?php echo '<br><i class="fas fa-balance-scale-right"></i> Shortest Job First (Preemptive) algorithm selected.<br><br>';?></p><?php

                                $n = count($atArray);
                                $remainingTime = $btArray;
                                $completed = array_fill(0, $n, false);
                                $currentTime = 0;
                                $completedCount = 0;
                                $completionTime = array_fill(0, $n, 0);
                                
                                while ($completedCount < $n) {
                                    $minIndex = -1;
                                    for ($i = 0; $i < $n; $i++) {
                                        if (!$completed[$i] && $atArray[$i] <= $currentTime) {
                                            if ($minIndex == -1 || $remainingTime[$i] < $remainingTime[$minIndex]) {
                                                $minIndex = $i;
                                            }
                                        }
                                    }
                                
                                    if ($minIndex != -1) {
                                        $remainingTime[$minIndex]--;
                                
                                        if ($remainingTime[$minIndex] == 0) {
                                            $completionTime[$minIndex] = $currentTime + 1;
                                            $completed[$minIndex] = true;
                                            $completedCount++;
                                        }
                                    }
                                    $currentTime++;
                                }
                                
                                $totalTurnaroundTime = 0;
                                $totalWaitingTime = 0;
                                ?>
                                <div class="table-container"><?php
                                echo "<table class='custom-table'>";
                                echo "<tr>";
                                echo "<th>Process</th>";
                                echo "<th>Arrival Time</th>";
                                echo "<th>Burst Time</th>";
                                echo "<th>Time Finished</th>";
                                echo "<th>Turnaround Time</th>";
                                echo "<th>Waiting Time</th>";
                                echo "</tr>";
                                
                                for ($i = 0; $i < $n; $i++) {
                                    $turnaroundTime = $completionTime[$i] - $atArray[$i];
                                    $waitingTime = $turnaroundTime - $btArray[$i];
                                    $totalTurnaroundTime += $turnaroundTime;
                                    $totalWaitingTime += $waitingTime;
                                    
                                        echo "<tr>";
                                        echo "<td>" . ($i + 1) . "</td>";
                                        echo "<td>" . $atArray[$i] . "</td>";
                                        echo "<td>" . $btArray[$i] . "</td>";
                                        echo "<td>" . $completionTime[$i] . "</td>";
                                        echo "<td>" . $turnaroundTime . "</td>";
                                        echo "<td>" . $waitingTime . "</td>";
                                        echo "</tr>";

                                }
                                $averageTurnaroundTime = $totalTurnaroundTime / $n;
                                $averageWaitingTime = $totalWaitingTime / $n;
                                
                                $formattedAvgTurnaroundTime = $averageTurnaroundTime;
                                if (floor($averageTurnaroundTime) != $averageTurnaroundTime) {
                                    $formattedAvgTurnaroundTime = number_format($averageTurnaroundTime, 1);
                                }
                                
                                $formattedAvgWaitingTime = $averageWaitingTime;
                                if (floor($averageWaitingTime) != $averageWaitingTime) {
                                    $formattedAvgWaitingTime = number_format($averageWaitingTime, 1);
                                }
                                echo "<tr>";
                                echo "<td colspan='4'>Average:</td>";
                                echo "<td>" .  $formattedAvgTurnaroundTime . "</td>";
                                echo "<td>" . $formattedAvgWaitingTime . "</td>";
                                echo "</tr>";
                                echo "</table>";
                                ?></div><?php
                                
                                
                            break;

                            case 'prionon':
                                ?><p class="echo-message"><?php echo '<br>wait, sleep muna ko...<i class="fas fa-bed"></i><br>';?></p><?php
                            break;

                            case 'prio':
                                ?><p class="echo-message"><?php echo '<br>wait, sleep muna ko...<i class="fas fa-bed"></i><br>';?></p><?php
                            break;

                            case 'round':
                                ?><p class="echo-message"><?php echo '<br>wait, sleep muna ko...<i class="fas fa-bed"></i><br>';?></p><?php
                            break;
                                default:
                                ?><p class="echo-message"><?php echo "<br>"."Please select a valid algorithm." . "<br>";?></p><?php
                                echo "<br>"."Please select a valid algorithm." . "<br>";
                            }
                        } else {
                            ?><p class="echo-message"><?php echo "<br>"."The number of elements in Arrival Time and Burst Time arrays must be the same." . "<br>";?></p><?php
                        }
                    } else {
                        ?><p class="echo-message"><?php echo "<br>"."Please fill in all the required fields." . "<br>";?></p><?php
                    }
                }
                ?>
        </div>
    </div>

    <div class="footer">
                <h5>Armado, Stephen (2023) ITC121</h2>
    </div>
</body>
</html>