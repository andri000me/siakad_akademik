<?php
// Author: Emanuel Setio Dewo
// 23 May 2006
// http://www.sisfokampus.net
include "../sisfokampus.php";
// *** Include ***
include "db.mysql.php";
include "connectdb.php";
include "dwo.lib.php";
include "parameter.php";

echo "<HTML>
  <HEAD>
  <TITLE>Cari Matakuliah</TITLE>
  <link href=\"index.css\" rel=\"stylesheet\" type=\"text/css\">
  </HEAD>";

// *** Parameters ***
$MKKode = $_REQUEST['MKKode'];
$Nama = $_REQUEST['Nama'];
$prodi = $_REQUEST['prodi'];
if (empty($MKKode) && empty($Nama)) {
  $psn = ErrorMsg("Isikan Pencarian",
    "Isikan field Kode Matakuliah atau Nama matakuliah.<br />
    Isian tidak harus lengkap (parsial).<hr>
    <input type=button name='Tutup' value='Tutup' onClick='javascript:window.close()'>");
  die ($psn);
}

TampilkanKembalikanScript();
TampilkanJudul("Daftar Matakuliah");
TampilkanDaftarMatakuliah();

include "disconnectdb.php";
echo "</BODY></HTML>";

// *** Functions ***
function TampilkanKembalikanScript() {
  echo <<<END
  <SCRIPT>
  <!--
  function kembalikan(MKID, MKKode, Nama) {
    creator.data.MKID.value = MKID;
    creator.data.MKKode.value = MKKode;
    creator.data.Nama.value = Nama;
    window.close();
  }
  -->
  </SCRIPT>
END;
}
function TampilkanDaftarMatakuliah() {
  global $MKKode, $Nama, $prodi;
  $arr = array();
  if (!empty($MKKode)) $arr[] = "mk.MKKode like '$MKKode%' ";
  if (!empty($Nama)) $arr[] = "mk.Nama like '%$Nama%' ";
  $whr = (empty($arr))? '' : " and " . implode(' and ', $arr);
  $s = "select mk.MKID, mk.MKKode, mk.Nama, mk.Nama_en, mk.SKS, mk.Sesi, mk.KurikulumID
    from mk
      left outer join kurikulum kur on mk.KurikulumID=kur.KurikulumID
    where mk.ProdiID='$prodi'
      $whr
      and kur.NA='N'
      and mk.NA='N'
    order by MKKode";
  $r = _query($s);
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <tr><th class=ttl>Kode</th>
  <th class=ttl>Nama</th>
  <th class=ttl>English</th>
  <th class=ttl>SKS</th>
  <th class=ttl>Sesi</th>
  </tr>";
  while ($w = _fetch_array($r)) {
    echo "<tr><td class=ul nowrap><a href='javascript:kembalikan(\"$w[MKID]\", \"$w[MKKode]\", \"$w[Nama]\")'>
    <img src='img/share.gif'>
    $w[MKKode]</a></td>
    <td class=ul>$w[Nama]</td>
    <td class=ul>$w[Nama_en]</td>
    <td class=ul align=right>$w[SKS]</td>
    <td class=ul align=right>$w[Sesi]</td>
    </tr>";
  }
  echo "</table></p>";
}
?>
