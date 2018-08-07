<?php
$dbusername="spell_svc";
$dbpassword="sixth-S3cr3t";
$database="spell";
$tablepdbfiles="pdbfiles";
$tablejobs = "jobs";
$tableusers = "users";
$tableresults = "results";
$gaia_results = "gaia_results";
$target_path = "uploads/";
$workdir = "exec/";
$MMut = 5;  # maximum mutations allowed in one submission
$exec_checkPDB = "bin/checkPDB.pl";
$exec_extraCheckPDB = "bin/check";
$exec_checkAln = "bin/checkAlignmentFormat";
$getmlcs = "bin/getmlcs.sh";
$MEDUSA_HOME = "workspace/Medusa3";
$MEDUSA_PARAM = "workspace/Filters/parameter";
$FILTER_HOME = "workspace/Filters";
$EXEC_CHECKPDB = "bin/pdb2MEDUSApdb.linux workspace/Filters/parameter ";
$EXEC_PDBFILTER = "bin/pdbfilter.linux ";
$EXEC_PDBRENUM  = "bin/pdb_renum.pl ";
$EXEC_PDBMISSC  = "bin/complex_pdb_reconMissSC.linux -p workspace/Filters/parameter ";
$EXEC_COMPLEX_FIXBB = "bin/complex_fixbb_customDesign.linux -p workspace/Filters/parameter ";
$PYMOL_PATH     = "/usr/lib/python2.4/site-packages/pymol";
$spell_workdir           = "/home/html/spell/daemon/exec";


