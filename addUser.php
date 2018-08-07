<?php

$servername = "localhost";
$username = "spell_svc";
$password = "sixth-S3cr3t";
$dbname = "spell";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully<br>";

$username = 'jianopt';
$password = md5('Kang1994$');
$email = 'jianopt@ad.unc.edu';
$level = 10;
$confirmed = 1;
$approved = 1;
$sql = "insert into users(username, password, email, userlevel, emailConfirmed, emailApproved) values('$username', '$password', '$email', $level, $confirmed, $approved)";
$conn->query($sql);

$conn->close();

