<?php
// Author: SISFOKAMPUS
//$arrDiagram = array(
  //"SOP Akademik Mahasiswa Baru~SOP9.pdf",
  //"SOP Akademik Mahasiswa Lama~SOP3.pdf",
  //"SOP Penerimaan Mahasiswa Baru~SOP1.pdf",
  //"SOP Registrasi Mahasiswa Baru~SOP2.pdf",
  //"SOP Her-Registrasi dan Pembayaran Mahasiswa Lama~SOP6.pdf",
  //"SOP Pengisian KRS~SOP10.pdf",
  //"SOP Manajemen Praktek Kerja~SOP4.pdf",
  //"SOP Manajemen Tugas Akhir~SOP5.pdf",
  //"SOP Pindah Kelas/Prodi~SOP7.pdf",
  //"SOP Cuti Mahasiswa~SOP12.pdf",
  //"SOP Bipot Deposit~SOP11.pdf",
  //"SOP Administrasi Wisuda dan Alumni",
  //"SOP Login untuk Dosen (*fitur khusus*)~SOP8.pdf",
  //"SOP Login untuk Mahasiswa(*fitur khusus*)~SOP14.pdf",
  //"SOP Pembuatan Laporan EPSBED~SOP13.pdf"
  //);

$arrPanduan = array(
  "Panduan Instalasi awal Sisfokampus~Instalasi.pdf",
  "Panduan Login User~login.pdf",
  "Panduan Simbol Penting~simbol.pdf",
  //"Panduan Setup Master Identitas Perguruan Tinggi",
  //"Panduan Setup Master Pejabat",
  //"Panduan Setup Master Rekening Institusi",
  //"Panduan Setup Master Program",
  //"Panduan Setup Master Fakultas",
  //"Panduan Setup Master Kampus",
  //"Panduan Setup Master Ruang",
  //"Panduan Setup Master Dosen",
  //"Panduan Setup Master Mahasiswa",
  //"Panduan Setup Master Biaya dan Potongan",
  //"Panduan Setup Master Mata Kuliah",	
  //"Panduan Penggunaan Fitur Lain (Diluar SOP)" 
  //"Panduan PMB~ManualBookPMB.doc",
  //"Modul Admisi~Modul Admisi.doc",
  //"Modul Akademik~Modul Akademik.doc",
  //"Panduan Mahasiswa~PanduanMhsw.doc",
  //"Status Mahasiswa~Status Mahasiswa.doc",
  //"Panduan Modul Bugs dan Error~Modul Bugs Error.doc"
  );

TampilkanJudul("DAFTAR PANDUAN");
//TampilkanDaftarDiagram();
TampilkanDaftarPanduan();

//function TampilkanDaftarDiagram() {
  //global $arrDiagram;
  //echo "<p><h3>SOP PENGGUNAAN SISTEM</h3></p>";
  //echo "<ol>";
  //for ($i = 0; $i < sizeof($arrDiagram); $i++) {
    //$a = Explode('~', $arrDiagram[$i]);
    //echo "<li><a href='desain/" . $a[1] . "' target=_blank>" .
      //$a[0] . "</a>".
      //"</li>";
  //}
  //echo "</ol>";
//}
function TampilkanDaftarPanduan() {
  global $arrPanduan;
  //echo "<p><h3>Daftar Panduan Penggunaan System untuk Administrator</h3></p>";
  echo "<ol>";
  for ($i = 0; $i < sizeof($arrPanduan); $i++) {
    $a = Explode('~', $arrPanduan[$i]);
    echo "<li><a href='desain/" . $a[1] . "' target=_blank>" .
      $a[0] .
      "</li>";
  }
  echo "</ol>";
}
?>
