<?php
require('config/config.inc.php');
?>
<?php
#	$_SESSION = array();
	session_destroy();
	header("Location: login.php");
?>
<html>
<body>

</body>
</html>
