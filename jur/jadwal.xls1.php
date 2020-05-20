<?php
session_start();

  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";

// *** Parameters ***
$TahunID = GetSetVar('TahunID');
$ProdiID = GetSetVar('ProdiID');
$HariID = GetSetVar('HariID');
$ProgramID = GetSetVar('ProgramID');
$namafile = "rekap-jadwal-perruang.xls";
header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename=$namafile");
header("Expires:0");
header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
header("Pragma: public");

echo "<style>td{font-family:'Trebuchet MS';mso-number-format:'\@';}</style>";
// Buat header dulu
BuatHeader($TahunID);
// Tampilkan datanya
HeaderTabel();
AmbilJadwal($HariID);
// Buat footer
BuatFooter();


// *** Functions ***
function BuatFooter() {
  // Tanda tangan
  
}
function HeaderTabel($p) {
	echo "<Table border=1 width=1800><tr>";
  echo "<th>No</th>";
  echo "<th>Nama Dosen</th>";
  echo "<th>Hari</th>";
  echo "<th>Jam</th>";

 echo "<th>Kode MK</th>";
  echo "<th>Matakuliah</th>";
 echo "<th>SKS</th>";
  echo "<th>Kelas</th>";
 echo "<th>Ruang</th>
 		<th>Mhsw</th>
		<th>Prg</th>
 		<th>Jurusan</th>
		<th>Fakultas</th></tr>";
}
function AmbilJadwal($HariID, $TahunID, $p) {
  // Ambil Isinya
  $whr_prodi = "and j.ProdiID = '$_SESSION[ProdiID]'
      and j.ProgramID = '$_SESSION[ProgramID]'";
  if (!empty($HariID)) $whr = " And j.HariID='$HariID' ";
  $s = "select m.Nama as NamaKelasID, j.*, fk.Nama as Fakultas,
      j.Nama as MK,
      h.Nama as HR, k.KampusID, k.Nama as NamaKampus,
      LEFT(j.JamMulai, 5) as JM, LEFT(j.JamSelesai, 5) as JS,
      if (d.Nama is NULL or d.Nama = '', 'Belum diset', concat(d.Nama, ', ', d.Gelar)) as DSN,
      date_format(j.UASTanggal, '%d-%m-%Y') as _UASTanggal,
      date_format(j.UASTanggal, '%w') as _UASHari,
      huas.Nama as HRUAS,
	  if (j.JadwalRefID != 0,'(LAB)','') as _lab,
      LEFT(j.UASJamMulai, 5) as _UASJamMulai, LEFT(j.UASJamSelesai, 5) as _UASJamSelesai
    from jadwal j
      left outer join hari h on h.HariID = j.HariID
      left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
      left outer join hari huas on huas.HariID = date_format(j.UASTanggal, '%w')
      left outer join ruang r on r.RuangID = j.RuangID and r.KodeID = '".KodeID."'
      left outer join kampus k on k.KampusID = r.KampusID and k.KodeID = '".KodeID."'
	  left outer join kelas m on m.KelasID=j.NamaKelas
	  left outer join prodi pr on pr.ProdiID=j.ProdiID
	  left outer join fakultas fk on fk.FakultasID=pr.FakultasID
    where j.KodeID = '".KodeID."'
		$whr
		and j.TahunID = '$_SESSION[TahunID]'
    order by j.HariID, j.JamMulai";
  $r = _query($s);
  $n = 0; $_rg = 'asdijf;asldkjf';
  $t = 6;

  while ($w = _fetch_array($r)) {
    if ($_rg != $w['RuangID']) {
      $_rg = $w['RuangID'];
      //echo "$lbr, 8, 'Ruang: ' . $w['RuangID'] . ', Gedung: ' . $w['KampusID']";
      
    }
    $n++;

    echo "<tr><td>$n</td>";
	echo "<td>".$w['DSN']."</td>";
    echo "<td>$w[HR]</td>";
    echo '<td>'.$w['JM'] . '-' . $w['JS']."</td>";
    echo "<td>".$w['MKKode']."</td>";
    echo "<td>".$w['MK'].' '.$w[_lab]."</td>";
    echo "<td>".$w['SKS']."</td>";
    echo "<td>".$w['NamaKelasID']."</td>";
    echo "<td>".$w['RuangID']."</td>";
	echo "<td>".$w['JumlahMhsw']."</td>";
	echo "<td>".$w['ProgramID']."</td>";
	echo "<td>".$w['ProdiID']."</td>";
	echo "<td>".$w['Fakultas']."</td>";
  }
}
function BuatHeader($TahunID, $p) {
}
?>
