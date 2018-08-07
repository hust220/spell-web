<?php
// Filename : showresults.php
# Mean and SD
# Buried Hbond -   0.134   1.308
# Shell Hbond  -   7.523   2.031
# MSA          -  67.104   8.949
# SASA         - 201.846  26.254
# Void Volume  -   0.209   0.690

require('config/config.inc.php');
require('config/functions.inc.php');

?>
<?php
	if (empty($_SESSION['username'])){
	#	header("Location: login.php");
	}
?>
<html>
<?php 
	include("txt/head.txt");
	$jobid = $_GET[ 'jobid'];
	$queue = $_GET[ 'queue'];
	$spacer        = "<div class=hspacer50></div>";
	$invalid_url   = "<strong style='color:#990000'>The URL you supplied, is invalid ! <br><br></strong> Please click on the link you received by e-mail or carefully copy and paste the URL onto a web browser and try again.<br>";
	$scrambled_url = "<strong style='color:#990000'>The URL you supplied, is scrambled ! <br><br></strong> Please verify the URL and try again later.<br>";
	$suggestion    = "<br>If you think you reached this page by mistake, please contact the administrator via the contact page on the website. Please include the URL in your message to help the administrators debug the problem.";


	//if($_SESSION[ 'username'] == "guest" ) {

		if(!isset($_GET[ 'authKey'])) {
			echo $spacer;
			echo $invalid_url;
			die;
		} else {
			$authKey = $_GET[ 'authKey'];
			if(strlen($authKey) != 24) {
				echo $spacer;
				echo $scrambled_url;
				die;
			}
			$query = "SELECT pdbid,authKey,status FROM $tablejobs WHERE id='$jobid'"; //queue='$queue'";
			$result= mysql_query($query);
			$rows  = mysql_num_rows($result);
			if($rows == 0 ) {
				echo $spacer;
				echo $invalid_url;
				echo $suggestion;
				die;
			}
			$row = mysql_fetch_array($result);
			$db_authKey = $row[ 'authKey'];
			$status     = $row[ 'status'];
			if($authKey != $db_authKey) {
				echo $spacer;
				echo $scrambled_url;
				echo $suggestion;
				die;
			}
                        $pdbid = $row[ 'pdbid'];
		}

	//}
?>
	<div id="content">
		<div id="nav">
			<?php 
				include("txt/menu.php");
			?>
		</div>
		
		<div id="main_content">
			<div class="indexTitle"> Results </div>
<?php
	$userid = $_SESSION['userid'];
	if (isset($_SESSION['tlogin']) ) {
		$tlogin = $_SESSION['tlogin'];
	} else {
		$tlogin = '0000-00-00 00:00:00';
	}
	echo '<div class="hspacer10"></div>';
	if($status<2) {
		echo 'This job is not processed yet. Please check back later for results.';
		die;
	}


        $query = "SELECT ipdb,status FROM $tablejobs WHERE id='$jobid'";
        $result = mysql_query($query);
        if($result) {

     	     while ( $row = mysql_fetch_array($result)){  
	       $fpdb     = getgzStreamAsArray($row['ipdb']);
	     }

	}

// temporary solution should communicate through DB

        $dirid = "$pdbid-$jobid";
        $tdir = $spell_workdir."/".$dirid;
        $rout = $tdir."/result.txt";
        exec("ls $rout > out.txt; cp $rout download/");
        $splitEout = $tdir."/splitEnergy.png";
        exec("cp $splitEout download/");
        $saaout = $tdir."/saa.png";
        exec("cp $saaout download/");
        $consout = $tdir."/cons.png";
        exec("cp $consout download/");


        $naln = 0;
        $oaln = fopen("$tdir/$dirid.pfamScan", "r");
        //$listAln = "<p>$tdir/$dirid.pfamScan</p>";
        if ($oaln) {
        while (($line = fgets($oaln)) !== false) {
	  $datf = preg_split('/\s+/', $line);
	  //	  $datf = explode(" ", $line);
          if($datf[0]==$dirid){ 
             $naln++;
             $alncopy = $tdir."/"."q".$datf[6].".clustal";
             exec("cp $alncopy download/");
             $listAln .= " <p  style=\"font-size:16px\"> <b> $naln. </b> Residues <b> $datf[1] - $datf[2] </b> are aligned to <b> $datf[6] </b> Pfam family </p> ";
             $listAln .= "<div id=\"msa$naln\">Loading Multiple Alignment...</div>";
             $listAln .= "<br> <br>";
             $jscript .= "<script> var rootDiv$naln = document.getElementById(\"msa$naln\");";
             $jscript .= "var opts$naln = { el: rootDiv$naln, importURL: \"./download/q$datf[6].clustal\", vis: { labelId: false, conserv: false, overviewbox: false, seqlogo: true}, conf: { dropImport: true }, zoomer: { menuFontsize: \"12px\", autoResize: true } }; var m$naln = msa(opts$naln);";
             $jscript .= "</script>";
          }
        }
        } else {
           $naln++;
           $alncopy = $tdir."/".$dirid.".clustal";
           exec("cp $alncopy download/");
           $listAln .= " <p  style=\"font-size:16px\"> User provided alignment </p> ";
           $listAln .= "<div id=\"msa$naln\">Loading Multiple Alignment...</div>";
           $listAln .= "<br> <br>";
           $jscript .= "<script> var rootDiv$naln = document.getElementById(\"msa$naln\");";
           $jscript .= "var opts$naln = { el: rootDiv$naln, importURL: \"./download/$dirid.clustal\", vis: { labelId: false, conserv: false, overviewbox: false, seqlogo: true}, conf: { dropImport: true }, zoomer: { menuFontsize: \"12px\", autoResize: true } }; var m$naln = msa(opts$naln);";
             $jscript .= "</script>";
        }

        if($naln==1){
	  $priAln = "Your protein contains $naln domain: <br> <br>";
        }
        if($naln>1){
          $priAln = "Your protein contains $naln domains: <br> <br>";
	}

	// Energy Graph Data
        $iFirst = -10000;
        $iLast = -10000; 
        $listEData .= "[";
        $fenergy = fopen("$tdir/split.txt", "r");
        if ($fenergy) {
	  while (($line = fgets($fenergy)) !== false) {
	    $datf = preg_split('/\s+/', $line);
            if($datf[4]>-400){
              $listEData .= "[$datf[0], $datf[4]],";
	    } else {
              $listEData .= "[$datf[0], 0.],";
	    }
	    if($iFirst == -10000){
              $iFirst = $datf[0];
	    }
            $iLast = $datf[0];
	    //      $datf = explode(" ", $line);                  
	  }
	}
	$listEData = substr($listEData, 0, -1);
        $listEData .= "]";
        fclose($fenergy);
	// 

	// Stride file
        $alnBand = "";
        $iAlnPrev = -100; 
        $iAlnStart = -100;
        $loopBand = "";
        $iLoopPrev = -100;
        $iLoopStart = -100;
        $iLoopState = 0;
        $listSAAData = "[";
        $listConsData = "[";
        $ostr = fopen("$tdir/$dirid.stride", "r");
        if ($ostr) {
	  while (($line = fgets($ostr)) !== false) {
	    $datf = preg_split('/\s+/', $line);
	    //      $datf = explode(" ", $line);
	    if($datf[0]=="ASG"){
	      //Conservation
              if($datf[3]>=$iFirst && $datf[3]<=$iLast){
                $consval = 0; 
                if($datf[13] != 100){
                   $consval = $datf[13];
                }
		$listConsData .= "[$datf[3], $consval],";
		//                  echo $datf[3]."  ".$datf[13]."  ".$consval."<br>";
              }

	      //SAA
	      if($datf[3]>=$iFirst && $datf[3]<=$iLast){
		  $listSAAData .= "[$datf[3], $datf[9]],";
//                  echo $datf[3]."  ".$datf[9]."  "."<br>"; 
	      }

	      //	      echo $datf[3]."  ".$datf[11]."  ";    
	      // Alignment 
              if($datf[11]=="NO"){ 
                if($iAlnPrev != -100){
       	          $alnBand .= "{xaxis:{from: $iAlnStart, to: $iAlnPrev }, color: \"#eeeeff\" },";
		  //		  echo "END  $iAlnPrev";
		}
	      }
              if($datf[11]!="NO"){
                if($iAlnPrev == -100){
                  $iAlnStart = $datf[3];
		  //                  echo "START  $datf[3]";
		}
	      }
	      if($datf[11]!="NO"){                
                $iAlnPrev = $datf[3];
//		echo $datf[11]."<br>";
	      } else {
                $iAlnPrev = -100;
	      }
	      //	      echo "<br>"; 
              // Loops
              if($datf[6]=="Turn" || $datf[6]=="Coil"){
		$iLoopState = 1; 
	      } else {
		$iLoopState = 0;
	      }
              if($iLoopState==0){
                if($iLoopPrev != -100){
		  //                  $loopBand .= "{xaxis:{from: $iLoopStart, to: $iLoopPrev }, color: \"#99ff66\" },";
                  $loopBand .= "{xaxis:{from: $iLoopStart, to: $iLoopPrev }, color: \"#ffff99\" },";
		  //		  echo "END  $iLoopPrev "; 
		}
	      }
              if($iLoopState!=0){
                if($iLoopPrev == -100){
                  $iLoopStart = $datf[3];
		  //                  echo "START  $datf[3] ";
		}
	      }
//              echo $datf[3]."  ".$datf[6]."  ".$iLoopState."  ".$iLoopState."  ".$iLoopPrev."<br>";

              if($iLoopState!=0){
                $iLoopPrev = $datf[3];
	      } else {
                $iLoopPrev = - 100;
	      }

//              echo $datf[3]."  ".$datf[6]."  ".$iLoopState."  ".$iLoopState."  ".$iLoopPrev."<br>";
	    } 
	  }
          if($iAlnPrev != -100){
	    $alnBand .= "{xaxis:{from: $iAlnStart, to: $iAlnPrev }, color: \"#eeeeff\" },";
	  } 
          if($iLoopPrev != -100){
            $loopBand .= "{xaxis:{from: $iLoopStart, to: $iLoopPrev }, color: \"#ffff99\" },";
	  }
	}
        $alnBand = substr($alnBand, 0, -1); 
	//        $alnBand .= "]";
        $loopBand = substr($loopBand, 0, -1);
        $listSAAData = substr($listSAAData, 0, -1);
        $listSAAData .= "]";
	$listConsData = substr($listConsData, 0, -1);
        $listConsData .= "]";
     	fclose($ostr);   



?>

<?php
$handle = fopen("download/result.txt", "r");
$i=0;
$istart=0;
if ($handle) {
  while (($line = fgets($handle)) !== false) {
    $i++;
    $ends = preg_split('/\s+/', $line);
    if($i==1) $istart = $ends[0];
    $rbuttons .= '<input id=r'.$i.' class=radio type=radio name=group1 onclick="colorProt('.$ends[0].');" align=middle >'.$ends[0].'-'.$ends[1].'<br>';
  }
}
?>


<meta charset="utf-8">
<!-- breaking out the library for debugging -->
<script type="text/javascript" src="jsmol/jquery/jquery.js"></script>
<script type="text/javascript" src="jsmol/js/JSmoljQueryExt.js"></script>
<script type="text/javascript" src="jsmol/js/JSmolCore.js"></script>
<script type="text/javascript" src="jsmol/js/JSmolApplet.js"></script>
<script type="text/javascript" src="jsmol/js/JSmolApi.js"></script>
<script type="text/javascript" src="jsmol/js/JSmolControls.js"></script>
<script type="text/javascript" src="jsmol/js/j2sjmol.js"></script>
<script type="text/javascript" src="jsmol/js/JSmol.js"></script>
<script type="text/javascript" src="jsmol/js/JSmolConsole.js"></script>
<script type="text/javascript" src="jsmol/js/JSmolMenu.js"></script>
	 <!-- // following two only necessary for WebGL version:                                                                                                                            
-->
<script type="text/javascript" src="jsmol/js/JSmolThree.js"></script>
<script type="text/javascript" src="jsmol/js/JSmolGLmol.js"></script>

<script type="text/javascript">

	 Jmol._isAsync = false;

Jmol.getProfile() // records repeat calls to overridden or overloaded Java methods

var jmolApplet0; // set up in HTML table, below

// use ?_USE=JAVA or _USE=SIGNED or _USE=HTML5

jmol_isReady = function(applet) {
  //  document.title = (applet._id + " is ready")
  Jmol._getElement(applet, "appletdiv").style.border="1px solid blue"

}

var strucState = 'set zoomlarge false;set antialiasdisplay; load async download/tmp; cartoons only; color structure; select resno<='+<?php echo $istart ?>+'; color lightblue; select resno>'+<?php echo $istart ?>+'; color red;  cartoons only;';


  Info = {
  width: 520,
  height: 400,
  debug: false,
  color: "#F0F0F0",
  zIndexBase: 20000,
  z:{monitorZIndex:100},
  //  addSelectionOptions: true,
  serverURL: "http://chemapps.stolaf.edu/jmol/jsmol/php/jsmol.php",
  use: "HTML5",
  //language: "fr", // NOTE: LOCALIZATION REQUIRES <meta charset="utf-8"> (see JSmolCore Jmol.featureDetection.supportsLocalization)
  jarPath: "jsmol/java",
  j2sPath: "jsmol/j2s",
  jarFile: "jsmol/JmolApplet.jar",
  isSigned: false,
  disableJ2SLoadMonitor: false,
  disableInitialConsole: false, // default now is true
  readyFunction: jmol_isReady,
  allowjavascript: true,
  //      appletLoadingImage: "none",
  //  script: "set zoomlarge false;set antialiasdisplay;load async jsmol/data/1crn.pdb;"
  //      script: "set zoomlarge false;set antialiasdisplay;load async jsmol/data/caffeine.mol;"
  //  script: "set zoomlarge false;set antialiasdisplay; load async download/tmp; cartoons only;color structure;"
  script: strucState  
  //      script: "set antialiasDisplay;set showtiming;load async data/caffeine.mol;"
  //,defaultModel: ":dopamine"
  //,noscript: true
  //console: "none", // default will be jmolApplet0_infodiv
  //script: "set antialiasDisplay;background white;load data/caffeine.mol;"
  //delay 3;background yellow;delay 0.1;background white;for (var i = 0; i < 10; i+=1){rotate y 3;delay 0.01}"                                                                     
  }                                                                                                                               
</script>  



<script> 

var whichPlot = 1;
var globalYmax; 
var globalLockStatus;
var curX;

function  colorProt() {
  var plot = 'select resno<'+arguments[0]+'; color lightblue; select resno>'+arguments[0]+'; color red;  cartoons only;';
      Jmol.script(jmolApplet0,plot);
      myFlot.setCrosshair({x: arguments[0]+0.5});
      myFlot.lockCrosshair({x: arguments[0]+0.5});
      globalLockStatus = true;
      latestPosition = arguments[0];
      str = latestPosition;
      var str1 = Number(str)+1;
      //      $("#hoverdata").text("Split sites: " + str + "-" + str1);

      var jmax = 0;
      var ymax = 0;
      var str1 = Number(str)+1;
      var i, j, dataset = myFlot.getData();
      for (i = 0; i < dataset.length; ++i) {
	var series = dataset[i];
	for (j = 0; j < series.data.length; ++j) {
	  if (series.data[j][0] >= parseInt(str)){
	    jmax = series.data[j][0];
	    ymax = series.data[j][1].toFixed(2);
	    break;
	  }
	}
      }
      //      $("#hoverdata").html("Split sites: <b>" + str + "-" + str1 + "</b>");
      if(whichPlot==1){
	$("#hoverdata").html("Split sites: <b>" + str + "-" + str1 + "</b>"); 
        $("#hoverdata1").html("E = <b>" + ymax + "</b> (kcal/mol)");
      }
      if(whichPlot==2){
	$("#hoverdata").html("Res. ID.: <b>" + str + "</b>"); 
	$("#hoverdata1").html("SAA = <b>" + ymax + "</b> (&#8491;<span class=sup>2</span>)"); 
      }
      if(whichPlot==3){
	$("#hoverdata").html("Res. ID.: <b>" + str + "</b>"); 
        $("#hoverdata1").html("Kullback-Leibler (KL) Divergence = <b>" + ymax + "</b> ");
      }

      curX = arguments[0];
}

</script> 

<table width=680 cellpadding=10>

<tr><td valign=top>

<div style="position:relative; font-size:50px; z-index:1;">
<script>

// note that the variable name MUST match the first parameter in quotes

jmolApplet0 = Jmol.getApplet("jmolApplet0", Info) 

// note that now scripts can be sent immediately after the _Applet object is created

//Jmol.script(jmolApplet0,"background gray;delay 0.5;background black")
var lastPrompt=0;

</script>
</div>

</td><td valign=top>

<b> Split Sites <a href="txt/submit/split.htm?width=270" name="What's this?" class="jTip" id='title'> <img src="style/img/help.png" border="0px"></a> : </b> <br> <br>

<?php
    echo $rbuttons;
    /*
  $handle = fopen("download/result.txt", "r");
  $i=0; 
  if ($handle) {
     while (($line = fgets($handle)) !== false) {
       $i++;
       $ends = preg_split('/\s+/', $line);
       echo '<input id=r'.$i.' class=radio type=radio name=group1 onclick="colorProt('.$ends[0].');" align=middle >'.$ends[0].'-'.$ends[1].'<br>';
     }
  }
    */
?> 

<script>
document.getElementById("r1").checked = true;
</script>

</td>
</tr>
</table>

<br>

<script src="https://cdn.bio.sh/msa/latest/msa.min.gz.js"></script>

<script src="flot/jquery.js"></script>
<script src="flot/jquery.flot.js"></script>
<script src="flot/jquery.flot.axislabels.js"></script>
<script src="flot/jquery.flot.crosshair.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>


<div class="accordion vertical">
   <h3> Supporting Information </h3>
   <div class="hspacer10"></div>
    <ul>
        <li>
            <input type="checkbox" id="checkbox-1" name="checkbox-accordion" />
    <label for="checkbox-1">Alignments&nbsp;</label>
            <div class="content">
                <h3> <?php  echo $priAln;   ?>  </h3>
               
<?php  echo $listAln; ?>
        

            </div>
        </li>
        <li>
            <input type="checkbox" id="checkbox-2" name="checkbox-accordion" />
					 <label for="checkbox-2">Energy profile & sequence conservation&nbsp;</label>
            <div class="content">

<!--           <div class="hspacer50"></div>  -->

<nav class="data-buttons">
<input type="radio" name="seg-1" value="Split Energy" id="seg-E" checked="checked" onclick="changeData2Energy()">
<label for="seg-E" style="text-shadow: none;">Split Energy</label>
<input type="radio" name="seg-1" value="SSA" id="seg-SSA" onclick="changeData2SAA()">
<label for="seg-SSA" style="text-shadow: none;">Solvent Accessibility </label>
<input type="radio" name="seg-1" value="Cons" id="seg-Cons" onclick="changeData2Cons('MAL')">
<label for="seg-Cons" style="text-shadow: none;">Sequence Conservation</label>
</nav>


<!--          <div class="hspacer50"></div> -->
        
	   <div id="showDetails" align="center">
                <input type="checkbox" id="cbox1" style="display:inline;width:10px;"> Alignments &nbsp; &nbsp; &nbsp;
                <input type="checkbox" id="cbox2" style="display:inline;width:10px;"> Loops
          </div>

           <div id="placeholder" style="width:600px;height:300px; margin:0 auto;"></div>
           <br> 
           <br>
             <span id="hoverdata"></span> &nbsp; &nbsp; &nbsp;
             <span id="hoverdata1"></span>
           <br>
           <br>

<!--           <p>  <img src="download/splitEnergy.png" border="0px" width="590px" height="auto"> </p> -->

<!--           <p>  <img src="download/saa.png" border="0px" width="590px" height="auto"> </p> -->

<!--            <p>  <img src="download/cons.png" border="0px" width="590px" height="auto"> </p> -->


            </div>
        </li>




    </ul>
</div>


 <?php   echo $jscript; ?>

<script>
$('#checkbox-1').click(function(){
    if ($('#checkbox-1').attr('checked')) {
      $('html,body').animate({scrollTop: 500}, 800);
    }
  }) 
$('#checkbox-2').click(function(){
    if ($('#checkbox-2').attr('checked')) {
      $('html,body').animate({scrollTop: 1000}, 800);
    }
  })
</script>


<script> 

var options =        
{
series: {
  lines: {
    show: true
  }
  //    bars: { show: true }                                                                                                                                                               
},
crosshair: {
  mode: "x",
  stay: true,
  lineWidth: 2
},
grid: {
  hoverable: true,
  clickable: true,
  autoHighlight: false,
  markings: [
	     //    {xaxis:{from: 1, to: 100 }, color: "#eeeeff" } 
   ]
}
};


var myFlot = $.plot($("#placeholder"), [ <?php echo $listEData ?> ],options);


var str = "<?php echo $istart ?>"; 
var str1 = Number(str)+1;
		  
var jmax = 0;
var ymax = 0;
var str1 = Number(str)+1;
var i, j, dataset = myFlot.getData();
for (i = 0; i < dataset.length; ++i) {     
  var series = dataset[i];
   for (j = 0; j < series.data.length; ++j) {
       if (series.data[j][0] >= parseInt(str)){
          jmax = series.data[j][0];
          ymax = series.data[j][1].toFixed(2); 
          break; 
       } 
   }
}
$("#hoverdata").html("Split sites: <b>" + str + "-" + str1 + "</b>");
$("#hoverdata1").html("E = <b>" + ymax + "</b> (kcal/mol)");

//$("#hoverdata").text("Split sites: " + str + "-" + str1);
//$("#hoverdata1").text("E = " + ymax + " (kcal/mol)");
//$("#hoverdata").text("Split sites: " + str + "-" + str1); 



myFlot.setCrosshair({x: <?php echo $istart?>+0.5});
myFlot.lockCrosshair({x: <?php echo $istart ?>+0.5}); 
globalLockStatus = true;
var latestPosition = <?php echo $istart ?>;

//myFlot.setCrosshair({x: 60});

//$.plot($("#placeholder"), [ <?php echo $listEData ?> ], { yaxis: { max: 2.5 } });

var xaxisLabel = $("<div class='axisLabel xaxisLabel'></div>")
    .text("Residue Number")
    .appendTo($('#placeholder'));

var yaxisLabel = $("<div class='axisLabel yaxisLabel'></div>")
    .text("Split Energy (kcal/mol)")
    .appendTo($('#placeholder'));

//var latestPosition = null;
var clicks = 0;
$("#placeholder").bind("plotclick", function (event, pos, item) { 
    clicks++;
    if(clicks%2==1){
      myFlot.unlockCrosshair();    
      globalLockStatus = false;
    }  else {
      var xc = pos.x; 
      myFlot.lockCrosshair({x: xc});
      globalLockStatus = true;
      latestPosition = pos.x.toFixed(0);;
    }
    
    
    var splitForm = $('[name="group1"]');
    for( var i=0; i<splitForm.length; i++ ){
      if( splitForm[i].checked ) {
        splitForm[i].checked = false;
      }
    }

});

var prevX; 

$("#placeholder").bind( "plothover", function ( evt, position, item ) {
    var str; 
    if(myFlot.getLockStatus()==true){
      str =  latestPosition;
    } else {
      str = position.x.toFixed(0);
      latestPosition = str;
    }
    var jmax = 0;
    var ymax = 0; 
    var str1 = Number(str)+1;
    var i, j, dataset = myFlot.getData();
    for (i = 0; i < dataset.length; ++i) {
      var series = dataset[i];
      for (j = 0; j < series.data.length; ++j) {
        if (series.data[j][0] >= parseInt(str)){
          jmax = series.data[j][0];
          ymax = series.data[j][1].toFixed(2); 
          break;
	}
      }
    }
    //    $("#hoverdata").html("Split sites: <b>" + str + "-" + str1 + "</b>");
    if(whichPlot==1){
      $("#hoverdata").html("Split sites: <b>" + str + "-" + str1 + "</b>");  
      $("#hoverdata1").html("E = <b>" + ymax + "</b> (kcal/mol)");
    } 
    if(whichPlot==2){
      $("#hoverdata").html("Res. ID.: <b>" + str + "</b>"); 
      $("#hoverdata1").html("SAA = <b>" + ymax + "</b> (&#8491;<span class=sup>2</span>)");   
    }
    if(whichPlot==3){
      $("#hoverdata").html("Res. ID.: <b>" + str + "</b>"); 
      $("#hoverdata1").html("Kullback-Leibler (KL) Divergence = <b>" + ymax + "</b>");
    }

    globalYmax = ymax;
   
    curX = parseInt(str);
    if(prevX != curX){
      var plot = 'select resno<'+curX+'; color lightblue; select resno>'+curX+'; color red;  cartoons only;';                                                                                     
      Jmol.script(jmolApplet0,plot);   
    }
    prevX = curX;
  });

var timeout = null;

/*
$(document).on('mousemove', function() {
    clearTimeout(timeout);

    timeout = setTimeout(function() {
	var plot = 'select resno<'+curX+'; color lightblue; select resno>'+curX+'; color red;  cartoons only;';
	Jmol.script(jmolApplet0,plot);
        //        alert('Mouse idle for 3 sec');                                                                                                                                                    
      }, 50);
  });
*/

/*
$(document).on('mousemove', function() {
    clearTimeout(timeout);
    timeout = setTimeout(function() {
        var plot = 'select resno<'+curX+'; color lightblue; select resno>'+curX+'; color red;  cartoons only;';
        Jmol.script(jmolApplet0,plot);
      }, 50);
  });        
*/

$('#cbox1').change(function() {
    if ($(this).is(':checked')) {
      var ddd = [];
      if($('#cbox2:checkbox:checked').length>0){
        ddd = <?php echo "[".$alnBand.",".$loopBand."]" ?>;
      } else {
	ddd = <?php echo "[".$alnBand."]" ?>;
      }
      //      var ddd = [{xaxis:{from: 1, to: 100 }, color: "#eeeeff" }]
	//      options.grid.markings[0].xaxis.from=100;
	//      options.grid.markings[0].xaxis.to=140;
      options.grid.markings = ddd;
      var LockStatus = myFlot.getLockStatus(); 
      if(whichPlot==1){
         myFlot = $.plot($("#placeholder"), [ <?php echo $listEData ?> ],options);
         xaxisLabel = $("<div class='axisLabel xaxisLabel'></div>")
	   .text("Residue Number")
	   .appendTo($('#placeholder'));
         yaxisLabel = $("<div class='axisLabel yaxisLabel'></div>")
	   .text("Split Energy (kcal/mol)")
	   .appendTo($('#placeholder'));
      } 
      if(whichPlot==2){
	 myFlot = $.plot($("#placeholder"), [ <?php echo $listSAAData ?> ],options);
	 xaxisLabel = $("<div class='axisLabel xaxisLabel'></div>")
	   .text("Residue Number")
	   .appendTo($('#placeholder'));
	 yaxisLabel = $("<div class='axisLabel yaxisLabel'></div>")
	   .html("Solvent Accessible Area (&#8491;<span class=sup>2</span>)")
	   .appendTo($('#placeholder'));
      }
      if(whichPlot==3){
	myFlot = $.plot($("#placeholder"), [ <?php echo $listConsData ?> ],options);
	xaxisLabel = $("<div class='axisLabel xaxisLabel'></div>")
	  .text("Residue Number")
	  .appendTo($('#placeholder'));
	yaxisLabel = $("<div class='axisLabel yaxisLabel'></div>")
	  .html("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;KL Divergence")
	  .appendTo($('#placeholder'));
      }
      myFlot.setCrosshair({x: latestPosition});
      if(LockStatus==true){
        myFlot.lockCrosshair({x: latestPosition});
      }

    } else {
      var ddd = [];
      if($('#cbox2:checkbox:checked').length>0){
        ddd = <?php echo "[".$loopBand."]" ?>;
      }
      options.grid.markings = ddd;
      var LockStatus = myFlot.getLockStatus();
      if(whichPlot==1){  
         myFlot = $.plot($("#placeholder"), [ <?php echo $listEData ?> ],options);
         xaxisLabel = $("<div class='axisLabel xaxisLabel'></div>")
           .text("Residue Number")
           .appendTo($('#placeholder'));
         yaxisLabel = $("<div class='axisLabel yaxisLabel'></div>")
           .text("Split Energy (kcal/mol)")
           .appendTo($('#placeholder'));
      }
      if(whichPlot==2){
	myFlot = $.plot($("#placeholder"), [ <?php echo $listSAAData ?> ],options);
	xaxisLabel = $("<div class='axisLabel xaxisLabel'></div>")
	  .text("Residue Number")
	  .appendTo($('#placeholder'));
	yaxisLabel = $("<div class='axisLabel yaxisLabel'></div>")
	  .html("Solvent Accessible Area (&#8491;<span class=sup>2</span>)")
	  .appendTo($('#placeholder'));
      }
      if(whichPlot==3){
	myFlot = $.plot($("#placeholder"), [ <?php echo $listConsData ?> ],options);
	xaxisLabel = $("<div class='axisLabel xaxisLabel'></div>")
	  .text("Residue Number")
	  .appendTo($('#placeholder'));
	yaxisLabel = $("<div class='axisLabel yaxisLabel'></div>")
	  .html("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;KL Divergence")
	  .appendTo($('#placeholder'));
      }
      myFlot.setCrosshair({x: latestPosition});
      if(LockStatus==true){
        myFlot.lockCrosshair({x: latestPosition});
      }
    }
  });


$('#cbox2').change(function() {
    if ($(this).is(':checked')) {
      var ddd = [];
      if($('#cbox1:checkbox:checked').length>0){
        ddd = <?php echo "[".$alnBand.",".$loopBand."]" ?>;
      } else {
        ddd = <?php echo "[".$loopBand."]" ?>;
      }
      options.grid.markings = ddd;
      var LockStatus = myFlot.getLockStatus();
      if(whichPlot==1){
         myFlot = $.plot($("#placeholder"), [ <?php echo $listEData ?> ],options);
         xaxisLabel = $("<div class='axisLabel xaxisLabel'></div>")
           .text("Residue Number")
           .appendTo($('#placeholder'));
         yaxisLabel = $("<div class='axisLabel yaxisLabel'></div>")
           .text("Split Energy (kcal/mol)")
           .appendTo($('#placeholder'));
      }
      if(whichPlot==2){
        myFlot = $.plot($("#placeholder"), [ <?php echo $listSAAData ?> ],options);
        xaxisLabel = $("<div class='axisLabel xaxisLabel'></div>")
          .text("Residue Number")
          .appendTo($('#placeholder'));
        yaxisLabel = $("<div class='axisLabel yaxisLabel'></div>")
          .html("Solvent Accessible Area (&#8491;<span class=sup>2</span>)")
          .appendTo($('#placeholder'));
      }
      if(whichPlot==3){
        myFlot = $.plot($("#placeholder"), [ <?php echo $listConsData ?> ],options);
        xaxisLabel = $("<div class='axisLabel xaxisLabel'></div>")
          .text("Residue Number")
          .appendTo($('#placeholder'));
        yaxisLabel = $("<div class='axisLabel yaxisLabel'></div>")
          .html("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;KL Divergence")
          .appendTo($('#placeholder'));
      }
      myFlot.setCrosshair({x: latestPosition});
      if(LockStatus==true){
        myFlot.lockCrosshair({x: latestPosition});
      }
    } else {
      var ddd = [];
      if($('#cbox1:checkbox:checked').length>0){
	ddd = <?php echo "[".$alnBand."]" ?>;
      } 
      options.grid.markings = ddd;
      var LockStatus = myFlot.getLockStatus();
      if(whichPlot==1){
         myFlot = $.plot($("#placeholder"), [ <?php echo $listEData ?> ],options);
         xaxisLabel = $("<div class='axisLabel xaxisLabel'></div>")
           .text("Residue Number")
           .appendTo($('#placeholder'));
         yaxisLabel = $("<div class='axisLabel yaxisLabel'></div>")
           .text("Split Energy (kcal/mol)")
           .appendTo($('#placeholder'));
      }
      if(whichPlot==2){
        myFlot = $.plot($("#placeholder"), [ <?php echo $listSAAData ?> ],options);
        xaxisLabel = $("<div class='axisLabel xaxisLabel'></div>")
          .text("Residue Number")
          .appendTo($('#placeholder'));
        yaxisLabel = $("<div class='axisLabel yaxisLabel'></div>")
          .html("Solvent Accessible Area (&#8491;<span class=sup>2</span>)")
          .appendTo($('#placeholder'));
      }
      if(whichPlot==3){
        myFlot = $.plot($("#placeholder"), [ <?php echo $listConsData ?> ],options);
        xaxisLabel = $("<div class='axisLabel xaxisLabel'></div>")
          .text("Residue Number")
          .appendTo($('#placeholder'));
        yaxisLabel = $("<div class='axisLabel yaxisLabel'></div>")
          .html("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;KL Divergence")
          .appendTo($('#placeholder'));
      }
      myFlot.setCrosshair({x: latestPosition});
      if(LockStatus==true){
        myFlot.lockCrosshair({x: latestPosition});
      }
    }
  });


</script>


<script>
function changeData2Energy() {
  whichPlot = 1; 
  myFlot = $.plot($("#placeholder"), [ <?php echo $listEData ?> ],options);
  xaxisLabel = $("<div class='axisLabel xaxisLabel'></div>")
    .text("Residue Number")
    .appendTo($('#placeholder'));   
  yaxisLabel = $("<div class='axisLabel yaxisLabel'></div>")
    .text("Split Energy (kcal/mol)")
    .appendTo($('#placeholder'));
  myFlot.setCrosshair({x: latestPosition});
  if(globalLockStatus==true){
    myFlot.lockCrosshair({x: latestPosition});
  }
  var jmax = 0;
  var ymax = 0;
  var i, j, dataset = myFlot.getData();
  for (i = 0; i < dataset.length; ++i) {
    var series = dataset[i];
    for (j = 0; j < series.data.length; ++j) {
      if (series.data[j][0] >= parseInt(curX)){
	jmax = series.data[j][0];
	ymax = series.data[j][1].toFixed(2);
	break;
      }
    }
  }
  $("#hoverdata").html("Split sites: <b>" + str + "-" + str1 + "</b>");   
  $("#hoverdata1").html("E = <b>" + ymax + "</b> (kcal/mol)");
}
function changeData2SAA(){
  whichPlot = 2; 
  myFlot = $.plot($("#placeholder"), [ <?php echo $listSAAData ?> ],options);
  xaxisLabel = $("<div class='axisLabel xaxisLabel'></div>")
    .text("Residue Number")
    .appendTo($('#placeholder'));
  yaxisLabel = $("<div class='axisLabel yaxisLabel'></div>")
    .html("Solvent Accessible Area (&#8491;<span class=sup>2</span>)")
    .appendTo($('#placeholder'));
  myFlot.setCrosshair({x: latestPosition});
  var LockStatus = myFlot.getLockStatus();
  if(globalLockStatus==true){
    myFlot.lockCrosshair({x: latestPosition});
  }
  var jmax = 0;
  var ymax = 0;
  var i, j, dataset = myFlot.getData();
  for (i = 0; i < dataset.length; ++i) {
    var series = dataset[i];
    for (j = 0; j < series.data.length; ++j) {
      if (series.data[j][0] >= parseInt(curX)){
	jmax = series.data[j][0];
	ymax = series.data[j][1].toFixed(2);
	break;
      }
    }
  }
  $("#hoverdata").html("Res. ID.: <b>" + str + "</b>");
  $("#hoverdata1").html("SAA = <b>" + ymax + "</b> (&#8491;<span class=sup>2</span>)");
}

function changeData2Cons(){
  whichPlot = 3; 
  myFlot = $.plot($("#placeholder"), [ <?php echo $listConsData ?> ],options);
  xaxisLabel = $("<div class='axisLabel xaxisLabel'></div>")
    .text("Residue Number")
    .appendTo($('#placeholder'));
  yaxisLabel = $("<div class='axisLabel yaxisLabel'></div>")  
    .html("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;KL Divergence") 
    .appendTo($('#placeholder')); 
  myFlot.setCrosshair({x: latestPosition});
  var LockStatus = myFlot.getLockStatus();
  if(globalLockStatus==true){
    myFlot.lockCrosshair({x: latestPosition});
  }
  var jmax = 0;
  var ymax = 0;
  var i, j, dataset = myFlot.getData();
  for (i = 0; i < dataset.length; ++i) {
    var series = dataset[i];
    for (j = 0; j < series.data.length; ++j) {
      if (series.data[j][0] >= parseInt(curX)){
        jmax = series.data[j][0];
        ymax = series.data[j][1].toFixed(2);
        break;
      }
    }
  }
  $("#hoverdata").html("Res. ID.: <b>" + str + "</b>");
  $("#hoverdata1").html("Kullback-Leibler (KL) Divergence = <b>" + ymax + "</b>");

}
</script>



<script>
  var rootDiv = document.getElementById("msa");

var opts = {
el: rootDiv,
importURL: "https://raw.githubusercontent.com/wilzbach/msa/master/test/dummy/samples/p53.clustalo.clustal",
vis: {
  labelId: false,
  conserv: false,
  overviewbox: false,
  seqlogo: true
  },
conf: {
  dropImport: true
},
zoomer: {
  menuFontsize: "12px",
  autoResize: true
}
};
var m = msa(opts);
</script>



		</div>
	</div>
</body>
</html>
