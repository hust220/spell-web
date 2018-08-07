<?php
function set_level($userid, $username, $level){

  if (! ereg("^[0-9]$",$level) ) {return; } # if not a valid level

  #first get the old level status
  $query  = "SELECT * FROM $tableusers WHERE username='$username' AND userid='$userid' ";
  $result = mysql_query($query); 
  $row = mysql_fetch_array($result) or die("mysql query failed");
  if ($row == 0) {
    die("Record not found: userid=$userid username=$username");
  }
  $oldlevel=$row["level"];

  #set level
  $mylevel = $_SESSION['level'];
  if ($mylevel <= $level || $mylevel <= $oldlevel) {die("User level violation");}
  $query="UPDATE $tableusers SET level='$level' WHERE id='$userid' ";
  $result = mysql_query($query) or die("failed to update user database");
}

?>
