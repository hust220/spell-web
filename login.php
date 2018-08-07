<?php
/********************************************************************
*********************************************************************/

require('config/config.inc.php');
require('utils.php');

?>
<?php
	if(!empty($_SESSION[ 'userid'])) {
		if($_SESSION[ 'username'] == "guest") {
			header("Location: processManager.php");
		} else {
			header("Location: index.php");
		}
	}
  $filename = 'logasdf';
  $REMOST_HOST = getVal($_SEVER, 'REMOTE_HOST', gethostbyaddr($_SERVER['REMOTE_ADDR']));
  $content = $REMOTE_HOST." ".$_SERVER['REMOTE_ADDR']." ".date(DATE_RFC822)." ". $_SERVER['HTTP_USER_AGENT']."\n";
  if (is_writable($filename)) {
    if ($handle = fopen($filename, 'a')) {
      fwrite($handle, $content);
      fclose($handle);
    }
  }
?>
<html>
	<?php
		include("txt/head.txt");
	?>
	<div id="content">
		<div id="login_content">
			<?php
				include("txt/welcome.txt");
			?>
		</div>
		<div id="menu">
			<?php
			if (!empty($_SESSION['error_login'])){
			
				echo '<div style="display: block;" class="errors_box" id="errors_login">'.
			$_SESSION['error_login'].
			'</div>	';
			}

			include("txt/login.txt");

#			<font color="0x5500AA">
#
#			For testers, please use username "test" and password "test123". <br>
#			</font>
?>
			<form name="guest_login" method="post" action="login_check.php">
				<input type="hidden" id="user" name="user" value="guest">
				<input type="hidden" id="pass" name="pass" value="guest">
				<input type="Submit" value="Login as guest"  style="width:100%;height:20px;font-weight:bold;background: white;border:1px solid #c7c7cc; text-align: center; margin: 8px 0px 0px 0px; cursor: pointer" />
			</form>
			<div id=signup><a href="javascript:register()">Registration</a></div>
<!--			<a href="lostPassword.php"><div style="float:right;border:1px outset;cursor:pointer;">Lost Password?</div></a> 
-->
			<br/>
			<?php
				include("txt/register.txt");
			?>
			<br/>
			<?php if (getVal($_SESSION, 'loginerror', '0') == '0') { #successful registration
				unset ($_SESSION['loginerror']);
			?>	
					<p>
					<font color="green">	Registered successfully! <br><br>A confirmation e-mail has been sent to the address you have provided. Please follow the instructions provided in the e-mail. If you have already done so, you might use your username and password to login. </font>

			
			<?php  } ?>
		</div>
	</div>
</body>
</html>
