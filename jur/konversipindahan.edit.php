<?php

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Konversi Matakuliah", 1);

// *** infrastruktur **
echo <<<SCR
  <script src="../$_SESSION[mnux].edit.js"></script>
SCR;

// *** Parameters ***
$md = $_REQUEST['md']+0;
$mhsw = sqling($_REQUEST['mhsw']);
$thn = sqling($_REQUEST['thn']);
$id = $_REQUEST['id']+0;

// *** Main ***
$jdl = ($md == 0)? "Edit Matakuliah Konversi" : "Tambah Matakuliah Konversi";
TampilkanJudul($jdl);
$gos = (empty($_REQUEST['gos']))? 'Edit' : $_REQUEST['gos'];
$gos($md, $mhsw, $thn, $id);


// *** Functions ***
function Edit($md, $mhsw, $thn, $id) {
  if ($md == 0) {
    $w = GetFields('krs', 'KRSID', $id, '*');
  }
  elseif ($md == 1) {
    $w = array();
    $w['TahunID'] = $thn;
  }
  else die(ErrorMsg('Error',
    "Terjadi kesalahan mode edit.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    <input type=button name='btnTutup' value='Tutup'
      onClick='window.close()' />"));
  
  $m = GetFields('mhsw', "MhswID = '$mhsw' and KodeID", KodeID, '*');
  $optnilai = GetOption2('nilai', "concat(Nama, ' ', '-', ' ', Bobot)",
    "Bobot desc", $w['BobotNilai'], "ProdiID='$m[ProdiID]' and KodeID='".KodeID."'", 'Bobot');
  // Tampilkan
  CheckFormScript("thn,MKID,BobotNilai,SetaraKode,SetaraNama,SetaraGrade");
  echo <<<ESD
  <table class=bsc cellspacing=1 width=100%>
  <form name='frmEdit' action='../$_SESSION[mnux].edit.php' method=POST onSubmit="return CheckForm(this)">
  <input type=hidden name='gos' value='Simpan' />
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='mhsw' value='$mhsw' />
  <input type=hidden name='id' value='$id' />
  
  <tr><td class=inp width=120>Tahun Akd:</td>
      <td class=ul><input type=text name='thn' value='$w[TahunID]' size=6 maxlength=6 /></td>
      </tr>
  <tr><td class=inp>Matakuliah:</td>
      <td class=ul>
      <input type=hidden name='MKID' value='$w[MKID]' />
      <input type=text name='MKKode' value='$w[MKKode]' size=10 maxlength=50 />
      <input type=text name='MKNama' value='$w[Nama]' size=30 maxlength=50 onKeyUp="javascript:CariMK('$m[ProdiID]', 'frmEdit')"/>
      <input type=text name='SKS' value='$w[SKS]' size=3 maxlength=3> <sub>SKS</sub>
      <div style='text-align:right'>
      &raquo;
      <a href='#'
        onClick="javascript:CariMK('$m[ProdiID]', 'frmEdit')" />Cari...</a> |
      <a href='#' onClick="javascript:frmEdit.MKID.value='';frmEdit.MKKode.value='';frmEdit.MKNama.value='';frmEdit.SKS.value=0">Reset</a>
      </div>
      </td>
      </tr>
  <tr><td class=inp>Nilai:</td>
      <td class=ul>
        <select name='BobotNilai'>$optnilai</select>
      </td></tr>
  <tr><td class=inp>Catatan:</td>
      <td class=ul><textarea name='Catatan' cols=40 rows=3>$w[Catatan]</textarea></td>
      </tr>
  
  <tr><th class=ttl colspan=2>Matakuliah Asli</th></tr>
  <tr><td class=inp>Kode Asal:</td>
      <td class=ul>
        <input type=text name='SetaraKode' value='$w[SetaraKode]' size=30 maxlength=50 />
      </td></tr>
  <tr><td class=inp>Nama MK:</td>
      <td class=ul>
        <input type=text name='SetaraNama' value='$w[SetaraNama]' size=50 maxlength=50 />
      </td></tr> 
  <tr><td class=inp>Grade Nilai:</td>
      <td class=ul>
        <input type=text name='SetaraGrade' value='$w[SetaraGrade]' size=4 maxlength=4 />
      </td></tr>
  <tr><td class=ul colspan=2 align=center>
      <input type=submit name='btnSimpan' value='Simpan' />
      <input type=button name='btnCancel' value='Batal' onClick='window.close()' />
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
  </script>
ESD;
}
function Simpan($md, $mhsw, $thn, $id) {
  $MKID = $_REQUEST['MKID'];
  $BobotNilai = $_REQUEST['BobotNilai'];
  $Catatan = sqling($_REQUEST['Catatan']);
  $SetaraKode = sqling($_REQUEST['SetaraKode']);
  $SetaraNama = sqling($_REQUEST['SetaraNama']);
  $SetaraGrade = sqling($_REQUEST['SetaraGrade']);
  $SKS = $_REQUEST['SKS']+0;
  
  $m = GetFields('mhsw', "MhswID = '$mhsw' and KodeID", KodeID, '*');
  $GradeNilai = GetaField('nilai', "ProdiID='$m[ProdiID]' and NA='N' and Bobot",
    $BobotNilai, "Nama");
  $mk = GetFields('mk', 'MKID', $MKID, '*');
  if ($md == 0) {
    $krsid = $_REQUEST['id'];
    $s = "update krs
      set MKID='$MKID',
          TahunID = '$thn',
          MKKode = '$mk[MKKode]',
          Nama = '$mk[Nama]',
          SKS = '$mk[SKS]',
          GradeNilai = '$GradeNilai',
          BobotNilai = '$BobotNilai',
          Setara = 'Y',
          SetaraKode = '$SetaraKode',
          SetaraNama = '$SetaraNama',
          SetaraGrade = '$SetaraGrade',
          Catatan = '$Catatan',
          Sah = 'Y',
          Final = 'Y',
          LoginEdit = '$_SESSION[_Login]',
          TanggalEdit = now()
      where KRSID = '$krsid' ";
    $r = _query($s);
    Tutup();
  }
  elseif ($md == 1) {
    // Cek tahun dulu
    $khsid = CekKHS($mhsw, $m, $thn);
    $s = "insert into krs
      (KodeID, KHSID, MhswID, TahunID,
      MKID, MKKode, Nama, SKS,
      GradeNilai, BobotNilai, StatusKRSID,
      Setara, Final, Sah, Catatan,
      LoginBuat, TanggalBuat,SetaraKode,SetaraNama,SetaraGrade)
      values
      ('".KodeID."', $khsid, '$mhsw', '$thn',
      '$MKID', '$mk[MKKode]', '$mk[Nama]', '$SKS',
      '$GradeNilai', '$BobotNilai', 'A',
      'Y', 'Y', 'Y', '$Catatan',
      '$_SESSION[_Login]', now(),'$SetaraKode','$SetaraNama','$SetaraGrade')";
    $r = _query($s);
    Tutup();
  }
  else echo ErrorMsg("Error",
    "Ada kesalahan mode edit.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    <input type=button name='btnClose' value='Tutup' onClick='window.close()' />");
}
function CekKHS($mhsw, $m, $thn) {
  $ada = GetaField('khs', "MhswID='$mhsw' and TahunID='$thn' and KodeID", KodeID, 'KHSID')+0;
  if ($ada == 0) {
    $Sesi = GetaField('khs', "MhswID='$mhsw' and TahunID<'$thn' and KodeID", KodeID, "Sesi")+1;
    $s = "insert into khs
      (TahunID, KodeID, ProgramID, 
      ProdiID, MhswID, StatusMhswID,
      Sesi, LoginBuat, TanggalBuat)
      values
      ('$thn', '".KodeID."', '$m[ProgramID]',
      '$m[ProdiID]', '$mhsw', 'A',
      '$Sesi', '$_SESSION[_Login]', now())";
    $r = _query($s);
    $ada = GetLastID();
  }
  return $ada;
}
function Tutup() {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    opener.location='../index.php?mnux=$_SESSION[mnux]&gos=';
    self.close();
    return false;
  }
  ttutup();
</SCRIPT>
SCR;
}
?>
