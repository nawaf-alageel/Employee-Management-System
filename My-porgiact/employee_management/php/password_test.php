<?php
$plaintext_password = 'Abdulrahman';
$hashed_password = password_hash($plaintext_password, PASSWORD_DEFAULT);
echo "Plaintext Password: " . $plaintext_password . "<br>";
echo "Hashed Password: " . $hashed_password . "<br>";

// Verify the password
if (password_verify($plaintext_password, $hashed_password)) {
    echo "Password verification successful!";
} else {
    echo "Password verification failed.";
}
?>
