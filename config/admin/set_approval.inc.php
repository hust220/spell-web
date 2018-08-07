<?php
# Basic user approval operations
# We define following actions:
#	Disapprove
#	Approve
#	Suspend
#	Resume

require_once('config.php');

function set_approval($userid, $username,$action, $email_flag,$reason,$oplevel){
  global $host;

  include('../para.inc.php'); # load the database para
  #first get the email address of the user
  $query  = "SELECT * FROM $tableusers WHERE username='$username' AND id='$userid' ";
  $result = mysql_query($query); 
  $row = mysql_fetch_array($result) or die("mysql query failed");
  if ($row == 0) {
    die("Record not found: id=$userid username=$username");
  }

  $email=$row["email"];
  $flag_approved=$row['approved'];
  $level = $row['level'];
  if($level >= $oplevel){return("Leval violation");}
  switch($action){
    case "Disapprove":
      #do nothing if the account was  approved
      if ($flag_approved != 0) { return("No need to disapprove"); }
      #set approved flag to 3, disapprove
      $query="UPDATE $tableusers SET approved='3' WHERE id='$userid' ";
      $result = mysql_query($query) or die("Cannot update user database");
      #send email to user
      if ($email_flag == 0) {return(0); }
      $msg = "";
      $msg .= "Your user account in Chiron server has not been approved due to the following reason(s): \n";
      $msg .= $reason . "\n" ;
      $msg .= "Please contact us if you have any questions \n";
      $msg .=  "Thanks for using Chiron server \n";
     
      break;
    case "Approve":
      if ($flag_approved == 1) {return("Already approved"); } 
      $query="UPDATE $tableusers SET approved='1' WHERE id='$userid' ";
      $result = mysql_query($query) or die("Cannot update user database");
      if ($email_flag == 0) {return(0); }
      $msg = "Congradulations! ";
      $msg .= "Your Chiron user account has been approved. ";
      $msg .= "You can now log in using your username '$username' to";
      $msg .= " the Chiron server ( $host/chiron ). \n";
      $msg .= "Please contact us if you have any questions \n";
      $msg .=  "Thanks for using Chiron server \n";
      break;

    case "Suspend":
      if ($flag_approved == 0 || $flag_approved==2) { return("No need to suspend"); }
      $query="UPDATE $tableusers SET approved='2' WHERE id='$userid' ";
      $result = mysql_query($query) or die("Cannot update user database");
      if ($email_flag == 0) {return(0); }
      $msg = "";
      $msg .= "Your user account in Chiron server has been suspended due to the following reason(s): \n";
      $msg .= $reason . "\n" ;
      $msg .= "Please contact us if you have any questions \n";
      $msg .= "Thanks for using Chiron server \n";
      break;
      
    case "Resume":
      if ($flag_approved != 2 ) {return("No need to resume"); } 
      $query="UPDATE $tableusers SET approved='1' WHERE id='$userid' ";
      $result = mysql_query($query) or die("failed to update user database");
      #send email to user
      if ($email_flag == 0) {return(0); }
      $msg = "Congratulations! ";
      $msg .= "Your Chiron user account has been resumed \n";
      $msg .= "You can now log in using your username '$username' to";
      $msg .= " the Chiron server ( $host/chiron ). \n";
      $msg .= "Please contact us if you have any questions \n";
      $msg .=  "Thanks for using Chiron server \n";
      break;
  }


  $subject="Important notice regarding your Chiron user account";
  include('../email.inc.php'); # load the eris_mail function
  eris_mail('$email',"$subject",$msg);
  #eris_mail('syin@email.unc.edu',"$subject",$msg);
  return(0);
}


?>
