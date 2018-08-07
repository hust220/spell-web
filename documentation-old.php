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
				Frequently asked questions
			</div>
			<div class="hspacer10"></div>
			<div id="tabs">
				<ul>
					<li><a href="#chiron">Chiron</a></li>
					<li><a href="#gaia">Gaia</a></li>
				</ul>
				<div id="chiron" style="font-size: 0.9em;">
					<h2>Scientific</h2>

					<div id="div_scientific" style="padding-left:25px;">
			
						<h3><strong><a href="javascript:slide_sq1.slideIt();slide_sq1.hideRest();">What is Chiron?</a></strong></h3>
						<div id="div_sq1" style="display:none"><p>Named after the Thessalian god of healing, Chiron is a server that rapidly minimizes steric clashes in proteins using short discrete molecular dynamics(DMD) simulations.</p></div>
						
						<h3><strong><a href="javascript:slide_sq2.slideIt();slide_sq2.hideRest();">What is a steric clash?</a></strong></h3>
						<div id="div_sq2" style="display:none">
							<p>Any atomic overlap resulting in Van der Waal&#39;s repulsion energy greater than 0.3 kcal/mol (0.5 k<sub>B</sub>T) except in the following cases:<ol><li>When the atoms are bonded</li><li>When the atoms form a hydrogen bond (The heavy atoms involved in the hydrogen bond, we assign the Van der Waal&#39;s radius of hydrogen to be zero)</li><li>When the atoms involved are backbone atoms and have separation of 2 residues.</li></ol> The Van der Waal&#39;s repulsion energy is calculated using CHARMM non-bonded  parameters, which are identical to CNS parameters except for carboxyl oxygens.
</p>
						</div>
				
						<h3><strong><a href="javascript:slide_sq3.slideIt();slide_sq3.hideRest();">Why care about clashes?</a></strong></h3>
						<div id="div_sq3" style="display:none">
							<p>Steric clashes arise when there is unfavorable overlap of the electron clouds of two atoms. Proteins pack tight enough to avoid these clashes. Even though only limited amount of clashes are seen in high resolution crystal structures of proteins (probably to facilitate formation of hydrogen bonds and/or electrostatic interactions), many protein structures feature large number of clashes, either because of lack of resolution, or as side-effects of homology models. One would expect that while generating homology models, the probability of side-chain atoms clashing among themselves and with the backbone atoms increases upon changing side chain identities of the protein. In fact, we observe that the mean normalized clash energy of high-resolution crystal structures (2.5-3.5 &Aring;) is significantly higher than that for structures with resolution better than 2.5 &Aring;. In these cases, the clashes are not the property of the protein but rather an artifact of model building. To arrive at a more physical model in such cases, one would like to first identify these clashes and then remove them with minimum perturbation of the backbone.</p>
						</div>
					
						<h3><strong><a href="javascript:slide_sq4.slideIt();slide_sq4.hideRest();">How does Chiron remove clashes?</a></strong></h3>
						<div id="div_sq4" style="display:none">
							<p>MD or DMD performed under physiological conditions with the structures having high clash energy would result in the protein rapidly unfolding. However, Chiron utilizes a high heat exchange rate of the solute (protein) with the bath in DMD simulations; thus rapidly quenching high velocities arising due to clashes. Using our simulation conditions and the inherent sampling power of DMD, Chiron employs an iterative protocol aimed at minimizing the given protein until it attains an &#39;acceptable clash score&#39;. Chiron rapidly minimizes clashes while at the same time causing minimal perturbation of the protein backbone. The resulting protein structure has normalized clash score that is comparable to high-resolution protein structures (&lt;2.5 &Aring;).</p>
						</div>
					
						<h3><strong><a href="javascript:slide_sq5.slideIt();slide_sq5.hideRest();">How did you arrive at the &#39;acceptable clash score&#39;?</a></strong></h3>
						<div id="div_sq5" style="display:none">
							<p>We built a distribution of normalized clash-score using nearly 4300 single chain crystal structures at least 25 residues long and having resolution equal to or better than 2.5 &Aring;. A structure with normalized clash score less than one standard deviation away from the mean of the above distribution is said to have an acceptable clash score.</p>
						</div>
			
					</div>
			
					<h2>Technical</h2>
					<div id="div_technical" style="padding-left:25px;">
						<h3><strong><a href="javascript:slide_tq1.slideIt();slide_tq1.hideRest();">How do I submit a job?</a></strong></h3>
						<div id="div_tq1" style="display:none"><p>For every minimization run, the server requires a protein structure, which can be provided either by specifying a PDB identifier or by a direct file upload. The submitted tasks are queued and processed when there are available computational resources. The recently submitted tasks (since last login) are listed under the "Home/Overview" menu.</p></div>
			
						<h3><strong><a href="javascript:slide_tq2.slideIt();slide_tq2.hideRest();">Can my PDB contain small molecules?</a></strong></h3>
						<div id="div_tq2" style="display:none"><p>Chiron currently recognizes small molecules in a given PDB and maintains them static during the minimization process. During job submission, you may select the small molecules you want to consider for minimization by selecting the checkboxes next to the desired molecule names. If the PDB contains small molecules, both the clashes of the protein atoms within themselves and with the small molecules will be minimized to acceptable levels. Currently, some atom types are not supported by Chiron. Furthermore, bonds between atoms of the protein and the small molecule will not be recognized. Do not submit jobs that do not conform to the above requirements since they may fail.</p></div>
						
						<h3><strong><a href="javascript:slide_tq3.slideIt();slide_tq3.hideRest();">How do I view results?</a></strong></h3>
						<div id="div_tq3" style="display:none"><p>Finished jobs are listed under &#39;User Activity&#39; page. Clicking the &#39;eye&#39; icon under action column for a particular job will take you to the results page ofthat job. In the result page, you can<ol><li>Download the minimized structure in the PDB format</li><li>View/Download the clash reports of the initial and final structures that lists all the atom-pairs that clash, their distance of separation, their accepted distance of separation (at which the repulsive energy will be less than 0.3 kcal/mol) and the raw clash-energy.</li><li>Download the PyMOL script (see &#39;What do I do with the PyMOL script (.py file)?&#39;).</li><li>View the minimization summary (see &#39;What is displayed under Minimization Summary&#39;?).</li></ol></p></div>
			
						<h3><strong><a href="javascript:slide_tq4.slideIt();slide_tq4.hideRest();">What do I do with the PyMOL script (.py file)?</a></strong></h3>
						<div id="div_tq4" style="display:none"><p>To enable visualization of the clashes before and after minmization, Chiron also provides a python script that can be used in conjunction with pymol (http://www.pymol.org/). Just download the .py file and open it with pymol. Alternatively, if pymol is open, type in the pymol command line &#39;run &lt;.py file with path&gt; &#39;  and press return</p><p><strong>Note : The python script will refresh pymol to start a new session, so save existing work before running the script. Before running the script, make sure the current directory is writeable.</strong></p><p>Upon successfully running the script, two objects will be created. The first object is the protein structure after minimization, represented with lines and cartoon. The second object contains all the clashes as cylinders. The clashes are color-coded (rainbow spectrum) cylinders of different base-radii. Cylinders with the smallest base radii and colored violet denote the clashes with the lowest repulsion energy in the structure, while cylinders with the largest base radii and colored red correspond to the clashes with highest repulsion energy.</p></div>
			
						<h3><strong><a href="javascript:slide_tq5.slideIt();slide_tq5.hideRest();">What is displayed under &#39;Minimization Summary&#39;?</a></strong></h3>
						<div id="div_tq5" style="display:none"><p>To compare the clash-score of the input and minimized structures to our benchmark set of 4300 high-resolution structures, we plot the distribution of the normalized clash-score of our benchmark set and indicate the intial and final clash-score in the plot with respect to the distribution.</p></div>
			
						<h3><strong><a href="javascript:slide_tq6.slideIt();slide_tq6.hideRest();">Why did my job fail?</a></strong></h3>
						<div id="div_tq6" style="display:none"><p>Please shoot an email to the site admin and we will fix individual issues. There are many possible issues with input pdb files, some of which we list here and some under the question Why does the server say <strong>&#34;Error loading pdb file&#34;</strong>?, but the list is not exhaustive.<ol><li>The PDB has non-protein atoms, like DNA or RNA. The server does not read in HETATM, so that is not a problem, but the server does not recognize ATOM records that contain non-protein coordinates.</li><li>Multi-Chain PDBs are acceptable, however, the chains should be separated by either TER card or have different chain-IDs to differentiate chains. Continuous residue numbering combined with same chain ID and lack of TER card between two chains might cause jobs to fail.</li><li>If there is a chain break (missing coordinates for residues of a loop, for example), the server will still work. However, such chain breaks need to be indicated in one of these ways: retaining original residue numbering (then the server will detect missing residue numbers, thus accounting for chain break) or by putting a TER card between the residues lining the break, or by using different chain IDs for different parts of the broken chain.</li><li>Some PDB files have a continuous chain but discontinuous residue ids. Such a case would be wrongly identified as a chain break, causing DMD to fail.</li><li>If the HETATM section of the input pdb contains atom types that have not yet been parametrized in DMD or if it contains atoms that are bonded to the protein, your job is likely to fail. Also, bonds between small molecules and protein atoms are not recognized and hence reported as clashes.</li></ol></div>
						<h3><strong><a href="javascript:slide_tq7.slideIt();slide_tq7.hideRest();">Why does the server say &#34;Error loading pdb file&#34;?</a></strong></h3>
						<div id="div_tq7" style="display:none"><p>This error refers to a case where our program encounters an error reading the PDB file. Following are some of the possible reasons.<ol><li>You might have provided a PDB ID that does not exist.</li><li>Your PDB file contains unnatural amino acids. Our current implementation only supports the 20 natural amino acid types: ALA, CYS, ASP, GLU, PHE, GLY, HIS, ILE, LYS, LEU, MET, ASN, PRO, GLN, ARG, SER, THR, VAL, TRP and TYR. Existence of residues other than the listed types in the ATOM records can cause the error.</li><li>Backbone heavy atoms are missing. The program requires coordinates of all backbone N, C and CA atoms for each residue, and will reconstruct other missing atoms from the input pdb file.</li></ol></p></div>
					</div>
					<script type="text/javascript">
						var slide_sq1 = new animatedDiv('slide_sq1','div_sq1',500,null,false,null);
						var slide_sq2 = new animatedDiv('slide_sq2','div_sq2',500,null,false,null);
						var slide_sq3 = new animatedDiv('slide_sq3','div_sq3',500,null,false,null);
						var slide_sq4 = new animatedDiv('slide_sq4','div_sq4',500,null,false,null);
						var slide_sq5 = new animatedDiv('slide_sq5','div_sq5',500,null,false,null);

						var slide_tq1 = new animatedDiv('slide_tq1','div_tq1',500,null,false,null);
						var slide_tq2 = new animatedDiv('slide_tq2','div_tq2',500,null,false,null);
						var slide_tq3 = new animatedDiv('slide_tq3','div_tq3',500,null,false,null);
						var slide_tq4 = new animatedDiv('slide_tq4','div_tq4',500,null,false,null);
						var slide_tq5 = new animatedDiv('slide_tq5','div_tq5',500,null,false,null);
						var slide_tq6 = new animatedDiv('slide_tq6','div_tq6',500,null,false,null);
						var slide_tq7 = new animatedDiv('slide_tq7','div_tq7',500,null,false,null);
					</script>
				</div>
				
				
				<div id="gaia" style="font-size: 0.9em;">
					<h2>Scientific</h2>

					<div id="div_scientific" style="padding-left:25px;">
			
						<h3><strong><a href="javascript:slide_gsq1.slideIt();slide_gsq1.hideRest();">What is Gaia?</a></strong></h3>
						<div id="div_gsq1" style="display:none"><p>Gaia, named after the Greek personification of mother nature, is a tool to estimate the quality of a given protein structure in comparison against high-resolution crystal structures. The parameters used in quality estimates include steric-clashes, unsatisfied hydrogen-bond partners, packing artifacts (voids), solvent accessible surface area and the covalent geometry of the protein structure.</p></div>
						
						<h3><strong><a href="javascript:slide_gsq2.slideIt();slide_gsq2.hideRest();">What is a steric clash?</a></strong></h3>
						<div id="div_gsq2" style="display:none">
							<p>Any atomic overlap resulting in Van der Waal&#39;s repulsion energy greater than 0.3 kcal/mol (0.5 k<sub>B</sub>T) except in the following cases:<ol><li>When the atoms are bonded</li><li>When the atoms form a hydrogen bond (The heavy atoms involved in the hydrogen bond, we assign the Van der Waal&#39;s radius of hydrogen to be zero)</li><li>When the atoms involved are backbone atoms and have separation of 2 residues.</li></ol> The Van der Waal&#39;s repulsion energy is calculated using CHARMM non-bonded  parameters, which are identical to CNS parameters except for carboxyl oxygens.
</p>
						</div>
				
						<h3><strong><a href="javascript:slide_gsq3.slideIt();slide_gsq3.hideRest();">How do you define unsatisfied hydrogen-bonding partners?</a></strong></h3>
						<div id="div_gsq3" style="display:none">
							<p>Hydrogen bonds are essential anchors that stabilize a folded protein. Even though the exact balance of the energetics of hydrogen bonds between polar atoms in proteins compared to the hydrogen bonds between polar atoms and solvent is debated, in the absence of the solvent in the protein core, any polar atom that does not form a hydrogen bond would destabilize the protein. Absence of secondary structural elements in the core or the presence of polar sidechains leads to unsatisfied hydrogen bonding partners (considering surface polar atoms to form hydrogen bonds with solvent). We define a polar nitrogen/oxygen atom as an unsatisfied hydrogen bond donor/acceptor if it is buried from the solvent and is not involved in a hydrogen bond. If a polar atom belongs to a residue whose total SASA is zero, it is marked as buried. On the other hand, if the polar atom itself is buried, but the residue it belongs to features a non-zero SASA, rotamer changes/side chain dynamics could expose the polar atom, and thus, the polar atom is classified as being in the shell: an intermediate layer between buried and solvent accessible regions of the protein. We measure unsatisfied hydrogen bonds as the percentage of total polar atoms that do not form hydrogen bonds in i) buried region of the protein and ii) shell region of the protein. The percentage of unsatisfied hydrogen bond partners in the buried region of a protein across our dataset fits to a Gaussian distribution centered at 0.14 with a standard deviation of 1.3, indicating 84% of the proteins in our dataset feature less than 1.44 % polar atoms that do not form hydrogen bonds (Figure 3a). Similarly, in the shell region, we observe 7.5 &plusmn; 2 % of the total polar atoms that do not form hydrogen bonds (Figure 3b). In the absence of hydrogen bonding partners, polar atoms in the buried regions can form hydrogen bonds with buried structural waters. Since we use high-resolution structures in our datasets, most of structural waters in these structures are expected to be resolved. Hence we ask the question: what is the influence of buried waters in lowering the number of polar unsatisfied atoms, thus stabilizing the protein. We observe that for buried polar atoms, the percentage of unsatisfied polar atoms decreases from 0.14 &plusmn; 1.3 to -0.08 &plusmn; 0.95, when hydrogen bonds to buried structural waters are considered. Similarly, for shell region, the percentage of polar atoms decreases from 7.5 &plusmn; 2 to 4.8 &plusmn; 2. Thus, structural waters make a significant contribution in forming hydrogen bonds in the shell and buried regions of the protein.</p>
						</div>
					
						<h3><strong><a href="javascript:slide_gsq4.slideIt();slide_gsq4.hideRest();">How do you calculate solvent accessible surface area (SASA)?</a></strong></h3>
						<div id="div_gsq4" style="display:none">
							<p>We define the SASA of a protein as the area covered by the center of a solvent sphere, as it rolls over the protein surface. Considering the radius of the solvent sphere to be 1.4, we can obtain SASA by calculating the surface area of the protein, when the radii of all its atoms are increased by 1.4. We use the method originally developed by LeGrand and Merz for calculating SASA. Briefly, Boolean masks are used and we use 1024 dots on the surface of each atom. We calculated SASA of our high-resolution structures and observe that the SASA scales as a function of chain length (N), N<sup>0.74</sup>. Thus, when the total SASA for all structures are normalized by the factor (chain length)<sup>-0.74</sup>, we observe the mean SASA / (chain length)<sup>0.74</sup> as 195.1 &plusmn; 14.5 &Aring;<sup>2</sup>.</p>
						</div>
					
						<h3><strong><a href="javascript:slide_gsq5.slideIt();slide_gsq5.hideRest();">How do you define/detect voids in proteins?</a></strong></h3>
						<div id="div_gsq5" style="display:none">
							<p>We define void volume as the volume of the free space inside a protein that is not accessible to solvent. Voids in proteins are thermodynamically unfavorable, because of the work done in creating vacuum in the protein core, PV. Here, P is the pressure and V is the total volume of the voids inside a protein. To identify the voids in a protein, we first use the method of LeGrand and Merz, to obtain all the dots on the surface of each atom that is not buried by other atoms. These exposed dots could either belong to the surface or internal voids in the protein. Performing single linkage clustering on these exposed dots would yield one large cluster corresponding to the solvent accessible surface and several small clusters corresponding to the internal voids. We use distance between dots for performing single linkage clustering. After identification of the internal voids, we calculate the volume of each of these voids by numerical integration: we iteratively increment the radii of all the atoms forming the void by 0.01 &Aring; and sum up the surface area of these voids times 0.01 at each increment till the area converges to zero. To estimate the error in integration to calculate void volumes, we constructed a toy-model consisting of a cube whose surface is completely lined with spheres and whose void volume can be determined analytically. Performing our void identification on this model would separate the internal and external surfaces of the cube. By calculating the void volume using our integration method on the toy model, we can estimate the error in our integration. Since active sites of some enzymes could be identified as voids, we divided our dataset into enzymes and non-enzymes to observe if enzymes on average have larger voids compared to non-enzymes. We first examine the distribution of the total void volume of a protein, which is a sum of the volumes of all the voids in a protein. To avoid bias due to size of the protein, we divide the total void volume by its chain length (number of amino acids), which is proportional to the total volume of the protein. We observe that both enzymes and non-enzymes mostly prefer a total void-volume of nearly zero. The rest of the points follow an exponentially decreasing distribution, with enzymes on an average having larger total void volume compared to non-enzymes. The difference in total void-volume distribution of enzymes and non-enzymes, we infer that several of the active sites may be buried. The fact that most proteins prefer no voids reiterates that voids are unfavorable structural anomalies. Further, an exponential decay of the probability of a given total void volume suggests a Poisson distribution: finding voids in protein cores is a rare event. In order to examine the range of voids that are observed in proteins, we next plot the distribution of the volume of all individual voids detected in our dataset. We observe that the distribution of voids can be fit with an exponential function each for lower and higher void volumes respectively. The lower values of void volumes are identical for enzymes and non-enzymes, reflecting non-active site voids. The probability of finding larger voids is slightly higher for enzymes. From the distribution, we infer that the penalty for forming smaller voids is much more than for forming larger voids as observed from the difference in the slope of the two distributions. Interestingly, the transition point from smaller voids to larger voids occurs at &tilde;11.5 &Aring;<sup>3</sup>, which is close to the molecular volume of water (assuming water radius to be 1.4 &Aring;). Thus, these distributions suggest that voids larger than the size of a water molecule are less unfavorable compared to voids smaller than a water molecule.</p>
						</div>
			
						<h3><strong><a href="javascript:slide_gsq6.slideIt();slide_gsq6.hideRest();">How did you arrive at the &#39;acceptable&#39; scores for each parameter?</a></strong></h3>
						<div id="div_gsq6" style="display:none">
							<p>Using the distributions obtained from our high-resolution dataset, we report a p-value for the input protein structure (the p-value indicates the fraction of structures with a score worse than the input structure). A p-value lower than 0.05 indicates that the input structure is in need of further refinement.</p>
						</div>
					</div>
			
					<h2>Technical</h2>
					<div id="div_technical" style="padding-left:25px;">
						<h3><strong><a href="javascript:slide_gtq1.slideIt();slide_gtq1.hideRest();">How do I submit a job?</a></strong></h3>
						<div id="div_gtq1" style="display:none"><p>For every minimization run, the server requires a protein structure, which can be provided either by specifying a PDB identifier or by a direct file upload. In case the PDB ID corresponds to an NMR structure, only the first model is considered. Should you wish to upload an NMR structure, please make sure you submit only one model, in order to avoid failed jobs. The submitted tasks are queued and processed when there are available computational resources. The recently submitted tasks (since last login) are listed under the "Home/Overview" menu.</p></div>
			
						<h3><strong><a href="javascript:slide_gtq2.slideIt();slide_gtq2.hideRest();">Can my PDB contain small molecules?</a></strong></h3>
						<div id="div_gtq2" style="display:none"><p>Gaia currently ignores small molecules in a given PDB. Therefore, results, especially those pertaining to voids, must be reviewed carefully since buried small molecule binding sites will be represented as voids.</p></div>
						
						<h3><strong><a href="javascript:slide_gtq3.slideIt();slide_gtq3.hideRest();">How do I view results?</a></strong></h3>
						<div id="div_gtq3" style="display:none"><p>Finished jobs are listed under &#39;User Activity&#39; page. Clicking the &#39;eye&#39; icon under action column for a particular job will take you to the results page ofthat job. In the result page, you can<ol><li>Download the minimized structure in the PDB format</li><li>View/Download the clash reports of the initial and final structures that lists all the atom-pairs that clash, their distance of separation, their accepted distance of separation (at which the repulsive energy will be less than 0.3 kcal/mol) and the raw clash-energy.</li><li>Download the PyMOL script (see &#39;What do I do with the PyMOL script (.py file)?&#39;).</li></ol></p></div>
			
						<h3><strong><a href="javascript:slide_gtq4.slideIt();slide_gtq4.hideRest();">What do I do with the PyMOL script (.py file)?</a></strong></h3>
						<div id="div_gtq4" style="display:none"><p>To enable visualization of different features and anamolous regions of the input structure, Gaia also provides a python script that can be used in conjunction with pymol (http://www.pymol.org/). Just download the .py file and open it with pymol. Alternatively, if pymol is open, type in the pymol command line &#39;run &lt;.py file with path&gt; &#39;  and press return</p><p><strong>Note : The python script will refresh pymol to start a new session, so save existing work before running the script.</strong></p><p>Upon successfully running the script, independent objects representing each filter will be created.</p></div>
			
						<h3><strong><a href="javascript:slide_gtq6.slideIt();slide_gtq6.hideRest();">Why did my job fail?</a></strong></h3>
						<div id="div_gtq6" style="display:none"><p>Please shoot an email to the site admin and we will fix individual issues. There are many possible issues with input pdb files, some of which we list here and some under the question Why does the server say <strong>&#34;Error loading pdb file&#34;</strong>?, but the list is not exhaustive.<ol><li>The PDB has non-protein atoms, like DNA or RNA. The server does not read in HETATM, so that is not a problem, but the server does not recognize ATOM records that contain non-protein coordinates.</li><li>Multi-Chain PDBs are acceptable, however, the chains should be separated by either TER card or have different chain-IDs to differentiate chains. Continuous residue numbering combined with same chain ID and lack of TER card between two chains might cause jobs to fail.</li><li>If there is a chain break (missing coordinates for residues of a loop, for example), the server will still work. However, such chain breaks need to be indicated in one of these ways: retaining original residue numbering (then the server will detect missing residue numbers, thus accounting for chain break) or by putting a TER card between the residues lining the break, or by using different chain IDs for different parts of the broken chain.</li><li>Some PDB files have a continuous chain but discontinuous residue ids. Such a case would be wrongly identified as a chain break, causing DMD to fail.</li><li>If the HETATM section of the input pdb contains atom types that have not yet been parametrized in DMD or if it contains atoms that are bonded to the protein, your job is likely to fail. Also, bonds between small molecules and protein atoms are not recognized and hence reported as clashes.</li></ol></div>
						<h3><strong><a href="javascript:slide_gtq7.slideIt();slide_gtq7.hideRest();">Why does the server say &#34;Error loading pdb file&#34;?</a></strong></h3>
						<div id="div_gtq7" style="display:none"><p>This error refers to a case where our program encounters an error reading the PDB file. Following are some of the possible reasons.<ol><li>You might have provided a PDB ID that does not exist.</li><li>Your PDB file contains unnatural amino acids. Our current implementation only supports the 20 natural amino acid types: ALA, CYS, ASP, GLU, PHE, GLY, HIS, ILE, LYS, LEU, MET, ASN, PRO, GLN, ARG, SER, THR, VAL, TRP and TYR. Existence of residues other than the listed types in the ATOM records can cause the error.</li><li>Backbone heavy atoms are missing. The program requires coordinates of all backbone N, C and CA atoms for each residue, and will reconstruct other missing atoms from the input pdb file.</li></ol></p></div>
					</div>
					<script type="text/javascript">
						var slide_gsq1 = new animatedDiv('slide_gsq1','div_gsq1',500,null,false,null);
						var slide_gsq2 = new animatedDiv('slide_gsq2','div_gsq2',500,null,false,null);
						var slide_gsq3 = new animatedDiv('slide_gsq3','div_gsq3',500,null,false,null);
						var slide_gsq4 = new animatedDiv('slide_gsq4','div_gsq4',500,null,false,null);
						var slide_gsq5 = new animatedDiv('slide_gsq5','div_gsq5',500,null,false,null);
						var slide_gsq6 = new animatedDiv('slide_gsq6','div_gsq6',500,null,false,null);

						var slide_gtq1 = new animatedDiv('slide_gtq1','div_gtq1',500,null,false,null);
						var slide_gtq2 = new animatedDiv('slide_gtq2','div_gtq2',500,null,false,null);
						var slide_gtq3 = new animatedDiv('slide_gtq3','div_gtq3',500,null,false,null);
						var slide_gtq4 = new animatedDiv('slide_gtq4','div_gtq4',500,null,false,null);
						var slide_gtq6 = new animatedDiv('slide_gtq6','div_gtq6',500,null,false,null);
						var slide_gtq7 = new animatedDiv('slide_gtq7','div_gtq7',500,null,false,null);
					</script>
			</div>
		</div>
</body>
</html>
