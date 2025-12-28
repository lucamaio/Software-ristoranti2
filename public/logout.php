<?php
include '../includes/functions.php';
session_start();
session_destroy();

message("Logout Effetuato!", "../index.php");

?>