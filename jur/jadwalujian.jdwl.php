<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 26/11/2008

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Jadwal Kuliah", 1);

// *** Parameters ***
$_jdwlProdi = GetSetVar('_jdwlProdi');
$_jdwlProg  = GetSetVar('_jdwlProg');
$_jdwlTahun = GetSetVar('_jdwlTahun');
$_jdwlHari  = GetSetVar('_jdwlHari');
$_jdwlUjian = GetSetVar('_jdwlUjian');

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'fnDftrJdwl' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function TampilkanHeaderJadwal() {
  $optprodi = GetProdiUser($_SESSION['_Login'], $_SESSION['_jdwlProdi']);
  $opthr = GetOption2('hari', 'Nama', 'HariID', $_SESSION['_jdwlHari'], '', 'HariID');
  echo <<<ESD
  <table class=box cellspacing=1 width=100%>
  <form name='frmJDWL' action="../$_SESSION[mnux].jdwl.php" method=POST>
  
  <tr><td class=wrn width=2></td>
      <td class=inp>Prodi:</td>
      <td class=ul><select name='_jdwlProdi' onChange='this.form.submit()'>$optprodi</select></td>
      <td class=inp>Hari:</td>
      <td class=ul><select name='_jdwlHari' onChange='this.form.submit()'>$opthr</select></td>
      <td class=ul><input type=submit name='btnKirim' value='Kirim' /></td>
  </form>
  </table>
ESD;
}
function fnDftrJdwl() {
  TampilkanHeaderJadwal();
  $whr_prog = ($_SESSION['_jdwlProg'] == '')? '' : "and j.ProgramID='$_SESSION[_jdwlProg]' ";
  $whr_hari = ($_SESSION['_jdwlHari'] == '')? '' : "and j.HariID = '$_SESSION[_jdwlHari]' ";
  $s = "select j.*,
      left(j.Nama, 50) as _Nama,
      left(j.JamMulai, 5) as _JM,
      left(j.JamSelesai, 5) as _JS,
      d.Nama as NamaDosen, d.Gelar
    from jadwal j
      left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
    where j.KodeID = '".KodeID."'
      and j.ProdiID = '$_SESSION[_jdwlProdi]'
      and j.TahunID = '$_SESSION[_jdwlTahun]'
      and j.$_SESSION[_jdwlU]RuangID = ''
      $whr_prog
      $whr_hari
    order by j.HariID, j.JamMulai, j.JamSelesai";
  $r = _query($s); $n = 0;
  
  $hdr = "<tr><th class=ttl width=10>#</th>
      <th class=ttl width=70>Jam Kuliah</th>
      <th class=ttl>Matakuliah blm dijadwalkan ujian</th>
      <th class=ttl>Kelas</th>
      <th class=ttl>Prodi</th>
      <th class=ttl width=10>$_SESSION[_jdwlU]</th>
      </tr>";
  RandomStringScript();
  echo <<<ESD
  <table class=box cellspacing=1 width=100%>
  <script>
  function RefreshAll() {
    parent.RefreshAll();
  }
  function JadwalkanUjian(jid) {
    var _rnd = randomString();
    lnk = "../$_SESSION[mnux].edit.php?jid="+jid;
    win2 = window.open(lnk, "", "width=500, height=400, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  </script>
ESD;
  $hr = '-1';
  while ($w = _fetch_array($r)) {
    $n++;
    if ($hr != $w['HariID']) {
      $hr = $w['HariID'];
      $_hr = GetaField('hari', 'HariID', $hr, 'Nama');
      echo "<tr><td class=ul1 colspan=6><font size=+1>$_hr</font></td></tr>";
      echo $hdr;
    }
    echo <<<ESD
    <tr><td class=inp width=10>$n</td>
        <td class=ul width=70><sup>$w[_JM]</sup>~<sub>$w[_JS]</sub></td>
        <td class=ul>
          <sup>$w[MKKode] &minus; $w[SKS] sks</sup><br />
          $w[_Nama]
          <div align=right>
            <sub>$w[NamaDosen], $w[Gelar]</sub>
          </div>
          </td>
        <td class=ul align=center>
          $w[NamaKelas]<br />
          $w[JumlahMhsw] <sub>mhs</sub>
          </td>
        <td class=ul align=center>
          <sup>$w[ProgramID]</sup><br />
          $w[ProdiID]
          </td>
        <td class=ul width=10 align=center>
          <a href='#' onClick="javascript:JadwalkanUjian($w[JadwalID])"><img src='../img/check.gif' /></a>
          </td>
        </tr>
ESD;
  }
  echo "</table>";
}
?>
