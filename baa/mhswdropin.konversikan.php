<?php
session_start();
include_once "../sisfokampus1.php";
HeaderSisfoKampus("Konversi Matakuliah");

// *** Parameters ***
$KRSID = $_REQUEST['KRSID'];
$MhswID = $_REQUEST['MhswID'];

// *** Main ***
$gos = (empty($_REQUEST['gos']))? "fnKonversikan" : $_REQUEST['gos'];
$gos($KRSID, $MhswID);

// *** Functions ***
function fnKonversikan($KRSID, $MhswID) {
  $krs = GetFields('krs', 'KRSID', $KRSID, '*');
  $mhsw = GetFields('mhsw', "MhswID='$MhswID' and KodeID", KodeID, '*');
  $optnilai = GetOption2('nilai',
    "concat(Nama, ' … ', Bobot)", 'Bobot desc', '', "ProdiID='$mhsw[ProdiID]'", 'NilaiID');
  TampilkanJudul("Konversi Matakuliah");
  CheckFormScript("TahunID,MKID,NilaiID");
  echo <<<ESD
  <table class=box cellspacing=1 width=100%>
  <tr><td class=inp>Tahun Akd:</td>
      <td class=ul>$krs[TahunID]</td>
      <td class=inp>Mhsw:</td>
      <td class=ul>$mhsw[Nama] <sup>$MhswID</sup></td>
      </tr>
  <tr><td class=inp>Kode:</td>
      <td class=ul>$krs[MKKode]</td>
      <td class=inp>Matakuliah:</td>
      <td class=ul>$krs[Nama]</td>
      </tr>
  <tr><td class=inp>SKS:</td>
      <td class=ul>$krs[SKS]</td>
      <td class=inp>Nilai:</td>
      <td class=ul>$krs[GradeNilai] <sup>($krs[BobotNilai])</sup></td>
      </tr>
  
  <form name='frmKonv' action='?' method=POST onSubmit="return CheckForm(this)">
  <input type=hidden name='MhswID' value='$MhswID' />
  <input type=hidden name='KRSID' value='$KRSID' />
  <input type=hidden name='gos' value='fnSimpan' />
  
  <tr><th class=ttl colspan=4>Konversikan ke:</th></tr>
  <tr>
      <td class=inp>Matakuliah:</td>
      <td class=ul colspan=3>
      <input type=hidden name='MKID' value='$w[MKID]' />
      <input type=text name='MKKode' size=10 maxlength=50 />
      <input type=text name='MKNama' size=30 maxlength=50 onKeyUp="javascript:CariMK('$mhsw[ProdiID]', 'frmKonv')"/>
      <input type=text name='SKS' size=3 maxlength=3> <sub>SKS</sub>
      <div style='text-align:right'>
      &raquo;
      <a href='#'
        onClick="javascript:CariMK('$mhsw[ProdiID]', 'frmKonv')" />Cari...</a> |
      <a href='#' onClick="javascript:frmKonv.MKID.value='';frmKonv.MKKode.value='';frmKonv.MKNama.value='';frmKonv.SKS.value=0">Reset</a>
      </div>
      </td>
      </tr>
  <tr><td class=inp>Tahun Akd:</td>
      <td class=ul>
      <input type=text name='TahunID' value='$krs[TahunID]' size=6 maxlength=6 />
      </td>
      <td class=inp>Nilai:</td>
      <td class=ul>
        <select name='NilaiID'>$optnilai</select>
      </td>
      </tr>
  <tr><td class=ul colspan=4 align=center>
      <input type=submit name='btnSimpan' value='Simpan Konversi' />
      <input type=button name='btnBatal' value='Batalkan'
        onClick="window.close()" />
      </td></tr>
  </form>
  </table>
  
  <div class='box0' id='carimk'></div>
  
  <script>
  function toggleBox(szDivID, iState) // 1 visible, 0 hidden
  {
    if(document.layers)	   //NN4+
    {
       document.layers[szDivID].visibility = iState ? "show" : "hide";
    }
    else if(document.getElementById)	  //gecko(NN6) + IE 5+
    {
        var obj = document.getElementById(szDivID);
        obj.style.visibility = iState ? "visible" : "hidden";
    }
    else if(document.all)	// IE 4
    {
        document.all[szDivID].style.visibility = iState ? "visible" : "hidden";
    }
  }
  function CariMK(ProdiID, frm) {
    if (eval(frm + ".MKNama.value != ''")) {
      eval(frm + ".MKNama.focus()");
      showMK(ProdiID, frm, eval(frm +".MKNama.value"), 'carimk');
      toggleBox('carimk', 1);
    }
  }
  
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
  </script>
ESD;
}
function fnSimpan($KRSID, $MhswID) {
  $TahunID = sqling($_REQUEST['TahunID']);
  $MKID = $_REQUEST['MKID']+0;
  $SKS = $_REQUEST['SKS']+0;
  $NilaiID = $_REQUEST['NilaiID'];
  $nilai = GetFields('nilai', 'NilaiID', $NilaiID, '*');
  $KRSID = $_REQUEST['KRSID'];
  $oldkrs = GetFields('krs', 'KRSID', $KRSID, '*');
  
  $mhsw = GetFields('mhsw', "MhswID='$MhswID' and KodeID", KodeID, "*");
  $mk = GetFields('mk', 'MKID', $MKID, '*');
  
  
  // Insert di KRS
  $s = "insert into krs
    (KodeID, KHSID, MhswID, TahunID,
    MKID, MKKode, Nama, SKS,
    GradeNilai, BobotNilai, StatusKRSID,
    Setara, SetaraKode, SetaraGrade, SetaraNama,
    LoginBuat, TanggalBuat)
    values
    ('".KodeID."', $KHSID, '$MhswID', 'KONVERSI',
    $MKID, '$mk[MKKode]', '$mk[Nama]', $SKS,
    '$nilai[Nama]', '$nilai[Bobot]', 'A',
    'Y', '$oldkrs[MKKode]', '$oldkrs[GradeNilai]', '$oldkrs[Nama]',
    '$_SESSION[_Login]', now())";
   $r = _query($s);
   // Non-aktifkan yg lama
   $s1 = "update krs
     set StatusKRSID = 'K'
     where KRSID='$KRSID' ";
   $r1 = _query($s1);
   // Kembalikan
   echo <<<ESD
   <script>
   opener.RefreshParent();
   window.close();
   </script>
ESD;
}

?>

</BODY>
</HTML>
