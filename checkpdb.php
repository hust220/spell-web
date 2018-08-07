<?php
	require('config/config.inc.php');
	$uploaddir = "pdb";
	$error = "";
	$msg = "";
	$smlc = "no";
	$mlclist = "";
	$pdbid = $_POST['pdbid'];
	$output = array();
	$nl = "\n";
        $jsonvar = array('error','msg','smlc','mlclist','missing','nchn');
	$jsonstr = "";
	$wgetstr = "wget -m -nd -P ".$uploaddir." http://www.rcsb.org/pdb/files/".$pdbid.".pdb";
	exec($wgetstr,$output);

	foreach ($output as $line) {
		if(preg_match('/ERROR 404/',$line)==1) {
			$error .= "<small>Invalid pdb code</small>";
			break;
		}
	}
	if(file_exists("$uploaddir/$pdbid.pdb")) {
		$pdbfile = "$uploaddir/$pdbid.pdb";
		$msg .= "<small>Verified successfully</small>";
		unset($output);
		exec("$getmlcs $pdbfile", $output);
		if(file_exists($output[0])) {
			$mlcfile = fopen($output[0],"r");
			$mlclist = str_replace("\n",":",fread($mlcfile, filesize($output[0])));
			$smlc = "yes";
		}
	} else {
		$error .= "<small>Invalid PDB code</small>";
	}
       exec("cd $uploaddir; ../$exec_extraCheckPDB '$pdbid.pdb'; mv whole.pdb '$pdbid.pdb'; cd ../");
       $handle = fopen("pdb/cont.report", "r");
       if ($handle) {
	 while (($line = fgets($handle)) !== false) {
	   $missing .= trim($line) . ", ";
	 }
       }
       $missing=chop($missing,", ");

       $chains = fopen("pdb/chains.report", "r");
       if ($chains) {
	 while (($line = fgets($chains)) !== false) {
	   $nchn .= trim($line);
	 }
       }

	foreach ($jsonvar as $jv):
		$jsonstr .= '"' . $jv . '" : "' . ${$jv} . '",';
	endforeach;
	$jsonstr = rtrim($jsonstr,",");
	echo "{".$nl."\t".$jsonstr.$nl."}";
?>
