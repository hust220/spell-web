<?php
require('config/config.inc.php');
require('config/emailConfirm.inc.php');
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
$userid = $_SESSION['userid'];

$varlist=array('namefirst','namelast','email','organization');
foreach ($varlist as $var) {
	if (strlen($_POST[$var]) > 100 ) {die("invalid input");}
        $_SESSION["save_$var"] = $_POST[$var];
}
foreach ($varlist as $var) {
        unset( $_SESSION["error_$var"]);
}
unset($_SESSION['loginerror']); 
#$username = $_POST['username'];
$namefirst = $_POST['namefirst'];
$namelast = $_POST['namelast'];
$email = $_POST['email'];
$organization = $_POST['organization'];

$error = array();
#if (strlen($username) <3 || strlen($username) > 10) {
#	$error['username']=("username should contains 3-10 charactors");
#}
#if (preg_match('/^0-9a-zA-Z/',$username)) {
#	$error['username']=("only number and letters are allowed in username");
#}
if (empty($namefirst)) {
	$error['namefirst'] = "first name cannot be empty";
}
if (empty($namelast)) {
	$error['namelast'] = "last name cannot be empty";
}
if (empty($organization)) {
	$error['organization'] = "organization cannot be empty";
}
if (strlen($email)  >100 || !eregi('^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$',$email)) 
{ 	$error['email'] = ("Invalid email address\n");
}

if (count($error) > 0) {
        foreach ($varlist as $var) {
		$_SESSION['loginerror'] = 1;
                $_SESSION["error_$var"] = $error[$var];
        }
        header("Location: profile.php");
	die;
}
# check if the email or username already used by another id
$query = "SELECT * FROM $tableusers WHERE email='$email' AND id!='$userid'";
$result = mysql_query($query);
$row = mysql_num_rows($result);
if ($row > 0) {
        $error['email'] = ("email address already used by another user");
}
#$query = "SELECT * FROM $tableusers WHERE username='$username' AND id!='$userid'";
#$result = mysql_query($query);
#$row = mysql_num_rows($result);
#if ($row > 0) {
#        $error['username'] = ("the username is already in use");
#}
	
if (count($error) > 0) {
	foreach ($varlist as $var) {
		$_SESSION["error_$var"] = $error[$var];
	}
	$_SESSION['loginerror'] = 1;
	header("Location: profile.php");
	die;
}


#-----------delete the saved forms--------------------#
foreach ($varlist as $var) {
	unset( $_SESSION["save_$var"] ); 
}


#------------send confirmation email if it's changed-----------------#
$query="SELECT email,emailConfirmed FROM $tableusers WHERE id='$userid'";
$result = mysql_query($query);
$row = mysql_fetch_array($result);
$oldemail = $row['email'];
$emailConfirmed = $row['emailConfirmed'];

$_SESSION['emailconfirm'] = 0;	

if ($oldemail != $email || $emailConfirmed != 1) {
	$query="UPDATE $tableusers SET emailConfirmed='0' WHERE id='$userid'";
	$result = mysql_query($query) or die("failed to update user information");
	
	$secret = "emailconfirmmagiccode";
	$username = $_SESSION['username'];
	$link = "http://" . $_SERVER['HTTP_HOST'] . '/eris/emailconfirm.php'."?username=$username&key=".
        sha1(md5($email) . md5($secret) );
	emailConfirm($email, $link);
	$_SESSION['emailconfirm'] = 1;	
	
} else {
	$_SESSION['emailconfirm'] = 0; 
}


$query="UPDATE $tableusers SET namefirst='$namefirst', ".
	" namelast='$namelast',email='$email',organization='$organization' ".
	"WHERE id='$userid'";
//die($query);
$result = mysql_query($query) or die("failed to insert into user database");

$_SESSION['loginerror'] = 0;
header("Location: profile.php");
?>
<html>
<body>

</body>
</html>
