<?php

// $name = $_POST['name'];
// $email = $_POST['email'];
// $phone = $_POST['phone'];
// $message = $_POST['message'];

$name = 'Afzal';
$email = 'afzalansari@gmail.com';
$phone = '8765432123';
$message = 'hello';

$to = "afzalansari891@gmail.com"; // your email
$subject = "New Query";

$body = "Name: $name\n";
$body .= "Email: $email\n";
$body .= "Phone: $phone\n\n";
$body .= "Message:\n$message";

$headers = "From: $email";

mail($to, $subject, $body, $headers);

// simple response
echo "Message sent successfully!";

?>