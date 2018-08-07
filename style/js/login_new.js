var regShow=0;
function register(){
		GB_show("close","terms.html");
		var tr = document.getElementById("frmRegister");
		if (regShow == 0) {
			tr.style.display = "";
			regShow = 1;
		}else{
			tr.style.display = "none";
			regShow = 0;
		}

}
var lostShow=0;
function lostPassword(){
		var tr = document.getElementById("frmLostPassword");
		if (lostShow == 0) {
			tr.style.display = "";
			lostShow = 1;
		}else{
			tr.style.display = "none";
			lostShow = 0;
		}

}
var flagAgree=0;
function termagree(){
	flagAgree = 1;
	var div=document.getElementById("terms");
	div.parentNode.removeChild(div);
//	div.style.display="none";
	register();
}

function termdisagree(){
	flagAgree = 0;
	var div=document.getElementById("terms");
	div.parentNode.removeChild(div);
	//delete div;
	history.go(-1);
	
}
