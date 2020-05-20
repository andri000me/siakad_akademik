<?php
// Author: Emanuel Setio Dewo
// 23 Feb 2006
include "../sisfokampus.php";
include "../db.mysql.php";
include_once "../connectdb.php";
include_once "../dwo.lib.php";
include_once "../parameter.php";

echo "<HTML>
  <HEAD>
  <TITLE>Cari Perguruan Tinggi</TITLE>
    <link rel='stylesheet' type='text/css' href='../themes/".$_Themes."/index.css' />
  </HEAD>
  <BODY>";

$Cari = $_REQUEST['Cari'];
if (empty($Cari)) {
  die (ErrorMsg('Error',
    "Tidak ada yang harus dicari.<hr size=1 />
    Masukkan Nama & Kota dari perguruan tinggi yg dicari dalam format: [<font color=maroon>NamaPT/SingkatanPT, KotaPT</font>]
    <hr size=1 color=silver />
    Opsi: <input type=button name='Tutup' value='Tutup' onClick='window.close()' />"));
}
else {

TampilkanKembalikanScript();
TampilkanJudul("Daftar Perguruan Tinggi");
TampilkanDaftarPerguruanTinggi();

echo "</BODY>
</HTML>";
}

function TampilkanKembalikanScript() {
echo <<<END
  <script>
  <!--
  function kembalikan(PerguruanTinggiID, Nama, Kota){
    creator.data.AsalPT.value = PerguruanTinggiID;
    creator.data.NamaPT.value = Nama + ", " + Kota;
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
  echo "<p><table class=box cellspacing=1 cellpadding=4 width=100%>
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
    <td class=ul>$w[SingkatanNama]&nbsp;</td>
    <td class=ul>$w[Nama]&nbsp;</td>
    <td class=ul>$w[Kota]&nbsp;</td>
    </tr>";
  }
  echo "</table></p>";
}
?>
