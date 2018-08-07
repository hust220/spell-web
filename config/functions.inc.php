<?php
require_once('para.inc.php');

function getLog($id) {
	global $tablejobs;
	$query  = "SElECT log FROM $tablejobs WHERE id='$id'";
	$result = mysql_query($query);
	mysql_error();
	$row = mysql_fetch_array($result);
	$log_array = getgzStreamAsArray($row[ 'log']);
	$log = implode($log_array,'');
	return $log;
}

function writePyScript($id) {
	global $tablejobs, $tableresults;
	$query   = "SELECT pdbid, ipdb FROM $tablejobs WHERE id='$id'";
	$row     = mysql_fetch_array(mysql_query($query));
	$pdbid   = $row[ 'pdbid'];
	$ipdb    = $row[ 'ipdb'];
	$query   = "SELECT fpdb, iClashR, fClashR FROM $tableresults WHERE jobid='$id'";
	$row     = mysql_fetch_array(mysql_query($query));
	$fpdb    = $row[ 'fpdb'];
	$iclashr = $row[ 'iClashR'];
	$fclashr = $row[ 'fClashR'];
	$fp      = fopen("tmp/i.pdb", 'w');
	fwrite($fp, $ipdb);
	fclose($fp);
	$fp      = fopen("tmp/i.clash", 'w');
	fwrite($fp, $iclashr);
	fclose($fp);
	$fp      = fopen("tmp/o.pdb", 'w');
	fwrite($fp, $fpdb);
	fclose($fp);
	$fp      = fopen("tmp/o.clash", 'w');
	fwrite($fp, $fclashr);
	fclose($fp);
	unset($output);
	exec("python bin/pyGenerator.py tmp/i.pdb tmp/i.clash tmp/o.pdb tmp/o.clash download/$pdbid-$id.py",$output);
	return "download/$pdbid-$id.py";
}

function check_pdb($filename){
	include('config/para.inc.php');
	$temp_renum_pdb    = "pdb/temp_renum.pdb";
	$temp_filtered_pdb = "pdb/temp_filtered.pdb";
	$temp_Medusa_pdb   = "pdb/temp_Medusa.pdb";
	#Filter the pdb file first (filter out unknown residue types)
	unset($output);
	exec("$EXEC_PDBRENUM '$filename'  $temp_renum_pdb 2>&1",$output);
	#Filter the pdb file first (filter out unknown residue types)
	#unset($output);
	#exec("$EXEC_PDBFILTER '$temp_renum_pdb'  $temp_filtered_pdb 2>&1",$output);
	#use medusa to read and generate pdbfile
	//unset($output);
	//exec("$EXEC_PDBMISSC -i $temp_renum_pdb -o $temp_Medusa_pdb 2>&1 ",$output);
	return is_file($temp_renum_pdb);
}

function getRandomString($length) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $string = ''; 

    for ($p = 0; $p < $length; $p++) {
        $string .= $characters[mt_rand(0, strlen($characters)-1)];
    }

    return $string;
}

function getgzStreamAsArray($gzstream) {
	$fp = fopen("download/tmp.gz",'w');
	fwrite($fp, $gzstream);
	fclose($fp);
	return getgzFileAsArray("download/tmp.gz");
}

function getgzFileAsArray($filenamegz) {
	exec("gunzip -f $filenamegz");
	$filename = substr($filenamegz,0,strrpos($filenamegz,"."));
	return file($filename);
}

function prepareInsert($dbtable, $params) {
	$fieldlist = "";
	while(list($key,$value) = each($params)) {
		$fieldlist .= "`".$key."`,";
		$valuelist .= $value.",";
	}
	$fieldlist = rtrim($fieldlist, ",");
	$valuelist = rtrim($valuelist, ",");
	$query = "INSERT INTO $dbtable ($fieldlist) VALUES($valuelist)";
	return $query;
}

function getJSONString($params) {
	$jsonstr = "{";
	while(list($key,$value) = each($params)) {
		$jsonstr .= '"'.$key.'":"'.$value.'",';
	}
	$jsonstr = rtrim($jsonstr,",");
	$jsonstr .= "}";
	return $jsonstr;
}

function parseJSONString($jsonstr) {
	$cleaned_json = str_replace("{","",str_replace("}","",str_replace("\"","",$jsonstr)));
	$pairs        = explode(",",$cleaned_json);
	foreach ($pairs as $pair):
		list($key,$value) = explode(":",$pair);
		$json_hash{$key}  = $value;
	endforeach;
	return $json_hash;
}

function emailNotification($email_address,$link,$jobid,$guestFlag){
	if(empty($link)) {
		$link = "http://chiron.dokhlab.org/";
	}
	$eol = "\r\n"; # for unix
# Boundary for marking the split & Multitype Headers
	$mime_boundary=md5(date('r',time()));

	$send_to  = $email_address;
	$send_from= "chiron@dokhlab.org";
	$reply_to = "no-reply@email-notification";
	$subject  = "Chiron job status notification";
		
	$headers .= 'From: '.$send_from.$eol;
	$headers .= 'Reply-To: '.$reply_to.$eol;
	$headers .= "Message-ID: <".$now." TheSystem@".$_SERVER['SERVER_NAME'].">".$eol;
	$headers .= "X-Mailer: PHP v".phpversion().$eol;          // These two to help avoid spam-filters
	$headers .= 'MIME-Version: 1.0'.$eol;
	$headers .= "Content-Type: multipart/related; boundary=\"".$mime_boundary."\"".$eol;
	ob_start(); //Turn on output buffering

	$msg = "";
#HTML version
	/*$msg .= "--".$mime_boundary.$eol;
	$msg .= 'Content-Type: "text/html"; charset="iso-8859-1"'.$eol;
	$msg .= "\nThanks for using Chiron!<br><br>\n" .
		"Your job - $jobid - has been processed.\n".
		"Please click the following link to check the results:<br>" .
		"\n\n<a href=\"$link\">$link</a><br><br>\n\ncheers!\n\n<br><br>Chiron Administrators".$eol.$eol;*/
#Text version
	$msg .= "--".$mime_boundary.$eol;
	$msg .= "Content-Type: text/plain; charset=iso-8859-1".$eol;
	$msg .= "\nThanks for using Chiron!\n" .
		"Your job - $jobid - has been processed.\n".
		"Please click the link below or copy and paste the address in a web browser to retrieve results.\n".
		"Address : $link\n\ncheers!\n\nChiron Administrators\n\n";
	if(!empty($guestFlag)) {
		$msg .= "WARNING : Please retrieve your results in the next 48 hours. Your job will be lost after 48 hours.\n\n";
	}
	$msg .=	"NOTE : This e-mail is automatically generated. Replies to this e-mail do not reach the administrator. Use the contact page on the http://chiron.dokhlab.org to contact the administrators.".$eol.$eol;
	$msg .= "--".$mime_boundary.$eol;
	
	$mail_sent = @mail($send_to, $subject, $msg, $headers);
}  

function emailConfirm($email_address, $link) {
  $eol = "\n"; # for unix
# Boundry for marking the split & Multitype Headers
  $mime_boundary=md5(time());
  
  $headers .= 'From: Chiron - Rapid Protein Energy Minimization Server '.$eol;
  $headers .= 'Reply-To: no-reply@email-notification'.$eol;
  $headers .= "Message-ID: <".$now." TheSystem@".$_SERVER['SERVER_NAME'].">".$eol;
  $headers .= "X-Mailer: PHP v".phpversion().$eol;          // These two to help avoid spam-filters
  $headers .= 'MIME-Version: 1.0'.$eol;
  $headers .= "Content-Type: multipart/related; boundary=\"".$mime_boundary."\"".$eol;

  $mail_subject = "Chiron user email confirmation";
  

  $msg = "";
  $msg .= "--".$mime_boundary.$eol;
  $msg .= "Content-Type: text/html; charset=iso-8859-1".$eol;
  $msg .= "\nThanks for using Chiron!<br/><br/>\n" .
        "\nPlease click on the following link to confirm your email address:<br/>" .
        "\n\n<a href=\"$link\">$link</a><br/><br/>\n\nThanks!\n\n<br/><br/>Chiron Administrators".$eol.$eol;
#Text version
  $msg .= "--".$mime_boundary.$eol;
  $msg .= "Content-Type: text/plain; charset=iso-8859-1".$eol;
  $msg .= "\nThanks for using Chiron!\n" .
        "\nPlease copy and paste the following link to web browser confirm your email address:" .
        "\n\n$link\n\nThanks!\n\nChiron Administrators".$eol.$eol;
  mail($email_address, $mail_subject, $msg, $headers);
}

function dieJSON($str,$var){
	echo "{";
	echo         '"error":"' . $str . '"'.",\n";
	echo         '"msg":"' . $var . '"';
	echo "}";
	die;
}

function pValue($observed, $mean, $stdev) {
	$output = array();
	$zsc    = ($observed-$mean)/$stdev;
	exec("bin/p-value.linux $zsc", $output);
	return $output[0];
	unset($output);
}

function generateImage($gzstream, $jobname, $extension) {
	$output = array();
	if(is_writable("exec")) {
		if(!is_dir("exec/$jobname")) {
			mkdir("exec/$jobname");
		}
		$fp = fopen("exec/$jobname/$jobname-$extension.pdf.gz",'w');
		fwrite($fp, $gzstream);
		fclose($fp);
		unset($output);
		exec("gunzip -f exec/$jobname/$jobname-$extension.pdf.gz",$output);
		if(!empty($output[0])) {
			echo "Unable to write stream";
			die;
		}
		unset($output);
		exec("convert -flatten exec/$jobname/$jobname-$extension.pdf exec/$jobname/$jobname-$extension.jpeg", $output);
	}
}
# Deprecated method - now done on faust
function generatePlots($jobname) {
	global $workdir;
	$output = array();
	$cwd    = getcwd();
	if(is_writable("$workdir/$jobname/pdf")) {
		chdir("$workdir/$jobname/pdf");
		unset($output);
		exec("ps2epsi ../$jobname-clash.ps", $output);
		exec("epstopdf $jobname-clash.epsi", $output);
		exec("ps2epsi ../$jobname-hbond-shell.ps", $output);
		exec("epstopdf $jobname-hbond-shell.epsi", $output);
		exec("ps2epsi ../$jobname-hbond-buried.ps", $output);
		exec("epstopdf $jobname-hbond-buried.epsi", $output);
		exec("ps2epsi ../$jobname-sasa.ps", $output);
		exec("epstopdf $jobname-sasa.epsi", $output);
	}
	chdir($cwd);
}

# Deprecated method - now done on faust
function generateSummary($json_hash, $jobname) {
	global $workdir;
	$cwd     = getcwd();
	$output  = array();
	$retvar  = "";
	$tex_str = "";

	$tex_str .= "%This is a TeX file which when compiled generates a pdf with all the dossiers from all filters\n";
	$tex_str .= "%This file provides only the brief report.\n";
	
	$tex_str .= "%----Begin Preamble----%\n";
	$tex_str .= "\\documentclass[11pt,twoside,letterpaper]{article}\n";
	$tex_str .= "\n";
	$tex_str .= "\\usepackage[margin=2.5cm]{geometry}\n";
	$tex_str .= "\\usepackage{fancyhdr}\n";
	$tex_str .= "\\usepackage{type1cm} % scalable\n";
	$tex_str .= "\\usepackage{lettrine}\n";
	$tex_str .= "\\usepackage{graphicx}\n";
	$tex_str .= "\\usepackage{booktabs}\n";
	$tex_str .= "\\usepackage{multirow}\n";
	$tex_str .= "\\usepackage{amsmath}\n";
	$tex_str .= "\\usepackage[table]{xcolor}\n";
	$tex_str .= "\\usepackage{colortbl}\n";
	$tex_str .= "\\usepackage[raggedright]{sidecap}\n";
	$tex_str .= "\\setlength{\\headheight}{14pt}\n";
	$tex_str .= "\n";
	$tex_str .= "%----End Preamble----%\n";
	$tex_str .= "%----Begin Document----%\n";
	$tex_str .= "\\begin{document}\n";
	$tex_str .= "\n";
	$tex_str .= "%Header and footer\n";
	$tex_str .= "\\pagestyle{fancy}\n";
	$tex_str .= "\\lhead{}\n";
	$tex_str .= "\\chead{}\n";
	$tex_str .= "\\rhead{\\bfseries Summary of results}\n";
	$tex_str .= "\\lfoot{Generated by Gaia}\n";
	$tex_str .= "\\cfoot{\\thepage}\n";
	$tex_str .= "\\rfoot{\\today}\n";
	$tex_str .= "\\renewcommand{\\headrulewidth}{0.4pt}\n";
	$tex_str .= "\\renewcommand{\\footrulewidth}{0.4pt}\n";
	$tex_str .= "\n";
	$tex_str .= "%Introduction\n";
	$tex_str .= "\\lettrine[lines=2,nindent=-1pt]{G}{aia} evaluates the quality of a given protein structure based on different criteria. The statistics used by Gaia for comparison are generated from high resolution crystal structures. The observed and expected scores for different criteria used by Gaia to score the given structure are summarized in the table below:\n";
	$tex_str .= "\n";
	$tex_str .= "%Summary table\n";
	$tex_str .= "\\begin{table}[!h]\n";
	$tex_str .= "\t\\textbf{\\caption{Summary of scores for the input structure}}\n";
	$tex_str .= "\t\\begin{center}\n";
	$tex_str .= "\t\t\\begin{tabular}{l@{\\hspace{1cm}}c@{\\hspace{1cm}}cc}\n";
	$tex_str .= "\t\t\\toprule%\n";
	$tex_str .= "\t\t\\multicolumn{2}{l}{\\cellcolor[gray]{0.9} \\textbf{Criterion}} & \\cellcolor[gray]{0.9} \\textbf{Observed} & \\cellcolor[gray]{0.9} \\textbf{Target} \\\\\\midrule\n";
	$clash_cellcolor = ($json_hash[ 'clashscore'] > 0.02) ? "purple!50!white" : "white";
	$tex_str .= "\t\t\\multicolumn{2}{l}{Steric clashes} & \\cellcolor{".$clash_cellcolor."}".sprintf("%.3f",$json_hash[ 'clashscore'])." & 0.02\\\\\\midrule\n";
	$unsat_shell_cellcolor = ($json_hash[ 'percent_shell'] > 9.56) ? "purple!50!white" : "white";
	$tex_str .= "\t\t\\multirow{2}{*}{Hydrogen bonds}  & \\%Unsatisfied in shell & \\cellcolor{".$unsat_shell_cellcolor."}".sprintf("%.3f",$json_hash[ 'percent_shell'])." & 9.56\\\\\\cmidrule(r){2-4}\n";
	$unsat_core_cellcolor  = ($json_hash[ 'percent_unsatisfied'] > 1.45) ? "purple!50!white" : "white";
	$tex_str .= "\t\t\t& \\%Unsatisfied in core & \\cellcolor{".$unsat_core_cellcolor."}".sprintf("%.3f",$json[ 'percent_unsatisfied'])." & 1.45\\\\\\midrule\n";
	$sasa_cellcolor = ($json[ 'sasa_rescaled'] > 221.64) ? "purple!50!white" : "white";
	$tex_str .= "\t\t\\multicolumn{2}{l}{Solvent accessible surface area} & \\cellcolor{".$sasa_cellcolor."}".sprintf("%.3f",$json_hash[ 'sasa_rescaled'])." & 221.64\\\\\\midrule\n";
	$tex_str .= "\t\t\\multicolumn{2}{l}{Void volume} & 10 & 11\\\\\\midrule\n";
	$tex_str .= "\t\t\\multicolumn{2}{l}{Protein geometry} & 12 & 13 \\\\\\midrule\n";
	$scchi_cellcolor = "white";
	$tex_str .= "\t\t\\multicolumn{2}{l}{Sidechain \$\\chi\$-angles} & 14 & 15 \\\\\\bottomrule\n";
	$tex_str .= "\t\\end{tabular}\n";
	$tex_str .= "\t\\end{center}\n";
	$tex_str .= "\\end{table}\n";
	$tex_str .= "\n";
	$tex_str .= "%Steric Clashes\n";
	$tex_str .= "\\begin{center}\n";
	$tex_str .= "\t\\textbf{Figure 1. Comparison of clash-score with the distribution of clash-scores of high resolution crystal structures}\n";
	$tex_str .= "\\end{center}\n";
	$tex_str .= "\n";
	$tex_str .= "\\begin{center}\n";
	$tex_str .= "\t\\includegraphics[width=0.55\\textwidth]{".$jobname."-clash.pdf}\\\\\n";
	$tex_str .= "\\end{center}\n";
	$tex_str .= "Clash score for the input structure is \\textbf{".sprintf("%.4f",$json_hash[ 'clashscore'])."}. The red line in the plot above, represents the clash score of the input structure in context of the distribution of clash scores for high resolution crystal structures.\n";
	$tex_str .= "\n";
	$tex_str .= "%Hydrogen bonds in the shell\n";
	$tex_str .= "\\begin{center}\n";
	$tex_str .= "\t\\includegraphics[width=0.55\\textwidth]{".$jobname."-hbond-shell.pdf}\\\\\n";
	$tex_str .= "\\end{center}\n";
	$tex_str .= "\\%Unsatisfied hydrogen bonds in the shell of the input structure is \\textbf{".sprintf("%.4f",$json_hash[ 'percent_shell'])."}. The red line in the plot above, represents the \\%unsatisfied hydrogen bonds in the shell of the input structure in context of the distribution generated from high resolution crystal structures.\n";
	$tex_str .= "\n";
	$tex_str .= "\\vspace{1cm}\n";
	$tex_str .= "\n";
	$tex_str .= "%Hydrogen bonds in the core\n";
	$tex_str .= "\\begin{center}\n";
	$tex_str .= "\t\\includegraphics[width=0.55\\textwidth]{".$jobname."-hbond-buried.pdf}\\\\\n";
	$tex_str .= "\\end{center}\n";
	$tex_str .= "\\%Unsatisfied hydrogen bonds in the core of the input structure is \\textbf{".sprintf("%.4f",$json_hash[ 'percent_unsatisfied'])."}. The red line in the plot above represents the \\%unsatisfied hydrogen bonds in the core of the input structure in context of the distribution generated from high resolution crystal structures.\n";
	$tex_str .= "\n";
	$tex_str .= "%Solvent accessible surface area\n";
	$tex_str .= "\\begin{center}\n";
	$tex_str .= "\t\\includegraphics[width=0.55\\textwidth]{".$jobname."-sasa.pdf}\\\\\n";
	$tex_str .= "\\end{center}\n";
	$tex_str .= "The solvent accessible surface area (SASA) of the input structure is \\textbf{".sprintf("%.4f",$json_hash[ 'sasa'])."} \${\\mathring{A}\\textsuperscript{2}}\$. In order to eliminate bias due to length of the protein, we normalize the SASA with a scaling exponent of 0.74 for the chain length. The rescaled SASA of the input structure is \\textbf{".sprintf("%.4f",$json_hash[ 'sasa_rescaled'])."} \${\\mathring{A}\\textsuperscript{2}}\$. The red line in the plot above represents the rescaled SASA of the input structure in context of the distribution generated from high resolution crystal structures.\n";
	$tex_str .= "\n";
	$tex_str .= "\\end{document}\n";
	$tex_str .= "%----End Document----%\n";
	
	$tex_fh   = fopen("$workdir/$jobname/pdf/$jobname-summary.tex",'w');
	fwrite($tex_fh, $tex_str);
	fclose($tex_fh);

	generatePlots($jobname);
	chdir("$workdir/$jobname/pdf");
	exec("pdflatex $jobname-summary.tex", $output, $retvar);
	chdir($cwd);
}

# Deprecated method - now done on faust
function generateFullReport($json_hash, $jobname) {
	global $workdir;
	$cwd     = getcwd();
	$tex_str = "";

	$tex_str .= "%This is a TeX file which when compiled generates a pdf with all the dossiers from all filters\n";
	$tex_str .= "%This file provides a complete report from Gaia.\n";
	
	$tex_str .= "%----Begin Preamble----%\n";
	$tex_str .= "\\documentclass[11pt,twoside,letterpaper]{article}\n";
	$tex_str .= "\n";
	$tex_str .= "\\usepackage[margin=2.5cm]{geometry}\n";
	$tex_str .= "\\usepackage{fancyhdr}\n";
	$tex_str .= "\\usepackage{type1cm} % scalable\n";
	$tex_str .= "\\usepackage{lettrine}\n";
	$tex_str .= "\\usepackage{graphicx}\n";
	$tex_str .= "\\usepackage{booktabs}\n";
	$tex_str .= "\\usepackage{multirow}\n";
	$tex_str .= "\\usepackage{amsmath}\n";
	$tex_str .= "\\usepackage[table]{xcolor}\n";
	$tex_str .= "\\usepackage{colortbl}\n";
	$tex_str .= "\\usepackage[raggedright]{sidecap}\n";
	$tex_str .= "\\setlength{\\headheight}{14pt}\n";
	$tex_str .= "\n";
	$tex_str .= "%----End Preamble----%\n";
	$tex_str .= "%----Begin Document----%\n";
	$tex_str .= "\\begin{document}\n";
	$tex_str .= "\n";
	$tex_str .= "%Header and footer\n";
	$tex_str .= "\\pagestyle{fancy}\n";
	$tex_str .= "\\lhead{}\n";
	$tex_str .= "\\chead{}\n";
	$tex_str .= "\\rhead{\\bfseries Comprehensive report for job:".$jobname."}\n";
	$tex_str .= "\\lfoot{Generated by Gaia}\n";
	$tex_str .= "\\cfoot{\\thepage}\n";
	$tex_str .= "\\rfoot{\\today}\n";
	$tex_str .= "\\renewcommand{\\headrulewidth}{0.4pt}\n";
	$tex_str .= "\\renewcommand{\\footrulewidth}{0.4pt}\n";
	$tex_str .= "\n";
	$tex_str .= "%Introduction\n";
	$tex_str .= "\\lettrine[lines=2,nindent=-1pt]{G}{aia} evaluates the quality of a given protein structure based on different criteria. The statistics used by Gaia for comparison are generated from high resolution crystal structures. The observed and expected scores for different criteria used by Gaia to score the given structure are summarized in the table below:\n";
	$tex_str .= "\n";
	$tex_str .= "%Summary table\n";
	$tex_str .= "\\begin{table}[!h]\n";
	$tex_str .= "\t\\textbf{\\caption{Summary of scores for the input structure}}\n";
	$tex_str .= "\t\\begin{center}\n";
	$tex_str .= "\t\t\\begin{tabular}{l@{\\hspace{1cm}}c@{\\hspace{1cm}}cc}\n";
	$tex_str .= "\t\t\\toprule%\n";
	$tex_str .= "\t\t\\multicolumn{2}{l}{\\cellcolor[gray]{0.9} \\textbf{Criterion}} & \\cellcolor[gray]{0.9} \\textbf{Observed} & \\cellcolor[gray]{0.9} \\textbf{Target} \\\\\\midrule\n";
	$clash_cellcolor = ($json_hash[ 'clashscore'] > 0.02) ? "purple!50!white" : "white";
	$tex_str .= "\t\t\\multicolumn{2}{l}{Steric clashes} & \\cellcolor{".$clash_cellcolor."}".sprintf("%.3f",$json_hash[ 'clashscore'])." & 0.02\\\\\\midrule\n";
	$unsat_shell_cellcolor = ($json_hash[ 'percent_shell'] > 9.56) ? "purple!50!white" : "white";
	$tex_str .= "\t\t\\multirow{2}{*}{Hydrogen bonds}  & \\%Unsatisfied in shell & \\cellcolor{$unsat_shell_cellcolor}".sprintf("%.3f",$json_hash[ 'percent_shell'])." & 9.56\\\\\\cmidrule(r){2-4}\n";
	$unsat_core_cellcolor  = ($json_hash[ 'percent_unsatisfied'] > 1.45) ? "purple!50!white" : "white";
	$tex_str .= "\t\t\t& \\%Unsatisfied in core & \\cellcolor{$unsat_core_cellcolor}".sprintf("%.3f",$json[ 'percent_unsatisfied'])." & 1.45\\\\\\midrule\n";
	$sasa_cellcolor = ($json[ 'sasa_rescaled'] > 221.64) ? "purple!50!white" : "white";
	$tex_str .= "\t\t\\multicolumn{2}{l}{Solvent accessible surface area} & \\cellcolor{$sasa_cellcolor}".sprintf("%.3f",$json_hash[ 'sasa_rescaled'])." & 221.64\\\\\\midrule\n";
	$tex_str .= "\t\t\\multicolumn{2}{l}{Void volume} & 10 & 11\\\\\\midrule\n";
	$tex_str .= "\t\t\\multicolumn{2}{l}{Protein geometry} & 12 & 13 \\\\\\midrule\n";
	$scchi_cellcolor = ($json[ 'schain_z'] > 1.0653) ? "purple!50!white" : "white";
	$tex_str .= "\t\t\\multicolumn{2}{l}{Sidechain \$\\chi\$-angles} & 14 & 15 \\\\\\bottomrule\n";
	$tex_str .= "\t\\end{tabular}\n";
	$tex_str .= "\t\\end{center}\n";
	$tex_str .= "\\end{table}\n";
	$tex_str .= "\n";
	$tex_str .= "%Steric Clashes\n";
	$tex_str .= "\\begin{center}\n";
	$tex_str .= "\t\\textbf{Figure 1. Comparison of clash-score with the distribution of clash-scores of high resolution crystal structures}\n";
	$tex_str .= "\\end{center}\n";
	$tex_str .= "\n";
	// Write a list of initial clashes
	$tex_str .= "%Initial clash report\n";
	$tex_str .= "\\begin{table}[h!]\n";
	$tex_str .= "\t\\textbf{\\caption{List of clashes in the input structure}}\n";
	$tex_str .= "\t\\begin{center}\n";
	$tex_str .= "\t\\begin{tabular}{lr>{\\hspace{1cm}}lr>{\\hspace{1cm}}r>{\\hspace{1cm}}r>{\\hspace{1cm}}r}\n";
	$tex_str .= "\t\t\\toprule%\n";
	$tex_str .= "\t\t\t\\rowcolor[gray]{0.9} \\textbf{\$Atom_{i}\$} & \\textbf{\$Res_{i}\$} & \\textbf{\$Atom_{j}\$} & \\textbf{\$Res_{j}\$} & \\textbf{\$d_{acc}\$} & \\textbf{\$d_{obs}\$} & \textbf{\$\\Delta{G}_{VDWR}\$} \\\\\\midrule\n";

	//		CD & 3 & OD2 & 24 & 3.660 & 3.161 & 0.586\\\midrule
	$tex_str .= "\t\t\\end{tabular}\n";
	$tex_str .= "\t\\end{center}\n";
	$tex_str .= "\\end{table}\n";

	$tex_str .= "\\begin{center}\n";
	$tex_str .= "\t\\includegraphics[width=0.55\\textwidth]{".$jobname."-clash.pdf}\\\\\n";
	$tex_str .= "\\end{center}\n";
	$tex_str .= "Clash score for the input structure is \\textbf{".sprintf("%.4f",$json_hash[ 'clashscore'])."}. The red line in the plot above, represents the clash score of the input structure in context of the distribution of clash scores for high resolution crystal structures.\n";
	$tex_str .= "\n";
	$tex_str .= "%Hydrogen bonds in the shell\n";
	$tex_str .= "\\begin{center}\n";
	$tex_str .= "\t\\includegraphics[width=0.55\\textwidth]{".$jobname."-hbond-shell.pdf}\\\\\n";
	$tex_str .= "\\end{center}\n";
	$tex_str .= "\\%Unsatisfied hydrogen bonds in the shell of the input structure is \\textbf{".sprintf("%.4f",$json_hash[ 'percent_shell'])."}. The red line in the plot above, represents the \\%unsatisfied hydrogen bonds in the shell of the input structure in context of the distribution generated from high resolution crystal structures.\n";
	$tex_str .= "\n";
	$tex_str .= "\\vspace{1cm}\n";
	$tex_str .= "\n";
	$tex_str .= "%Hydrogen bonds in the core\n";
	$tex_str .= "\\begin{center}\n";
	$tex_str .= "\t\\includegraphics[width=0.55\\textwidth]{".$jobname."-hbond-buried.pdf}\\\\\n";
	$tex_str .= "\\end{center}\n";
	$tex_str .= "\\%Unsatisfied hydrogen bonds in the core of the input structure is \\textbf{".sprintf("%.4f",$json_hash[ 'percent_unsatisfied'])."}. The red line in the plot above represents the \\%unsatisfied hydrogen bonds in the core of the input structure in context of the distribution generated from high resolution crystal structures.\n";
	$tex_str .= "\n";
	$tex_str .= "%Solvent accessible surface area\n";
	$tex_str .= "\\begin{center}\n";
	$tex_str .= "\t\\includegraphics[width=0.55\\textwidth]{".$jobname."-sasa.pdf}\\\\\n";
	$tex_str .= "\\end{center}\n";
	$tex_str .= "The solvent accessible surface area (SASA) of the input structure is \\textbf{".sprintf("%.4f",$json_hash[ 'sasa'])."} \${\\mathring{A}\\textsuperscript{2}}\$. In order to eliminate bias due to length of the protein, we normalize the SASA with a scaling exponent of 0.74 for the chain length. The rescaled SASA of the input structure is \\textbf{".sprintf("%.4f",$json_hash[ 'sasa_rescaled'])."} \${\\mathring{A}\\textsuperscript{2}}\$. The red line in the plot above represents the rescaled SASA of the input structure in context of the distribution generated from high resolution crystal structures.\n";
	$tex_str .= "\n";
	$tex_str .= "\\end{document}\n";
	$tex_str .= "%----End Document----%\n";

	$tex_fh   = fopen("$workdir/$jobname/pdf/$jobname-report.tex",'w');
	fwrite($tex_fh, $tex_str);
	fclose($tex_fh);
	generatePlots($jobname);
	chdir("$workdir/$jobname/pdf");
	exec("pdflatex $jobname-report.tex");
	chdir($cwd);
}

# Deprecated method - now done on faust
function generatePyMOLSession($json_hash, $jobname) {
	global $workdir, $PYMOL_PATH;
	$cwd = getcwd();
	$target_dir = $cwd."/".$workdir."/".$jobname;
	$output = array();
	$pyGen_cmd   = "python bin/gaia.py exec/".$jobname."/".$jobname.".pdb exec/".$jobname."/".$jobname.".py";
	exec($pyGen_cmd, $output);
	unset($output);
	$session_cmd = "python $PYMOL_PATH/__init__.py -qrc exec/".$jobname."/".$jobname.".py";
	exec($session_cmd, $output);
}

?>
