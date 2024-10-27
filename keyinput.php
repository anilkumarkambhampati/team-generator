<?php
$con = new mysqli("localhost", "root", "", "anil");

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Retrieve POST data
$rol = $_POST['id'];
$cgpa = isset($_POST['cgpa']) ? (float)$_POST['cgpa'] : null;
$backlogs=isset($_POST['backlogs']) ? (int)$_POST['backlogs'] : null;

$exist=$con->query("select count(id) as count from student where id='$rol'")->fetch_assoc()['count'];
//echo $exist;
if($exist>0){
    echo "<script>
            alert('ID $rol $exist already exist');
            window.location.href = 'keyinput.html';
        </script>";
        exit;
}
if($backlogs>0){
    $cgpa=0;
}
// Prepare and bind
$stmt = $con->prepare("INSERT INTO student (id, cgpa, backlogs) VALUES (?, ?, ?)");
$stmt->bind_param("sdi", $rol, $cgpa, $backlogs);

// Execute the prepared statement
if ($stmt->execute()) {
    // Fetch the number of students
    session_start();
    $ns = isset($_SESSION['nostudents']) ? $_SESSION['nostudents'] : 0;
    $res = $con->query("SELECT COUNT(id) AS count FROM student")->fetch_assoc()['count'];
    // Redirect based on the count of students
    if ($res < $ns) {
        header("Location: keyinput.html");
        exit;
    } else {
        header("Location: teamsizeinput.html");
        exit;
    }
} else {
    echo "Error: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$con->close();
?>
