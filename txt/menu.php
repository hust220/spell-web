<?php
	if($_SESSION[ 'username'] != "guest") {
		echo '<a href="index.php" class="nav"><div class="ui-state-default ui-corner-all navi"> <table><tr><td><img src="style/img/home.png" border=0px title="Home/Overview"></td><td valign=top><small> Home/Overview </small></td></tr></table> </div></a><br/>';
	}
	echo '<a href="processManager.php" class="nav"><div class="ui-state-default ui-corner-all navi"> <table><tr><td><img src="style/img/submit.png" border=0px title="Submit a task"></td><td	valign=top><small> Submit Task </small></td></tr></table></div></a><br/>';
	if($_SESSION[ 'username'] != "guest") {
		#echo '<a href="activity.php" class="nav"><div class="ui-state-default ui-corner-all navi"> <table><tr><td><img src="style/img/activity.png" border=0px title="User Activity"></td><td valign=top><small> User Activity </small></td></tr></table></div></a><br/>';
		echo '<a href="profile.php" class="nav"><div class="ui-state-default ui-corner-all navi"> <table><tr><td><img src="style/img/profile.png" border=0px title="User Profile"></td><td valign=top><small> User Profile </small></td></tr></table></div></a><br/>';
	}
	echo '<a href="documentation.php" class="nav"><div class="ui-state-default ui-corner-all navi"> <table><tr><td><img src="style/img/documentation.png" border=0px title="Chiron Documentation"></td><td valign=top><small> Documentation </small></td></tr></table></div></a><br/>';
	echo '<a href="contact.php" class="nav"><div class="ui-state-default ui-corner-all navi"> <table><tr><td><img src="style/img/contact.png" border=0px title="Contact Us"></td><td valign=top><small> Contact Us </small></td></tr></table></div></a><br/>';
?>
