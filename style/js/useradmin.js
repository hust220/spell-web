var eris_is_ie = true;
var poplayer;
var poplayer_main;
var cx = 0;
var cy = 0;
var pwidth = 300; // the dimension of the popup-layer
var pheight = 300;
var xhReq = "";
var theid = 0;
init();

function init(){
  var agt = navigator.userAgent.toLowerCase();
  eris_is_ie = (agt.indexOf("msie")!=-1 && document.all);
  if (eris_is_ie) {
    document.attachEvent("onmousedown", erisCheck);
  } else {
    document.addEventListener("mousedown", erisCheck, true);
  }
}


function action(url,userid, username){
  var actionstr=document.getElementById("sel_"+userid).value;
  if (actionstr==0) return;
  poplayer = document.getElementById("action_popup");
  if (!poplayer){
    new_layer();
  }
  poplayer.style.display="";
  appear();
  poplayer_main.src=url+"?userid="+userid +"&username="+username+"&action="+actionstr;
  //poplayer_main.src="index.php";

}

function close_layer(){
  poplayer.style.display="none";
  poplayer_main.classname="hidden";
}

function appear(){
  if (eris_is_ie) {
    cx = document.documentElement.scrollLeft
      + document.body.scrollLeft + self.innerWidth/2;
    cy = document.documentElement.scrollTop
      + document.body.scrollTo + self.innerHeightp/2;
  }else {
    cx = window.scrollX +self.innerWidth/2;
    cy = window.scrollY +self.innerHeight/2;
  }
  cx -=pwidth/2;
  cy -=pheight/2;

  poplayer.style.display="";
  poplayer.style.left = cx + "px"; 
  poplayer.style.top = cy +"px";
}



function new_layer(){
  // The popup layer
  poplayer = document.createElement("div");
  poplayer.setAttribute("style","position:absolute;z-index:6000;display:none;background-color:#FFF;filter:Alpha(Opacity=96);border:1px,onset");
  poplayer.setAttribute("width",pwidth);
  poplayer.setAttribute("height",pheight);
  poplayer.setAttribute("id","action_popup");
  var tb1 = document.createElement("table"); 
  
  // the title line
  tb1.setAttribute("style","border:1px solid #7E98D6;");
  tr1 = document.createElement("tr");
  td1 = document.createElement("td");
  var div = document.createElement("div");
  h  = '<div width="100%" style="cursor:move;background-color:#C8DAF3;border:1px;">' ;
  h += '<table width="100%"><tr><td align="left" width="100%" style="background-color:#C8DAF3;">';
  h += '<div style="color:#1A9100;font-size:14px;background-color:#C8DAF3;">Admin. Menu</div>';
  h += '</td>';
  h += '<td align="right" style="background-color:#C8DAF3;">';
  h += '<a href="javascript:close_layer()" title="Close this window">';
  h += '<img src="style/img/close.gif" style="border:none;display:inline;" align="absmiddle">';
  h += '</a>';
  h += '</td></tr></table>';
  h += '</div>';
  div.innerHTML = h;
  poplayer.appendChild(tb1);
  tb1.appendChild(tr1); tr1.appendChild(td1);
  td1.appendChild(div);

//  top = document.createElement("div");
//  top.setAttribute("align","top");
//  top.innerHTML = h;

  var tb2 = document.createElement("table"); //the table
  tr2 = document.createElement("tr");
  td2 = document.createElement("td");
  var iframe = document.createElement("iframe");
  iframe.setAttribute("HEIGHT", pheight-10);
  iframe.setAttribute("src","about:blank");
  iframe.setAttribute("FRAMEBORDER","0");
  iframe.setAttribute("width",'100%');
  td1.appendChild(tb2);
  tb2.appendChild(tr2);
  tr2.appendChild(td2);
  td2.appendChild(iframe);
  poplayer_main = iframe;
//  poplayer_main = document.createElement("div");
//  poplayer_main.setAttribute("id","action_popup_main");
//  poplayer_main.setAttribute("align","bottom");
//  poplayer.appendChild(top);
//  poplayer.appendChild(poplayer_main);
  document.body.appendChild(poplayer); 
  //now hid the action_popup window
  poplayer.style.display = "none";
}

function erisCheck(event){// get the cursor position
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

function onactionsubmission(){
  if (xhReq.readyState != 4)  { return; }
  var serverResponse = xhReq.responseText;
  var obj = document.getElementById("entrytag"+theid);
  obj.innerHTML = serverResponse;
}

function update_row(userid){
 theid = userid; //set for onactionsubmission
 xhReq = createXMLHttpRequest();
 var url="update_row.php?userid="+userid;
 xhReq.open("get", url, true);
 xhReq.onreadystatechange = onactionsubmission;
 xhReq.send(null);
}

function user_details(userid){
  poplayer = document.getElementById("action_popup");
  if (!poplayer){
    new_layer();
  }
  poplayer.style.display="";
  appear();
  poplayer_main.src="config/admin/user_detail.php?userid="+userid;

}
