<?php
require('config/config.inc.php');
?>
<?php
/*	if (empty( $_SESSION['username']) ){
		header("Location: login.php");
	}
*/
?>
<?php
#get sumitted data and check format
#--------save the form data-------------------------#
$varlist=array('oldpassword','newpassword','confirm_pass');
foreach ($varlist as $var) {
	if (strlen($_POST[$var]) > 100 ) {die("invalid input");}
}
foreach ($varlist as $var) {
        unset( $_SESSION["error_$var"]);
}
unset($_SESSION['changepasserror']); 
$oldpassword = $_POST['oldpassword'];
$newpassword = $_POST['newpassword'];
$confirm_pass = $_POST['confirm_pass'];
$error = array();
if (strlen($newpassword) <3 || strlen($newpassword) > 15) {
	$error['newpassword']=("password should contains 3-15 charactors");
}
if ($newpassword != $confirm_pass) {
	$error['confirm_pass'] = "confirm password is not the same.";
}
$userid = $_SESSION['userid'];
$oldpassmd5 = md5($oldpassword);
$query = "SELECT id from $tableusers WHERE id='$userid' AND password='$oldpassmd5'";
$result = mysql_query($query) or die("connection to database failed");
$nrow = mysql_num_rows($result);
if ( $nrow == 0) {
	$error['oldpassword'] = "Wrong password";
}

if (count($error) > 0) {
        foreach ($varlist as $var) {
		$_SESSION['changepasserror'] = 1;
                $_SESSION["error_$var"] = $error[$var];
        }
        header("Location: profile.php");
	die;
}
$newpassmd5 = md5($newpassword);
$query="UPDATE $tableusers SET password ='$newpassmd5' WHERE id='$userid'";
$result = mysql_query($query) or die("failed to insert into user database");
$_SESSION['changepasserror'] = 0;
header("Location: profile.php");
?>


<html>
<body>
</body>
</html>
