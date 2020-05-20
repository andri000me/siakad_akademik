<?php
session_start(); //error_reporting(E_ALL);

  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";

// *** Parameters ***
$TahunID = GetSetVar('TahunID');
$ProdiID = GetSetVar('ProdiID');
$ProgramID = GetSetVar('ProgramID');
$HariID = GetSetVar('HariID');

$thn = GetFields('tahun', "TahunID='$TahunID' and KodeID='".KodeID."' and ProdiID='$ProdiID' and ProgramID", $ProgramID, "*");


$namafile = "rekap-jadwal.xls";
header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename=rekap-jadwal");
header("Expires:0");
header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
header("Pragma: public");

echo "<style>td{font-family:'Trebuchet MS';mso-number-format:'\@';}</style>";
// Buat header dulu
BuatHeader($thn, $xls);
// Tampilkan datanya
AmbilJadwal($thn, $xls);
// Buat footer
BuatFooter($thn, $xls);

echo $xls;

// *** Functions ***
function BuatFooter($thn, $p) {
 
}
function AmbilJadwal($thn, $p) {
  // Buat headernya dulu
  $p .=  "<table width=800 cellpadding=2>
  			<tr>
				<th>No</th>
				<th>Hari</th>
				<th>Jam</th>
				<th>Kode MK</th>
				<th>Matakuliah</th>
				<th>SKS</th>
				<th>Dosen</th>
				<th>Kelas</th>
				<th>Ruangan</th>
				<th>Mhs</th>
			</tr>";

  // Parameters
  $whr_hari = ($_SESSION['_jdwlHari'] == '')? '' : "and j.HariID = '$_SESSION[_jdwlHari]' ";
  $whr_kelas = ($_SESSION['_jdwlKelas'] == '')? '' : "and j.NamaKelas = '$_SESSION[_jdwlKelas]' ";
  $whr_smt  = ($_SESSION['_jdwlSemester'] == '')? '' : "and mk.Sesi = '$_SESSION[_jdwlSemester]' ";
  $whr_Prodi = "and j.ProdiID = '$_SESSION[ProdiID]'
      and j.ProgramID = '$_SESSION[ProgramID]'";
  // Ambil Isinya
  $s = "select j.*,
      j.Nama as MK,
      h.Nama as HR, 
      LEFT(j.JamMulai, 5) as JM, LEFT(j.JamSelesai, 5) as JS,
      if (d.Nama is NULL or d.Nama = '', 'Belum diset', concat(d.Gelar1, ' ', d.Nama, ', ', d.Gelar)) as DSN,
      date_format(j.UASTanggal, '%d-%m-%Y') as _UASTanggal,
      date_format(j.UASTanggal, '%w') as _UASHari,
      huas.Nama as HRUAS,
	  if (j.JadwalRefID != 0,'(LAB)','') as _lab,
      LEFT(j.UASJamMulai, 5) as _UASJamMulai, LEFT(j.UASJamSelesai, 5) as _UASJamSelesai,
	  k.Nama AS namaKelas
    from jadwal j
      left outer join hari h on h.HariID = j.HariID
      left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
      left outer join hari huas on huas.HariID = date_format(j.UASTanggal, '%w')
      left outer join mk on mk.MKID = j.MKID 
	  LEFT OUTER JOIN kelas k ON k.KelasID = j.NamaKelas
    where j.KodeID = '".KodeID."'
      and j.TahunID = '$_SESSION[TahunID]'
      $whr_hari $whr_kelas $whr_smt
    order by j.ProgramID, j.HariID, j.JamMulai";
  $r = _query($s);
  //die("<pre>$s</pre>");
  $n = 0; $_h = 'akjsdfh'; $_p = 'la;skdjfadshg';

  while ($w = _fetch_array($r)) {
    $n++;
		$s2= "select count(k.MhswID) as MKini from krs k, khs h where
			h.MhswID=k.MhswID And 
			h.TahunID=k.TahunID And 
			k.JadwalID=$w[JadwalID] And
			(h.Bayar>0 or h.Potongan>0)
			";
	
	$r2 = _query($s2);
	  while ($w2 = _fetch_array($r2)) {
	  $jMhsw=$w2['MKini'];
	  }
	  $s2= "select count(k.MhswID) as MKini from krs k, khs h where
			h.MhswID=k.MhswID And 
			h.TahunID=k.TahunID And 
			k.JadwalID=$w[JadwalID]
			";
	
	$r2 = _query($s2);
	  while ($w2 = _fetch_array($r2)) {
	  $jMhswKRS=$w2['MKini'];
	  }
    /*
    if ($_p != $w['ProgramID']) {
      $_p = $w['ProgramID'];
      $_prg = GetaField('program', "KodeID='".KodeID."' and ProgramID", $_p, 'Nama');
      $p->SetFont('Helvetica', 'B', 10);
      $p->Cell(190, $t, $_prg, 1, 1, 'C');
    }
    */
    //if ($_h != $w['HR']) {
    //  $_h = $w['HR'];
      $hr = $w['HR'];
    //} else $hr = '-';

	$p .="<tr>	<td>".$n."</td>
				<td>".$hr."</td>
				<td>".$w['JM']."</td>
				<td>".$w['MKKode']."</td>
				<td>".$w['MK'].' '.$w['_lab']."</td>
				<td>".$w['SKS']."</td>
				<td>".$w['DSN']."</td>
				<td>".$w['namaKelas']."</td>
				<td>".$w['RuangID']."</td>
				<td>".$jMhswKRS.'/'. $jMhsw."</td>
			</tr>";
   
  }
}
function BuatHeader($thn, $p) {
  $p->SetFont('Helvetica', 'B', 10);
  
  $prodi = GetaField('prodi', "KodeID='".KodeID."' and ProdiID", $thn['ProdiID'], 'Nama');
  $prg   = GetaField('program', "KodeID='".KodeID."' and ProgramID", $thn['ProgramID'], 'Nama');
  $jenjang = GetFields('jenjang j,prodi p',"j.JenjangID=p.JenjangID AND p.ProdiID",$thn['ProdiID'],'j.Nama');
  //Header
  $p .= "Thn Akd.: " . $thn['Nama'];
  $p .= "Prg Studi: " . $prodi . " " . $jenjang['Nama'];
  $p .= "Prg Pendidikan: " . $prg;

  // Filter
  $hari = ($_SESSION['_jdwlHari'] == '')? '(Semua)' : GetaField('hari', 'HariID', $_SESSION['_jdwlHari'], 'Nama');
  $kelas = GetaField('kelas',"KelasID",$_SESSION['_jdwlKelas'],'Nama');
  $smt = ($_SESSION['_jdwlSemester'] == '')? '(Semua)' : $_SESSION['_jdwlSemester'];
  $p .=  "Hari: ". $hari;
  $p .=  "Kelas: $kelas";
  $p .=  "Semester: $smt";
}
function HeaderLogo($jdl, $p, $orientation='P') {

}
?>
