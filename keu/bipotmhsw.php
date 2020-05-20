<?php

// *** Parameters ***
$Angkatan = GetSetVar('Angkatan');
$ProdiID = GetSetVar('ProdiID');
$BIPOTID = GetSetVar('BIPOTID');

// *** Main ***
TampilkanJudul("Set BIPOT per Angkatan");
$gos = (empty($_REQUEST['gos']))? 'TampilkanHeaderAngkatan' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function TampilkanHeaderAngkatan() {
  $optprodi = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID',
    $_SESSION['ProdiID'], "KodeID='".KodeID."'", 'ProdiID');
  $optbipot = GetOption2('bipot', "concat(Tahun, ' - ', Nama)", 'Tahun Desc',
    $_SESSION['BIPOTID'], "KodeID='".KodeID."' and ProdiID='$_SESSION[ProdiID]'", 'BIPOTID');
  $rs = 6;
  if (!empty($_SESSION['ProdiID'])) {
    $mm = GetFields('mhsw', "KodeID='".KodeID."' and ProdiID", $_SESSION['ProdiID'],
      "min(TahunID) as _min, max(TahunID) as _max");
    $min = $mm['_min'];
    $max = $mm['_max'];
    $_mm = "$min &#8594; $max";
  }
  else {
    $_mm = '&nbsp;';
  }
  CheckFormScript('ProdiID,BIPOTID');
  echo <<<ESD
  <table class=bsc cellspacing=1 align=center width=400>
  <form name='frm' action='?' method=POST onSubmit="return CheckForm(this)">
  <input type=hidden name='gos' value='CheckDulu' />
  
  <tr><td class=wrn width=2 rowspan=$rs></td>
      <td class=ul colspan=2>
      <ul>
      <li>Anda akan mengisi field BIPOT ke master mahasiswa secara massal.</li>
      <li>Tentukan angkatan mahasiswa yang akan diproses dan pilih BIPOT yang akan digunakan.</li>
      <li>Mahasiswa yang telah memiliki BIPOT tidak akan diproses lagi.</li>
      </ul>
      </td>
      <td class=wrn width=2 rowspan=$rs></td>
      </tr>
  <tr>
      <td class=inp>Prodi:</td>
      <td class=ul><select name='ProdiID' onChange="location='?mnux=$_SESSION[mnux]&ProdiID='+frm.ProdiID.value">$optprodi</select></td>
      </tr>
  <tr><td class=inp>Angkatan Prodi:</td>
      <td class=ul>$_mm</td></tr>
  <tr><td class=inp>Angkatan:</td>
      <td class=ul><input type=text name='Angkatan' value='$_SESSION[Angkatan]' size=4 maxlength=4></td>
      </tr> 
  <tr><td class=inp>BIPOT:</td>
      <td class=ul><select name='BIPOTID'>$optbipot</select></td>
      </tr>
  <tr><td class=ul colspan=2 align=center>
      <input type=submit name='Proses' value='Set Bipot Mhsw' />
      </td></tr>
  </form>
  </table>
ESD;
}
function CheckDulu() {
  $Angkatan = sqling($_REQUEST['Angkatan']);
  $ProdiID = sqling($_REQUEST['ProdiID']);
  $BIPOTID = $_REQUEST['BIPOTID'];
  
  $s = "select m.MhswID, m.ProdiID, m.ProgramID, k.TahunID
    from mhsw m
	left outer join khs k on k.MhswID = m.MhswID
    where m.KodeID = '".KodeID."'
      and m.ProdiID = '$ProdiID'
      and LEFT(m.TahunID,4) = '$Angkatan'
      and k.BIPOTID = 0
    order by k.MhswID";
  $r = _query($s); $n = 0;
  while ($w = _fetch_array($r)) {
    $n++;
    $_SESSION['_bipotMhswID_'.$n] = $w['MhswID'];
    $_SESSION['_bipotProdiID_'.$n] = $w['ProdiID'];
    $_SESSION['_bipotProgramID_'.$n] = $w['ProgramID'];
    $_SESSION['_bipotTahunID_'.$n] = $w['TahunID'];
  }
  $_SESSION['_bipotBIPOTID'] = $BIPOTID;
  $_SESSION['_bipotJumlah'] = $n;
  $_SESSION['_bipotProgress'] = 1;
  echo Konfirmasi("Proses Isi BIPOT Mhsw",
    "Ada <b>$n</b> data mahasiswa yg akan diproses.<br />
    Silakan klik tombol di bawah ini untuk memulai proses.
    <hr size=1 color=silver />
    Opsi: <input type=button name='Proses' value='Proses'
      onClick=\"javascript:ProsesBipotMhsw('$ProdiID', '$Angkatan')\" />
      <input type=button name='Batal' value='Batal'
      onClick=\"location='?mnux=$_SESSION[mnux]'\" />");
  echo <<<ESD
  <script>
  <!--
  function ProsesBipotMhsw(prd, angk) {
    lnk = "$_SESSION[mnux].proses.php?ProdiID="+prd+"&Angkatan="+angk;
    win2 = window.open(lnk, "", "width=300, height=250, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  //-->
  </script>
ESD;
}

?>
