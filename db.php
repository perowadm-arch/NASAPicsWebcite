<?php
$mi = new mysqli("localhost", "root", "", "baseformywebcite");
if ($mi->connect_errno) {
    die("DB Error: " . $mi->connect_error);
}
$mi->set_charset("utf8mb4");
?>