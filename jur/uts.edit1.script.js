// Author : Emanuel Setio Dewo
// Agustus 2008


function showRuangan(ProdiID, frm, RuangID, NamaDiv,id) { 
  xmlHttp=GetXmlHttpObject()
  if (xmlHttp == null) {
    alert ("Browser does not support HTTP Request")
    return
  }
  var url = "../jur/cariruanguts1.php"
  url = url + "?ProdiID=" + ProdiID;
  url = url + "&frm=" + frm;
  url = url + "&ID=" + id;
  url = url + "&UTSRuangID=" + RuangID;
  url = url + "&div=" + NamaDiv;
  url = url + "&sid=" + Math.random();
  xmlHttp.onreadystatechange = stateChangedRuang;
  xmlHttp.open("GET",url,true);
  xmlHttp.send(null);
}




