<title>Cetak Calon Mahasiswa Jalur Undangan</title>
<?php
session_start();
	include_once "../dwo.lib.php";
  	include_once "../db.mysql.php";
  	include_once "../connectdb.php";
  	include_once "../parameter.php";
  	include_once "../cekparam.php";
$namafile = "daftar-cama-jalur-undangan-bidikmisi-$gelombang.xls";
header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename=$namafile");
header("Expires:0");
header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
header('Content-Transfer-Encoding: text'); 
header("Pragma: public");
echo "<style>th,td{font-family: 'Courier New'}</style>";
echo '<table border="1" cellpadding="2">
	<thead>
		  <tr>
		  	  <th rowspan="2">No.</th>
			  <th rowspan="2">NISN</th>
			  <th rowspan="2">Nama</th>
			  <th rowspan="2">Tempat/Tgl Lahir</th>
			  <th rowspan="2">Asal Sekolah</th>
              <th rowspan="2">Handphone</th>
			  <th rowspan="2">Pilihan1</th>
			  <th rowspan="2">Pilihan2</th>
			  <th rowspan="2">Status</th>
			  <th rowspan="2">Jalur</th>
			  <th colspan="2">Semester 1</th>
			  <th colspan="2">Semester 2</th>
			  <th colspan="2">Semester 3</th>
			  <th colspan="2">Semester 4</th>
			  <th colspan="2">Semester 5</th>
		  </tr>
		  <tr>
		  		<th>Nilai</th>
				<th>Peringkat</th>
				<th>Nilai</th>
				<th>Peringkat</th>
				<th>Nilai</th>
				<th>Peringkat</th>
				<th>Nilai</th>
				<th>Peringkat</th>
				<th>Nilai</th>
				<th>Peringkat</th>
		 	</tr>
  </thead>   
<tbody>';
	$year = date('Y');
	if ($_GET['id']==1) $whr = "AND c.NA='Y'"; // Sudah Lengkap
	elseif ($_GET['id']==2) $whr = "AND c.NA='N' AND Pilihan1 != ''"; // Belum lengkap tapi sudah ada pilihan
	elseif ($_GET['id']==3) $whr = "AND c.NA='N' AND Pilihan1 =''";
	$s = "SELECT c.*,if(c.AsalSekolahID='99999999',c.NamaSekolah,s.Nama) as Sekolah,j.namajurusan as Pilihans1, j2.namajurusan as Pilihans2,c.NamaSekolah from 
			ubh_undangan.camapmdk c 
			left outer join ubh_undangan.asalsekolah s on s.SekolahID=c.AsalSekolahID
			left outer join ubh_undangan.jurusan j on j.jurid = c.Pilihan1
			left outer join ubh_undangan.jurusan j2 on j2.jurid = c.Pilihan2
			where c.Tahun='$year' $whr order by s.Nama,c.Nama";
	$r = _query($s);$no=0;
	while ($w = _fetch_array($r)){
		$no++;
		$foto = "http://undangan.bunghatta.ac.id/foto_file/small_".$w['Foto'];
		$status = ($w['NA']=='Y'? "Lengkap":"Belum Lengkap");
		$rekomendasi = "http://undangan.bunghatta.ac.id/rekomendasi_file/".$w['RekomendasiSekolah'];
		echo "<tr>
					<td>".$no."</td>
					<td><b>".$w['NISN']."</b></td>
					<td>".strtoupper($w['Nama'])."</td>
					<td>".strtoupper($w['TempatLahir'].", ".TanggalFormat($w['TanggalLahir']))."</td>
					<td>".strtoupper($w['Sekolah'])."</td>
					<td>".$w['Handphone']."</td>
					<td>".$w['Pilihans1']."</td>
					<td>".$w['Pilihans2']."</td>
					<td>".$status."</td>
					<td>".$w['Jalur']."</td>
					<td>".$w['Nilai1']."</td>
					<td align='center'>".$w['Peringkat1']."</td>
					<td>".$w['Nilai2']."</td>
					<td align='center'>".$w['Peringkat2']."</td>
					<td>".$w['Nilai3']."</td>
					<td align='center'>".$w['Peringkat3']."</td>
					<td>".$w['Nilai4']."</td>
					<td align='center'>".$w['Peringkat4']."</td>
					<td>".$w['Nilai5']."</td>
					<td align='center'>".$w['Peringkat5']."</td>
			  </tr>";
	}
?>
</tbody>
</table>
