var regShow=0;
function register(){
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

