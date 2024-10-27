<?php
// Check if form is submitted and iptype is set
if(isset($_POST['iptype'])) {
    $it = filter_input(INPUT_POST, 'iptype', FILTER_SANITIZE_STRING);

    if ($it === "Excel file") {
        header("Location: excelinput.html");
        exit();
    } else if ($it === "keyboard") {
        header("Location: keyboard.html");
        exit();
    } else {
        echo "Please provide valid details.";
    }
} else {
    echo "No input type selected.";
}

?>