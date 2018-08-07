<?php
require_once('config.php')
?>

<head>
<title> SPELL : Protein Split Sites Prediction </title>
<style type="text/css" media="all">
<!--

@import url(style/ifold.css);
/*@import url(style/greybox.css);*/
@import url(style/lytebox.css);
@import url("http://redshift.med.unc.edu/spell/style/js/jQueryUI/themes/custom-theme/jquery.ui.all.css");
-->
</style>
<script src="<?php echo $host ?>/spell/style/js/login.js" language="javascript" type="text/javascript"></script>
<script src="<?php echo $host ?>/spell/style/js/query.js" language="javascript" type="text/javascript"></script>
<script src="<?php echo $host ?>/spell/style/js/jetpack.js" language="javascript" type="text/javascript"></script>
<script src="<?php echo $host ?>/spell/style/js/lytebox.js" language="javascript" type="text/javascript"></script>
<script src="<?php echo $host ?>/spell/style/js/jquery-1.4.2.min.js" language="javascript" type="text/javascript"></script>
<script src="<?php echo $host ?>/spell/style/js/jtip.js" language="javascript" type="text/javascript"></script>
<script type="text/javascript" language="javascript" src="<?php echo $host ?>/spell/style/js/jQueryUI/external/jquery.bgiframe-2.1.1.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo $host ?>/spell/style/js/jQueryUI/ui/jquery.ui.core.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo $host ?>/spell/style/js/jQueryUI/ui/jquery.ui.widget.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo $host ?>/spell/style/js/jQueryUI/ui/jquery.ui.mouse.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo $host ?>/spell/style/js/jQueryUI/ui/jquery.ui.position.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo $host ?>/spell/style/js/jQueryUI/ui/jquery.ui.selectable.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo $host ?>/spell/style/js/jQueryUI/ui/jquery.ui.dialog.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo $host ?>/spell/style/js/jQueryUI/ui/jquery.ui.tabs.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo $host ?>/spell/style/js/jQueryUI/ui/jquery.ui.accordion.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo $host ?>/spell/style/js/spell.jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo $host ?>/spell/style/js/ajaxfileupload.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo $host ?>/spell/style/js/jquery.form.js"></script>
</head>

<body>
<div id="main">
<div id="header">
<div class='ui-state-hover ui-corner-all'><img src="style/img/SPELL_NEW.png" width="848" height="65" alt="SPELL" border=0 usemap="#lab" /></div>
<map name="lab">
<area shape="rect" coords="700, 0, 850, 50" href="http://danger.med.unc.edu" alt="Dokholyan Lab">
</map>
</div>
<div id="meta" class="smallfont">
<?php
include("txt/userbar.txt");
?>
</div>
