<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$con = new mysqli("localhost", "root", "", "anil");
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Truncate the table to start fresh
$con->query("TRUNCATE student");

$x = $_POST['xfile'];
$i = 0;

try {
    $spsheet = IOFactory::load($x);
    $spsheet = $spsheet->getActiveSheet();

    foreach ($spsheet->getRowIterator() as $row) {
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);
        $data = [];
        
        foreach ($cellIterator as $cell) {
            $data[] = $cell->getValue();
        }
        
        // Skip the first three rows, if necessary
        if ($i > 3) {
            $con->query("INSERT INTO student (id, cgpa, backlogs) VALUES ('$data[1]', '$data[2]', '$data[4]')");
        }
        $i++;
    }

    // Check for duplicates
    $duplicateQuery = "
        SELECT id, COUNT(*) as count 
        FROM student 
        GROUP BY id 
        HAVING count > 1;
    ";

    $result = $con->query($duplicateQuery);
    if ($result->num_rows > 0) {
        $dup = "";
        while ($row = $result->fetch_assoc()) {
            $dup .= $row['id'] . ", ";
        }
        // Remove the trailing comma and space
        $dup = rtrim($dup, ", ");
        
        // Show alert and redirect
        echo "<script>
            alert('$dup above IDs are repeated');
            window.location.href = 'excelinput.html';
        </script>";
        exit;
    } else {
        header("Location: teamsizeinput.html");
        exit;
    }
} catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
    echo 'Error loading file: ', $e->getMessage();
}

$con->close();
?>
