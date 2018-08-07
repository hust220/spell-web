<?php
	require('config/config.inc.php');
	if (empty( $_SESSION['username']) ){
		header("Location: login.php");
	}
	echo "<html>";
	include("txt/head.txt");
?>
	<div id="content">
		<?php 
			echo '<div id="nav">';
			include("txt/menu.php");
			echo '</div>';
		?>
		<div id="main_content">
			<div class="indexTitle">
				How to view output? 
			</div>
			<div class="hspacer10"></div>

                        <ul>
                           <li> You can jump between split sites
			  by checking buttons on the right from protein 3D structure. Split sites are ranked by their relevance. </li> 
                           <br>
                           <center> <img src="style/img/Figure1.png" width="500" alt="SPELL"  align="middle" border=0/>  </center>                  
                           <br> <br>

                           <li>You can expang <b> ALIGNMENTS </b> section and scroll over it using side buttons</li>
                           <br>
                           <center> <img src="style/img/Figure2.png" width="500" alt="SPELL"  align="middle" border=0/>  </center>
                           <br><br>

			   <li>You can expang <b> ENERGY PROFILE & SEQUENCE CONSERVATION </b> section. This section allows you to access a few types of data. 
                                The plots can interact with 3D protein image. The plots can also be scrolled to allow user to check other possible split sites. </li>
                           <br>
                           <center> <img src="style/img/Figure3.png" width="650" alt="SPELL"  align="middle" border=0/>  </center>
                           <br><br>

                        </ul>
		</div>
	</div>
</body>
</html>
