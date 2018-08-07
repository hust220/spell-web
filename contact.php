<?php
require('config/config.inc.php');
?>
<?php
	if (empty($_SESSION['username'])){
		header("Location: login.php");
	}
	
?>
<?php
if (getVal($_POST, 'subject', False) && getVal($_POST, 'body', False)){

  $mail_subject=$_POST['subject'];
  $mail_message=$_POST['body'];
  $name = ($_SESSION['username']);
  $query = "SELECT email,firstname,lastname FROM $tableusers WHERE username='$name'";
  $result = mysql_query($query);
  $row = mysql_fetch_array($result);
  $useremail = $row['email'];
  $firstname = $row['firstname'];
  $lastname = $row['lastname'];

  $username = $_SESSION['username'];
  $mail_message.="\r\nfrom:".$username; 
  $mail_headers = "From:\"SPELL server\" <spell@spell.dokhlab.org>\r\nReply-To:\"$firstname $lastname\" <$useremail>\r\nX-Mailer: SPELL PHP script" . phpversion ();
  $mailsent = mail("krohotin@email.unc.edu", $mail_subject, $mail_message, $mail_headers);
  header("Location: contact_confirm.php");
}
?>


<html>
<?php 
	include("txt/head.txt");
?>
	<div id="content">
		<div id="nav">
		<?php 
			include("txt/menu.php");
		?>
		</div>
		
		<div id="main_content">

<div class="indexTitle" id="step"> Questions and comments </div>
<form action="contact.php" method="post">
	<font size=4> Note: Guest users are requested to provide their <b>e-mail address</b> for us to be able to provide assistance. </font> </br></br>
	subject: <input type="text" name="subject" style="width:400"> <br>
	 <br>
	<textarea name="body" rows="12" cols="80" style="width:500;height:200"> </textarea> <br>
	<br>
	<input type="submit" value="send"> &nbsp; <br>

</form>
		</div>

		<div id="rightbar">
			<div id="ataglance">
				your information at a glance.
			</div>
		</div>

	</div>
</div>
</body>
</html>
