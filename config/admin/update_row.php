<?php
require_once('../config.inc.php');
if (empty($_SESSION['username'])){
  die("Not logged in");
}
if ($_SESSION['level'] < 3){
  die("Illigal operation");
}
$userid=$_GET['userid'];

# check the user information
include('../para.inc.php'); # load the database para
#first get the email address of the user
$query  = "SELECT * FROM $tableusers WHERE id='$userid' ";
$result = mysql_query($query); 
$row = mysql_fetch_array($result) or die("mysql query failed");
if ($row == 0) {
  die("Record not found: id=$userid");
}

$userid = $row['id'];
$username = $row['username'];
$namefirst = $row['namefirst'];
$namelast = $row['namelast'];
$organization = $row['organization'];
$email = $row['email'];
$emailConfirmed = $row['emailConfirmed'];
$approved = $row['approved'];
$level = $row['level'];

$emailstr="";
if ($emailConfirmed == 1){
  $emailstr="<img src='style/img/verified.png' title='email verified' height='14px'>";
}
$statusstr ="";
switch ($approved) {
  case 0:
    $statusstr="Pending";break;
  case 1:
    $statusstr="";break;
  case 2:
    $statusstr="Suspended";break;
}
$levelstr="";
if ($level==1) {$levelstr="User";}
elseif ($level > 1) {$levelstr="<font color='red'> Admin</font>";}

require("useradmintable.inc.php"); # for the gen_action_str()
$actionstr=gen_action_str($row); #generate action forms for this user
$actionstr.="<img src='style/img/info.png' title='details' onclick='user_details(\"$userid\")' >";


echo "<td align=left valign=top>$userid</td>";
echo "<td align=left valign=top>$username</td>";
echo "<td align=left valign=top>$namefirst $namelast </td>";
echo "<td align=left valign=top>$organization</td>";
echo "<td align=left valign=top>$email $emailstr</td>";
echo "<td align=left valign=top> $statusstr </td>";
echo "<td align=left valign=top> $levelstr </td>";
echo "<td align=left valign=top> $actionstr </td>";

?>
