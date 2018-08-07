<?php
require('config/config.inc.php');

$secret = "emailconfirmmagiccode";

#get sumitted data and check format
$username=$_GET['username'];
if (strlen($username)>10 || eregi('^0-9a-z',$username) ) {die("Invalid username");}
$key=$_GET['key'];
if (strlen($key) != 40 || eregi('^0-9a-z',$key) ){die("Invalid confirmation");}

#check entry in database
$query = "SELECT email FROM $tableusers WHERE username='$username'";
$result = mysql_query($query);
$row = mysql_fetch_array($result);
if ($row == 0) {
	die("No user found with username $username.");
}
$email=$row["email"];
if( sha1(md5($email) . md5($secret)) != $key ){ die("Invalid confirmation key");};

#now the email is confirmed
$query = "UPDATE $tableusers SET emailConfirmed='1' WHERE username='$username'";
$result = mysql_query($query) or die("Failed to update database");

header( 'refresh: 2; url="index.php"' );

?>
<html>
<body>
	Email confirmed. Account updated accordingly. Redirecting to Chiron home in 2 seconds... 
</body>
</html>
