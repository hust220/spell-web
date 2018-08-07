<?php
/********************************************************************
* @name Register Confirm
* @abstract - Allows users to confirm their email address 
* @since Feb 27, 2006
* -------------------------------------------------------------------/
*
* @author		Jameson Lopp jameson@unc.edu
* @author		Adi Unnithan adi@unc.edu
*				Daniel Watson dcwatson@email.unc.edu
*********************************************************************/

require('config.inc.php');

if (isset($_POST['email'])) {
	// check that email address matches an account and find corresponding password
	$user = new userDAO();
	$user->set('email', $_POST['email']);
	if ($rs = $user->search(array('email'), array($_POST['email'])))
	{
		$user->digest($rs[0]);
    	$charset = "abcdefghijklmnopqrstuvwxyz0123456789";
    	for ($i=0; $i<7; $i++) // build a 7 character random string as the new password
    		$newPassword .= $charset[(mt_rand(0,(strlen($charset)-1)))];
    	$user->set('password', $newPassword);
		// save new password to database
		if (! $user->save()) {
			$smarty->assign('lostPasswordError', "Unable to save reset password to database. Please try again later.");
			exit;
		}
	   	$mail_subject = "iFold lost password: " . ucwords ($rs[0]['namefirst']) . " " . ucwords ($rs[0]['namelast']);
	    $mail_message = ucwords($rs[0]['namefirst']) . ' ' . ucwords($rs[0]['namelast']) . " requested the password reset for username: {$rs[0]['username']}" .
						"\n\nThe new password is: " . $newPassword . "\n\n\nThe iFold Team";
	    $mail_headers = "From: iFold: Interactive Folding\r\nReply-To:\r\nX-Mailer: iFold Lost Password PHP Script " . phpversion ();
	    mail ($_POST['email'], $mail_subject, $mail_message, $mail_headers);
       	$smarty->assign('lostPasswordError',  "An email with your reset password has been sent to the given address.");
	}
	else {
   		$smarty->assign('lostPasswordError',  "Email address not found in database");
	}
}

$smarty->display_welcome('lostPassword.tpl');
?>









<?php
/********************************************************************
*********************************************************************/

require('config/config.inc.php');

?>
<html>
<head>
<title> Rapid Protein Energy Minimization Server </title>
	<style type="text/css">
      <!--
        @import url(style/ifold.css);
        @import url(style/greybox.css);
      -->
    </style>
    <script src="style/js/login.js" language="javascript" type="text/javascript"></script>
</head>

<body>
<div id="main">
	<div id="header">
		<div style="float:left"><img src="style/img/head.jpg" alt="iFold: Proteins on Demand" border="0" /></div>
		<div style="float:left"><img src="style/img/pstability.jpg" alt="Chiron: Rapid energy minimization server" border="0" /></div>
		<div style="float:right"><a href="http://dokhlab.unc.edu/main.html"><img src="style/img/dokhlab.jpg" alt="Dokholyan Lab" border="0" /></a></div>
	</div>
	<div id="meta" class="smallfont">
	<?php
		include('meta.php');
	?>
	</div>
	<div id="content">
		<div id="login_content">
		<p>
		<form  method="post" action="lostPassword.php">
		<input type="text" name="email"/>
		<input type="submit" />
		</form>
		</div>

		<div id="menu">
			<?php 
			if (!empty($_SESSION['error_login'])){
			
				echo '<div style="display: block;" class="errors_box" id="errors_login">'.
			$_SESSION['error_login'].
			'</div>	';
			}

			?>
			<form id="frmLogin" method="post" action="login_check.php">
			<table border="0" cellpadding="0" cellspacing="0" width="75%" style="float:left">
			<tr><td>Username:</td><td><input id="user" type="text" name="user" /></td></tr>
			<tr><td>Password:</td><td><input id="pass" type="password" name="pass" /></td></tr>
			</table>
			<div style="float:left;margin:2px 0 0 2px">
				<input type="Submit" value="Login"  style="width:40px;height:36px;font-weight:bold;background:#f3ebc1;border:0px" />
			</div>
			</form>

			<a href="javascript:register()"><div style="float:left;border:1px outset #6c6015;cursor:pointer;font:bold"><font color="#5c5010 ">Sign up</font></div></a>
<!--			<a href="lostPassword.php"><div style="float:right;border:1px outset;cursor:pointer;">Lost Password?</div></a> 
-->
			<br/>
			<br/>
			<form action="register.php" method="post" >
					<table id="frmRegister" border="0" cellpadding="0" cellspacing="0" width="100%"  
					<?php if (!isset($_SESSION['loginerror'])){
						echo ( ' style="display:none"');
					}elseif($_SESSION['loginerror']=='0') {
						echo ( ' style="display:none"');
					}else{
						unset($_SESSION['loginerror']);
					} ?> >

						<tr>
							<td>Username:</td>
							<td><input id="username" type="text" name="username" maxlength="10" value="<?php myform("username")?>"/></td>
						</tr>
						<?php if (isset($_SESSION["error_username"])) {echo("<tr><td></td>");myerror('username');echo("</tr>"); } ?>
						<tr>
							<td>Password:</td>
							<td><input id="password" type="password" name="password" maxlength="15" /></td>
						</tr>
						<?php if (isset($_SESSION["error_password"])) {echo("<tr><td></td>");myerror('password');echo("</tr>"); } ?>
						<tr>
							<td>Confirm Pass:</td>
							<td><input id="confirm_pass" type="password" name="confirm_pass" maxlength="15" /></td>
						</tr>
						<?php if (isset($_SESSION["error_confirm_pass"])) {echo("<tr><td></td>");myerror('confirm_pass');echo("</tr>"); } ?>
						<tr>
							<td>First Name:</td>
							<td><input id="namefirst" type="text" name="namefirst"  maxlength="15" value="<?php myform("namefirst")?>" /></td>
						</tr>
						<?php if (isset($_SESSION["error_namefirst"])) {echo("<tr><td></td>");myerror('namefirst');echo("</tr>"); } ?>
						<tr>
							<td>Last Name:</td>
							<td><input id="namelast" type="text" name="namelast"  maxlength="15" value="<?php myform("namelast")?>" /></td>
						</tr>
						<?php if (isset($_SESSION["error_namelast"])) {echo("<tr><td></td>");myerror('namelast');echo("</tr>"); } ?>
						<tr>
							<td>Email:</td>
							<td><input id="email" type="text" name="email" maxlength="100"  value="<?php myform("email")?>" /></td>
						</tr>
						<?php if (isset($_SESSION["error_email"])) {echo("<tr><td></td>");myerror('email');echo("</tr>"); } ?>
						<tr>
							<td>Organization:</td>
							<td><input id="organization" type="text" name="organization" maxlength="20"  value="<?php myform("organization")?>" /></td>
						</tr>
						<?php if (isset($_SESSION["error_organization"])) {echo("<tr><td></td>");myerror('organization');echo("</tr>"); } ?>
						<tr>
							<td colspan="2" align="center"><input type="Submit" onClick="return validateRegister();" value="Register an Account" style="width:150px"/></td>
						</tr>
					</table>


			</form>
			<?php if ($_SESSION['loginerror'] == '0' ) { #successful login 
				unset ($_SESSION['loginerror']);
			?>	
					<p>
					<font color="green">	Registered successfully! Please use the username and password to login. </font>

			
			<?php  } ?>
		</div>
	</div>
</body>
</html>
