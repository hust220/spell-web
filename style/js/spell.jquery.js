$(document).ready(function() {

var HOST = "http://47.251.1.191";
	
	var tasktype = "####";
	var uploaded = false;
        var uploadedaln = false; 
	var username = $('#username').val();

	$(".swapbtn").click(function() {
		$(this).children(".eye").toggle();
		$("#iclashr").slideToggle();
		return false;
	});

	$('.navi').each(function() {
		$(this).hover(	
			function() {
				$(this).addClass('ui-state-hover')
			}, 
			function() {
				$(this).removeClass('ui-state-hover');
			}
		);
	});

	//Select all checkboxes in the gaia result page
	$('#selallres').click(function() {
		var checked_status = this.checked;
		$("input[name*='sel']").each(function() {
			this.checked=checked_status;
		});
	});
	
	//Disable form submission upon hitting the enter key
	$('#jobform').keypress(function(e) {
		if (e.which == 13) {
			var $targ = $(e.target);
			if (!$targ.is("textarea") && !$targ.is(":button,:submit")) {
				var focusNext = false;
				$(this).find(":input:visible:not([disabled],[readonly]), a").each(function(){
					if (this === e.target) {
						focusNext = true;
					}
					else if (focusNext){
						$(this).focus();
						return false;
					}
				});
				
				return false;
			}
		}
	});

	// The following automatically rescales all images on the page that belong to
	// a particular css class, to a predetermined width and height and yet preserves
	// its aspect ratio. All you need to do is to give the img tag a class name that
	// matches with the class name this function applies to. 
	//
	// Note: The class name represents the rescale width. Height is modified
	// depending on the aspect ratio
	//
	// Tip: You can create any number of classes and appropriately modify this
	// function for different widths of images.
	
	(function($) {
		$.fn.scaleImg = function(options) {
			var imgParams = jQuery.extend({
					height: 9,
					width: 9
				},options);
			
			this.each(function() {
				var h = imgParams.height;
				var w = imgParams.width;
				var image_h = $(this).attr('height');
				var image_w = $(this).attr('width');
				var m_ceil = Math.ceil;
				var m_floor = Math.floor;
	
				if ( image_h >= image_w ) {
					w = m_floor(m_ceil(image_w / image_h * h));
				} else {
					h = m_floor(m_ceil(image_h / image_w * w));
				}
				$(this).attr({'height': h, 'width': w});
			});
		};
	})(jQuery);

	// Chiron Pagination - Used in jobadmin
	$('.pages').hover(
		function() {
			if(!$(this).hasClass('pages-current')) {
				$(this).addClass('pages-hover');
			}
		},
		function() {
			$(this).removeClass('pages-hover');
		}
	);


	$('.pages').click(
		function() {
			var currid = $(this).attr('id');
			var npage  = 0;
			var npages = 0;
			$('.pages').each(function(i) {
				npages++;
			});
			if(currid!="prevpage" && currid!="nextpage") {
				$('.pages').each(function(i) {
					if($(this).attr('id') == currid) {
						$(this).removeClass('pages-hover');
						$(this).addClass('pages-current');
						$('#table'+npage).removeClass('hideEl');
					} else {
						$('#table'+npage).addClass('hideEl');
						if($(this).hasClass('pages-current')) {
							$(this).removeClass('pages-current');
						}
					}
					npage++;
				});
			}
			else {
				if(currid=="prevpage") {
					$('.pages').each(function(i) {
						if($(this).attr('id')!="prevpage" && $(this).attr('id')!="nextpage") {
							if($(this).hasClass('pages-current')) {
								var pno = parseInt($(this).attr('id').split("page")[1]);
								if(pno>1) {
									$('#table'+pno).addClass('hideEl');
								}
								pno--;
								if(pno>0) {
									$('#page'+pno).addClass('pages-current');
									$('#table'+pno).removeClass('hideEl');
									$(this).removeClass('pages-current');
								} else {
									pno=1;
								}
							}
						}
					});
				}
				if(currid=="nextpage") {
					npage = 0;
					$('.pages').each(function(i) {
						var ppg;
						var npg;
						if($(this).attr('id')!="prevpage" && $(this).attr('id')!="nextpage") {
							if(npage>2 && npage<npages-3) { 
								$('#page'+npage).addClass('hideEl');
								$('#sepleft').removeClass('hideEl');
							}
							if(npage>npages-8) {
								$('#sepright').addClass('hideEl');
							}
							if($(this).hasClass('pages-current')) {
								var pno = parseInt($(this).attr('id').split("page")[1]);
								$('#table'+pno).addClass('hideEl');
								pno++;
								ppg = pno-1;
								npg = pno+1;
								$('#page'+pno).removeClass('hideEl');
								$('#page'+ppg).removeClass('hideEl');
								$('#page'+npg).removeClass('hideEl');
								if(pno>npages-2){ 
									pno=npages-2;
								}
								$(this).removeClass('pages-current');
								$('#page'+pno).addClass('pages-current');
								$('#table'+pno).removeClass('hideEl');
								return false;
							}
							npage++;
						}
					});
				}
			}
	/*		if($(this).attr('id')!="prevpage" && $(this).attr('id')!="nextpage") {
				var pno=$(this).attr('id').split("page")[1];
				if(parseInt(pno)>3) {
					
				}
				$(this).addClass('pages-current');
			}*/
		}
	);

	// Delegate action to chiron and gaia submit buttons
	$('#chiron_submit').click(function() {
		tasktype="chiron";
		$('#jobform').submit();
	});

	$('#gaia_submit').click(function() {
		tasktype = "gaia";
		$('#jobform').submit();
	});

        $('#spell_submit').click(function() {
                tasktype="spell";
                $('#jobform').submit();
        });


	// Initialize accordion for chiron and gaia
	$(function() {
		$('#accordion').accordion({
			active: false,
			autoHeight: false,
			collapsible: true
		});
	});

	// Initialize nested accordion for documentation
	$(function() {
		$('#chiron-help').accordion({
			active: false,
			autoHeight: false,
			collapsible: true
		});
	});

	$(function() {
		$('#chiron-stopic').accordion({
			active: false,
			autoHeight: false,
			collapsible: true
		});
	});

	$(function() {
		$('#chiron-ttopic').accordion({
			active: false,
			autoHeight: false,
			collapsible: true
		});
	});

	$(function() {
		$('#gaia-help').accordion({
			active: false,
			autoHeight: false,
			collapsible: true
		});
	});

	$(function() {
		$('#gaia-stopic').accordion({
			active: false,
			autoHeight: false,
			collapsible: true
		});
	});

	$(function() {
		$('#gaia-ttopic').accordion({
			active: false,
			autoHeight: false,
			collapsible: true
		});
	});

	$(function() {
		$('#gaia-otopic').accordion({
			active: false,
			autoHeight: false,
			collapsible: true
		});
	});

	// Initialize tabs for chiron and gaia documentation
	$(function() {
		$('#tabs').tabs();
	});


	// This may be removed temporarily ---- Starts here
	//$('#dialog').dialog('destroy');
	
	//$("#dialog").dialog({ 
	//	bgiframe: true, autoOpen: false, height: 100, modal: true 
	//});
	$('#dialog').dialog({
		bgiframe: true, 
		autoOpen: false,
		width: 500,
		height: 175, 
		modal: true,
		resizable: false,
		buttons: {
			'Ok': function() {
						$(this).dialog('close');
						if($(this).data('stcode')==2) {
							window.location.replace($(this).data('redir'));
						}
					},
			'Cancel': function() {
							$(this).dialog('close');
						}
			}
		});
														// Ends here ----

	// Set display to none for divs in the submit form on processManager
	$('#pdb_mlcs').css("display","none");
	$('#pdb_mlcyn').css("display","none");
	$('#pdb_mlclist').css("display","none");
	$('#file_mlcs').css("display","none");
	$('#file_mlcyn').css("display","none");
	$('#file_mlclist').css("display","none");
	$('#filediv').css("display","none");
	$('#jobstatus').css("display","none");
	$('#clash_content').css("display","none");
	$('#hbshell_content').css("display","none");
	$('#hbcore_content').css("display","none");
	$('#sasa_content').css("display","none");
	$('#void_content').css("display","none");
	$('#geom_content').css("display","none");
	$('#schain_content').css("display","none");
	//$('#submit_to_chiron').css("display","none");
	//$('#submit_to_medusa').css("display","none");
	$('#download_refined').css("display","none");
	$('#hbond_content').css("display","none");
	$('#processing_content').css("display","none");
	$('#processing_shadow').css("display","none");
	$('#ramaplot_content').css("display","none");
	$('#ramaplot_shadow').css("display","none");
	$('#download_ramaplot').css("display","none");
	$('#summary_options').css("display","none");
	$('#fullreport_options').css("display","none");
	$('#session_options').css("display","none");
        $('#filealn').css("display","none");
    //    $('#subwarning').css("display","none");
//        $('#pdbdiv').css("display","none");
        $('#filewarning').css("display","none");
        $('#pdbwarning').css("display","none");
//    $('#filerep').css("display","none");
//    $('#pdbrep').css("display","none");


    $('#rb2').css("display","none");
    $('#rb3').css("display","none");
    $('#rb4').css("display","none");
    $('#rb5').css("display","none");
    $('#rb6').css("display","none");
    $('#rb7').css("display","none");
    $('#rb8').css("display","none");
    $('#rb9').css("display","none");
    $('#rb10').css("display","none");   

	// *******************************************************************
	// Temporary code for debugging
	// *******************************************************************

	// ******************************************************************
	// End temporary code
	// ******************************************************************
	
	// The following code corresponds to the results section of Gaia
	// Each function below deals with changing the properties of different 
	// div tags in results.htm to appropriately display the results.

    $("input[name*='inpaln']").click(function() {
	var thisid = $(this).attr('id');
	if(thisid == "pfam") {
            $('#filealn').slideUp();
        } else {
            $('#filealn').slideDown();      
	}
    });




	$('#clash_header').click(function() {
		$('#clash_header').toggleClass('ui-corner-top ui-helper-reset');
		$('#clash_content').slideToggle('slow',function() {
			$('#clash_header').toggleClass('ui-corner-all');
			$('#clash_content').toggleClass('ui-accordion-content-active');
		});
	});

	$('#hbshell_header').click(function() {
		$('#hbshell_header').toggleClass('ui-corner-top ui-helper-reset');
		$('#hbshell_content').slideToggle('slow',function() {
			$('#hbshell_header').toggleClass('ui-corner-all');
			$('#hbshell_content').toggleClass('ui-accordion-content-active');
		});
	});
	
	$('#hbcore_header').click(function() {
		$('#hbcore_header').toggleClass('ui-corner-top ui-helper-reset');
		$('#hbcore_content').slideToggle('slow',function() {
			$('#hbcore_header').toggleClass('ui-corner-all');
			$('#hbcore_content').toggleClass('ui-accordion-content-active');
		});
	});
	
	$('#sasa_header').click(function() {
		$('#sasa_header').toggleClass('ui-corner-top ui-helper-reset');
		$('#sasa_content').slideToggle('slow',function() {
			$('#sasa_header').toggleClass('ui-corner-all');
			$('#sasa_content').toggleClass('ui-accordion-content-active');
		});
	});
	
	$('#void_header').click(function() {
		$('#void_header').toggleClass('ui-corner-top ui-helper-reset');
		$('#void_content').slideToggle('slow',function() {
			$('#void_header').toggleClass('ui-corner-all');
			$('#void_content').toggleClass('ui-accordion-content-active');
		});
	});
	
	$('#geom_header').click(function() {
		$('#geom_header').toggleClass('ui-corner-top ui-helper-reset');
		$('#geom_content').slideToggle('slow',function() {
			$('#geom_header').toggleClass('ui-corner-all');
			$('#geom_content').toggleClass('ui-accordion-content-active');
		});
	});
	
	$('#schain_header').click(function() {
		$('#schain_header').toggleClass('ui-corner-top ui-helper-reset');
		$('#schain_content').slideToggle('slow',function() {
			$('#schain_header').toggleClass('ui-corner-all');
			$('#schain_content').toggleClass('ui-accordion-content-active');
		});
	});

	$('#generate_ramaplot').click(function() {
		$('#imgformat').attr("src","style/img/pdf.png");
		$('#ramaplot_content').fadeIn('slow',function() {
			$('#ramaplot_shadow').fadeIn();
		});
		generateDownloadableOutput("phipsi");
	});	

	$('#generate_fullreport').click(function() {
		$('#imgformat').attr("src","style/img/pdf.png");
		$('#processing_content').fadeIn('slow',function() {
			$('#processing_shadow').fadeIn();
		});
		generateDownloadableOutput("full");
	});	

	$('#generate_summary').click(function() {
		$('#imgformat').attr("src","style/img/pdf.png");
		$('#processing_content').fadeIn('slow',function() {
			$('#processing_shadow').fadeIn();
		});
		generateDownloadableOutput("summary");
	});	

	$('#generate_session').click(function() {
		$('#imgformat').attr("src","style/img/pse.png");
		$('#processing_content').fadeIn('slow',function() {
			$('#processing_shadow').fadeIn();
		});
		generateDownloadableOutput("session");
	});


	$(function() {
		$(".img550").each(function() {
			$(this).load(function() {
				$(this).scaleImg({height: 550, width:550});
			});
		});
	});
								

	
		/*						$('#step').html("Review Results");
								$('#processing').fadeOut('slow', function() {
									$('#jobstatus').slideDown('slow');
								});
							}
							if(typeof(data.error) != 'undefined' && data.error != "") {
								$('#jobstatus').html("<font color=#990000>"+data.error+"</font>");
								$('#step').html("Error");
								$('#processing').fadeOut('slow',function() {
									$('#jobstatus').slideDown('slow');
								});
							}*/

	// Check whether the pdb id or file upload is chosen on processManager
	$("input[name*='inptype']").click(function() {
		var thisid = $(this).attr('id');

		// If pdbid is chosen, hide file div and show pdb div
		if(thisid == "rpdb") {
			$('#filediv').slideUp();
			$('#pdbdiv').slideDown();
		} else {
			// If file upload is chosen, hide pdb div and show file div
			$('#pdbdiv').slideUp();
			$('#filediv').slideDown();
		}
	});

	// Check if small molecules are to be considered. ---- PDB ----
	$("input[name*='psmlcs']").click(function() {
		var thisid = $(this).attr('id');
		if(thisid == "pysmlcs") {
			$('#pdb_mlclist').slideDown('slow');
		} else {
			$('#pdb_mlclist').slideUp('slow');
		}
	});

	// Check if small molecules are to be considered. ---- FILE ----
	$("input[name*='fsmlcs']").click(function() {
		var thisid = $(this).attr('id');
		if(thisid == "fysmlcs") {
			$('#file_mlclist').slideDown('slow');
		} else {
			$('#file_mlclist').slideUp('slow');
		}
	});

	// Start checking when the user starts entering the pdb id
	$('#pdbid').keyup(checkPDB);

	// If the length of the pdb id is 4, attempt to download the pdb
	// Check the downloaded pdb for small molecules and populate the
	// pdb_mlclist with the list of small molecules in the pdb file.
	function checkPDB() {
		var pdbid='####';
		if($('#pdbid').val().length==4) {
			if($('#pdbid').val() != pdbid) {
				$('#pdb_mlclist').empty();
				pdbid = $('#pdbid').val();
				$('#pdbid').blur();
				$('#chiron_submit').fadeOut('slow');
				$('#gaia_submit').fadeOut('slow');
                                $('#spell_submit').fadeOut('slow');
				$('#verifying').fadeIn('slow');
				var indata = "pdbid="+$('#pdbid').val();
				$.ajax ({
					type: "POST",
					url: HOST + "/spell/checkpdb.php",
					secureuri: false,
					dataType: "json",
					data: indata,
					success: function(data, status) {
									$('#verifying').fadeOut('slow');
									if(typeof(data.error) != 'undefined') {
										if(data.error != '') {
											$('#pdbinfo').html("<font color=red>"+data.error+"</font>");
										} else {
											$('#chiron_submit').fadeIn('slow');
											$('#gaia_submit').fadeIn('slow');
                                                                                        $('#spell_submit').fadeIn('slow');
											$('#pdbinfo').html(data.msg);
											if(data.smlc == 'yes') {
												$('#pdb_mlcyn').fadeIn('slow');
												var mlcs = data.mlclist.split(":");
												var cbx = "List of small molecules</br><small>Note 1: Not all atom types are supported. Please review documentation</small></br><small>Note 2: Please do not include small molecules bonded to the protein</small></br>";
												for(var i=1; i<=mlcs.length; i++) {
													if(mlcs[i-1]!="") {
														cbx += '<label><input class="chkbox" name="pmlcs[]" type="checkbox" id="pmlc'+i+'" value="'+mlcs[i-1]+'" checked>'+mlcs[i-1]+"</label></br>";
													}
												}
												$('#pdb_mlclist').append(cbx);
												$('#pdb_mlcs').fadeIn('slow');
												//$('#mlclist').html(data.mlclist);
											} else {
												$('#pdb_mlcyn').fadeOut('slow');
											}

										        var report = "";
										        if(data.nchn != ''){
                                                                                           report += "<br>WARNING! The structure file contains multiple chains. Only the " +
											   "first chain (chain " + data.nchn + ") will be processed. <br>";
										        }
										        if(data.missing != ''){
                                                                                           report += "<br> <font> WARNING! The structure contains missing residues. " +
                                                                                           "You can continue with a submission, however missing regions will not be " +
                                                                                           "analyzed for potential split sites. The list of missing residues: <b>" +
											   data.missing + " </b> </font> <br> </div>";
										        }
										       if(data.nchn != '' || data.missing != ''){
											$('#pdbrep').html(report);
										       }

										}
									}
								},
					error: function(data, status, e) {
								$('#chiron_submit').fadeIn('slow');
								$('#gaia_submit').fadeIn('slow');
                                                                $('#spell_submit').fadeIn('slow');
								$('#verifying').fadeOut('slow');
							},	
					complete: function(data, status) {
									$('#verifying').fadeOut('slow');
								}
				});
			}
		}
	}
	
	// If the file field changes, i.e. if a new file is chosen, prepare to upload the file automatically
	$('#file').change(prepareUpload);

	// This function may not be required. Just a descriptive little function to call ajaxFileUpload.
	function prepareUpload() {
		uploaded = false;
		ajaxFileUpload();
	}

	// Actual ajax file upload function
	function ajaxFileUpload() {
		$('#chiron_submit').fadeOut('slow');
		$('#gaia_submit').fadeOut('slow');
                $('#spell_submit').fadeOut('slow');
 		$('#uploading').fadeIn('slow');
 		$.ajaxFileUpload ({
			url: HOST + '/spell/doajaxfileupload.php',
			secureuri: false,
			fileElementId: 'file',
			dataType: 'json',
			success: function(data, status) {
							$('#chiron_submit').fadeIn('slow');
							$('#gaia_submit').fadeIn('slow');
                                                        $('#spell_submit').fadeIn('slow');
							$('#uploading').fadeOut('slow');
							$('#file_mlclist').empty();
							if(typeof(data.error) != 'undefined') {
								if(data.error != '') {
									$('#fileinfo').html("<font color=red>"+data.error+"</font>");
								} else {
									uploaded = true;
									$('#fileinfo').html(data.msg);
									if(data.smlc == 'yes') {
										$('#file_mlcyn').fadeIn('slow');
										var mlcs = data.mlclist.split(":");
										var cbx = "List of small molecules</br><small>Note 1: Not all atom types are supported. Please review documentation</small></br><small>Note 2: Please do not include small molecules bonded to the protein</small></br>";
										for(var i=1; i<mlcs.length; i++) {
											if(mlcs[i-1]!="") {
												cbx += '<label><input class="chkbox" name="fmlcs[]" type="checkbox" id="fmlc'+i+'" value="'+mlcs[i-1]+'" checked>'+mlcs[i-1]+"</label></br>";
											}
										}
										$('#file_mlclist').append(cbx);
										$('#file_mlcs').fadeIn('slow');
									} else {
										$('#file_mlcyn').fadeOut('slow');
									}

                                                                        var report = "";
                                                                        if(data.nchn != ''){ 
                                                                            report += "<br>WARNING! The structure file contains multiple chains. Only the first " +   
                                                                              "chain (chain " + data.nchn + ") will be processed. <br>";
								        }
                                                                        if(data.missing != ''){
                                                                            report += "<br> <font> WARNING! The structure contains missing residues. " +              
                                                                            "You can continue with a submission, however missing regions will not be analyzed for " +
                                                                            "potential split sites. The list of missing residues: <b>" +
                                                                            data.missing + " </b> </font> <br> </div>";
									}
                                                                        if(data.nchn != '' || data.missing != ''){
                                                                            $('#filerep').html(report);
//                                                                            $('#filewarning').fadeIn('slow'); 
								        }

								}
							}
							$('#file').change(prepareUpload);
						},
			error: function(data, status, e) {
						alert(data.responseText);
						alert(e+"test");
						$('#chiron_submit').fadeIn('slow');
						$('#gaia_submit').fadeIn('slow');
                                                $('#spell_submit').fadeIn('slow');
						$('#uploading').fadeOut('slow');
					}
		});

	}

        $('#alnfile').change(alnFileUpload);


        function alnFileUpload() {

                $('#spell_submit').fadeOut('slow');
                $('#uploadingaln').fadeIn('slow');
                $.ajaxFileUpload ({
                    url: HOST + '/spell/alnfileupload.php',
                    secureuri: false,
                    fileElementId: 'alnfile',
                    dataType: 'json',
                    success: function(data, status) {
			$('#spell_submit').fadeIn('slow');
                        $('#uploadingaln').fadeOut('slow');
                        if(typeof(data.error) != 'undefined') {
                            if(data.error != '') {
                                $('#alninfo').html("<font color=red>"+data.error+"</font>");
			    } else {
				uploadedaln = true;
                                $('#alninfo').html(data.msg);
			    }
			}
                    },
                    error: function(data, status, e) {
                    }
                });

	}


	
	function validateSubmit() {
		var jobtitle = $('input#title').val();									// Optional
		var chkradio = $("input[name='inptype']:checked").attr('id');	// Input type - PDB ID or File
                var chkradioaln = $("input[name='inpaln']:checked").attr('id');
		var inptype;																	// Variable to hold input type
																							// Possible values : rpdb, rfile
		var mlcyn = "0";																// Consider molecules or not?
		var mopt;																		// Get selected option for small molecules
		var selectedMols;																// Get list of checked boxes
		var molstr="";																	// String representation of small molecules
		if(chkradio == "rpdb") {													// Set input type based on the
			inptype = "rpdb";															// selected radio button
		} else if(chkradio == "rfile") {
			inptype = "rfile";
		}
		var pdbid = $('input#pdbid').val();										// Retrieve PDB ID
		//var pdbfile = $("input[type='file']").val();							// Retrieve PDB File
		if(pdbid.length < 4 && chkradio == "rpdb") {
			alert('Please enter a four-letter PDB ID');
			$('#pdbid').focus();
			return false;
		}
		if(chkradio == "rfile" && !uploaded) {
			alert('No input structure file was provided. Please choose a file to upload');
			return false;
		}
                
                if(chkradioaln == "afile" && !uploadedaln){
                    alert('No alignment file was provided. Please choose a file to upload');
                    return false;
	        }

		var notify = ""																// Check if user notification
																							// upon job completion is requested
		//var guestEmail="";															// If notification is requested and if the
																							// user is a guest, email id is required
		//if(username=="guest") {
			/*if(jQuery.trim($('#guestEmail').val()).length == 0) {
				alert('Please enter a valid email id');
				return false;
			}
			guestEmail = jQuery.trim($('#guestEmail').val());
			notify = "on";*/
		//} else {
			notify = $('#notify').attr('checked'); 
		//}
		var constr = $('#constrain').attr('checked');							// Check if sidechain constraints
																							// are requested
		if(inptype == "rpdb") {
			mopt = $("input[name='psmlcs']:checked").attr('id');
			if(mopt == "pysmlcs") {
				mlcyn = "1";
				selectedMols = new Array();
				$("input[name='pmlcs[]']:checked").each(function() {
					selectedMols.push($(this).val());
				});
				if(selectedMols.length == 0) {
					alert('Please select at least one small molecule to continue');
				} else {
					molstr = selectedMols.join(':');
					formdata += '&molstr='+molstr;
				}
			}
		} else {
			mopt = $("input[name='fsmlcs']:checked").attr('id');
			if(mopt == "fysmlcs") {
				mlcyn = "1";
				selectedMols = new Array();
				$("input[name='fmlcs[]']:checked").each(function() {
					selectedMols.push($(this).val());
				});
				if(selectedMols.length == 0) {
					alert('Please select at least one small molecule to continue');
				} else {
					molstr = selectedMols.join(':');
					formdata += '&molstr='+molstr;
				}
			}
		}
		var formdata = 'title='+jobtitle;
		if(inptype == "rpdb") {
			formdata += '&pdbid='+pdbid;
		}
		//formdata += '&inptype='+inptype;


		(notify === true) ? notify = 1 : notify = 0;
		(constr === true) ? constr = 1 : constr = 0;
		formdata += '&notify='+notify+'&constrain='+constr+'&mlcs='+mlcyn+'&mlclist='+molstr+'&aln='+chkradioaln;
		//if(username=="guest") {
			//formdata += '&guestEmail='+guestEmail;
		//}
		return formdata;
	}

	function execDBA(formdata) {
		$(this).ajaxStart(function() {
			$('#chiron_submit').fadeOut();
			$('#gaia_submit').fadeOut();
                        $('#spell_submit').fadeOut();
		}).ajaxComplete(function() {
			$('#chiron_submit').fadeIn();
			$('#gaia_submit').fadeIn();
                        $('#spell_submit').fadeIn();
		});
		$.ajax ({
			type: "POST",
			url: "dbAssistant.php",
			data: formdata,
			dataType: 'json',
			success: function(data,status) {
							if(typeof(data.error)!='undefined') {
								if(data.error != '') {
									$('#jobstatus').html("<font color=red>"+data.error+"</font>");
								}
							} 
							if(typeof(data.jobstatus) != 'undefined') {
								if(data.jobstatus == '0') {
									$('#dialog').data('msg','The PDB you requested is already in queue. Please copy the following URL to check the results later');
									$('#dialog').data('url','showresults.php?jobid='+data.jobid+'&authKey='+data.authKey+'&minimize='+data.minimize);
									$('#dialog').data('stcode','0');
									$('#dialog').html($('#dialog').data('msg')+'<br>'+$('#dialog').data('url'));
									$('#dialog').dialog('open');
								} else if(data.jobstatus == '1') {
									$('#dialog').data('msg','The PDB you requested is currently being processed. Please copy the following URL to check the results later');
									$('#dialog').data('url','showresults.php?jobid='+data.jobid+'&authKey='+data.authKey+'&minimize='+data.minimize);
									$('#dialog').data('stcode','1');
									$('#dialog').html($('#dialog').data('msg')+'<br>'+$('#dialog').data('url'));
									$('#dialog').dialog('open');
								} else if(data.jobstatus == '2') {
									$('#dialog').data('msg','The PDB you requested has already been processed. Do you want to view the results now?');
									$('#dialog').data('url','');
									$('#dialog').data('redir', 'showresults.php?jobid='+data.jobid+'&authKey='+data.authKey+'&minimize='+data.minimize);
									$('#dialog').data('stcode','2');
									$('#dialog').html($('#dialog').data('msg')+'<br>'+$('#dialog').data('url'));
									$('#dialog').dialog('open');
								} else if(data.jobstatus == '3') {
									$('#dialog').data('msg', 'The PDB you requested was submitted earlier and could not be processed due to inherent problems. Please see documentation for more details');
									$('#dialog').data('url','');
									$('#dialog').data('stcode','3');
									$('#dialog').html($('#dialog').data('msg')+'<br>'+$('#dialog').data('url'));
									$('#dialog').dialog('open');
								}
							}
							if(typeof(data.jobexists)=='undefined' && typeof(data.jobid)!='undefined') {
                                                            username = "guest";
								if(data.jobid != '') {
									jobstatusstr = "<br>Your job with id: "+data.jobid+" has been submitted to the server. ";
									if(username == "guest") {
										jobstatusstr += "Please copy the following URL to check for your results periodically.</br></br>";
										var minimize = 1;
										if(tasktype == "gaia") {
											minimize = 0;
										}
										jobstatusstr += '<a href="'+HOST+'/spell/showresults.php?jobid='+data.jobid+'&authKey='+data.authKey+'" target="_blank">'+HOST+'/spell/showresults.php?jobid='+data.jobid+'&authKey='+data.authKey+'</a></br></br>';
									} else {
										jobstatusstr += "Please check back later for the results</br></br>";
									}
									jobstatusstr += "Thank you for using SPELL !";
									$('#jobstatus').html(jobstatusstr);
									//$("#"+data.elid).html(data.msg);
								}
//								$('#submitform').slideUp('slow');
//								$('#task').slideUp('slow',function() {
                                                            $('#submitform').slideUp('slow',function() {  
									$('#step').html("Submission Complete");
									$('#jobstatus').fadeIn('slow');
								});


							}
						},
			error: function(data, status, e) {
						if(typeof(data.error) != 'undefined') {
							alert(data.error);
						}
						alert(status);
						alert(e);
					},
			complete: function(data,status) {
						}
		});
	}

	// Handle actual form submission
	$('#jobform').submit(function() {
		// Get field values. Form submission is handled differently depending on 
		// whether the user provides a pdb id or uploads a file. Small molecules
		// are considered according to the input selection (pdbid or file upload)
		var formdata = validateSubmit();
		if(formdata) { 
			if(tasktype == "chiron") {
				formdata += "&queue=chiron";
				//processChironSubmission(formdata); // Old code - see execDBA() for new code
			} else if(tasktype == "gaia") {
				formdata += "&queue=gaia";
				//processGaiaSubmission(formdata); // Old code - see execDBA() for new code
			}
                        if(tasktype == "spell") {
                            formdata += "&queue=spell";
   		        }
			execDBA(formdata);
		}
		return false;
	});

	function generateDownloadableOutput(outputType) {
	//$('#submitform').slideUp('slow');
		var dataInput = "function=generateReport&jobid="+$("input[name='jobid']").val()+"&rtype="+outputType+"&table=gaia_results";
		var dbField   = '';
		var filextn   = '';
		if(outputType=="summary") {
			dbField = "summary";
			filextn = "tex";
		} else if(outputType=="full") {
			dbField = "report";
			filextn = "tex";
		} else if(outputType=="session") {
			dbField = "pym";
			filextn = "py";
		} else if(outputType=="phipsi") {
			dbField = "phipsipdf";
			filextn = "pdf";
		}
		dataInput +="&field="+dbField+"&fextn="+filextn+"";
		$.ajax( {
			type:    "POST"
		, url:      HOST+"/chiron/ajaxClient.php"
		, data:     dataInput
		, dataType: "json"
		, success:	function(data, status) {
				alert(data.msg);
				if(typeof(data.msg)!='undefined' && data.msg=="success") {
					var prefix = "exec/"+data.jobdir+"/"+data.jobdir;
					if(outputType == "summary") {
						$('#summary_view').attr("href",prefix+"-summary.pdf");
						$('#summary_view').attr("target","_blank");
						//$('#summary_download').click(function() {
						//	forceDownload(prefix+"-summary.pdf");
						//});
						$('#summary_download').attr('href',HOST+'/chiron/forcedownload.php?fname='+prefix+'-summary.pdf');
						$('#summary_options').fadeIn();
					} else if(outputType == "full") {
						$('#full_view').attr("href",prefix+"-full.pdf");
						$('#full_view').attr("target","_blank");
						//$('#full_download').click(function() {
						//	forceDownload(prefix+"-summary.pdf");
						//});
						$('#full_download').attr('href',HOST+'/chiron/forcedownload.php?fname='+prefix+'-full.pdf');
						$('#fullreport_options').fadeIn();
					} else if(outputType == "session") {
						//$('#session_download').click(function() {
						//	forceDownload(prefix+"-session.py");
						//});
						$('#session_download').attr('href',HOST+'/chiron/forcedownload.php?fname='+prefix+'-session.py');
						$('#session_options').fadeIn();
					} else if(outputType == "phipsi") {
						$('#download_ramaplot').fadeIn('slow', function() {
							$('#generate_ramaplot').fadeOut('slow');
						});
						$('#download_ramaplot').click(function() {
							window.location.replace(HOST+'/chiron/forcedownload.php?fname='+prefix+'-phipsi.pdf');
						});
					}
				}
			}
		, error:    function(data,status,e) {
				alert(e);
			}
		, complete: function(data,status) {
				if(outputType == "phipsi") {
					$('#ramaplot_shadow').fadeOut('slow',function() {
						$('#ramaplot_content').fadeOut();
					});
				} else {
					$('#processing_shadow').fadeOut('slow',function() {
						$('#processing_content').fadeOut();
					});
				}
			}
		} );
	}

	function forceDownload(fname) {
		$.ajax({
		  type:     "POST"
		, url :     HOST+"/chiron/forcedownload.php"
		, data:     "fname="+fname
		, dataType: "JSON"
		, success:  function(data, status) {
				
			}
		, error:    function(data, status, e) {
			}
		});
	}

	$('#refine_sc').click(function() {
		var chkstr = '';
		var check_flag = 0;
		$("input[name*='sel']").each(function() {
			var checked_status = this.checked;
			if(checked_status) {
				check_flag = 1;
				chkstr += $(this).attr('name').split('_')[1]+"_";
			}
		});
		if(!check_flag) {
			alert('Please choose at least one residue to refine');
			return;
		}
		chkstr = chkstr.replace(/_$/,"");
		var prefixstr = $("input[name='pdbid']").val()+"-"+$("input[name='jobid']").val();
		$('#dialog-results').dialog('destroy');
		$('#dialog-results').html("<img src='style/img/running.gif' width=15 height=15 border=0>Please be patient while the side chains are fixed. Alternately, you can copy the following link to access the pdb file when it is ready.<br>"+HOST+"/chiron/forcedownload.php?fname=exec/"+prefixstr+"/"+prefixstr+"-fixed.pdb");
		$('#dialog-results').dialog({
			modal: true,
			width: 450,
			autoOpen: false
		});
		$('#dialog-results').dialog('open');
		$.ajax({
			type: "POST",
			url: HOST+"/chiron/ajaxClient.php",
			data: "function=fixSideChains&jobid="+$("input[name='jobid']").val()+"&resstr="+chkstr,
			dataType: "json",
			success: function(data,status) {
					if(typeof(data.msg)!='undefined') {
						$('#dialog-results').dialog('close');
						$('#download_refined').fadeIn('slow',function() {
							$('#refine_sc').fadeOut('slow');
						});
						$('#download_refined').click(function() {
							window.location.replace(HOST+'/chiron/forcedownload.php?fname='+data.outfile);
						});
					}
					if(typeof(data.error)!='undefined') {
						$('#dialog-results').dialog('close');
						$('#dialog-results').dialog('destroy');
						$('#dialog-results').html("There was a fatal error while fixing side chains. Please contact the administrator with a description of your job.");
						$('#dialog-results').dialog({
							modal: true,
							width: 450,
							buttons: {
								Ok: function() {
									$(this).dialog('close');
								}
							}
						});
					}
				},
			error: function(data,status,e) {
					$('#dialog-results').dialog('close');
					$('#dialog-results').dialog('destroy');
					$('#dialog-results').html("There was a fatal error while fixing side chains. Please contact the administrator with a description of your job.");
					$('#dialog-results').dialog({
						modal: true,
						width: 450,
						buttons: {
							Ok: function() {
								$(this).dialog('close');
							}
						}
					});
				}
		});
	});

	$('#remove_clashes').click(function() {
		$.ajax ({
			type: "POST",
			url: HOST+"/chiron/ajaxClient.php",
			data: "function=submitToChiron&jobid="+$("input[name='jobid']").val(),
			dataType: "json",
			success: function(data, status) {
					if(typeof(data.msg) != 'undefined') {
						$('#dialog-results').dialog('destroy');
						$('#dialog-results').html("Your job has been submitted for clash removal. Please check your home page to monitor the job.");
						$('#dialog-results').dialog({
							modal: true,
							width: 450,
							buttons: {
								Ok: function() {
									$(this).dialog('close');
								}
							}
						});
					}
				},
			error: function(data, status, e) {
				}
		});
			
	});

});

        // Check whether the pdb id or file upload is chosen on processManager                                                                                          
	
// The following is deprecated code. Original usage was as follows:
// Chiron submission was handled by processChironSubmission(formdata) where the form 
// data is processed by chiron.php and jobs were submitted to the database and an ajax
// response posted back to the browser. Currently this process is handled by
// dbAssistant.php. Similarly, Gaia used to keep users waiting while the job is processed
// and the results were updated on the browser by replacing content of appropriate div
// tags in results.htm. The function for this process is processGaiaSubmission(formdata)
// and is given below. Now, both functions are merged into one and is called execDBA(formdata).
// The two servers are streamlined into one and all database requests are handled by 
// dbAssistant.php. The deprecated php code can be found in the directory named 'deprecated' 
// in the root directory of the web server.
	/*function processChironSubmission(formdata) {
		$.ajax ({
			type: "POST",
			url: "chiron.php",
			data: formdata,
			dataType: 'json',
			success: function(data,status) {
							if(typeof(data.error)!='undefined') {
								if(data.error != '') {
									//alert('error:'+data.elid);
									//$("#"+data.elid).html("<font color=red>"+data.error+"</font>");
									$('#jobstatus').html("<font color=red>"+data.error+"</font>");
								}
							}
							if(typeof(data.jobid)!='undefined') {
								if(data.jobid != '') {
									//alert('success:'+data.elid);
									jobstatusstr = "<br>Your job with id: "+data.jobid+" has been submitted to the server. ";
									if(username == "guest") {
										jobstatusstr += "Please copy the following URL to check for your results periodically.</br></br>";
										jobstatusstr += "http://redshift.med.unc.edu/chiron/showresults.php?jobid="+data.jobid+"&authKey="+data.authKey+"</br></br>";
									} else {
										jobstatusstr += "Please check back later for the results</br></br>";
									}
									jobstatusstr += "Thank you for using Chiron !";
									$('#jobstatus').html(jobstatusstr);
									//$("#"+data.elid).html(data.msg);
								}
							}
							//$('#step').html("Review Results");
							//$('#results').html('<img src="exec/'+data.jobdir+'/'+data.jobdir+'.clash.jpeg">');
							$('#submitform').slideUp('slow');
							$('#task').slideUp('slow',function() {
								$('#step').html("Submission Complete");
								$('#jobstatus').fadeIn('slow');
							});
							$('#dialog').dialog('close');
						},
			error: function(data, status, e) {
						if(typeof(data.error) != 'undefined') {
							alert(data.error);
						}
						alert(status);
						alert(e);
					}
		});
	}

	function processGaiaSubmission(formdata) {
		//$('#dialog')
		//.ajaxStart(function() {
		//	$('#dialog').dialog('open');
		//})
		//.ajaxComplete(function() {
		//	$('#dialog').dialog('close');
		//});
		//$('#dialog').html('<img width=15 height=15 src=style/img/running.gif>'+
		//	'&nbsp;&nbsp;Please wait while your job is being processed...</br>Please do not refresh this page !');
		$('#submitform').slideUp('slow');
		$('#processing').html('<img width=15 height=15 src=style/img/running.gif>'+
			'&nbsp;&nbsp;Please wait while your job is being processed... Please do not refresh this page !');
		$('#task').slideUp('slow',function() {
			$('#step').html("Progress");
			$('#processing').fadeIn('slow');
		});

		$.ajax ({
			type: "POST",
			url: "gaia.php",
			data: formdata,
			dataType: 'json',
			success: function(data, status) {
							if(typeof(data.msg) != 'undefined') {
								var clashinfo = "Clash score for the input structure : "+data.clashscore+"<br>";
								clashinfo += '<img class="img550" src="exec/'+data.job+'/'+data.job+'-clash.jpeg">';
								$('#clash_content_main').html(clashinfo);
								$('#clash_header').click(function() {
									$('#clash_header').toggleClass('ui-corner-top ui-helper-reset');
									$('#clash_content').slideToggle('slow',function() {
										$('#clash_header').toggleClass('ui-corner-all');
										$('#clash_content').toggleClass('ui-accordion-content-active');
									});
								});
								if(parseFloat(data.clashscore) > 0.02) {
									$('#submit_to_chiron').css("display","block");
									$('#clash_header').removeClass('ui-state-highlight');
									$('#clash_header').addClass('ui-state-error');
									$('#clash_status').removeClass('ui-icon-info');
									$('#clash_status').addClass('ui-icon-alert');
								}
	
								var hbshellinfo = "Number of unsatisfied hydrogen bonds in the shell : "+data.shell_unsatisfied+"<br>";
								hbshellinfo += '<img class="img550" src="exec/'+data.job+'/'+data.job+'-hbond-shell.jpeg">';
								$('#hbshell_content').html(hbshellinfo);
								$('#hbshell_header').click(function() {
									$('#hbshell_header').toggleClass('ui-corner-top ui-helper-reset');
									$('#hbshell_content').slideToggle('slow',function() {
										$('#hbshell_header').toggleClass('ui-corner-all');
										$('#hbshell_content').toggleClass('ui-accordion-content-active');
									});
								});
	
								var hbcoreinfo = "Number of unsatisfied hydrogen bonds in the core : "+data.unsatisfied+"<br>";
								hbcoreinfo += '<img class="img550" src="exec/'+data.job+'/'+data.job+'-hbond-buried.jpeg">';
								$('#hbcore_content').html(hbcoreinfo);
								$('#hbcore_header').click(function() {
									$('#hbcore_header').toggleClass('ui-corner-top ui-helper-reset');
									$('#hbcore_content').slideToggle('slow',function() {
										$('#hbcore_header').toggleClass('ui-corner-all');
										$('#hbcore_content').toggleClass('ui-accordion-content-active');
									});
								});
	
								var sasainfo = "Total solvent accessible surface area : "+data.sasa+"<br>";
								sasainfo += '<img class="img550" src="exec/'+data.job+'/'+data.job+'-sasa.jpeg">';
								$('#sasa_content').html(sasainfo);
								$('#sasa_header').click(function() {
									$('#sasa_header').toggleClass('ui-corner-top ui-helper-reset');
									$('#sasa_content').slideToggle('slow',function() {
										$('#sasa_header').toggleClass('ui-corner-all');
										$('#sasa_content').toggleClass('ui-accordion-content-active');
									});
								});
	
								var voidinfo = "Total void volume : "+data.voidvolume+"<br>";
								voidinfo += '<img class="img550" src="exec/'+data.job+'/'+data.job+'-voids.jpeg">';
								$('#void_content').html(voidinfo);
								$('#void_header').click(function() {
									$('#void_header').toggleClass('ui-corner-top ui-helper-reset');
									$('#void_content').slideToggle('slow',function() {
										$('#void_header').toggleClass('ui-corner-all');
										$('#void_content').toggleClass('ui-accordion-content-active');
									});
								});
	
								//$('#submitform').slideUp('slow');
								$(function() {
									$(".img550").each(function() {
										$(this).load(function() {
											$(this).scaleImg({height: 550, width:550});
										});
									});
								});
								
								$('#generate_fullreport').click(function() {
									$('#imgformat').attr("src","style/img/pdf.png");
									if(!generated_report) {
										$('#processing_content').fadeIn('slow',function() {
											$('#processing_shadow').fadeIn('slow');
										});
										generateDownloadableOutput(data.job,"full");
									}
								});	

								$('#generate_summary').click(function() {
									$('#imgformat').attr("src","style/img/pdf.png");
									if(!generated_summary) {
										$('#processing_content').fadeIn('slow',function() {
											$('#processing_shadow').fadeIn('slow');
										});
										generateDownloadableOutput(data.job,"summary");
									}
								});	

								$('#generate_session').click(function() {
									$('#imgformat').attr("src","style/img/pse.png");
									$('#processing_content').fadeIn('slow',function() {
										$('#processing_shadow').fadeIn('slow');
									});
									generateDownloadableOutput(data.job,"session");
								});


								$('#step').html("Review Results");
								$('#processing').fadeOut('slow', function() {
									$('#jobstatus').slideDown('slow');
								});
							}
							if(typeof(data.error) != 'undefined' && data.error != "") {
								$('#jobstatus').html("<font color=#990000>"+data.error+"</font>");
								$('#step').html("Error");
								$('#processing').fadeOut('slow',function() {
									$('#jobstatus').slideDown('slow');
								});
							}
						},
			error: function(data, status, e) {
						if(typeof(data.error) != 'undefined') {
							$('#jobstatus').html("<font color=red>"+data.error+"</font>");
							$('#step').html("Error");
							$('#processing').fadeOut('slow',function() {
								$('#jobstatus').slideDown('slow');
							});
						}
					}
		});
	}*/
