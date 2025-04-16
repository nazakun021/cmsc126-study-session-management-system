<?php
function OpenCon() {
    $db_server = "localhost";
    $db_name = "cmsc126_database"; 
    $db_user = "root@localhost";  
    $db_pass = ""; // (default empty for XAMPP)

    // Create connection
    $conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);

    // Check connection
    if (!$conn) {
        exit("Connection failed: " . mysqli_connect_error());
    }

    return $conn;
}

function CloseCon($conn) {
    if ($conn) {
        $conn->close();
    }
}
?>