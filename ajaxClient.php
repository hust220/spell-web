<?php
	//Filename : ajaxClient.php

	include('config/config.inc.php');
	include('config/functions.inc.php');

	$function = $_POST[ 'function'];
	$function();	// Each function must return JSON. 
						// Php on redshift does not support json encoding: last checked on 09/10/2010. 
						// See getJSONString in the functions repository to encode JSON.

	function submitToChiron() {
		global $_POST, $tablejobs;
		$jobid = $_POST[ 'jobid'];
		$query = "UPDATE $tablejobs SET minimize='1', status='0' WHERE id='$jobid'";
		$result = mysql_query($query);
		if(mysql_affected_rows()==1) {
			$jsonparams[ 'msg'] = "success";
			echo getJSONString($jsonparams);
		} else {
			$jsonparams[ 'error'] = mysql_error();
			echo getJSONString($jsonparams);
		}
	}

	function fixSideChains() {
		global $_POST, $tablejobs, $workdir, $EXEC_COMPLEX_FIXBB;
		$jobid = $_POST[ 'jobid'];
		$resstr = $_POST[ 'resstr'];
		$reslist = explode("_",$resstr);
		$query = "SELECT pdbid, ipdb FROM $tablejobs WHERE id='$jobid'";
		$result = mysql_query($query);
		if(mysql_num_rows($result) == 1) {
			$row = mysql_fetch_array($result);
			$pdbid = $row[ 'pdbid'];
			$ipdbgz= $row[ 'ipdb'];
			if(!is_dir("$workdir/$pdbid-$jobid")) {
				mkdir("$workdir/$pdbid-$jobid");
			}
			$fh = fopen("$workdir/$pdbid-$jobid/dt",'w');
			fwrite($fh,"DEFAULT FIXNR\n");
			foreach ($reslist as $res):
				fwrite($fh,"$res NATAA\n");
			endforeach;
			fclose($fh);
			$fh = fopen("$workdir/$pdbid-$jobid/$pdbid-$jobid.pdb.gz",'w');
			fwrite($fh, $ipdbgz);
			fclose($fh);
			$output = array();
			exec("gunzip -f $workdir/$pdbid-$jobid/$pdbid-$jobid.pdb.gz", $output);
			exec("$EXEC_COMPLEX_FIXBB -i $workdir/$pdbid-$jobid/$pdbid-$jobid.pdb -o $workdir/$pdbid-$jobid/$pdbid-$jobid -t $workdir/$pdbid-$jobid/dt -n 1", $output, $retval);
			if($retval == 0) {
				unset($output);
				exec("cp $workdir/$pdbid-$jobid/$pdbid-$jobid.run0000 $workdir/$pdbid-$jobid/$pdbid-$jobid-fixed.pdb",$output);
				$jsonparams[ 'msg'] = "success";
				$jsonparams[ 'outfile'] = "exec/$pdbid-$jobid/$pdbid-$jobid-fixed.pdb";
			} else {
				$jsonparams[ 'error'] = "failed";
			}
		} else {
			$jsonparams[ 'error'] = "failed";
			$jsonparams[ 'mysql_error'] = mysql_error();
		}
		echo getJSONString($jsonparams);
	}

	function generateReport() {
		global $_POST, $tablejobs;
		sleep(5);
		$jobid  = $_POST[ 'jobid'];
		$table  = $_POST[ 'table'];
		$field  = $_POST[ 'field'];
		$rtype  = $_POST[ 'rtype'];
		$fextn  = $_POST[ 'fextn'];
		$query = "SELECT $table.$field, $tablejobs.pdbid FROM $table LEFT JOIN $tablejobs ON ($tablejobs.id=$table.jobid) WHERE $tablejobs.id=$jobid";
		$result = mysql_query($query);
		if(mysql_num_rows($result) == 1) {
			$row = mysql_fetch_row($result);
			$filegz = $row[0];
			$pdbid  = $row[1];
			if(!is_dir("exec/$pdbid-$jobid")) {
				mkdir("exec/$pdbid-$jobid");
			}
			$fh = fopen("exec/$pdbid-$jobid/$pdbid-$jobid-$rtype.$fextn.gz",'w');
			fwrite($fh,$filegz);
			fclose($fh);
			$output = array();
			exec("gunzip -f exec/$pdbid-$jobid/$pdbid-$jobid-$rtype.$fextn.gz",$output,$retval);
			if($retval!=0) {
				$jsonparams[ 'error'] = "gunzip-failed";
				echo getJSONString($jsonparams);
				die;
			}
			/*if($field == "phipsipdf") {
				unset($output);
				exec("convert -rotate 90 -density 600x600 -resize 2000x exec/$pdbid-$jobid/$pdbid-$jobid-dihe.$fextn -density 600x600 exec/$pdbid-$jobid/$pdbid-$jobid-ramaplot.png", $output, $retval);
				if($retval != 0) {
					$jsonparams[ 'error'] = "Failed to create image";
					echo getJSONString($jsonparams);
					die;
				}
			}*/
			if($fextn == "tex") {
				unset($output);
				$cwd = getcwd();
				chdir("exec/$pdbid-$jobid");
				exec("pdflatex $pdbid-$jobid-$rtype.$fextn",$output,$retval);
				chdir($cwd);
				if($retval!=0) {
					$jsonparams[ 'error'] = "latex-failed";
					echo getJSONString($jsonparams);
					die;
				}
			}
			$jsonparams[ 'jobdir'] = "$pdbid-$jobid";
			$jsonparams[ 'msg'] = "success";
			echo getJSONString($jsonparams);
			die;
		} else {
		
			$jsonparams[ 'error'] = mysql_error();
			echo getJSONString($jsonparams);
			die;
		}
	}

?>
	
