<?php

require('config/config.inc.php');

?>
<?php
	if (empty($_SESSION['username'])){
		header("Location: login.php");
	}
?>
<html>
<?php 
	include("txt/head.txt");
?>
	<div id="content">
		<div id="nav">
			<?php 
				include("txt/menu.txt");
				include("txt/adminmenu.txt");
			?>
		</div>
		
		<div id="main_content">
			<div class="indexTitle"> Overview </div>
<?php
	$userid = $_SESSION['userid'];
	#---last login time-----
//	$query = "SELECT tlogin FROM $tableusers WHERE id='$userid'";
//	$result = mysql_query($query) or die("cannot connect database for user login time");
//	$row = mysql_fetch_array($result);
//	if ($row) {
//		$tlogin = $row['tlogin'];
//	}else{
//		$tlogin = '0000-00-00 00:00:00';
//	}
	if (isset($_SESSION['tlogin']) ) {
		$tlogin = $_SESSION['tlogin'];
	} else {
		$tlogin = '0000-00-00 00:00:00';
	}
	$query="SELECT id,pdbid,status,message,tsubmit FROM $tablejobs WHERE created_by='$userid' AND tsubmit >= '$tlogin' AND status < '2' AND status >= '0' AND flag = '0'".
	" ORDER by id DESC  ";
	$result = mysql_query($query);
	$nrows = mysql_num_rows($result);
	$fields = array('Job ID','Protein','Submitted at','Status','Action');
	if($nrows > 0) {
		echo '<div class="indexStatus">';
		echo 'Your recent submissions.';
		echo '</div>';
		//myshowresults($query,$fields,0,20,"index.php?page");
		$result = mysql_query($query);
		$nrow = mysql_num_rows($result);
	
		$page=0;$nlimit=20;
		$offset = $page*$nlimit;
		$query.="LIMIT $offset,$nlimit";
		$result = mysql_query($query);
		$tableheader = '<table class="queryTable" width=100%> <tbody id="tbodytag"> <tr class="queryTitle">';
		foreach ($fields as $f):
			$tableheader .= '<td>'.$f.'</td>';
		endforeach;
		$tableheader .= '</tr>';
		echo "$tableheader";
		while ( $row = mysql_fetch_array($result)){
	
			$jobid   = $row['id'];
			$pdbid   = $row['pdbid'];
			$subat   = $row['tsubmit'];
			$status  = $row['status'];
			$message = $row['message'];

			switch ($status) {
				case 0:
					$iconstr="<img src='style/img/waiting.png' title='Waiting for Submission...' border='0px'>";$statusstr="Wating";break;
				case 1:
					$iconstr="<img src='style/img/running.gif' width=16 height=16 title='Processing...' border='0px'>";$statusstr="Running";break;
				case 2:
					$iconstr="<img src='style/img/done.png' title='Completed Successfully !' border='0px'>";$statusstr="Done";break;
				case 3:
					$iconstr="<img src='style/img/failed.png' title='Job Failed !' border='0px'>";$statusstr="Failed";break;
			}
		
			$haspdb = $row['!ISNULL(fpdb)'];
			if ($haspdb) {
				$pdbstr = "<a href='downloadpdb.php?id=$jobid'><img src='style/img/zipped.gif' title='download output pdb' border='0px'> </a>";
//				$url = "loadPage(\"downloadpdb.php?id=$jobid\")";
//				$pdbstr = "<img src='style/img/zipped.gif' title='download out pdb' onclick ='$url' style='cursor:pointer;' >";
			}else{
				$pdbstr = "";
			}
			$href = "javascript:delentry($jobid);";
			$str = "<a href='$href'> <img src='style/img/remove.png' title='delete this entry' border='0px'> </a>";
		
			echo "<tr id='entrytag$jobid'>";
			echo "<td>$jobid</td>";
			echo "<td>$pdbid </td>";
			echo "<td>$subat </td>";
			echo "<td> <table><tr><td>$iconstr</td><td valign=top> <small>$statusstr</small> </td></tr></table> </td>";
			echo "<td> $str</td>";
			echo "</tr>";
		}

		echo "</tbody></table>";
	} else {
		echo '<div class="indexStatus">';
		echo 'You have no recent submissions.';
		echo '</div>';
	}

?>
	<br><br>
<?php
	#---last login time-----
	$query="SELECT COUNT(*) AS jobcount FROM $tablejobs WHERE created_by='$userid' AND tsubmit >= '$tlogin' AND status >= '2' and flag = '0'";
	$result = mysql_query($query);
	while($row=mysql_fetch_array($result)) {
		$nrows = $row['jobcount'];
	}
	if($nrows > 0) {
		echo '<div class="indexStatus">';
		echo 'Your recent results.';
		echo '</div>';
		//myshowresults($query,$fields,0,20,"index.php?page");
		$query = "SELECT $tableresults.*, $tablejobs.pdbid, $tablejobs.status FROM $tableresults LEFT JOIN $tablejobs ON (status='2' AND created_by='$userid' AND $tablejobs.id=$tableresults.jobid) WHERE userid='$userid' AND $tablejobs.flag='0'";
		$result = mysql_query($query);
		$nrow = mysql_num_rows($result);

		if($nrow>0) {
			$page = 0;
			$nlimit = 20;
			$offset = $page*$nlimit;
			//$query.=" LIMIT $offset,$nlimit";
			$result = mysql_query($query);
			$fields = array('Job ID','Protein','<div style="line-height: 1.8">E<sub>Clash</sub><sup style="margin-left: -4ex">initial</sup> <small>(kcal/mol)</small></div>','<div style="line-height: 1.8">E<sub>Clash</sub><sup style="margin-left: -4ex">final</sup> <small>(kcal/mol)</small></div>','RMSD (&Aring;)','&tau; <small>(DMD Units)</small>','Status','Action');
			$tableheader = '<table class="queryTable" width=100%> <tbody id="tbodytag"> <tr class="queryTitle">';
			foreach ($fields as $f):
				$tableheader .= '<td>'.$f.'</td>';
			endforeach;
			$tableheader .= '</tr>';
			echo $tableheader;
			while ( $row = mysql_fetch_array($result)){
				$jobid   = $row['jobid'];
				//$pdbid   = $row[''];
				$postat   = $row['tposted'];
				$iclashe  = $row['iClashE'];
				$fclashe  = $row['fClashE'];
				$rmsd     = $row['rmsd'];
				$niter    = $row['niter'];
				$pdbid    = $row['pdbid'];

				$iconstr="<img src='style/img/done.png' title='Completed Successfully !' border='0px'>";
				$statusstr="Done";
		
				$haspdb = $row['!ISNULL(fpdb)'];
				if ($haspdb) {
					$pdbstr = "<a href='downloadpdb.php?id=$jobid'><img src='style/img/zipped.gif' title='download output pdb' border='0px'> </a>";
//					$url = "loadPage(\"downloadpdb.php?id=$jobid\")";
//					$pdbstr = "<img src='style/img/zipped.gif' title='download out pdb' onclick ='$url' style='cursor:pointer;' >";
				}else{
					$pdbstr = "";
				}
				$href = "javascript:delentry($jobid);";
				$viewstr = "<a href='showresults.php?jobid=$jobid'> <img src='style/img/view.png' title='view results' border='0px'> </a>";
				//$pdbstr = "<a href='$href'> <img src='style/img/download.png' title='download files' border='0px'> </a>";
				$str = "<a href='$href'> <img src='style/img/remove.png' title='delete this entry' border='0px'> </a>";
		
				echo "<tr id='entrytag$jobid'>";
				echo "<td>$jobid</td>";
				echo "<td> $pdbid </td>";
				//echo "<td> $postat </td>";
				echo "<td> $iclashe </td>";
				echo "<td> $fclashe </td>";
				echo "<td> $rmsd </td>";
				echo "<td> $niter </td>";
				echo "<td> <table><tr><td>$iconstr</td><td valign=top> <small>$statusstr</small> </td></tr></table> </td>";
				echo "<td> $viewstr $pdbstr $str</td>";
				echo "</tr>";
			}
			echo "</tbody></table>";
		}
		$query = "SELECT * FROM $tablejobs WHERE created_by='$userid' AND status='3'";
		$result = mysql_query($query);
		$nrow = mysql_num_rows($result);

		if($nrow>0) {
			$page = 0;
			$nlimit = 20;
			$offset = $page*$nlimit;
			$query.="LIMIT $offset,$nlimit";
			$result = mysql_query($query);
			$fields = array("Job ID","PDB ID","Submitted at","Status","Action");
			$tableheader = '<table class="queryTable" width=100%> <tbody id="tbodytag"> <tr class="queryTitle">';
			foreach ($fields as $f):
				$tableheader .= '<td>'.$f.'</td>';
			endforeach;
			$tableheader .= '</tr>';
			echo $tableheader;
			while ( $row = mysql_fetch_array($result)){
				$jobid    = $row['id'];
				$pdbid    = $row['pdbid'];
				$subat    = $row['tsubmit'];
				//$message  = $row['message'];
				$iconstr="<img src='style/img/failed.png' title='Job Failed !' border='0px'>";
				$statusstr="Failed";
		
				$str = "<a href='$href'> <img src='style/img/remove.png' title='delete this entry' border='0px'> </a>";
		
				echo "<tr id='entrytag$jobid'>";
				echo "<td>$jobid</td>";
				echo "<td> $pdbid </td>";
				echo "<td> $subat </td>";
				echo "<td> <table><tr><td>$iconstr</td><td valign=top> <small>$statusstr</small> </td></tr></table> </td>";
				//echo "<td> $message </td>";
				echo "<td> $str</td>";
				echo "</tr>";
			}
			echo "</tbody></table>";
		}
		
	} else {
		echo '<div class="indexStatus">';
		echo 'You have no recent results.';
		echo '</div>';
	}

?>
		</div>
		<div id="rightbar">
			<div id="ataglance">
				your infomration at a glance.
			</div>
		</div>
	</div>
</body>
</html>
