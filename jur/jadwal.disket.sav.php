<?php
// Author: Emanuel Setio Dewo
// 15 June 2006
// www.sisfokampus.net
session_start();

function SimpanPenilaian() {
  // Includes
  include "sisfokampus.php";
  echo "<script language='JavaScript1.1'>
  window.resizeTo(400, 400);
  </script>";
  HeaderSisfoKampus("Metode Penilaian Dosen");
  // Parameter
  $JadwalID = $_REQUEST['JadwalID'];
  $Penilaian = $_REQUEST['Penilaian'];
  // Simpan
  $s = "update jadwal set Penilaian='$Penilaian' where JadwalID='$JadwalID' ";
  $r = _query($s);
  echo "<center><font size=+4>Berhasil</font></center>
  <hr size=1 color=silver>
  <p>Metode penilaian sudah disimpan, yaitu: <font size=+2>$Penilaian</font></p>
  <hr size=1 color=silver>
  <input type=button name='Tutup' value='Tutup' onClick=\"javascript:window.close()\">
  ";
}
function DownloadFileNilai() {
  // include
  include "db.mysql.php";
  include "connectdb.php";
  include "dwo.lib.php";
  // parameter
  $KodeID = $_REQUEST['KodeID'];
  $JadwalID = $_REQUEST['JadwalID'];
  $jdwl = GetFields("jadwal j
    left outer join dosen d on j.DosenID=d.Login
	  left outer join hari h on j.HariID=h.HariID", 
	  "j.JadwalID", $JadwalID, 
	  "j.*, concat(d.Nama, ', ', d.Gelar) as DSN, h.Nama as HR");
  $LGN = $_REQUEST['LGN'];
  $prd = TRIM($jdwl['ProdiID'], '.');
  $prd = explode('.', $prd);
  $prd = $prd[0];
  $_PRD = GetFields('prodi', 'ProdiID', $prd, 'Nama, FakultasID');
  $_FAK = GetaField('fakultas', 'FakultasID', $_PRD['FakultasID'], 'Nama');
  // buat file
  $nmf = HOME_FOLDER  .  DS . "tmp/$LGN.dat";
  $f = fopen($nmf, 'w');
  // Data Jadwal
  fwrite($f, "[KULIAH]\r\n");
  fwrite($f, "ID=$jdwl[JadwalID]\r\n");
  fwrite($f, "TahunID=$jdwl[TahunID]\r\n");
  fwrite($f, "ProgramID=$jdwl[ProgramID]\r\n");
  fwrite($f, "ProdiID=$jdwl[ProdiID]\r\n");
  fwrite($f, "ProdiID1=$prd\r\n");
  fwrite($f, "PRD=$_PRD[Nama]\r\n");
  fwrite($f, "FakultasID=$_PRD[FakultasID]\r\n");
  fwrite($f, "FAK=$_FAK\r\n");
  fwrite($f, "MKID=$jdwl[MKID]\r\n");
  fwrite($f, "MKKode=$jdwl[MKKode]\r\n");
  fwrite($f, "Nama=$jdwl[Nama]\r\n");
  fwrite($f, "NamaKelas=$jdwl[NamaKelas]\r\n");
  fwrite($f, "SKS=$jdwl[SKS]\r\n");
  fwrite($f, "JenisJadwalID=$jdwl[JenisJadwalID]\r\n");
  fwrite($f, "DosenID=$jdwl[DosenID]\r\n");
  fwrite($f, "DSN=$jdwl[DSN]\r\n");
  fwrite($f, "HariID=$jdwl[HariID]\r\n");
  fwrite($f, "HR=$jdwl[HR]\r\n");
  fwrite($f, "JamMulai=$jdwl[JamMulai]\r\n");
  fwrite($f, "JamSelesai=$jdwl[JamSelesai]\r\n");
  fwrite($f, "Pendownload=$_SESSION[_Login]\r\n");
  // Bobot Penilaian
  fwrite($f, "[BOBOT]\r\n");
  fwrite($f, "TugasMandiri=$jdwl[TugasMandiri]\r\n");
  fwrite($f, "Tugas1=$jdwl[Tugas1]\r\n");
  fwrite($f, "Tugas2=$jdwl[Tugas2]\r\n");
  fwrite($f, "Tugas3=$jdwl[Tugas3]\r\n");
  fwrite($f, "Tugas4=$jdwl[Tugas4]\r\n");
  fwrite($f, "Tugas5=$jdwl[Tugas5]\r\n");
  fwrite($f, "Presensi=$jdwl[Presensi]\r\n");
  fwrite($f, "UTS=$jdwl[UTS]\r\n");
  fwrite($f, "UAS=$jdwl[UAS]\r\n");
  fwrite($f, "Responsi=$jdwl[Responsi]\r\n");
  // Rentang Nilai
  $sn = "select *
    from nilai
	where ProdiID='$prd'
	order by Bobot desc";
  $rn = _query($sn);
  $jmln = _num_rows($rn)+0;
  fwrite($f, "[GRADE]\r\n");
  fwrite($f, "Jumlah=$jmln\r\n");
  $n = 0;
  while ($wn = _fetch_array($rn)) {
    $n++;
    fwrite($f, "$n=$wn[Nama],$wn[Bobot],$wn[NilaiMin],$wn[NilaiMax]\r\n");
  }

  // isinya mahasiswa
  fwrite($f, "[MHSW]\r\n");
  $smhsw = "select k.MhswID, m.Nama
    from krs k
	  left outer join mhsw m on k.MhswID=m.MhswID
	where k.JadwalID='$JadwalID'
	order by k.MhswID";
  $rmhsw = _query($smhsw);
  $jml = _num_rows($rmhsw)+0;
  fwrite($f, "Jumlah=$jml\r\n");
  $n = 0;
  while ($wmhsw = _fetch_array($rmhsw)) {
    $n++;
    $wmhsw['Tugas1'] += 0;
    $wmhsw['Tugas2'] += 0;
    $wmhsw['Tugas3'] += 0;
    $wmhsw['Tugas4'] += 0;
    $wmhsw['Tugas5'] += 0;
    $wmhsw['Presensi'] += 0;
    $wmhsw['UTS'] += 0;
    $wmhsw['UAS'] += 0;
    $wmhsw['Responsi'] += 0;
    $wmhsw['NilaiAkhir'] += 0;
    $wmhsw['BobotNilai'] += 0;
    $wmhsw['GradeNilai'] = (empty($wmhsw['GradeNilai']))? '-' : $wmhsw['GradeNilai'];
    fwrite($f, "$n=$wmhsw[MhswID],$wmhsw[Nama],$wmhsw[Tugas1],$wmhsw[Tugas2],$wmhsw[Tugas3],$wmhsw[Tugas4],$wmhsw[Tugas5],$wmhsw[Presensi],$wmhsw[UTS],$wmhsw[UAS],$wmhsw[NilaiAkhir],$wmhsw[Responsi],$wmhsw[GradeNilai],$wmhsw[BobotNilai]\r\n");
  }
  // Tuliskan TAG
  fwrite($f, "[TAG]\r\n");
  fwrite($f, "AUTHOR=EMANUEL SETIO DEWO\r\n");
  fwrite($f, "EMAIL=$_AuthorEmail\r\n");
  fwrite($f, "WEBSITE=$_AuthorWebsite\r\n");
  
  fclose($f);
  // download
  header("Content-type: application/vnd.text");
  header("Content-Disposition: attachment; filename=\"$jdwl[DosenID].$jdwl[MKKode].$jdwl[JadwalID].dat\"");
  header("Expires: 0");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  header("Pragma: public");
  readfile($nmf);
}

// *** Main ***
if (!empty($_REQUEST['gos'])) $_REQUEST['gos']();
?>
