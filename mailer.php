<?php
# ---------------------------------------------------------------------
#
# Filename : mailer.php
# Purpose  : Send e-mail to select users from the users table of chiron
#            database - Available groups : Administrators, users, select users
#
# ---------------------------------------------------------------------
require('config/config.inc.php');
?>
<?php
	if (empty($_SESSION['username'])){
		header("Location: login.php");
	}
	
?>
<?php
if ($_POST[ 'mailto'] && $_POST['subject'] && $_POST['body']){
	
	$mail_subject=$_POST['subject'];
	$mail_message=$_POST['body'];
	$bcclist = "";

	if($_POST[ 'mailto'] == "admin") {
		$to = "\"Admin listserv\" <admin-listserv@chiron.dokhlab.org>";
	} else {
		$to = "\"User listserv\" <user-listserv@chiron.dokhlab.org>";
		$mail_message.="\r\n\r\nDisclaimer : Replies to this e-mail may not reach the administrators. Please use the contact page on the website for questions and suggestions."; 
	}
	
	if($_POST[ 'mailto'] == "admin") {
		$query = "SELECT firstname,lastname,email FROM $tableusers WHERE userlevel='5'";
	} else if($_POST[ 'mailto'] == "all") {
		$query = "SELECT firstname,lastname,email FROM $tableusers";
	}
	$result = mysql_query($query);
	while($row = mysql_fetch_array($result)) {
		$firstname = $row['firstname'];
		$lastname  = $row['lastname'];
		$useremail = $row['email'];
		if($useremail != "") {
			$bcclist  .= "\"".$firstname." ".$lastname."\" <".$useremail.">,";
		}
	}
	$trimmed_bcclist = rtrim($bcclist,",");
	
	$mail_headers = "From:\"Chiron Server\" <chiron@dokhlab.org>\r\nReply-To:no-reply@dokhlab.org\r\nBCC:".$trimmed_bcclist."\r\nX-Mailer: Chiron PHP script" . phpversion ();
	$mailsent = mail($to, $mail_subject, $mail_message, $mail_headers);
	if($mailsent) {
		header("Location: mailer.php?status=success");
	} else {
		header("Location: mailer.php?status=failure");
	}
}
?>


<html>
<?php 
	include("txt/head.txt");
?>
	<div id="content">
		<?php 
			echo '<div id="nav">';
			include("txt/menu.php");
			include("txt/adminmenu.txt");
			echo '</div>';
		?>
		
		<div id="main_content">
			<div class="indexTitle" id="step"> Message center </div>
			<div class="hspacer10"></div>
			<?php 
				if(!isset($_GET[ 'status'])) {
					echo '<form action="mailer.php" method="post">';
					echo '<table border=0 cellspacing=0 cellpadding=3 width=100%>';
					echo '<tr><td>Notify</td><td>:</td>';
					echo '<td><select name=mailto id=mailto>';
					echo '<option name="Administrators" value="admin">Administrators</option>';
					echo '<option name="All Users" value="all">All users</option>';
					//echo '<option name="Select Users" value="users">Select users</option>';
					echo '</select></td></tr>';
					echo '<tr><td>subject</td><td>:</td><td><input type="text" name="subject" style="width:400"></td>';
					echo '<tr><td valign=top>Message</td><td valign=top>:</td>';
					echo '<td><textarea name="body" rows="12" cols="80" style="width:500;height:200"> </textarea></td>';
					echo '<tr><td></td><td></td><td><input type="submit" value="send"> &nbsp;</td></tr></table>';
					echo '</form>';
				} else if($_GET[ 'status'] == "success") {
					
				} else if($_GET[ 'status'] == "failure") {
				
				}
			?>
		</div>
	</div>
</div>
</body>
</html>
