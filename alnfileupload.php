<?php
require('config/config.inc.php');
$uploaddir = "pdb";
$error = "";
$msg = "";
$fileElementName = 'uploadedaln';
$nl = "\n";
$jsonvar = array('error','msg','missing','nchn');
$jsonstr = "";
if(!empty($_FILES[$fileElementName]['error']))
{
  switch($_FILES[$fileElementName]['error'])
    {
    case '1':
      $error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
      break;
    case '2':
      $error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
      break;
    case '3':
      $error = 'The uploaded file was only partially uploaded';
      break;
    case '4':
      $error = 'No file was uploaded.';
      break;

    case '6':
      $error = 'Missing a temporary folder';
      break;
    case '7':
      $error = 'Failed to write file to disk';
      break;
    case '8':
      $error = 'File upload stopped by extension';
      break;
    case '999':
    default:
      $error = 'No error code avaiable';
    }
}elseif(empty($_FILES['uploadedaln']['tmp_name']) || $_FILES['uploadedaln']['tmp_name'] == 'none')
{
    $error = "No file was uploaded..";
} else {
    $msg .= " <small>File: " . $_FILES['uploadedaln']['name'] . ", ";
    $msg .= " Size: " . @filesize($_FILES['uploadedaln']['tmp_name']);
    $msg .= " uploaded successfully </small>";
    $alnfile = "pdb/alignment.aln";
    if(!move_uploaded_file($_FILES['uploadedaln']['tmp_name'],$alnfile)) {
      $error = "<small>Error uploading file !</small>";
    }
    exec("$exec_checkAln '$alnfile'", $output);
    if( $output[0] == 0 ) { 
      $error = "<small>Invalid Alignment file !  </small>"; 
      unlink($pdbfile);
    }
}
foreach ($jsonvar as $jv):
  $jsonstr .= '"' . $jv . '" : "' . ${$jv} . '",';
endforeach;
$jsonstr = rtrim($jsonstr,",");
echo "{".$nl."\t".$jsonstr.$nl."}";
?>
