<?php
include "../sisfokampus.php";
$Cari = $_REQUEST['Cari'];
if (empty($Cari)) {
  $_REQUEST['Pesan'] = "<b>Tidak ada yang harus dicari.<hr size=1 />
    Masukkan Nomor PMB yang ada di map<b>";
  include "pesan.html.php";
}
else {
include "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";
include_once "parameter.php";

echo "<HTML>
  <HEAD>
  <TITLE>Cek Mahasiswa PMB</TITLE>";
include_once "css.php";
echo "</HEAD>
  <BODY>";
//TampilkanKembalikanScript();
TampilkanJudul("<center>Cek Mahasiswa PMB</center>");
TampilkanCekPMB();

include_once "disconnectdb.php";
echo "</BODY>
</HTML>";
}

function TampilkanKembalikanScript() {
echo <<<END
  <script>
  <!--
  function kembalikan(SekolahID, Nama, Kota){
    creator.data.AsalSekolah.value = SekolahID;
    creator.data.NamaSekolah.value = Nama + ", " + Kota;
    window.close();
  }
  -->
  </script>
END;
}
function TampilkanCekPMB() {
  global $Cari;

  $Jml = GetaField('pmb', "PMBRef", $Cari, "PMBID");
  if (!empty($Jml)) {
    echo "<p><b>PERHATIAN : </b> Mahasiswa yang ingin anda input formulirnya sudah terdaftar dengan No Ujian :
          <font color=red>$Jml</font></p>";
  } else {
    echo "Mahasiswa Belum Terdaftar";
  }
  include "pesan.html.php";
}
?>
