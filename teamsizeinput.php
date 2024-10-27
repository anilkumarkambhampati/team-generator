<?php
require 'vendor/autoload.php'; // Include PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$con = new mysqli("localhost", "root", "", "anil");

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$no = $con->query("SELECT COUNT(id) AS count FROM student")->fetch_assoc()['count'];
$acs = $con->query("SELECT COUNT(id) AS count FROM student WHERE cgpa>0")->fetch_assoc()['count'];
$ncs = $con->query("SELECT COUNT(id) AS count FROM student WHERE cgpa=0")->fetch_assoc()['count'];

$size = isset($_POST['size']) ? (int)$_POST['size'] : null;
$nt = isset($_POST['nt']) ? (int)$_POST['nt'] : null;

if ($nt == null) {
    $nt = floor($no / $size);
}

if ($no < $nt) {
    echo "<script>
            alert('Enter valid data');
            window.location.href = 'teamsizeinput.html';
        </script>";
    exit;
}

$res = $con->query("SELECT id, cgpa FROM student WHERE cgpa > 0 ORDER BY cgpa DESC");
$res1 = $con->query("SELECT id, backlogs FROM student WHERE cgpa = 0 ORDER BY backlogs DESC");

$id = array();
$cgpa = array();
$id1 = array();
$backlogs = array();

while ($row = $res->fetch_assoc()) {
    $id[] = $row['id'];
    $cgpa[] = $row['cgpa'];
}
while ($row = $res1->fetch_assoc()) {
    $id1[] = $row['id'];
    $backlogs[] = $row['backlogs'];
}

$k = 0;
$teams = array();
$tot = array();

$ts = floor($acs / $nt);
$rem = $acs % $nt;

for ($i = 0; $i < $ts; $i++) {
    $f = 0;
    for ($j = 0; $j < $nt; $j++) {
        if ($i % 2 == 0) {
            $teams[$j][$i] = $id[$k];
            $tot[$j][$i] = $cgpa[$k];
            $k++;
        } else {
            if ($f == 0) {
                $k = $k + $nt - 1;
                $f = 1;
            }
            $teams[$j][$i] = $id[$k];
            $tot[$j][$i] = $cgpa[$k];
            $k--;
        }
    }
    if ($i % 2 != 0) {
        $k = $k + $nt + 1;
    }
}

$remain = array();
$remtot = array();
for ($i = 0; $i < $rem; $i++) {
    $remain[] = $id[$k];
    $remtot[] = $cgpa[$k];
    $k = $k + 1;
}

$k = 0;
$ts1 = floor($ncs / $nt);
$rem1 = $ncs % $nt;
for ($i = $ts; $i < $ts + $ts1; $i++) {
    $f = 0;
    for ($j = 0; $j < $nt; $j++) {
        if ($i % 2 == 0) {
            $teams[$j][$i] = $id1[$k];
            $tot[$j][$i] = $backlogs[$k];
            $k++;
        } else {
            if ($f == 0) {
                $k = $k + $nt - 1;
                $f = 1;
            }
            $teams[$j][$i] = $id1[$k];
            $tot[$j][$i] = $backlogs[$k];
            $k--;
        }
    }
    if ($i % 2 != 0) {
        $k = $k + $nt + 1;
    }
}

for ($i = 0; $i < $rem1; $i++) {
    $remain[] = $id1[$k];
    $remtot[] = $backlogs[$k];
    $k = $k + 1;
}

$remain = array_reverse($remain);
$remtot = array_reverse($remtot);

$k = 0;
$i = $ts + $ts1;
if (sizeof($remain) >= $nt) {
    for ($j = 0; $j < $nt; $j++) {
        $teams[$j][$i] = $remain[$k];
        $tot[$j][$i] = $remtot[$k];
        $k++;
    }
    $i++;
}
$m = $nt - (sizeof($remain) - $k);
for ($j = $nt - 1; $j > $m - 1; $j--) {
    $teams[$j][$i] = $remain[$k];
    $tot[$j][$i] = $remtot[$k];
    $k++;
}

// Display the teams
echo '<html>
<head> 
    <style>
        /* Reset default styles */
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: black; /* Light background color */
            margin: 0;
            padding: 20px; /* Padding for body */
        }

        /* Team cards styles */
        .team-card {
            background-color: white; /* Card background color */
            border-radius: 8px; /* Rounded corners */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            padding: 20px; /* Inner padding */
            margin: 10px; /* Space between cards */
            width: 300px; /* Fixed width for each card */
            transition: transform 0.3s; /* Transition for hover effect */
        }

        .team-card:hover {
            transform: translateY(-5px); /* Lift card on hover */
        }

        /* Team title styles */
        .team-card h2 {
            text-align: center; /* Center the title */
            color: #007bff; /* Primary blue color for the title */
        }

        /* List styles */
        .team-card ul {
            list-style-type: none; /* Remove default list styles */
            padding: 0; /* Remove padding */
        }

        .team-card li {
            padding: 5px 0; /* Space between list items */
            border-bottom: 1px solid #ddd; /* Bottom border for separation */
        }

        .team-card li:last-child {
            border-bottom: none; /* Remove border for the last item */
        }

        /* Download button styles */
        .download-btn {
            background-color: #007bff; /* Primary blue color */
            border: none; /* Remove default border */
            border-radius: 5px; /* Rounded corners */
            padding: 10px 20px; /* Padding for the button */
            color: white; /* Text color */
            font-size: 16px; /* Font size */
            cursor: pointer; /* Pointer cursor */
            margin-top: 20px; /* Space above the button */
            transition: background-color 0.3s, transform 0.2s; /* Transition effects */
            outline: none; /* Remove focus outline */
        }

        .download-btn:hover {
            background-color: #0056b3; /* Darker blue on hover */
            transform: translateY(-2px); /* Slight lift on hover */
        }

        .download-btn a {
            text-decoration: none; /* Remove underline */
            color: white; /* Text color */
        }
    </style>
</head>
<body>';

for ($i = 0; $i < $nt; $i++) {
    echo '<div class="team-card">';
    echo "<h2>Team " . ($i + 1) . "</h2>";
    echo '<ul>';
    for ($j = 0; $j < sizeof($teams[$i]); $j++) {
        echo "<li>Student ID: " . $teams[$i][$j] . "</li>";
    }
    echo '</ul>';
    echo '</div>';
}

// Create a new Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set the headers
$sheet->setCellValue('A1', 'Team Number');
$sheet->setCellValue('B1', 'Student ID');
$sheet->setCellValue('C1', 'CGPA / Backlogs');

// Fill the spreadsheet with data
$row = 2; // Starting row for data
for ($i = 0; $i < $nt; $i++) {
    $sheet->setCellValue('A' . $row, 'Team ' . ($i + 1));
    for ($j = 0; $j < sizeof($teams[$i]); $j++) {
        $sheet->setCellValue('B' . $row, $teams[$i][$j]);
        $sheet->setCellValue('C' . $row, isset($tot[$i][$j]) ? $tot[$i][$j] : '');
        $row++;
    }
}

// Write the file
$writer = new Xlsx($spreadsheet);
$filename = 'teams.xlsx';
$writer->save($filename);

// Provide download link
echo "<button class='download-btn'><a href='$filename'>Save Teams Excel File</a></button>";

echo '</body></html>';
$con->close();
?>
