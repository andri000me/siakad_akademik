<?php 
session_start();

// Ready
$_gos 	= (empty($gos) ? "TampilkanCama":$gos);
$_gos();

// ===== GO
function TampilkanCama(){
TampilkanJudul("Daftar Calon Mahasiswa Jalur Undangan");
echo "<p style='margin-bottom:20px'><button class='btn-primary' onclick=\"javascript:Undangan(1)\"><i class='icon-white icon icon-print'></i> Cetak yang Syarat Sudah Lengkap</button>
	<button class='btn-primary' onclick=\"javascript:Undangan(2)\"><i class='icon-white icon icon-print'></i> Cetak yang Syarat Belum Lengkap Sudah Ada Pilihan</button>
	<button class='btn-primary' onclick=\"javascript:Undangan(3)\"><i class='icon-white icon icon-print'></i> Cetak yang Syarat Belum Lengkap Belum Ada Pilihan</button>
	</p>";
echo '<table class="table table-striped datatable">
	<thead>
		  <tr>
		  	  <th>No.</th>
			  <th>NISN</th>
			  <th>Nama</th>
			  <th>Tempat/Tgl Lahir</th>
              <th>Handphone</th>
			  <th>Jalur</th>
			  <th>Pilihan</th>
			  <th>Foto</th>
			  <th>Status</th>
		  </tr>
  </thead>   
<tbody>';
	$year = date('Y');
	$s = "SELECT * from ubh_undangan.camapmdk where Tahun='$year' and NA='Y' order by NA";
	$r = _query($s);$no=0;
	while ($w = _fetch_array($r)){
		$no++;
		$w['Nama'] = str_replace("'","`",$w['Nama']);
		$labelattr = ($w['NA']=='Y'? "label-success":"");
		$status = ($w['NA']=='Y'? "Lengkap":"Belum");
		$foto = "http://undangan.bunghatta.ac.id/foto_file/small_".$w['Foto'];
		$rekomendasi = "http://undangan.bunghatta.ac.id/rekomendasi_file/".$w['RekomendasiSekolah'];
		$pilihan1 = GetaField('ubh_undangan.jurusan',"jurid",$w['Pilihan1'],"jur_singkatan");
		$pilihan2 = GetaField('ubh_undangan.jurusan',"jurid",$w['Pilihan2'],"jur_singkatan");
		$cek	= GetaField('pmb', "AplikanID", $w['NISN'],"AplikanID");
		$Password	= GetaField('aplikan', "AplikanID", $w['NISN'],"HashPassword");
		$prsLulus = (!empty($cek )) ? "Sudah Proses": "<input type='button' value='Import Data' onclick=\"javascript:pindahkePMB('$w[NISN]','$w[Nama]')\">";
		$Jalur = ($w['Jalur']=='BidikMisi') ? "Bidik Misi" : "Undangan" ;
		echo "<tr>
					<td>".$no.".</td>
					<td><b>".$w['NISN']."</b></td>
					<td>".$w['Nama']."</td>
					<td>".$w['TempatLahir']."<br>".TanggalFormat($w['TanggalLahir'])."</td>
					<td>".$w['Handphone']."</td>
					<td>".(empty($w['Jalur'])? "Prestasi":$w['Jalur'])."</td>
					<td>1. ".$pilihan1."<br>2. ".$pilihan2."</td>
					<td>".(empty($w['Foto'])? "":"<img src='".$foto."' width=100>")."</td>
					<td><span class='label ".$labelattr."'>".$status."</span><br>
					<br>
					<div id='StatusLulus$w[NISN]'>$Jalur<br>$prsLulus<br>Pass: $Password</div>
					</td>
			  </tr>";
	}
?>
</tbody>
</table>
<?php } 
function SAV(){
	$query = "UPDATE pmb set 
					UkuranJaket = '$_SESSION[UkuranJaket]'
				where PMBID='$_SESSION[PMBID]' and MhswID='$_SESSION[MhswID]'";
	$r = _query($query);
  //echo "<pre>$s</pre>";
  $gel = GetaField('aplikan','PMBID',$_SESSION['PMBID'],"PMBPeriodID");
  $AplikanID = GetaField('aplikan','PMBID',$_SESSION['PMBID'],"AplikanID");
  SetStatusAplikan2('JAK', $AplikanID, $gel);
  BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=&PMBID=$pmb[PMBID]", 500);
}
?>
<script>
 function Undangan(id) {
 	var jUndangan = window.open("pmb/pmbundangan.cetak.php?id="+id,'jUndangan');
	jUndangan.focus();
 }
 function pindahkePMB(nisn, nama){
 	$("#StatusLulus"+nisn).load("pmb/ajx/pmbundangan.import.pmb.php?nisn="+nisn);
 }
</script>