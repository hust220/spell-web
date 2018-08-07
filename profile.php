<?php
require('config/config.inc.php');
?>
<?php
	if (empty($_SESSION['username'])){
		header("Location: login.php");
	}
?>

<html>
<?php 
	include("txt/head.txt");
	if($_SESSION[ 'username'] == "guest") { 
		echo "<div class=hspacer50></div>";
		echo "<strong style='color:#990000;'>We are sorry ! Guest users do not have access to this page. Please register to gain access.</strong>";
	} else {
?>
	<div id="content">
		<div id="nav">
			<?php 
				include("txt/menu.php");
			?>
		</div>
		
		<div id="main_content">
			<div class="indexTitle">
				Update User Information 
			</div>

			Please fill in the new user information and submit.
			<br/>
			<br/>

		<?php
		//print_r($_SESSION);
		//die;
			$varlist=array('firstname','lastname','email','organization');
			if (!isset($_SESSION['loginerror'])) { #first visit to the page
				$userid = $_SESSION['userid'];
				$query = "SELECT username,firstname,lastname,email,organization ".
				" FROM $tableusers WHERE id='$userid' LIMIT 1";
				$result = mysql_query($query) or die("cannot connet to database");
				$row = mysql_fetch_array($result);
				foreach ($varlist as $var) {
					$_SESSION["save_$var"] = $row[$var];
				}
			}
			
		?>
		<?php if ($_SESSION['loginerror'] != '0') {  ?>
		
			<form action="profile_check.php" method="post" >
				<fieldset><legend>Your Profile</legend>
				<table id="genTable" border="0" cellpadding="0" cellspacing="0" align="center">
					<tr>
						<td width='100px'>Username</td>
						<td>&nbsp;:&nbsp;</td>
						<td><input id="username" type="text" name="username" maxlength="10" disabled
						value="<?php print($_SESSION['username']); ?>"/></td>
					</tr>
					<?php if (isset($_SESSION["error_username"])) {echo("<tr><td></td>");myerror('username');echo("</tr>"); } ?>
						<td>First Name</td>
						<td>&nbsp;:&nbsp;</td>
						<td><input id="namefirst" type="text" name="namefirst"  maxlength="15" value="<?php myform("firstname")?>" /></td>
					</tr>
					<?php if (isset($_SESSION["error_namefirst"])) {echo("<tr><td></td>");myerror('namefirst');echo("</tr>"); } ?>
					<tr>
						<td>Last Name</td>
						<td>&nbsp;:&nbsp;</td>
						<td><input id="namelast" type="text" name="namelast"  maxlength="15" value="<?php myform("lastname")?>" /></td>
					</tr>
					<?php if (isset($_SESSION["error_namelast"])) {echo("<tr><td></td>");myerror('namelast');echo("</tr>"); } ?>
					<tr>
						<td>Email</td>
						<td>&nbsp;:&nbsp;</td>
						<td><input id="email" type="text" name="email" maxlength="100"  value="<?php myform("email")?>" /></td>
					</tr>
					<?php if (isset($_SESSION["error_email"])) {echo("<tr><td></td>");myerror('email');echo("</tr>"); } ?>
					<tr>
						<td>Organization</td>
						<td>&nbsp;:&nbsp;</td>
						<td><input id="organization" type="text" name="organization" maxlength="20"  value="<?php myform("organization")?>" /></td>
					</tr>
					<?php if (isset($_SESSION["error_organization"])) {echo("<tr><td></td>");myerror('organization');echo("</tr>"); } ?>
					<tr><td><br/></td></tr>
					<tr>
						<td colspan="3" align="center"><input type="Submit" id="button" value="Update" style="width:100px"/></td>
					</tr>
					</table>
					</fieldset>


			</form>
		<?php unset($_SESSION['loginerror']);
		} ?>
			<?php if ($_SESSION['loginerror'] == '0' ) { #successful update 
				unset ($_SESSION['loginerror']);
			?>	
					<p>
					<font color="green">	User information updated  successfully! </font>
					<?php if ($_SESSION['emailconfirm'] == '1') { 
						unset($_SESSION['emailconfirm']);
					?>
						<br>
						<font color="red">Email address needs to be confirmed. </font>
					<?php } ?>
			
			<?php  } ?>
			
			<br/>
			<br/>
			<!--<div id="main_content"><div class="indexTitle" style="border:0px"> Change Password. </div>-->

			<form action="password_check.php" method="post" >
				<fieldset><legend>Change Password</legend>
				<table border="0" cellpadding="0" cellspacing="0" align="center">
					<tr>
						<td width='150px'>Old password</td>
						<td>&nbsp;:&nbsp;</td>
						<td><input id="oldpassword" type="password" name="oldpassword" maxlength="15" /></td>
					</tr>
					<?php if (isset($_SESSION["error_oldpassword"])) {echo("<tr><td></td>");myerror('oldpassword');echo("</tr>"); } ?>
					<tr>
						<td>New Password</td>
						<td>&nbsp;:&nbsp;</td>
						<td><input id="newpassword" type="password" name="newpassword" maxlength="15" /></td>
					</tr>
					<?php if (isset($_SESSION["error_newpassword"])) {echo("<tr><td></td>");myerror('newpassword');echo("</tr>"); } ?>
	
					<tr>
						<td>Confirm Password</td>
						<td>&nbsp;:&nbsp;</td>
						<td><input id="confirm_pass" type="password" name="confirm_pass" maxlength="15" /></td>
					</tr>
					<?php if (isset($_SESSION["error_confirm_pass"])) {echo("<tr><td></td>");myerror('confirm_pass');echo("</tr>"); } ?>
					<tr><td><br/></td></tr>
					<tr>
						<td colspan="3" align="center"><input type="Submit" id="button" value="Update" style="width:100px"/></td>
					</tr>
					</table>
					</fieldset>


			</form>

			<?php if ($_SESSION['changepasserror'] == '0' ) { #successful login 
				unset ($_SESSION['changepasserror']);
			?>	
					<p>
					<font color="green">Password changed successfully! </font>

			
			<?php  } ?>
	
		
		<br />
		<br />

			<!--<form action="contact.php" method="post" >
				<fieldset><legend>Request Upgrade</legend>
				You may now request for an user level upgrade by filling out the following form. Be sure to clearly state the reason for your request. If we have further questions, one of our team members will contact you regarding your request.<br /><br />-->
<!--<div class="indexTitle" id="step"> Questions and comments </div>-->
				<!--<table cellpadding=0 cellspacing=0 align=center>
				<tr>
					<td>Subject</td>
					<td>&nbsp;:&nbsp;</td>
					<td><input type="text" name="subject" style="width:400" value="Reg: Request for user level upgrade"></td>
				</tr>
				<tr>
					<td valign=top>Justification</td>
					<td valign=top>&nbsp;: &nbsp;</td>
					<td><textarea name="body" rows="5" cols="80" style="width:400;height:100"> </textarea></td>
				</tr>
				<tr>
					<td colspan=3 align=center><br><input type="submit" value="Place Request"> &nbsp;</td>
				</tr>
				</table>
				
				</fieldset>
			</form>-->
		</div>
		<?php 
			} // Do not remove this. It belongs here. It is the closing brace for the content if the user is not a guest.
		?>

		<div id="rightbar">
			<div id="ataglance">
				your information at a glance.
			</div>
		</div>
	</div>
</body>
</html>
