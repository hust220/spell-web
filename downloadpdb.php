<?php

require('config/config.inc.php');

?>
<?php
if (empty( $_SESSION['username']) ){
	header("Location: login.php");
}



$ctype = 'chemical/x-pdb';


$jobid = $_GET['id'];
if(isset($jobid)){
	if (preg_match('/[^0-9]/',$jobid)) {
		die("invalid jobid");
		unset($jobid);
	}
}else{
	die("invalid jobid");
}

$filename = "$jobid.pdb";
$userid = $_SESSION['userid'];
$query = "SELECT pdb FROM $tablejobs WHERE created_by='$userid' AND id='$jobid'";
$result = mysql_query($query) or die("connection to database failed");
if ( mysql_num_rows($result) == 0) {
	die("jobid not found");
}
$row = mysql_fetch_array($result);
$pdbgz = $row['pdb'];
$fp = fopen("download/$filename",'w');
fwrite($fp, $pdbgz);
fclose($fp);

//die($pdb);


//header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: public");
header("Content-Description: File Transfer");
//Use the switch-generated Content-Type
header("Content-Type: $ctype");
//Force the download
$header="Content-Disposition: attachment; filename=".$filename.";";
header($header);
header("Content-Transfer-Encoding: binary");
//header("Content-Length: 1000");

readgzfile("download/$filename");


//else die("<html><body OnLoad=\"javascript: alert('Nessun file da scaricare!');history.back();\" bgcolor=\"#F0F0F0\"></body></html>");

exit;

?>

