<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 10 Sept 2008

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Cari Jadwal");

// *** Parameters ***
$JadwalID = $_REQUEST['JadwalID'];
$ProdiID = $_REQUEST['ProdiID'];
$frm = GetSetVar('frm');
$div = GetSetVar('div');
$prd = GetaField('prodi', "KodeID='".KodeID."' and ProdiID", $ProdiID, 'Nama');

// *** Main ***
TampilkanJudul("Cari Jadwal - $prd <sup>($ProdiID)</sup><br /><font size=-1><a href='#' onClick=\"toggleBox('$div', 0)\">(&times; Close &times;)</a></font>");
TampilkanDaftar($JadwalID, $ProdiID);

// *** Functions ***
function TampilkanDaftar($JadwalID, $ProdiID) {
  $jdwl = GetFields('jadwal', 'JadwalID', $JadwalID, '*');
  $s = "select j.*,
    left(j.JamMulai, 5) as _JM, left(j.JamSelesai, 5) as _JS,
    h.Nama as HR, d.Nama as DSN, d.Gelar
    from jadwal j
      left outer join hari h on h.HariID = j.HariID
      left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
    where j.NA = 'N'
      and j.JadwalID <> $JadwalID
      and j.ProdiID = '$jdwl[ProdiID]'
    order by j.HariID, j.JamMulai, j.JamSelesai";
  $r = _query($s);
  $n = 0;
  $hari = ';alksdfj;asdf';
  echo "<table class=bsc cellspacing=1 width=100%>";
  echo "<tr>
    <th class=ttl width=20>#</th>
    <th class=ttl>Jam</th>
    <th class=ttl>Kode</th>
    <th class=ttl>Matakulah</th>
    <th class=ttl>Kelas<hr size=1 color=white>Peserta</th>
    <th class=ttl>Dosen</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    if ($hari != $w['HariID']) {
      $hari = $w['HariID'];
      echo "<tr>
        <td class=ul colspan=10>
        <font size=+1>$w[HR]</font>
        </td>
        </tr>";
    }
    echo "<tr>
      <td class=inp>$n</td>
      <td class=ul align=left nowrap><sup>$w[_JM]</sup><br /><sub>&#8594; $w[_JS]</sub></td>
      <td class=ul nowrap>
        <a href='#' onClick=\"javascript:$_SESSION[frm]._JadwalID.value='$w[JadwalID]';$_SESSION[frm]._Nama.value='$w[Nama]';$_SESSION[frm]._Dosen.value='$w[DSN], $w[Gelar]';$_SESSION[frm]._Jadwal.value='$w[HR], $w[_JM]-$w[_JS]';$_SESSION[frm]._Peserta.value='$w[JumlahMhsw]~$w[Kapasitas]';toggleBox('$_SESSION[div]', 0)\">
        &#8660;
        $w[MKKode]</a></td>
      <td class=ul>$w[Nama]</td>
      <td class=ul align=center>
        $w[NamaKelas] <sup>$w[RuangID]</sup>
        <br />
        $w[JumlahMhsw]<sup>&#8594;$w[Kapasitas]</sup></td>
      <td class=ul>$w[DSN] <sup>$w[Gelar]</sup></td>
      </tr>";
  }
  echo "</table>";
}

?>

</BODY>
</HTML>
