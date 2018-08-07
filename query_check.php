<?php
require('config/config.inc.php');
?>
<?php
	if (empty( $_SESSION['username']) ){
		header("Location: login.php");
	}
?>
<html>
<title> Protein stability prediction server </title>
<body>


<?php
#get sumitted data and check format
$jobid=$_GET['jobid'];
if (strlen($pdbid) > 5 || eregi('[^0-9]',$pdbid)) {die("Invalid jobid\n");}
$email=$_GET['email'];
if (strlen($email)  >100 || !eregi('^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$',$email)) 
	{die("Invalid email address\n");}

#check entry in database
mysql_connect(localhost,$username,$password);
mysql_select_db($database) or die( "Unable to select database");
$query = "SELECT * FROM $tablejobs WHERE id='$jobid' AND email='$email'";
$result = mysql_query($query);
$row = mysql_fetch_array($result);
if ($row == 0) {
	die("Not match found. Please check input information and try again.");
}
$id=$row["id"];
$pdbid=$row["pdbid"];
$email=$row["email"];
$mutation=$row["mutation"];
$method=$row["method"];
$prerelax=$row["prerelax"];
$status=$row["status"];
$ddg=$row["ddg"];
$nmut=$row["nmut"];
$tsubmit=$row["tsubmit"];
$tfinish=$row["tfinish"];
$tprocess=$row["tprocess"];
for ($i=0;$i<$nmut;$i++){
	$muts[$i] = $row["mutation$i"];
	$ddgs[$i] = $row["ddg$i"];
}

mysql_close();

switch ($status) {
case 0:
	$statusstr = "waiting for processing";
	break;
case 1:
	$statusstr = "runing";
	break;
case 2:
	$statusstr = "finished";
	break;
default:
	$statusstr = "unknown status";
}
echo "<b> Description of the job: </b> <br>";
echo "jobid: $id <br> ";
echo "protein: $pdbid <br>";
#echo "mutations: $mutation <br>";
$i = 0;
foreach ($muts as $muti) {
	$i++;
	echo "mutation $i:";
	$chars = preg_split('/ +/', $muti);
	foreach ($chars as $mut) {echo $mut; echo " ";}
	echo "<br>";
}
echo "Prediction method: $method<br>";
echo "Backbone pre-relaxation: $prerelax <br>";
echo "Submitted: $tsubmit <br>";
echo "=======================<br>";
echo "<b> Result: </b> <br>";
echo "status: $statusstr <br>";
echo "&Delta;&Delta;G: <br>";
$i=0;
foreach ($ddgs as $ddg) {
	$i++;
	echo "Mut $i: &Delta;&Delta;G = $ddg <br>";
}
?>

</body>
</html>
