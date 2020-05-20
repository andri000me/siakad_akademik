// Author : Emanuel Setio Dewo
// Agustus 2008

var xmlHttp

function showMK(ProdiID, frm, NamaMK, NamaDiv) { 
  xmlHttp=GetXmlHttpObject()
  if (xmlHttp == null) {
    alert ("Browser does not support HTTP Request")
    return
  }
  var url = "../jur/carimkprodi.php"
  url = url + "?ProdiID=" + ProdiID;
  url = url + "&frm=" + frm;
  url = url + "&Nama=" + NamaMK;
  url = url + "&div=" + NamaDiv;
  url = url + "&sid=" + Math.random();
  xmlHttp.onreadystatechange = stateChangedMK;
  xmlHttp.open("GET",url,true);
  xmlHttp.send(null);
}

function stateChangedMK() 
{ 
  if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete") { 
    document.getElementById("carimk").innerHTML=xmlHttp.responseText 
  } 
}

function GetXmlHttpObject() {
  var xmlHttp=null;
  try {
    // Firefox, Opera 8.0+, Safari
    xmlHttp=new XMLHttpRequest();
  }
  catch (e) {
    //Internet Explorer
    try {
      xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
    }
    catch (e) {
      xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
  }
  return xmlHttp;
}
