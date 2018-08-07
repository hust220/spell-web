<?php
require('config/config.inc.php');
?>
<?php
	if (empty($_SESSION['username'])){
		header("Location: login.php");
	}
?>

<?php 
#print_r($_GET);
#--------save the form data-------------------------#

#-------get sumitted data and check format--------#
$jobid = $_GET['jobid'];
#$pdbname = $_GET['pdbname'];
if ( eregi('[^0-9]',$jobid)) {
	die("invalid jobid");
}
$ip=$_SERVER['REMOTE_ADDR'];
#-----------set delete flag in the database--#
$userid = $_SESSION['userid'];
//$query  = "UPDATE $tablejobs SET flag='1' WHERE created_by='$userid' AND id='$jobid'" ;
$query  = "DELETE FROM $tablejobs WHERE id='$jobid'";
$result = mysql_query($query) or die(mysql_error());
die("ok");
?>
