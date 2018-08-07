<?php

mysql_connect('localhost',$dbusername,$dbpassword);
mysql_select_db($database) or die( "Unable to select database");

function myshowresults($query,$fields,$page,$nlimit,$url){

	$result = mysql_query($query);
	$nrow = mysql_num_rows($result);
	
	$offset = $page*$nlimit;
	$query.="LIMIT $offset,$nlimit";
	$result = mysql_query($query);
	$tableheader = '<table class="queryTable" width=100%>
	<tbody id="tbodytag">
	
	<tr class="queryTitle">';
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
			$statusstr="Wating";break;
		case 1:
			$statusstr="Processing";break;
		case 2:
			$statusstr="Finished";break;
		case 3:
			$statusstr="Failed";break;
		}

		
		$haspdb = $row['!ISNULL(fpdb)'];
		if ($haspdb) {
			$pdbstr = "<a href='downloadpdb.php?id=$jobid'><img src='style/img/zipped.gif' title='download output pdb' border='0px'> </a>";
//			$url = "loadPage(\"downloadpdb.php?id=$jobid\")";
//			$pdbstr = "<img src='style/img/zipped.gif' title='download out pdb' onclick ='$url' style='cursor:pointer;' >";
		}else{
			$pdbstr = "";
		}
		$href = "javascript:delentry($jobid)";
		$str = "<a href='$href'> <img src='style/img/delete.png' title='delete this entry' border='0px'> </a>";
		
		echo "<tr id='entrytag$jobid'>";
		echo "<td>$jobid</td>";
		echo "<td>$pdbid </td>";
		echo "<td>$subat </td>";
		echo "<td> $statusstr </td>";
		echo "<td> $pdbstr $str</td>";
		echo "</tr>";
	}

	echo "</tbody></table>";
#	
#	$pagelast = $page - 1;
#	$pagenext = $page + 1;
#	$ntotalpage = ceil($nrow/$nlimit);
#	if ($ntotalpage == 0) { # for display
#		$pageindex = 0;
#	}
#	else { 
#		$pageindex = $page +1;
#	}
#	
#	if ($offset > 0) {
#		echo " <a href=\"$url=$pagelast\"> &lt;prev </a>"; 
#	}
#	echo "( page $pageindex/$ntotalpage )";
#	if ($offset+$nlimit < $nrow) {
#		echo " <a href=\"$url=$pagenext\"> next &gt;</a>";
#	}
}

function myshowresultssort($query,$page,$nlimit,$url,$options){
	# the total number of results
	$result = mysql_query($query);
	$nrow = mysql_num_rows($result);
	# only the page
	$offset = $page*$nlimit;
	$query.="LIMIT $offset,$nlimit";
	$result = mysql_query($query);
	$tableheader = '<table class="queryTable" width=100%>
	<tbody id="tbodytag">
	
	<tr class="queryTitle">
	<td><a href="query.php?sortcol=jobid&sortorder='.$options['jobid'].'"> Job Id</a></td>
	<td><a href="query.php?sortcol=pdbid&sortorder='.$options['pdbid'].'"> Protein</td>
	<td><a href="query.php?sortcol=mutation&sortorder='.$options['mutation'].'"> Mutation</td>
	<td><a href="query.php?sortcol=flex&sortorder='.$options['flex'].'"> Backbone</td>
	<td><a href="query.php?sortcol=relax&sortorder='.$options['relax'].'"> Pre-relax</td>
	<td><a href="query.php?sortcol=ddg&sortorder='.$options['ddg'].'"> &Delta;&Delta;G <small> (kcal/mol) </small></td>
	<td><a href="query.php?sortcol=status&sortorder='.$options['status'].'"> Status </td>
	<td>Action </td>
	</tr>';
	echo "$tableheader";
	while ( $row = mysql_fetch_array($result)){
	
		$jobid = $row['id'];
		$pdbid = $row['pdbid'];
		$mutation = $row['mutation'];
		$flex = $row['flex'];
		$relax = $row['relax'];
		$ddg = $row['ddg'];
		$ddg = sprintf("%4.2f",$ddg);
		$status = $row['status'];
		$message = $row['message'];

		$flexstr= $flex==1?"flexible":"fixed";
		$relaxstr = $relax==1?"yes":"no";
		switch ($status) {
		case 0:
			$statusstr="Wating";break;
		case 1:
			$statusstr="Running";break;
		case 2:
			$statusstr="Done";break;
		case 3:
			$statusstr="Failed";break;
		}

		$haspdb = $row['!ISNULL(pdb)'];
		if ($haspdb) {
			$pdbstr = "<a href='downloadpdb.php?id=$jobid'><img src='style/img/zipped.gif' title='download output pdb' border='0px'> </a>";
//			$url = "loadPage(\"downloadpdb.php?id=$jobid\")";
//			$pdbstr = "<img src='style/img/zipped.gif' title='download out pdb' onclick ='$url' style='cursor:pointer;' >";
		}else{
			$pdbstr = "";
		}
		$href = "delentry($jobid)";
		$str = "<a href='javascript:;' onclick='$href'> <img src='style/img/delete.png' title='delete this entry' border='0px'> </a>";
		
		echo "<tr id='entrytag$jobid'>";
		echo "<td>$jobid</td>";
		echo "<td>$pdbid </td>";
		echo "<td>$mutation </td>";
		echo "<td>$flexstr </td>";
		echo "<td>$relaxstr </td>";
		echo "<td>$ddg </td>";
		echo "<td> $statusstr </td>";
		echo "<td> $pdbstr $str</td>";
		echo "</tr>";
	}

	echo "</tbody></table>";
	$pagelast = $page - 1;
	$pagenext = $page + 1;
	$ntotalpage = ceil($nrow/$nlimit);
	if ($ntotalpage == 0) { # for display
		$pageindex = 0;
	}
	else { 
		$pageindex = $page +1;
	}
	
	if ($offset > 0) {
		echo " <a href=\"$url=$pagelast\"> &lt;prev </a>"; 
	}
	echo "( page $pageindex/$ntotalpage )";
	if ($offset+$nlimit < $nrow) {
		echo " <a href=\"$url=$pagenext\"> next &gt;</a>";
	}
}


?>
