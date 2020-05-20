<?php
// Author: SISFOKAMPUS
$arrDiagram = array(
  "SOP Akademik Mahasiswa Baru~SOP9.pdf",
  "SOP Akademik Mahasiswa Lama~SOP3.pdf",
  "SOP Penerimaan Mahasiswa Baru~SOP1.pdf",
  "SOP Registrasi Mahasiswa Baru~SOP2.pdf",
  "SOP Her-Registrasi dan Pembayaran Mahasiswa Lama~SOP6.pdf",
  "SOP Pengisian KRS~SOP10.pdf",
  "SOP Manajemen Praktek Kerja~SOP4.pdf",
  "SOP Manajemen Tugas Akhir~SOP5.pdf",
  //"SOP Pindah Kelas/Prodi~SOP7.pdf",
  "SOP Cuti Mahasiswa~SOP12.pdf",
  //"SOP Bipot Deposit~SOP11.pdf",
  "SOP Administrasi Wisuda dan Alumni",
  "SOP Login untuk Dosen (*fitur khusus*)~SOP8.pdf",
  "SOP Login untuk Mahasiswa(*fitur khusus*)~SOP14.pdf",
  "SOP Pembuatan Laporan EPSBED~SOP13.pdf"
  );
 
TampilkanJudul("ALUR PENGGUNAAN SISTEM INFORMASI AKADEMIK KAMPUS");
TampilkanDaftarDiagram(); 

function TampilkanDaftarDiagram() {
  global $arrDiagram;
  echo "<ol>";
  for ($i = 0; $i < sizeof($arrDiagram); $i++) {
    $a = Explode('~', $arrDiagram[$i]);
    echo "<li><a href='desain/" . $a[1] . "' target=_blank>" .
      $a[0] . "</a>".
      "</li>";
  }
  echo "</ol>";
}
 
?>
