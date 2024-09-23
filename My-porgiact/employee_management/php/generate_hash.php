<?php
// generate_hash.php

$password = 'nawaf00'; // Plaintext password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
echo "Hashed Password: " . $hashed_password;
?>
