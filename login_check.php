<?php
require('config/config.inc.php');

$user=$_POST['user'];
if (  eregi('[^0-9a-zA-Z_]',$user)) {
	die("Invalid userid\n");
}
$pass=$_POST['pass'];

$ip=$_SERVER['REMOTE_ADDR'];

if (!$user) {
	$_SESSION['error_login'] = $error['login'];
	header("Location: login.php");
}


if ( $user) {
	$error = array();
	$passmd5 = md5($pass);
	$query = "SELECT id,username,emailConfirmed,emailApproved,lastlogin,userlevel FROM $tableusers WHERE username='$user' AND password='$passmd5' ";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	$valid_login = mysql_num_rows($result);
	if ($valid_login == 0) {
		$error['login'] = 'The supplied username and/or password was incorrect.<br />';
	} else {
		if ($row['emailApproved'] == '0') {
			$error['login']= 'This user account has not been approved. Please try again later<br />';
		}
		if ($row['emailConfirmed'] == '0') {
			$error['login']= 'The e-mail ID associated with this account is either unreachable by the administrators or not confirmed by the user. Please click on the link you received via e-mail during registration. Please send an e-mail to pkota at email dot unc dot edu if you need assistance.<br />';
		}
		if ($row['emailApproved'] == '2') {
			$error['login']= 'This user account has been suspended. Please contact us for assistance<br />';
		}
		if ($row['emailApproved'] == '3') {
			$error['login']= 'Registration of this user account has been rejected. Please register again<br />';
		}
	}
	if (count($error) == 0){
		#---------delete the session informations and set new ones----------#
#		session_unset();
		session_destroy();
		session_name("chiron_session");
		session_start();
#		unset($_SESSION['error_login']);
		$_SESSION['username'] = $row['username'];
		$_SESSION['userid'] = $row['id'];
		$_SESSION['lastlogin'] = $row['lastlogin'];
		$_SESSION['userlevel'] = $row['userlevel'];
		$userid = $row['id'];
		#-------set the login time---------------#
		$query = "UPDATE $tableusers SET lastlogin=NOW()  WHERE id = '$userid' ";
		mysql_query($query) or die('cannot access user database - '.mysql_error());
		
		if($_SESSION['username'] == "guest") {
			header("Location: processManager.php");
		} else {
			header("Location: index.php");
		}
	}else{
		$_SESSION['error_login'] = $error['login'];
		header("Location: login.php");
	}
}
?>
<html>
<title> Chiron : Protein Energy Minimization server </title>
<body>
</body>
</html>
