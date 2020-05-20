<?php 
include_once "../dwo.lib.php";
  	include_once "../db.mysql.php";
  	include_once "../connectdb.php";
  	include_once "../parameter.php";
  	include_once "../cekparam.php";
session_start();

// ********* Parameter
$gos 	= GetSetVar('gosx');

header("Content-type:application/vnd.ms-excel");
		header("Content-Disposition:attachment;filename=Daftar-Ukuran-Jaket-Mhsw.xls");
		header("Expires:0");
		header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
		header("Pragma: public");

echo '<table class="table table-striped datatable">
	<thead>
		  <tr>
		  	  <th>No.</th>
			  <th>ID</th>
			  <th>Fakultas</th>
			  <th>Prodi</th>
			  <th>NPM</th>
			  <th>Nama</th>
			  <th>Tanggal Lahir</th>
			  <th>Ukuran Jaket</th>
		  </tr>
  </thead>   
<tbody>';
	$s = "SELECT p.PMBID,p.MhswID,p.AplikanID,p.Nama,p.TanggalLahir,a.Foto,p.UkuranJaket, d.Nama as PRD, f.Nama as FK from 
			pmb p left outer join aplikan a on a.AplikanID=p.AplikanID and p.PMBID=a.PMBID
					left outer join pmbperiod pp on pp.PMBPeriodID=p.PMBPeriodID
					left outer join prodi d on d.ProdiID=p.ProdiID
					left outer join fakultas f on f.FakultasID=d.FakultasID
			where pp.NA='N' and p.LulusUjian='Y' and p.MhswID > 0 and p.UkuranJaket!='' order by f.FakultasID, p.ProdiID";
	$r = _query($s);$no=0;
	while ($w = _fetch_array($r)){
		$no++;
		$labelattr = ($w['UkuranJaket']!=''? "label-success":"");
		$status = ($w['UkuranJaket']!=''? "Sudah":"Belum");
		echo "<tr>
					<td>".$no.".</td>
					<td><b>".$w['PMBID']."</b></td>
					<td>".$w['FK']."</td>
					<td>".$w['PRD']."</td>					
					<td>'".$w['MhswID']."</td>
					<td>".$w['Nama']."</td>
					<td>".TanggalFormat($w['TanggalLahir'])."</td>
					<td><b>".$w['UkuranJaket']."</b>
					</td>
			  </tr>";
	}
?>
</tbody>
</table>
