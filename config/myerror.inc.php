<?php
function myerror($str){
    $key = "error_$str";
    if (!array_key_exists($key, $_SESSION)) $_SESSION[$key] = "";
	$errormessage = $_SESSION["error_$str"];
	echo "<td colspan=3 align=center> <font color=#990000> $errormessage </font> </td>";
	unset($_SESSION["error_$str"]);
}
function myform($str){
    $key = "save_$str";
    if (!array_key_exists($key, $_SESSION)) $_SESSION[$key] = "";
	$message = $_SESSION["save_$str"];
	echo "$message";
	unset($_SESSION["save_$str"]);
}
?>
