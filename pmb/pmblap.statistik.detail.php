<?php 
session_start();
	include_once "../dwo.lib.php";
  	include_once "../db.mysql.php";
  	include_once "../connectdb.php";
  	include_once "../parameter.php";
  	include_once "../cekparam.php";	
	
	// ********* Parameter
	$gos 		= GetSetVar('gos');
	$ProdiID 	= GetSetVar('ProdiID');
	$pil 		= GetSetVar('pil');
	
		header("Content-type:application/vnd.ms-excel");
		header("Content-Disposition:attachment;filename=Detail-Pelamar-$ProdiID-$TahunID.xls");
		header("Expires:0");
		header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
		header("Pragma: public");
 
?><style> 
	td, th, body{font-family:"Courier New", Courier, monospace; vertical-align:text-top}
	th{ background-color: #09F; color:#FFF;}
	td{  mso-number-format:"\@"; }
    a{text-decoration:none}
    a:hover{text-decoration:none}</style>
<?php





// ===== GO
echo '<table border=1>
	<thead>
		  <tr>
		  	  <th>No.</th>
			  <th>PIN</th>
			  <th>ID</th>
			  <th>Jalur</th>
			  <th>NPM</th>
			  <th>Nama</th>
			  <th>Kelamin</th>
			  <th>Tempat Lahir</th>
			  <th>Tanggal Lahir</th>
			  <th>Handphone</th>
			  <th>Propinsi</th>
			  <th>Kabupaten</th>
			  <th>Nama Sekolah</th>
			  <th>Jurusan</th>
			  <th>Grade</th>
			  <th>Gelombang</th>
			  <th>KD Prodi</th>
			  <th>Prodi</th>
			  <th>Status Awal</th>
			  <th>Pilihan1</th>
			  <th>Pilihan2</th>
			  <th>Ukuran Jaket</th>
		  </tr>
  </thead>   
<tbody>';
	$dt = substr(date('Y'),-2);
	$thn = GetSetVar('gel');
//echo $thn;
	if ($_REQUEST['ProdiID']){
		$whr_prodi = ($_REQUEST['pil']=='1' ? " and p.Pilihan1='$_REQUEST[ProdiID]'" : ($_REQUEST['pil']=='2' ? " and p.Pilihan2='$_REQUEST[ProdiID]'":  ($_REQUEST['pil']=='both' ? " AND (p.Pilihan1 = '$_REQUEST[ProdiID]') " : ($_REQUEST['pil']=='g1' ? "AND p.Hint='Gel1' and p.Pilihan1='$_REQUEST[ProdiID]' ": ($_REQUEST['pil']=='g2' ? "AND p.Hint='Gel2' and p.Pilihan1='$_REQUEST[ProdiID]'":($_REQUEST['pil']=='3' ? "AND p.Hint='Gel3' and p.Pilihan1='$_REQUEST[ProdiID]'": ($_REQUEST['pil']=='g4' ? "AND p.Hint='Gel4' and p.Pilihan1='$_REQUEST[ProdiID]'": ($_REQUEST['pil']=='g5' ? "AND p.Hint='Gel5' and p.Pilihan1='$_REQUEST[ProdiID]' ":" and (p.ProdiID='$_REQUEST[ProdiID]')")))))))) ;
	}else $whr_prodi='';
	$whr_nilai = ($_REQUEST['pil']=='1' || $_REQUEST['pil']=='both' ? "  " : "");
	$gel = $thn;
	//$where = ($_REQUEST['pil']=='all' ? "p.MhswID is not Null and p.MhswID > 0 and p.PMBPeriodID='$gel' order by p.ProdiID,p.MhswID" : "p.PMBPeriodID='$thn' and p.ProdiID!=''
		//	$whr_nilai $whr_prodi group by PMBID order by NilaiUjian DESC");
	$where = (empty($ProdiID) && $pil =='Lulus' ? "p.ProdiID!='' and p.PMBPeriodID like '$gel%'":"p.PMBPeriodID like '$gel%' $whr_prodi");
	$s = "SELECT p.PMBID, p.UkuranJaket, p.MhswID,p.AplikanID,p.Nama,p.LulusUjian,p.NilaiUjian, p.ProdiID,p.TempatLahir,p.TanggalLahir, p.Hint,p.NilaiUjian,
			p.Pilihan1, p.Pilihan2, p.Handphone, p.HandphoneOrtu, p.Kelamin, p.ProgramID, t.Nama as NamaStatus,m.StatusAwalID,pr.KodeLama  from 
			pmb p left outer join statusawal t on t.StatusAwalID=p.StatusAwalID
			left outer join mhsw m on m.MhswID=p.MhswID
			left outer join prodi pr on pr.ProdiID=p.ProdiID
			where $where order by p.ProdiID,m.MhswID";
	//die($s);
	$r = _query($s);$no=0;
	while ($w = _fetch_array($r)){
		$no++;
		$w1 = GetFields('aplikan',"PMBID='$w[PMBID]' and AplikanID", $w['AplikanID'], "NilaiRapor,NilaiSekolah,AsalSekolah,Jurusan,JurusanSekolah,Hint");
		$labelattr = ($w['LulusUjian']=='Y'? "label-success":"");
		$status = ($w['LulusUjian']=='Y'? $w['Pilihan1']:"Tidak Lulus");
		$w['_Pilihan1'] = GetaField('prodi',"ProdiID",$w['Pilihan1'], "Nama");
		$w['_Pilihan2'] = GetaField('prodi',"ProdiID",$w['Pilihan2'], "Nama");
		$sma = GetaField('asalsekolah',"SekolahID",$w1['AsalSekolah'], "Nama");
		$prop = GetaField('asalsekolah',"SekolahID",$w1['AsalSekolah'], "NamaPropinsi");
		$kota = GetaField('asalsekolah',"SekolahID",$w1['AsalSekolah'], "NamaKabupaten");
		$jurusan = GetaField('jurusansekolah',"JurusanSekolahID",$w1['Jurusan'], "Nama");
		$opt = '<option></option>';
		$opt .= ($w['_Pilihan1'] !='' ? "<option value='$w[Pilihan1]'>1. $w[_Pilihan1]</option>" : "");
		$opt .= ($w['_Pilihan2'] !='' ? "<option value='$w[Pilihan2]'>2. $w[_Pilihan2]</option>" : "");
		$opt .= "<option value='N'>Tidak Lulus</option>";
		$sdhLulus = ($w['ProdiID'] != "" ? "bgcolor=yellow" : "");
		$Jalur = substr($w['AplikanID'],0,2);
		$Jalur = ($Jalur == '16' || $Jalur=='CM')? "Reguler":"";
		$Jalur = ($w['StatusAwalID']=='S')? "BidikMisi":$Jalur;
		echo "<tr>
					<td>".$no.".</td>
					<td>".$w['AplikanID']."</td>
					<td><b>".$w['PMBID']."</b></td>
					<td>".$Jalur."</td>
					<td><b>".$w['MhswID']."</b></td>
					<td>".$w['Nama']."</td>
					<td>".$w['Kelamin']."</td>
					<td>".$w['TempatLahir']."</td>
					<td>".TanggalFormat($w['TanggalLahir'])."</td>
					<td>".$w['Handphone']."</td>
					<td>".$prop."</td>
					<td>".$kota."</td>
					<td>".$sma."</td>
					<td>".$w1['JurusanSekolah']."</td>
					<td>".$w['NilaiUjian']."</td>
					<td>".$w1['Hint']."</td>
					<td><b>".$w['KodeLama']."</b></td>
					<td><b>".$w['ProdiID']."</b></td>
					<td><b>".$w['NamaStatus']."</b></td>
					<td>$w[_Pilihan1]</td>
					<td>$w[_Pilihan2]</td>
					<td>$w[UkuranJaket]</td>
			  </tr>";

	}
?>
</tbody>
</table>
