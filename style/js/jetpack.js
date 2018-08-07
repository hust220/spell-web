var timerlen = 5;
var slideObjs = new Array();
var son = 0;
var expImg = "style/img/view.png";
var colImg = "style/img/hide.png";

function animatedDiv(objName, divID, animLen, imgID, imgFlag, parObj) {
	this.name = objName;
	this.divID = divID;
	this.animLen = animLen;
	this.imgID = imgID;
	this.imgFlag = imgFlag;
	this.parObj = parObj;
	this.divObj = document.getElementById(divID);
	if(!this.divObj.style.overflow) this.divObj.style.overflow = "hidden";
	slideObjs[son] = this;
	son++;
}

animatedDiv.prototype.slideIt=function() {
	(this.divObj.style.display)?this.origDisp = this.divObj.style.display:this.origDisp = "none";
	if(this.origDisp == "none") {
		this.divObj.style.opacity = 0.01;
		this.divObj.style.filter = "alpha(opacity=1)";
		this.divObj.style.display = '';
	}
	this.divHeight = this.divObj.offsetHeight;
	this.divObj.style.height = this.divHeight;
	this.divObj.style.display = this.origDisp;
	this.divObj.style.filter = "alpha(opacity=100)";
	this.divObj.style.opacity = 1;
	//this.hideRest();
	if(this.origDisp == "none") {
		if(this.imgFlag) document.getElementById(this.imgID).src=colImg;
		this.slideDown();
	} else {
		if(this.imgFlag) document.getElementById(this.imgID).src=expImg;
		this.slideUp();
	}
}

animatedDiv.prototype.slideDown=function() {
	if(this.sliding) return; //do not start moving an already moving object
	if(this.origDisp!="none") return; //do not move a visible object - sanity check
	this.sliding = true;
	this.direction = "down";
	this.startSlide();
}

animatedDiv.prototype.slideUp=function() {
	if(this.sliding) return; //do not start moving an already moving object
	if(this.origDisp == "none") return; //do not move a hidden object - sanity check
	this.sliding = true;
	this.direction = "up";
	this.startSlide();
}

animatedDiv.prototype.startSlide=function() {
	var pobj=null;
	if(this.parObj) {
		for(i=0;i<slideObjs.length;i++) {
			if(slideObjs[i].name==this.parObj) {
				this.parHeight = slideObjs[i].divHeight;
				if(this.direction == "down") slideObjs[i].divHeight += this.divHeight;
				if(this.direction == "up") slideObjs[i].divHeight -= this.divHeight;
				pobj = slideObjs[i];
			}
		}
	}
	var instance = this;
	this.startTime = (new Date()).getTime();
	if(this.direction == "down" ) {
		this.divObj.style.height = "1px";
	}
	this.divObj.style.display = "block";
	if(pobj) {
		this.runTimer = setInterval(function() { instance.slideTick(pobj); },timerlen);
	} else {
		this.runTimer = setInterval(function() { instance.slideTick(null); },timerlen);
	}
}

animatedDiv.prototype.slideTick=function(pobj) {
	var elapsed = (new Date()).getTime()-this.startTime;
	if(elapsed > this.animLen) 
		this.endSlide();
	else {
		var dt = Math.round(elapsed / this.animLen * this.divHeight);
		if(this.direction == "up") dt = this.divHeight - dt;
		this.divObj.style.height = dt + "px";
		if(pobj) {
			var pdt = Math.round(elapsed / this.animLen * this.divHeight);
			if(this.direction == "up") pdt = this.parHeight - pdt;
			if(this.direction == "down") pdt = this.parHeight + pdt;
			pobj.divObj.style.height = pdt + "px";
		}
	}
	return;
}

animatedDiv.prototype.endSlide=function() {
	clearInterval(this.runTimer);
	if(this.direction == "up") this.divObj.style.display = "none";
	this.divObj.style.height = this.divHeight + "px";
	this.sliding=false;
	this.direction='';
	this.startTime='';
	this.runTimer='';
	return;
}

animatedDiv.prototype.hideRest=function() {
  	for(var i=0;i<slideObjs.length;i++) {
		if(slideObjs[i]!=this) {
			if (slideObjs[i].divObj.style.display=="block"){
			  slideObjs[i].slideIt();
			 }
		}
	}
}
