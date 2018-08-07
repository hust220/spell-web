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
$tlogin = $row['tlogin'];
$created_on = $row['created_on'];
$numtasks = $row['numtasks'];

$emailstr="";
if ($emailConfirmed == 1){
  $emailstr="<img src='../../style/img/verified.png' title='email verified' height='14px'>";
}
$statusstr ="";
switch ($approved) {
  case 0:
    $statusstr="Pending";break;
  case 1:
    $statusstr="Approved";break;
  case 2:
    $statusstr="Suspended";break;
  case 2:
    $statusstr="Rejected";break;
}
$levelstr="";
if ($level==1) {$levelstr="User";}
elseif ($level > 1) {$levelstr="<font color='red'> Admin</font>";}

?>

<html>
<head>
<style type="text/css">
  <!--
    @import url(../../style/ifold.css);
    @import url(../../style/greybox.css);
</style>
</head>
<body>
<table>
<tr>
  <td><b> User account details: </b> <br></td>
</tr>
</table>
<form name="actionform"><table><tbody>
<tr>
    <td> Username </td>
    <td> <?php  echo $username; ?> </td>
</tr>
<tr>
    <td> ID </td>
    <td> <?php  echo $userid; ?></td>
</tr>
<tr>
    <td> First Name </td>
    <td> <?php  echo $namefirst ?></td>
</tr>
<tr>
    <td> Last Name </td>
    <td> <?php  echo $namelast ?></td>
</tr>
<tr>
    <td> Organization </td>
    <td> <?php  echo $organization ?></td>
</tr>
<tr>
    <td> Email </td>
    <td> <?php  echo $email.$emailstr ?></td>
</tr>
<tr>
    <td> Approval </td>
    <td> <?php  echo $statusstr ?></td>
</tr>
<tr>
    <td> Level </td>
    <td> <?php  echo $level?></td>
</tr>
<tr>
    <td> Created on </td>
    <td> <?php  echo $created_on ?></td>
</tr>
<tr>
    <td> Last login </td>
    <td> <?php  echo $tlogin ?></td>
</tr>
<tr>
  <td>
    <input type="button" style="width: 50px" value="Close" onclick="window.parent.close_layer()"/>
  </td>
</tr>
</tbody></table></form>

</body>
</html>


