<?php
// Author: Emanuel Setio Dewo
// Start: 13 March 2006
include "../sisfokampus.php";
include "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";
include_once "parameter.php";

echo "<HTML>
  <HEAD>
  <TITLE>Cari Dosen</TITLE>
  <link href=\"../themes/default/index.css\" rel=\"stylesheet\" type=\"text/css\">
  </HEAD>
  <BODY>";

// *** Parameters ***
$DosenID = $_REQUEST['DosenID'];
$NamaDosen = $_REQUEST['NamaDosen'];
$prodi = $_REQUEST['prodi'];
if (empty($DosenID) && empty($NamaDosen)) {
  $psn = ErrorMsg("Isikan Pencarian",
    "Isikan field Kode Dosen atau Nama dosen.<br />
    Isian tidak harus lengkap (parsial).<hr>
    <input type=button name='Tutup' value='Tutup' onClick='javascript:window.close()'>");
  die ($psn);
}

TampilkanKembalikanScript();
TampilkanJudul("Daftar Dosen");
TampilkanDaftarDosen();

include "disconnectdb.php";
echo "</BODY></HTML>";

// *** Functions ***
function TampilkanDaftarDosen() {
  global $DosenID, $NamaDosen, $prodi;
  $arr = array();
  if (!empty($DosenID)) $arr[] = "Login like '$DosenID%' ";
  if (!empty($NamaDosen)) $arr[] = "Nama like '%$NamaDosen%' ";
  if (!empty($prodi)) $arr[] = "INSTR(ProdiID, '.$prodi.')>0 ";
  $whr = (empty($arr))? '' : " and " . implode(' and ', $arr);
  
  $s = "select Login, Nama, concat(Nama, ', ', Gelar) as DSN, Homebase, ProdiID
    from dosen
    where NA='N'
      $whr
    order by Nama";
  $r = _query($s);
  //echo "<pre>$s</pre>";
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl>Kode</th>
    <th class=ttl>Nama Dosen</th>
    <th class=ttl>Homebase</th>
    <th class=ttl>Mengajar di</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $_prd = TRIM($w['ProdiID'], '.');
    $_prd = str_replace('.', ',', $_prd);
    echo "<tr>
      <td class=ul><a href='javascript:kembalikan(\"$w[Login]\", \"$w[Nama]\")'><img src='img/share.gif'>
      $w[Login]</a></td>
      <td class=ul>$w[DSN]</td>
      <td class=ul>$w[Homebase]</td>
      <td class=ul>$_prd</td>
      </tr>";
  }
  echo "</table></p>";
}
function TampilkanKembalikanScript() {
  echo <<<END
  <SCRIPT>
  <!--
  function kembalikan(DosenID, Nama) {
    creator.data.DosenID.value = DosenID;
    creator.data.NamaDosen.value = Nama;
    window.close();
  }
  -->
  </SCRIPT>
END;
}

?>
