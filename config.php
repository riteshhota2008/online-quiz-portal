<?php
$dbUser = "u372998677_test1";
$dbPass = "abhisekh96";
$dbHost = "localhost";
$dbDatabase = "u372998677_test1";

$dbConn = mysqli_connect($dbHost,$dbUser,$dbPass,$dbDatabase);

if(!$dbConn) {
    die ("Database not connected");
}
?>