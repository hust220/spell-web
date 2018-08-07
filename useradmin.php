<?php

require('config/config.inc.php');

?>
<?php
	if (empty($_SESSION['username'])){
		header("Location: login.php");
	}
	if ($_SESSION['userlevel'] < 3){
		header("Location: login.php");
	}
?>
<html>
<?php 
	include("txt/head.txt");
?>
	<div id="content">
		<div id="nav">
			<a href="index.php" class="nav"><div class='ui-state-default ui-corner-all navi'> Home/Overview </div></a><br/>
			<a href="useradmin.php" class="nav"><div class='ui-state-default ui-corner-all navi'> <font color='#990000'> User Admin </font></div></a><br/>
			<a href="jobadmin.php" class="nav"><div class='ui-state-default ui-corner-all navi'> <font color='#990000'> Job Admin </font></div></a><br/>
			<a href="mailer.php" class="nav"><div class='ui-state-default ui-corner-all navi'> <font color='#990000'> Message Center </font></div></a><br/>
			<!--<a href="pdbadmin.php" class="nav"><div class='navi'> <font color='red'>PDB Admin </font></div></a><br/>-->
		</div>
		
		<div id="main_content">
			<div class="indexTitle"> User Administration. </div>
			<div class="indexStatus">
				List of all users.<br><br>
			</div>
			<div id='useradmin_table' name='useradmin_table'> 
<?php
$page=0;
$nlimit = 50;
$page = $_GET['page'];
if (preg_match('/[^0-9]/',$page)) { 
  $page = 0;
}
$offset = $page*$nlimit;

$sortorder= $_GET['sortorder'];
if (! isset($sortorder) || $sortorder != "ASC"){
  $sortorder = "DESC";
  $orderswitch = "ASC";
} else {
  $sortorder = "ASC";
  $orderswitch = "DESC";
}

$sortcol = $_GET['sortcol'];
if (! isset($sortcol)) {
  $sortcol = "id";
}
#may put some filter later $sortstr = "id";
$options = "";
$options[$sortcol] = $orderswitch;

$userid = $_SESSION['userid'];
$query="SELECT id,created_on,username,firstname,lastname,organization,email,".
"emailConfirmed, emailApproved, userlevel,".
"lastlogin, numtasks FROM $tableusers ".
"ORDER by $sortcol $sortorder  ";
require_once('config/admin/useradmintable.inc.php');
useradmintablesort($query,$page,$nlimit,"useradmin.php?sortorder=$sortorder&sortcol=$sortcol",$options);	
?>
			<div id='useradmin_bar' name='useradmin_bar'> 
			</div>
			</div>
	
	<br><br>


		</div>
		<div id="rightbar">
			<div id="ataglance">
				your infomration at a glance.
			</div>
		</div>
	</div>
</body>
</html>
