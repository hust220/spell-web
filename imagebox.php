<html>
	<?php

	require('config/config.inc.php');
	require('config/functions.inc.php');
	
	?>
	<div id="content">
		<div id="main_content">
<?php
	$pdbid = $_GET['pdbid'];
	$jobid = $_GET['jobid'];

	//echo '<div style="border: 1px solid gray;" align=center>Minimization Summary</div>';
	echo '<div align=center><img width=720 src="download/'.$pdbid.'-'.$jobid.'.jpeg" border="0px"></div>';
	echo '<div align=right><table style="font-family:arial; font-size:12px;"><tr><td><a href="filedownload.php?type=jpeg&pdbid='.$pdbid.'&jobid='.$jobid.'"><img src="style/img/download.png" border="0px" ></a></td><td valign=center>Save Image</td></table></div>';
?>
		</div>
	</div>
</body>
</html>
