<?php
session_start();
require_once 'db.php';

$email = trim($_POST['email'] ?? '');
if ($email === '') {
    die("Enter your e-mail");
} 

$stmt = $mi->prepare("SELECT ID_User FROM email WHERE Email_User = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $_SESSION['email'] = $email;
    header("Location: index.php");
    exit;
} else {
    $ins = $mi->prepare("INSERT INTO email (Email_User) VALUES (?)");
    $ins->bind_param("s", $email);
    $ins->execute();
    $_SESSION['email'] = $email;
    header("Location: index.php");
    exit;
}
?>