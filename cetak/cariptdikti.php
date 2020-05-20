<?php
include "../sisfokampus.php";
$Cari = $_REQUEST['Cari'];
if (empty($Cari)) {
  $_REQUEST['Pesan'] = "Tidak ada yang harus dicari.<hr size=1 />
    Masukkan Nama & Kota dari perguruan tinggi yg dicari dalam format: [<font color=maroon>NamaPT/SingkatanPT, KotaPT</font>]";
  include "pesan.html.php";
}
else {
include "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";
include_once "parameter.php";

echo "<HTML>
  <HEAD>
  <TITLE>Cari Perguruan Tinggi</TITLE>";
include_once "css.php";
echo "</HEAD>
  <BODY>";
TampilkanKembalikanScript();
TampilkanJudul("Daftar Perguruan Tinggi");
TampilkanDaftarPerguruanTinggi();

include_once "disconnectdb.php";
echo "</BODY>
</HTML>";
}

function TampilkanKembalikanScript() {
echo <<<END
  <script>
  <!--
  function kembalikan(PerguruanTinggiID, Nama, Kota){
    creator.data.KodeHukum.value = PerguruanTinggiID;
    creator.data.Nama.value = Nama;
    creator.data.Kota.value = Kota;
    window.close();
  }
  -->
  </script>
END;
}
function TampilkanDaftarPerguruanTinggi() {
  global $Cari;
  $Max = 50;

  $arrcr = explode(',', $Cari);
  $arrwhr = array();
  if (!empty($arrcr[0])) $arrwhr[] = "((Nama like '%".TRIM($arrcr[0])."%') or (SingkatanNama like '%".TRIM($arrcr[0])."%')) ";
  if (!empty($arrcr[1])) $arrwhr[] = "Kota like '%".TRIM($arrcr[1])."%' ";
  $whr = implode(' and ', $arrwhr);
  // Hitung jumlah baris
  $Jml = GetaField('perguruantinggi', "$whr and NA", 'N', "count(PerguruanTinggiID)");
  if ($Jml > $Max) {
    $_Jml = number_format($Jml);
    echo "<p><b>Catatan:</b> Jumlah perguruan tinggi yang Anda cari mencapai: <b>$_Jml</b>, tetapi sistem membatasi
      jumlah perguruan tinggi yang ditampilkan dan hanya menampilkan: <b>$Max</b>.
      Gunakan Nama perguruan tinggi dan Kota Sekolah dengan lebih spesifik untuk membatasi
      jumlah perguruan tinggi yang ditampilkan.</p>

      <p><b>Format Pencarian:</b> NamaPerguruanTinggi/Singkatan, KotaSekolah</p>";
  }
  // Tampilkan
  $s = "select PerguruanTinggiID, SingkatanNama, Nama, Kota
    from perguruantinggi
    where $whr and NA='N'
    order by Nama limit $Max";
  $r = _query($s);
  $n = 0;
  echo "<p><table class=box cellspacing=1 cellpadding=4>
    <tr><th class=ttl>#</th>
    <th class=ttl>Kode Sekolah</th>
    <th class=ttl>Singkatan</th>
    <th class=ttl>Nama</th>
    <th class=ttl>Kota</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    echo "<tr><td class=ul>$n</td>
    <td class=ul><a href='javascript:kembalikan(\"$w[PerguruanTinggiID]\", \"$w[Nama]\", \"$w[Kota]\")'>$w[PerguruanTinggiID]</a></td>
    <td class=ul>$w[SingkatanNama]</td>
    <td class=ul>$w[Nama]</td>
    <td class=ul>$w[Kota]</td>
    </tr>";
  }
  echo "</table></p>";
}
?>