<?php
$nostudents = isset($_POST['ns']) ? (int)$_POST['ns'] : null;

if ($nostudents !== null && $nostudents > 0) {
    session_start();
    $_SESSION['nostudents']=$nostudents;
    f1();
    header("Location: keyinput.html");
    exit;
}else {
    // Handle the case where nemp is not provided or not valid
    echo "Please provide a valid number of students.";
}
function f1()
{
    $con=new mysqli("localhost","root","","anil");
    $con->query("truncate student");
    $con->close();
}
?>
