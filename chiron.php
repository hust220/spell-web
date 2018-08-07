<?php
require('config/config.inc.php');
require('config/functions.inc.php');
?>

<?php
	if (empty($_SESSION['username'])){
		header("Location: login.php");
	}
?>
<?php
/*function mydie($str,$var){
	//$_SESSION["error_$var"] = $str;
	echo "{";
	echo         '"error":"' . $str . '"'.",\n";
	echo         '"msg":"' . $var . '"';
	echo "}";
	//header("Location: submit.php");
	die;
}*/

#------------Variable Declaration-------------------#
$dbparams = array();
$jsonparams = array('msg','error');
$sq = "'";
//$error = array();

#-------Get submitted data and check format--------#
if(isset($_POST['title'])) {
	$title=$_POST['title'];
	$title=str_replace(" ","-",$title);
} else {
	$title="Custom";
}
$dbparams[ 'title'] = $sq.$title.$sq;

if(isset($_POST['pdbid'])) {
	$pdbid=$_POST['pdbid'];
	//$pdbid = trim(strtoupper($pdbid));
}
$dbparams[ 'ip'] = $sq.$_SERVER['REMOTE_ADDR'].$sq;
#-----------check if the pdb file exists in database------------#

$pdbfile = (($pdbid) ? "pdb/$pdbid.pdb" : "pdb/Custom.pdb");

if(check_pdb($pdbfile)) {
	unset($output);
	exec("gzip -f pdb/temp_renum.pdb",$output);
	if (! is_file("pdb/temp_renum.pdb.gz")) {
		$jsonparams[ 'error'] = "Failed to compress pdb file";
		echo getJSONString($jsonparams);
		die;
		//mydie("Failed to compress pdb file $pdbid.",'pdberr');
	}
	$pdbfilegz = "pdb/temp_renum.pdb.gz";
	$fp      = fopen($pdbfilegz, 'r');
	$content = fread($fp, filesize($pdbfilegz));
	$content = addslashes($content);
	fclose($fp);
	$dbparams[ 'ipdb'] = $sq.$content.$sq;
	//unlink("pdb/temp_filtered.pdb");
	//unlink("pdb/temp_renum.pdb.gz");
} else {
	$jsonparams[ 'error'] = "Failed during quality control of pdb";
	echo getJSONString($jsonparams);
	die;
	//mydie("Failed during quality control of pdb",'pdberr');
}
### now we have the pdbfileid for the submission ###
#-----------return if error---------------------------------#
/*if (count($error) > 0) {
	foreach ($varlist as $var) {
		echo "{";
		echo       '"' . $var . '":"' . $error[$var] . '"';
		echo "}";

		//$_SESSION["error_$var"] = $error[$var];
	}
	//header("Location: submit.php");
	die;
}*/

#-----------------update entry in job database-------------------------#
$dbparams[ 'pdbid'] = (($pdbid) ? $sq.$pdbid.$sq : "'Custom'");
/*if ($pdbid) {
	$pdbstr = $pdbid;
} else {
	$pdbstr = "Custom";
}*/

$authKey = getRandomString(24);

$dbparams[ 'harmonic'] = (isset($_POST[ 'constrain']) ? $sq.$_POST[ 'constrain'].$sq : $sq."0".$sq);
$dbparams[ 'emailFlag'] = (isset($_POST[ 'notify']) ? $sq.$_POST[ 'notify'].$sq : $sq."0".$sq);
$dbparams[ 'created_by'] = $sq.$_SESSION['userid'].$sq;

/*$harmonic = 0;
if (isset($_POST[ 'constrain']) && $_POST[ 'constrain'] == "1") {
	$harmonic = 1;
}

$notify = 0;
if (isset($_POST[ 'notify']) && $_POST[ 'notify'] == "1") {
	$notify = 1;
}*/

//$userid = $_SESSION['userid'];

/*$query = "INSERT INTO $tablejobs (created_by,pdbid,ipdb,title,ip,tsubmit,flag,harmonic,emailFlag) VALUES('$userid','$pdbstr','$content','$title','$ip',NOW(),'5','$harmonic','$notify')";*/

$dbparams[ 'authKey'] = $sq.$authKey.$sq;
$dbparams[ 'flag'] = $sq."0".$sq;
$dbparams[ 'mlcs'] = $sq.$_POST[ 'mlcs'].$sq;
$dbparams[ 'mlclist'] = $sq.$_POST[ 'mlclist'].$sq;
if($_SESSION[ 'username'] == "guest") {
	$dbparams[ 'guestEmail'] = $sq.$_POST[ 'guestEmail'].$sq;
}
$dbparams[ 'queue'] = $sq.$_POST[ 'queue'].$sq;
$dbparams[ 'tsubmit'] = "NOW()";
$query = prepareInsert($tablejobs, $dbparams);
$result = mysql_query($query) or mydie("Failed to insert pdb file in database",'pdberr');
$jobid = mysql_insert_id();
//$jobid = 1;
$jsonparams[ 'jobid'] = $jobid;
$jsonparams[ 'authKey'] = $authKey;
echo getJSONString($jsonparams);

/*unset($output);
exec("gunzip -f $pdbfilegz",$output);
if($output[0]!="") {
	mydie($output[0],'status');
}

unset($output);
exec("perl bin/run_filters.pl $pdbfile $jobid",$output);
echo $output[0];

unset($output);
unlink("exec/$pdbstr-$jobid/$pdbstr-$jobid.clash.xmgr");
exec("sh bin/getClashStats.sh $pdbfile $jobid",$output);
if($output[0]!="") {
	mydie($output[0],'status');
}

echo "{";
echo '"jobdir":"'.$pdbstr.'-'.$jobid.'"';
echo "}";*/

//unlink($pdbfile);
?>
