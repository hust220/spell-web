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
function mydie($str,$var){
	//$_SESSION["error_$var"] = $str;
	echo "{";
	echo         '"error":"' . $str . '"'.",\n";
	echo         '"msg":"' . $var . '"';
	echo "}";
	//header("Location: submit.php");
	die;
}

#------------Variable Declaration-------------------#
$dbparams = array();
$jsonparams = array('msg','error');
$sq = "'";
//$error = array();

#------------Temporary Exit - for testing ----------#
#$jsonparams[ 'msg'] = $jobid;
#echo getJSONString($jsonparams);
#return;

#-------Get submitted data and check format--------#
if(isset($_POST['title'])) {
	$title=$_POST['title'];
	$title=str_replace(" ","-",$title);
} else {
	$title="Custom";
}

if(isset($_POST['pdbid'])) {
	$pdbid=$_POST['pdbid'];
	//$pdbid = trim(strtoupper($pdbid));
}
#-----------check if the pdb file exists in database------------#

$pdbfile = (($pdbid) ? "pdb/$pdbid.pdb" : "pdb/Custom.pdb");

$jobid = rand(0,32768);
if(check_pdb($pdbfile)) {
	unset($output);
	exec("perl bin/run_filters.pl $pdbfile $jobid",$output);
	echo $output[0];
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

$jsonparams[ 'msg'] = $jobid;
//echo getJSONString($jsonparams);

/*unset($output);
exec("gunzip -f $pdbfilegz",$output);
if($output[0]!="") {
	mydie($output[0],'status');
}

echo "{";
echo '"jobdir":"'.$pdbstr.'-'.$jobid.'"';
echo "}";*/

//unlink($pdbfile);
?>
