var xmlHttp

function showDosen(frm, NamaDosen, NamaDiv,dicari) { 
  xmlHttp=GetXmlHttpObject()
  if (xmlHttp == null) {
    alert ("Browser does not support HTTP Request")
    return
  }
  var url = "../dosen/caridosenpenelitian.php"
  url = url + "?frm=" + frm;
  url = url + "&Nama=" + NamaDosen;
  url = url + "&div=" + NamaDiv;
  url = url + "&cari=" + dicari;
  url = url + "&sid=" + Math.random();
  xmlHttp.onreadystatechange = stateChanged;
  xmlHttp.open("GET",url,true);
  xmlHttp.send(null);
}

function stateChanged() 
{ 
  if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete") { 
    document.getElementById("caridosen").innerHTML=xmlHttp.responseText 
  } 
}


function stateChangedMhsw() 
{ 
  if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete") { 
    document.getElementById("carimhsw").innerHTML=xmlHttp.responseText 
  } 
}


function stateChangedMK() 
{ 
  if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete") { 
    document.getElementById("carimk").innerHTML=xmlHttp.responseText 
  } 
}



function stateChangedRuang() 
{ 
  if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete") { 
    document.getElementById("cariruang").innerHTML=xmlHttp.responseText 
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
