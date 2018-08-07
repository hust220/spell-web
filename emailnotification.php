<?php
require('config/config.inc.php');
require('config/functions.inc.php');

require_once('config.php');

#get submitted data and check format
$guestFlag = 0;
$userid=$_GET['userid'];
if (strlen($userid)>10 || eregi('^0-9',$userid) ) {die("Invalid userid");}
$jobid=$_GET['jobid'];
if (strlen($jobid)>10 || eregi('^0-9',$jobid) ) {die("Invalid jobid");}
#-----find the job status-----
$query = "SELECT status,emailFlag FROM $tablejobs WHERE id='$jobid' AND created_by='$userid' AND flag=0";
$result = mysql_query($query) or die("job query failed");
$row = mysql_fetch_array($result);
if ($row == 0) {
        die("No job found.");
}
$emailFlag = $row['emailFlag'];
if ($emailFlag == 0) { die("No need to send email notification");}
if ($emailFlag == 2) { die("Email notification already sent");}
#-----find the useraddress----

if($userid=="9") {
	$guestFlag = 1;
	$query = "SELECT guestEmail,authKey FROM $tablejobs WHERE id='$jobid'";
	$result = mysql_query($query) or die("job query failed.");
	$row = mysql_fetch_array($result);
	if($row == 0) {
		die("Job with jobid ".$jobid." not found.");
	}
	$guestEmail = $row[ 'guestEmail'];
	$authKey    = $row[ 'authKey'];
	if(!empty($guestEmail)) {
		$link = "$host/chiron/showresults.php?jobid=".$jobid."&authKey=".$authKey;
		emailNotification($guestEmail, $link, $jobid, $guestFlag);
		echo "Guest notified";
	}
} else {
	$query = "SELECT email,emailConfirmed FROM $tableusers WHERE id='$userid'";
	$result = mysql_query($query) or die("user query failed.");
	$row = mysql_fetch_array($result);
	if ($row == 0) {
		die("No user found.");
	}
	$email = $row['email'];
	$emailConfirmed = $row['emailConfirmed'];
	if ($emailConfirmed == 0) {
		die("email not confirmed");
	}
	if($email){emailNotification($email,"",$jobid, $guestFlag); echo "User notified."; }
}


#-----now the email is sent, update status--------
$query = "UPDATE $tablejobs SET emailFlag='2' WHERE id='$jobid'";
$result = mysql_query($query) or die("Failed to update database");

?>
