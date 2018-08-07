<?php
require_once('../config.inc.php');
if (empty($_SESSION['username'])){
  die("Not logged in");
}
if ($_SESSION['level'] < 3){
  die("Illigal operation");
}
$userid=$_GET['userid'];
$username=$_GET['username'];
$action=$_GET['action'];
$emailflag=$_GET['emailflag'];
$reason=$_GET['reason'];
#echo "$userid,$username,$action,$emailflag";
if(isset($userid)&&isset($username)&&isset($action)&&isset($emailflag)){
  # act only if the emailflag is set
  require_once('set_approval.inc.php');
  $status=0;
  $status=set_approval($userid,$username,$action,$emailflag,$reason,$_SESSION['level']);
}

?>
<html>
<head>
<style type="text/css">
  <!--
    @import url(../../style/ifold.css);
    @import url(../../style/greybox.css);
</style>

<?php  
if(isset($status)){ #if operation is performed
  if("$status" == "0"){ # "$status" is important for string comparison
    echo "Operation performed successfully! 
      <script language='javascript'>
	window.parent.update_row(\"$userid\");
	window.parent.close_layer();
      </script>";
  }else{
    die($status);
    exit;
  }
}else{

?>
</head>
<body>
<table>
<tr>
  <td><b> <?php  echo $action; ?> this user account? </b> <br></td>
</tr>
</table>
<form name="actionform"><table><tbody>
<tr>
    <td> Username: </td>
    <td> <?php  echo $username; ?> </td>
</tr>
<tr>
    <td> ID: </td>
    <td> <?php  echo $userid; ?></td>
</tr>
    <td> Email notification? : </td>
    <td>
    <input type="checkbox"  style="width: 30px;" id="emailFlag" name="emailFlag" checked/></td>
</tr>

<?php
if ($action == "Disapprove" || $action == "Suspend") {
?>

<tr>
    <td> Reason: </td>
    <td> <input type="text" name="reason" id="reason"> </td>
</tr>

<?php  } ?>
<tr> 
  <td>
    <input type="button" style="width: 50px" value="Yes" onclick='
    var emailflag = 0;
    if (document.actionform.emailFlag.checked){
      emailflag = 1;
    };
    var reasonstr="";
    var obj=document.getElementById("reason");
    if (obj){
	reasonstr="&reason="+obj.value;
    }
    window.location=location.href+"&emailflag="+emailflag+reasonstr;
    '/>
    </td><td>
    <input type="button" style="width: 50px" value="No" onclick="window.parent.close_layer()"/>
  </td>
</tbody></table></form>

</body>
</html>
<?php 
}
?>
