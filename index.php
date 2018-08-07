<?php

require('config/config.inc.php');
require('config/functions.inc.php');

if (empty($_SESSION['username'])){
	header("Location: login.php");
}
?>
<html>
<?php
include("txt/head.txt");
if($_SESSION[ 'username'] == "guest") { 
	echo "<div class=hspacer50></div>";
	echo "<strong style='color:#990000;'>We are sorry ! Guest users do not have access to this page. Please register to gain access.</strong>";
} else {
	?>
		<div id="content">
		<div id="nav">
		<?php 
		include("txt/menu.php");
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

	echo '<div class=hspacer10></div>';
	//	echo '<div id="tabs">';
	//	echo '	<ul>';
	//    	echo '	<li><a href="#chiron">SPELL</a></li>';
	//	echo '	<li><a href="#gaia">Gaia</a></li>';
	//	echo '	</ul>';
	echo '	<div id="chiron" style="font-size: 0.9em;">';

	/*-----------------------------------------------------------------------------
	  This section of code queries the database and displays any pending or running
	  jobs for the current user of the tool.
	  -----------------------------------------------------------------------------*/

	$query="SELECT id,pdbid,title,status,message,tsubmit FROM $tablejobs WHERE created_by='$userid' AND tsubmit >= '$tlogin' AND status < '2' AND status >= '0' AND flag = '0'".
		" ORDER by id DESC  ";
	$result = mysql_query($query);
	$nrows = mysql_num_rows($result);
	$fields = array('Job ID','Job Title','Protein','Submitted at','Status','Action');
	if($nrows > 0) {
		echo '<div class="indexStatus">';
		echo 'Your recent submissions.';
		echo '</div>';
		echo '<div align=center style="border:1px solid gray; padding: 1px 0px 1px 1px; margin: 3px 0px 3px 0px;">';
		echo '<img src="style/img/waiting.png" title="Waiting for Submission..." border="0px"> &ndash; Waiting &nbsp;&nbsp;&nbsp; <img src="style/img/running.gif" title="Processing" border="0px" width=16 height=16> &ndash; Running &nbsp;&nbsp;&nbsp; <img src="style/img/done.png" title="Completed Successfully !" border="0px"> &ndash; Done &nbsp;&nbsp;&nbsp; <img src="style/img/failed.png" border="0px" title="Job Failed !"> &ndash; Failed';
		echo '</div>';

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

			$jobid    = $row['id'];
			$pdbid    = $row['pdbid'];
			$title    = $row['title'];
			$subat    = $row['tsubmit'];
			$status   = $row['status'];
			$message  = $row['message'];
			//$harmonic = $row['harmonic'];

			switch ($status) {
				case 0:
					$iconstr="<img src='style/img/waiting.png' title='Waiting for Submission...' border='0px'>";$statusstr="Waiting";break;
				case 1:
					$iconstr="<img src='style/img/running.gif' width=16 height=16 title='Processing...' border='0px'>";$statusstr="Running";break;
				case 2:
					$iconstr="<img src='style/img/done.png' title='Completed Successfully !' border='0px'>";$statusstr="Done";break;
				case 3:
					$iconstr="<img src='style/img/failed.png' title='Job Failed !' border='0px'>";$statusstr="Failed";break;
			}

			//$harmonicstr = ($harmonic==0) ? "<img src='style/img/no.png'>" : "<img src='style/img/yes.png'>";

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
			echo "<td>$title </td>";
			echo "<td>$pdbid </td>";
			echo "<td>$subat </td>";
			//echo "<td align=center>$harmonicstr </td>";
			echo "<td>$iconstr</td>";
			echo "<td> $str</td>";
			echo "</tr>";
		}
		echo "</tbody></table>";
	} else {
#----The user does not have any jobs pending or running at this time----
		echo '<div class="indexStatus">';
		echo 'You have no recent submissions.';
		echo '</div>';
	}

	?>
		<br><br>
		<?php

		/*-------------------------------------------------------------------------
		  This section of code queries the database and displays any completed jobs
		  (including failed jobs) for the current user of the tool.
		  -------------------------------------------------------------------------*/

#----Query database to check if the user has completed jobs----
		$query="SELECT COUNT(*) AS jobcount FROM $tablejobs WHERE created_by='$userid' AND tsubmit >= '$tlogin' AND status >= '2' and flag = '0'";
	$result = mysql_query($query);
	while($row=mysql_fetch_array($result)) {
		$nrows = $row['jobcount'];
	}

#----The user has completed jobs. Display the results.----
	if($nrows > 0) {

		echo '<div class="indexStatus">';
		echo 'Your recent results.';
		echo '</div>';
		echo '<div align=center style="border:1px solid gray; padding: 1px 0px 1px 1px; margin: 3px 0px 3px 0px;">';
		echo '<img src="style/img/waiting.png" title="Waiting for Submission..." border="0px"> &ndash; Waiting &nbsp;&nbsp;&nbsp; <img src="style/img/running.gif" title="Processing" border="0px" width=16 height=16> &ndash; Running &nbsp;&nbsp;&nbsp; <img src="style/img/done.png" title="Completed Successfully !" border="0px"> &ndash; Done &nbsp;&nbsp;&nbsp; <img src="style/img/failed.png" border="0px" title="Job Failed !"> &ndash; Failed';
		echo '</div>';

		$query = "SELECT * FROM $tablejobs WHERE created_by='$userid' AND status='2'";
		$result = mysql_query($query);
		$nrow = mysql_num_rows($result);

		$page = 0;                                                                                                                                                           
		$nlimit = 20;                                                                                                                                                        
		$offset = $page*$nlimit;                     

		$fields = array('Job ID','Job Title','Protein','Status','Action'); 
		$tableheader = '<table class="queryTable" width=100%> <tbody id="tbodytag"> <tr class="queryTitle">';
		foreach ($fields as $f):                                                                                                                                             
			$tableheader .= '<td>'.$f.'</td>';                                                                                                                           
		endforeach;                                                                                                                                                          
		$tableheader .= '</tr>';                                                                                                                                             
		echo $tableheader;                                            




		while ( $row = mysql_fetch_array($result)) { 
			$jobid    = $row['id'];
			$pdbid    = $row['pdbid'];
			$title    = $row['title'];
			$subat    = $row['tsubmit'];
			$status   = $row['status'];
			$message  = $row['message'];
			$authKey  = $row[ 'authKey'];

			$iconstr="<img src='style/img/done.png' title='Completed Successfully !' border='0px'>";                                                                     
			$statusstr="Done"; 

			$haspdb = $row['!ISNULL(fpdb)'];                                                                                                                             
			if ($haspdb) {                                                                                                                                               
				$pdbstr = "<a href='downloadpdb.php?id=$jobid'><img src='style/img/zipped.gif' title='download output pdb' border='0px'> </a>";
			}else{                                                                                                                                                       
				$pdbstr = "";                                                                                                                                        
			}                                                                                                                                                            
			$href = "javascript:delentry($jobid);";                                                                                                                      
			$viewstr = "<a href='showresults.php?jobid=$jobid&authKey=$authKey'> <img src='style/img/view.png' title='view results' border='0px'> </a>";      
			//$pdbstr = "<a href='$href'> <img src='style/img/download.png' title='download files' border='0px'> </a>";                                                  
			$str = "<a href='$href'> <img src='style/img/remove.png' title='delete this entry' border='0px'> </a>";        

			echo "<tr id='entrytag$jobid'>";                                                                                                                             
			echo "<td>$jobid</td>";                                                                                                                                      
			echo "<td> $title </td>";                                                                                                                                    
			echo "<td> $pdbid </td>";                                                                                                                                    
			//echo "<td align=center> $harmonicstr </td>";                                                                                                               
			//echo "<td> $postat </td>";                                                                                                                                 
			//echo "<td> $iclashe </td>";                                                                                                                                  
			//echo "<td> $fclashe </td>";                                                                                                                                  
			//echo "<td> $rmsd </td>";                                                                                                                                     
			//echo "<td> $runtime </td>";                                                                                                                                  
			echo "<td align=center>$iconstr</td>";                                                                                                                       
			echo "<td> $viewstr $pdbstr $str</td>";                                                                                                                      
			echo "</tr>";                                                                                                                                                
		}
		//		}

		echo "</tbody></table>";                                                                                                                                             


#----Query for failed jobs----
		$query = "SELECT * FROM $tablejobs WHERE created_by='$userid' AND status='3'";
		$result = mysql_query($query);
		$nrow = mysql_num_rows($result);

#----Check if any jobs have failed and display if they have----
		if($nrow>0) {
			$page = 0;
			$nlimit = 20;
			$offset = $page*$nlimit;
			$query.="LIMIT $offset,$nlimit";
			$result = mysql_query($query);
			$fields = array("Job ID","Job Title","PDB ID","Submitted at","Status","Action");
			$tableheader = '<table class="queryTable" width=100%> <tbody id="tbodytag"> <tr class="queryTitle">';
			foreach ($fields as $f):
				$tableheader .= '<td>'.$f.'</td>';
			endforeach;
			$tableheader .= '</tr>';
			echo $tableheader;
			while ( $row = mysql_fetch_array($result)){
				$title    = $row['title'];
				$jobid    = $row['id'];
				$pdbid    = $row['pdbid'];
				$subat    = $row['tsubmit'];
				//$harmonic = $row['harmonic'];
				//$message  = $row['message'];
				$iconstr="<img src='style/img/failed.png' title='Job Failed !' border='0px'>";
				$statusstr="Failed";
				//$harmonicstr = ($harmonic==0) ? "<img src='style/img/no.png'>" : "<img src='style/img/yes.png'>";

				$href = "javascript:delentry($jobid);";
				$str = "<a href='$href'> <img src='style/img/remove.png' title='delete this entry' border='0px'> </a>";

				echo "<tr id='entrytag$jobid'>";
				echo "<td>$jobid</td>";
				echo "<td>$title</td>";
				echo "<td> $pdbid </td>";
				echo "<td> $subat </td>";
				//echo "<td align=center> $harmonicstr </td>";
				echo "<td>$iconstr</td>";
				//echo "<td> $message </td>";
				echo "<td> $str</td>";
				echo "</tr>";
			}
			echo "</tbody></table>";
		}

} else {
#----The user does not have completed or failed jobs recently----
	echo '<div class="indexStatus">';
	echo 'You have no recent results.';
	echo '</div>';
}
echo '</div>';
echo '<div id="gaia" style="font-size: 0.9em">';

/*-----------------------------------------------------------------------------
  This section of code queries the database and displays any pending or running
  jobs for the current user of the tool.
  -----------------------------------------------------------------------------*/


?>
<br><br>
<?php

/*-------------------------------------------------------------------------
  This section of code queries the database and displays any completed jobs
  (including failed jobs) for the current user of the tool.
  -------------------------------------------------------------------------*/
} // Do not remove this. It belongs here. It is the closing brace for the content if the user is not a guest.

?>
</div>
</div>
</body>
</html>
