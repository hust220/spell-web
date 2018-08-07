<?php

require('config/config.inc.php');
require('config/functions.inc.php');

?>
<?php
	if (empty($_SESSION['username'])){
		header("Location: login.php");
	}
	if ($_SESSION['userlevel'] < 3){
		header("Location: login.php");
	}
?>
<html>
<head>
	<style type="text/css">
      <!--
        @import url(style/ifold.css);
        @import url(style/greybox.css);
        @import url(style/lytebox.css);
      -->
    </style>
</head>
<body>
	<div id="content">
		<div id="main_content">
			<div class="indexTitle"> Log File </div>
<?php
	$log = getLog($_GET['jobid']);
	echo "<pre>$log</pre>";
?>
			</div>
		</div>
	</div>
</body>
</html>
