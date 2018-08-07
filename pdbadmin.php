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
			<a href="index.php" class="nav"><div class='navi'> Home/Overview </div></a><br/>
			<a href="useradmin.php" class="nav"><div class='navi'> <font color='red'> User Admin </font></div></a><br/>
			<a href="jobadmin.php" class="nav"><div class='navi'> <font color='red'> Job Admin </font></div></a><br/>
			<!--<a href="pdbadmin.php" class="nav"><div class='navi'> <font color='red'>PDB Admin </font></div></a><br/>-->
		</div>
		
		<div id="main_content">
			<div class="indexTitle"> PDB database administration. </div>
			<div class="indexStatus">
				To be implemented.
			</div>
			<div id='jobadmin_table' name='jobadmin_table'> 
<?php
#$page=0;
#$nlimit = 50;
#$page = $_GET['page'];
#if (preg_match('/[^0-9]/',$page)) { 
#  $page = 0;
#}
#$offset = $page*$nlimit;
#
#$sortorder= $_GET['sortorder'];
#if (! isset($sortorder) || $sortorder != "ASC"){
#  $sortorder = "DESC";
#  $orderswitch = "ASC";
#} else {
#  $sortorder = "ASC";
#  $orderswitch = "DESC";
#}
#
#$sortcol = $_GET['sortcol'];
#if (! isset($sortcol)) {
#  $sortcol = "id";
#}
#$sortstr = $sortcol; # may put filter here to get rid of unknow column
#$options = "";
#$options[$sortcol] = $orderswitch;
#
#require_once('config/admin/jobadmintable.inc.php');
#$query="SELECT id,created_by,pdbid,mutation,flex,relax,ddg,status,message,!ISNULL(pdb) FROM $tablejobs ".
#" WHERE  flag = '0' ".
#" ORDER BY $sortstr $sortorder ";
#jobadmintablesort($query,$page,$nlimit,"jobadmin.php?sortorder=$sortorder&sortcol=$sortcol",$options);	
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
