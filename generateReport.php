<?php
include_once "config/para.inc.php";
include_once "config/functions.inc.php";
# ---- Initialization ---- #
#$_POST[ 'jobname'] = $argv[1];
#$_POST[ 'type'] = $argv[2];
$jobname    = $_POST[ 'jobname'];
$mode       = $_POST[ 'type'];
$jobdir     = $workdir."/".$jobname;
$oprefix    = $jobdir."/".$jobname;
$opdfdir    = $jobdir."/pdf";
$jsonParams = array();
$json_hash  = array(); # Populated using the function - parseJSONString
$jsonstr    = "";

if(!is_dir($opdfdir)) {
	if(!mkdir($opdfdir, 0777)) {
		$jsonParams[ 'job'] = $jobname;
		$jsonParams[ 'error'] = "Could not create directories. Contact the administrator with this message";
		echo getJSONString($jsonParams);
		die;
	}
}

$json_fh = fopen("$oprefix.json",'r');
$jsonstr = fread($json_fh,filesize("$oprefix.json"));
fclose($json_fh);

$json_hash = parseJSONString($jsonstr);
if($mode == "summary") {
	generateSummary($json_hash, $jobname);
} else if($mode == "full") {
	generateFullReport($json_hash, $jobname);
} else if($mode == "session") {
	generatePyMOLSession($json_hash, $jobname);
}
?>
