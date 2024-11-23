<?php
$password = 'admin';
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

echo $hashedPassword;
?>
