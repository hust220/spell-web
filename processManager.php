<?php
require('config/config.inc.php');
if (empty($_SESSION['username'])) {
  header("Location: login.php");
}
print "<html>";
include("txt/head.txt");
?>
        <div id="content">
                <?php 
                   echo '<div id="nav">';
                   include("txt/menu.php");
                   echo "</div>";
                ?>

              <div id="main_content">
                      <div class="indexTitle" id="step"> Submit a task </div>
                      <div id="submitform" class="mdiv">
                      <form id="jobform" enctype="multipart/form-data" action="filter.php" method="POST" autocomplete="off">

                         <fieldset><legend align=center>Job</legend>
                            <div class="cdiv">
                               <div class="ldiv">Job Title
                                  <span class='formInfo'>
                                  <a href="txt/submit/jobtitle.htm?width=400" name="What's this?" class="jTip" id='title'> <img src="style/img/help.png" border="0px"></a>
                                  </span> :
                               </div>
                               <div class="rdiv"><input id="title" maxlength=10 type="text" name="title" value=""></div>
                            </div>
                         </fieldset>


                         <fieldset><legend align=center>Input Structure</legend>
                            <div class="cdiv">
		               <div class="ldiv">Input Type :</div>
		               <div class="rdiv"><input id=rpdb class=radio type=radio name=inptype value=pdb align=middle checked>PDB ID&nbsp;&nbsp;<input id=rfile class=radio type=radio name=inptype value=file align=middle>File</div>
                            </div>

                            <div class="cdiv" id="pdbdiv"> 
                               <div class="ldiv">PDB ID :</div>
                               <div class="rdiv"><input type=text name=pdbid value="" maxlength=4 id=pdbid><img id="verifying" style="display: none;" width="15px" height="15px" align="middle" src="style/img/running.gif" border="0px" /></div>
                               <div class="cdiv">
                                  <div class="ldiv"></div>
                                  <div class="rdiv" id="pdbinfo"></div>
                               </div>
                               <div class="cdiv" id="pdbrep"> </div>
                            </div> 

                            <div class="cdiv" id="filediv">
                               <div class="ldiv">Choose File :</div>
                               <div class="rdiv"><input type=file name=uploadedpdb value="" id=file><img id="uploading" style="display: none;" width="15px" height="15px" align="middle" src="style/img/running.gif" border="0px" /></div>
                               <div class="cdiv">
                                  <div class="ldiv"></div>
                                  <div class="rdiv" id="fileinfo"></div>
                               </div>
                               <div class="cdiv" id="filerep"> </div> 
                            </div>                 

                         </fieldset>

                         <fieldset><legend align=center>Input Alignment</legend>
                            <div class="cdiv">
                               <div class="ldiv">Input Type
                                  <span class='formInfo'>
                                  <a href="txt/submit/alignment.htm?width=400" name="What's this?" class="jTip" id='titlealn'> <img src="style/img/help.png" border="0px"></a>
                                  </span> :
                               </div>
                               <div class="rdiv"><input id=pfam class=radio type=radio name=inpaln value=pdb align=middle checked>Fetch from Pfam&nbsp;&nbsp;<input id=afile class=radio type=radio name=inpaln value=file align=middle>File</div>
                            </div>

                            <div class="cdiv" id="filealn">
                               <div class="ldiv">Choose File :</div>
                               <div class="rdiv"><input type=file name=uploadedaln value="" id=alnfile><img id="uploadingaln" style="display: none;" width="15px" height="15px" align="middle" src="style/img/running.gif" border="0px" /></div>                                                
                               <div class="cdiv">
                                 <div class="ldiv"></div>
                                 <div class="rdiv" id="alninfo"></div>
                               </div>
                           </div>
                         </fieldset>

                         <div class="sbut"><input type="button" id="spell_submit" class="submitbtn" value="Submit"></div>

                      </form>
                      </div>




        <div id=status></div>

        <div id="processing"></div>
        <div class=cdiv id="jobstatus">
           <fieldset><legend align=center>Job Report</legend>
           <?php include('txt/results.htm'); ?>
           </fieldset>
        </div>


              </div>
	</div>

        <div class="cdiv"></div>
           <div id=dialog  style="font-size: 13px;" title="Job Status Notification">
        </div>

        <!-- --------------------END OF PAGE------------------------ -->
        <br>
        <div id="instruction" style="border-top: 1px dotted #990000; width:100%; padding-top: 10px;">
              If you are using SPELL for the first time, we recommend that you read the documentation before submitting your job.
        </div>

</body>
</html>

