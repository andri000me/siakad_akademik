<?php
session_start();

  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  include_once "../header_pdf.php";
  header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename=absen-mhsw.xls");
header("Expires:0");
header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
header("Pragma: public");
?><style>
td{
  mso-number-format:"\@";/*force text*/
}</style>
<?php
  CetakNilai($id);



// *** Functions ***
function CetakNilai($jid) {
  $JadwalID = GetSetVar('JadwalID');
  $jdwl = GetFields("jadwal j
    left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
    left outer join prodi prd on prd.ProdiID = j.ProdiID and prd.KodeID = '".KodeID."'
    left outer join program prg on prg.ProgramID = j.ProgramID and prg.KodeID = '".KodeID."'
    left outer join mk mk on mk.MKID = j.MKID
    left outer join hari huas on huas.HariID = date_format(j.UASTanggal, '%w') 
	LEFT OUTER JOIN kelas k ON k.KelasID = j.NamaKelas
    ",
    "j.JadwalID", $JadwalID,
    "j.*, concat(d.Nama, ', ', d.Gelar) as DSN, d.NIDN,
    prd.Nama as _PRD, prg.Nama as _PRG,
    mk.Sesi,
    date_format(j.UASTanggal, '%d-%m-%Y') as _UASTanggal,
    date_format(j.UASTanggal, '%w') as _UASHari,
    huas.Nama as HRUAS,
    LEFT(j.UASJamMulai, 5) as _UASJamMulai, LEFT(j.UASJamSelesai, 5) as _UASJamSelesai, k.Nama AS namaKelas
    ");
  $TahunID = $jdwl['TahunID'];
  $thn = GetFields('tahun', "KodeID = '".KodeID."' and ProdiID = '$jdwl[ProdiID]' and ProgramID = '$jdwl[ProgramID]' and TahunID", $TahunID, "*");
  // Buat Header
  BuatHeader($jdwl, $thn);
  BuatIsinya($jdwl);
}
function BuatIsinya($jdwl) {
  BuatHeaderTabel();
  $s = "select k.*, m.Nama as NamaMhsw
    from krs k
      left outer join mhsw m on k.MhswID = m.MhswID and m.KodeID = '".KodeID."'
    where k.JadwalID = '$jdwl[JadwalID]'
    order by m.MhswID";
  $r = _query($s);
  $n = 0;
  while ($w = _fetch_array($r)) {
    $n++;
	echo "<tr>
  		<td>$n</td>
		<td>$w[MhswID]</td>
		<td>$w[NamaMhsw]</td>
	</tr>";
  }
}
function BuatHeaderTabel() {
  echo "<table border=1 cellpadding='0' cellspacing='0'>
  <tr>
  		<th width=30>No.</th>
		<th width=120>NPM</th>
		<th width=350>Nama</th>
	</tr>";
}
function BuatHeader($jdwl, $thn) {
  echo "<h4>Mata Kuliah: ".$jdwl['MKKode'] . '   ' . $jdwl['Nama']."</h4>";
  echo "<h4>Program Studi: ".$jdwl['_PRD'] . ' ('. $jdwl['_PRG'].')'."</h4>";
}