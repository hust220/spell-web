/******
var N=1;
var MN = 5; //extra 4 mutant allowed
function moremut(){
	N++;
	if (N<MN) {
		var tr = document.getElementById("mut"+N);
		tr.style.display = "";
		//tr.setAttribute("style","");
		var field = document.getElementById("numberofrows");
		var count = N+1;
		field.value = count;
	}
	if (N == MN) {
		var tr = document.getElementById("moremut");
		tr.style.display = "none";
	}
}

***/

var myPdbHash = "NULL";
var AAs = "ACDEFGHIKLMNPQRSTVWY";
var seq = "noneseq";
var sel = new Array(0);
//var ready = 1; // if seq selection is finished
var site = 0;
var eris_is_ie = true;
var poplayer;
var cx = 0;
var cy = 0;
var step = 0;
//var stepfield = document.getElementById("step");
//var nextfield = document.getElementById("next");
//var mutfield = document.getElementById("mut");
//var mutformfield = document.getElementById("mutform");
//var selectorfield = document.getElementById("selectordiv");
//var seqfield = document.getElementById("seq");
var xhReq = "";

function oniframeload(){
	var errorfield = document.getElementById("error");
	var myFrame = document.getElementById("iframe");
	var seqfield = document.getElementById("seq");
	var nextfield = document.getElementById("next");
	var mutformfield = document.getElementById("mutform");
	var stepfield = document.getElementById("step");
	var selectorfield = document.getElementById("selectordiv");
	var instructionfield = document.getElementById("instruction");
	if (errorfield.value != 0 ) { // error in the input field
	//	myFrame.className="display";
		//selectorfield.innerHTML = "";
		if (poplayer) { poplayer.style.display="none";}
		nextfield.style.display = "none";
		stepfield.innerHTML = "Submit a task - Step 1.";
	}else{ // initial step and other steps
		if ( step == 0) {
			myFrame.className="hidden";
			sel = new Array(seqfield.value.length);
			for (var i=0;i<seqfield.value.length;i++) {sel[i] = -1;}
			nextfield.style.display ="";
			init();
			mutformfield.style.display="";
			selectorfield.style.display="";
			instructionfield.style.display="";
			instructionfield.innerHTML="Click on residue sites and choose mutations.";
			stepfield.innerHTML = "Submit a task - Step 2.";
			seqSel();
			step = 2;
		} else if (step == 2) {
			myFrame.className="hidden";
		}
	}
}
function init(){
	var agt = navigator.userAgent.toLowerCase();
	eris_is_ie = (agt.indexOf("msie")!=-1 && document.all);
	if (eris_is_ie) {
		document.attachEvent("onmousedown", erisCheck);
	} else {
		document.addEventListener("mousedown", erisCheck, true);
	}
	// now moved to html code
/*	poplayer = document.createElement("div");
	poplayer.setAttribute("style","position:absolute;z-index:6000;display:none;background-color:#FFF;filter:Alpha(Opacity=96);border:1px,onset");
	poplayer.setAttribute("id","seqpopup");
	tb = document.createElement("table");
	tr = document.createElement("tr");
	for (var i=0;i<20;i++){
		var td = document.createElement("td");
		td.innerHTML = "<div style='cursor:pointer' onclick='selectM("+i+")'>"+AAs.substr(i,1) +"</div>";
		tr.appendChild(td);
	}
	tb.appendChild(tr);
	poplayer.appendChild(tb);
	document.body.appendChild(poplayer); */
	//div.setAttribute("style","display:hidden");
}

function erisCheck(event){
  if (eris_is_ie) {
      cx = window.event.clientX + document.documentElement.scrollLeft
            + document.body.scrollLeft;
      cy = window.event.clientY + document.documentElement.scrollTop
	      + document.body.scrollTop;
  }else {
      cx = event.clientX + window.scrollX;
      cy = event.clientY + window.scrollY;
  }
}

function seqSel(){
	var hashfield = document.getElementById("hash");
	var seqfield = document.getElementById("seq");
	var obj = document.getElementById("selector");
	var div=document.createElement("tbody");
	obj.appendChild(div);
	seq = seqfield.value;
	myPdbHash = hashfield.value;
	var LL = 20; //number per line
	var pt = 0;  // new line position
	for (var i=0;i<seq.length;i++){
		if ( i%LL == 0 ) {
			pt = i;
			var tr = document.createElement("tr");
			div.appendChild(tr);
			var td = document.createElement("td");
			td.setAttribute("width",25);
			td.innerHTML = "<font color='green'>"+(i+1)+"</font>";
			tr.appendChild(td);
		}
		td = document.createElement("td");
		td.setAttribute("id",i);
		td.setAttribute("width",15);
		tr.appendChild(td);
		
		td.innerHTML = '<div style="cursor:pointer" onClick="selectN('+i+');">'+seq.substr(i,1) +'</div>';
		if ( i%LL == LL-1 || i==seq.length-1) {
			// add second row
			tr = document.createElement("tr");
			div.appendChild(tr);
			var td = document.createElement("td");
			tr.appendChild(td);  // the index row
			td.innerHTML = "-";
			// now the mutant row
			for (var j=0;j<LL;j++) {
				var td = document.createElement("td");
				td.setAttribute("id","m"+(pt+j));
				tr.appendChild(td);
				td.innerHTML = "";
			}
		}
	}
	
	//alert(h);
}

function selectN(id){
	//if (ready == 0) return;
	//ready = 1;
	site = id;
	//popup windows;
	poplayer = document.getElementById("seqpopup");
	poplayer.style.display="";
	poplayer.style.top = cy+"px";
	poplayer.style.left = cx+"px";
}

function selectM(imut){
	var td = document.getElementById(site);
	var tdm = document.getElementById("m"+site);
	//ready = 1;
	if (seq.substr(site,1) == AAs.substr(imut,1) ) {
		tdm.innerHTML = "";
		sel[site] = -1;
	}else{
		tdm.innerHTML = AAs.substr(imut,1);
		sel[site] = imut;
	}
		
	site = 0;
	poplayer = document.getElementById("seqpopup");
	poplayer.style.display="none" ;
	var mutstr = "";
	mutfield = document.getElementById("mut");
        for (var i=0;i<sel.length;i++) {
		if ( sel[i] < 0 ) continue;
		mutstr+=seq.substr(i,1)+(i+1)+AAs.substr(sel[i],1)+" ";
	}
	mutfield.value = mutstr;
	
}
function nextstep(){
	if (step == 2 ) {
		return step3();
	}
	if (step == 3) {
		return step4();
	}
}

function step3(){
//hide all
	var obj = document.getElementById("selectordiv");
	//obj.style.display = "none";
	if (poplayer = document.getElementById("seqpopup")) {
		poplayer.style.display = "none"; 
	}
// change button function

// selection of methods
	
	var h = "<select id='method' name='method'>"
	+"<option value='0'> Fixed Backbone </option>"
	+"<option value='1'> Flexible Backbone </option>"
	+"</select> ";
	
//return results
	//var h2 = '<input name="prerelax" id="prerelax" type="checkbox" style="width:30px" checked>';	
	var h2 = '<input name="prerelax" id="prerelax" type="checkbox" style="width:30px" >';	
	//email notifications?
	var emailConfirmed = document.getElementById("emailConfirmed").value;
	var emailConfirmedText = "";
	if (emailConfirmed == 1) {
		var hemail = '<input name="emailFlag" id="emailFlag" type="checkbox" style="width:30px">';
	}else{
		var hemail = '<input name="emailFlag" id="emailFlag" type="checkbox" style="width:30px" disabled>';
		emailConfirmedText = '<td>For verified email addresses only.</td>';
	}
	var h3 =  "<form><table><tbody><tr><td>Prediction method: </td><td>"+h+"</td></tr>"
			+"<tr><td>Backbone Pre-relaxation: </td><td>"+h2+"</td></tr> "
			+"<tr><td>Email notification: </td><td>"+hemail+"</td>"+emailConfirmedText+"</tr>"
			+"</tbody></table></form>";
	obj.innerHTML  = h3; 
	var mutformfield = document.getElementById("mutform");
	mutformfield.style.display = "none";
	var stepfield = document.getElementById("step");
	stepfield.innerHTML = "Submit a task - Step 3.";
	var instructionfield = document.getElementById("instruction");
	instructionfield.innerHTML = "Select prediction method";
	step = 3;
}
function step4(){
	var method = document.getElementById("method").value;
	var prerelax = document.getElementById("prerelax").checked;
	var emailFlag = document.getElementById("emailFlag").checked;
	var mut = document.getElementById("mut").value;
	
	xhReq = createXMLHttpRequest();	

	var url = "submit2sub.php?method="+method+"&prerelax="+prerelax+"&hash="+myPdbHash+"&mut="+mut+"&emailFlag="+emailFlag;
//	alert(url);
	xhReq.open("get", url, true);
	xhReq.onreadystatechange = onsubmission;
	xhReq.send(null);

	step = 5;
}
function onsubmission(){
	if (xhReq.readyState != 4)  { return; }
	var serverResponse = xhReq.responseText;
	var obj = document.getElementById("selectordiv");
	obj.innerHTML = serverResponse;
	var nextfield = document.getElementById("next");
	nextfield.style.display = "none";
	var instructionfield = document.getElementById("instruction");
	instructionfield.style.display = "none";
}


