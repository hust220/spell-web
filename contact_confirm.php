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
?>
	<div id="content">
		<div id="nav">
		<?php 
			include("txt/menu.php");
		?>
		</div>
		
		<div id="main_content">

<div class="indexTitle" id="step"> Question and comments </div>
Thanks for your feedback!
		</div>

		<div id="rightbar">
			<div id="ataglance">
				your infomration at a glance.
			</div>
		</div>

	</div>
</div>
</body>
</html>
