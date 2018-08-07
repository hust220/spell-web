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
			<div class="indexTitle"> Job Administration </div>
			<div class="indexStatus">
				List of all the jobs
			</div>
			<div id='jobadmin_table' name='jobadmin_table'> 
<?php
$query="SELECT * FROM $tablejobs WHERE flag='0' ORDER BY id DESC";
$result = mysql_query($query);
$nrow = mysql_num_rows($result);
echo $nrow;
echo "<br>";

$nlimit = 20;
echo $nlimit;
echo "<br>";
$pagenum = 1;
$npages = intval($nrow / $nlimit);
if($nrow%$nlimit!=0) { $npages++; }
echo $npages;
$offset = $page * $nlimit;


if($nrow>0) {
	$recnum = 1;
	$nlimit = 20;
	$offset = $page*$nlimit;
	//$query.=" LIMIT $offset,$nlimit";
	$result = mysql_query($query);
	$currpage = 1;
	echo '<div align=center style="border:1px solid gray; padding: 1px 0px 1px 1px; margin: 3px 0px 3px 0px;">';
	echo '<img src="style/img/waiting.png" title="Waiting for Submission..." border="0px"> &ndash; Waiting &nbsp;&nbsp;&nbsp; <img src="style/img/running.gif" title="Processing" border="0px" width=16 height=16> &ndash; Running &nbsp;&nbsp;&nbsp; <img src="style/img/done.png" title="Completed Successfully !" border="0px"> &ndash; Done &nbsp;&nbsp;&nbsp; <img src="style/img/failed.png" border="0px" title="Job Failed !"> &ndash; Failed';
	echo '</div>';
	echo '<div align=right>';
	echo '<span class=pages id="prevpage">&lt;&lt;</span>';
	echo '<span class="pages pages-current" id=page'.$currpage.'>'.$currpage++.'</span>';
	echo '<span class=pages id=page'.$currpage.'>'.$currpage++.'</span>';
	echo '<span class="page-separator hideEl" id=sepleft >..</span>';
	for($i=$currpage;$i<$npages-1;$i++) {
		$currpage=$i;
		echo '<span class="pages hideEl" id=page'.$currpage.'>'.$currpage.'</span>';
	}
	echo '<span class=page-separator id=sepright>..</span>';
	echo '<span class=pages id=page'.++$currpage.'>'.$currpage.'</span>';
	echo '<span class=pages id=page'.++$currpage.'>'.$currpage.'</span>';
	echo '<span class=pages id="nextpage">&gt;&gt;</span>';
	echo '</div>';
	echo '<div class="cdiv hspacer5"> </div>';
	//$fields = array('Job ID','Protein','<div style="line-height: 1.8">E<sub>Clash</sub><sup style="margin-left: -4ex">initial</sup> <small>(kcal/mol)</small></div>','<div style="line-height: 1.8">E<sub>Clash</sub><sup style="margin-left: -4ex">final</sup> <small>(kcal/mol)</small></div>','RMSD (&Aring;)','&tau; <small>(DMD Units)</small>','Status','Action');
	$fields = array('Job ID','Job Title','Protein','Submitted by','Submitted at','Status','Action','Comments');
	while ( $row = mysql_fetch_array($result)){
		if($recnum==1) {
			($pagenum==1) ? $showTableClass = "queryTable" : $showTableClass = "hideEl queryTable";
			$tableheader = '<table id="table'.$pagenum.'" class="'.$showTableClass.'" width=100%>';
			$tableheader .= '<thead> <tr class=queryTitle>';
			foreach ($fields as $f):
				$tableheader .= '<td>'.$f.'</td>';
			endforeach;
			$tableheader .= '</tr></thead>';
			echo $tableheader;
		}
		$subat  = $row['tsubmit'];
		$jobid  = $row['id'];
		$title  = $row['title'];
		$pdbid  = $row['pdbid'];
		$status = $row['status'];
		$owner  = $row['created_by'];
		$message = $row['message'];
		//$harmonic = $row['harmonic'];

		$subquery = "SELECT username FROM $tableusers where id='$owner'";
		$resuser = mysql_query($subquery);
		$user_row = mysql_fetch_array($resuser);
		$username = $user_row[ 'username'];
		$lytebox = "";
		//$harmonicstr = ($harmonic==0) ? "<img src='style/img/no.png'>" : "<img src='style/img/yes.png'>";

		switch($status) {
			case 0:
				$iconstr = "<img src='style/img/waiting.png' border=0px title='Waiting for dispatch...'>"; $statusstr="Waiting"; break;
			case 1:
				$iconstr = "<img src='style/img/running.gif' width=16 height=16 border=0px title='Processing...'>"; $statusstr="Running"; break;
			case 2:
				$iconstr = "<img src='style/img/done.png' border=0px title='Completed Successfully'>"; $statusstr="Done"; break;
			case 3:
				$iconstr = "<img src='style/img/failed.png' border=0px title='Failed'>"; $statusstr="Failed"; $viewhref = "\"quickpeek.php?jobid=$jobid\" rel=\"lyteframe\" title=\"Log file\" rev=\"width: 630px; height: 400px; scrolling: yes;\""; break;
		}	
	
		$href = "javascript:delentry($jobid);";
		//$pdbstr = "<a href='$href'> <img src='style/img/download.png' title='download files' border='0px'> </a>";
		$str = "<a href='$href'> <img src='style/img/remove.png' title='delete this entry' border='0px'> </a>";
		$viewstr = "<a href=$viewhref> <img src='style/img/view.png' title='view results' border='0px'> </a>";
		
		echo "<tr id='entrytag$jobid'>";
		echo "<td>$jobid</td>";
		echo "<td>$title</td>";
		echo "<td> $pdbid </td>";
		//echo "<td align=center> $harmonicstr </td>";
		echo "<td> $username </td>";
		echo "<td> $subat </td>";
		echo "<td align=center> $iconstr </td>";
		if($status==3) {
			echo "<td> $viewstr $str</td>";
		} else {
			echo "<td> $str</td>";
		}
		echo "<td> $message </td>";
		echo "</tr>";
		$recnum++;
		if($recnum==21) { echo '</table>'; $recnum = 1; $pagenum++;}
	}
}
?>
			<div id='useradmin_bar' name='useradmin_bar'> 
			</div>
			</div>
	
	<br><br>


		</div>
	</div>
</body>
</html>
