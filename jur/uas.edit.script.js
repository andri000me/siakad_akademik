var xmlHttp

function showDosen(ProdiID, frm, NamaDosen, NamaDiv, Tanggal_y, Tanggal_m, Tanggal_d, Mulai_h, Mulai_n, Selesai_h, Selesai_n, Tahun, UntukRuang) { 
  xmlHttp=GetXmlHttpObject()
  if (xmlHttp == null) {
    alert ("Browser does not support HTTP Request")
    return
  }
  var url = "../jur/caridosenuas.php"
  url = url + "?ProdiID=" + ProdiID;
  url = url + "&frm=" + frm;
  url = url + "&Nama=" + NamaDosen;
  url = url + "&div=" + NamaDiv;
  url = url + "&Tahun=" + Tahun;
  url = url + "&Tanggal=" + Tanggal_y + "-" + Tanggal_m + "-" + Tanggal_d;
  url = url + "&Mulai=" + Mulai_h + ":" + Mulai_n;
  url = url + "&Selesai=" + Selesai_h + ":" + Selesai_n;
  url = url + "&sid=" + Math.random();
  url = url + "&UR=" + UntukRuang;
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

function showRuang(ProdiID, frm, RuangID, NamaDiv, kapasitasR, Tanggal_y, Tanggal_m, Tanggal_d, Mulai_h, Mulai_n, Selesai_h, Selesai_n, Tahun, UntukRuang) { 
  xmlHttp=GetXmlHttpObject()
  if (xmlHttp == null) {
    alert ("Browser does not support HTTP Request")
    return
  }
  var url = "../jur/cariruanguas.php"
  url = url + "?ProdiID=" + ProdiID;
  url = url + "&frm=" + frm;
  url = url + "&UASRuangID=" + RuangID;
  url = url + "&div=" + NamaDiv;
  url = url + "&kR=" + kapasitasR;
  url = url + "&Tahun=" + Tahun;
  url = url + "&Tanggal=" + Tanggal_y + "-" + Tanggal_m + "-" + Tanggal_d;
  url = url + "&Mulai=" + Mulai_h + ":" + Mulai_n;
  url = url + "&Selesai=" + Selesai_h + ":" + Selesai_n;
  url = url + "&sid=" + Math.random();
  url = url + "&UR=" + UntukRuang;
  xmlHttp.onreadystatechange = stateChangedRuang;
  xmlHttp.open("GET",url,true);
  xmlHttp.send(null);
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
