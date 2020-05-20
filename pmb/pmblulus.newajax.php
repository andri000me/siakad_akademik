<?php 
error_reporting(0);
session_start();
//if ($_SESSION['_LevelID'] != 1) die();
// ********* Parameter
$gos 		= GetSetVar('gos');
$ProdiID 	= GetSetVar('ProdiID');
$pil 		= GetSetVar('pil');

if (empty($_REQUEST['ProdiID'])) echo "<script>window.location='?mnux=pmb/pmblap.statistik'</script>";

// Ready
$_gos 	= (empty($gos) ? "TampilkanCama":"TampilkanCama");
$_gos();

// ===== GO
function TampilkanCama(){
TampilkanJudul("Penentuan Kelulusan Calon Mahasiswa Reguler");
// Function javascript
?>
<script>
  <!--
  function PenentuanLulus(id,status,span) {
	reqid = status.value;
	$.ajax({
        	url: "pmb/ajx/penentuan.lulus.php",
	        type: 'GET',
			data:  "PMBID="+id+"&Status="+reqid,
			mimeType:"multipart/form-data",
			contentType: false,
    	    cache: false,
        	processData:false,
			success: function(data, textStatus, jqXHR)
		    {
					$(span).html(data);
				
		    },
		  	error: function(jqXHR, textStatus, errorThrown) 
	    	{
				$(span).html('terjadi kesalahan...');
	    	} 	        
	   });
	}
	function hitungGrade() {
	reqid = status.value;
	$.ajax({
        	url: "pmb/ajx/pmblulus.hitung.grade.php",
	        type: 'GET',
			mimeType:"multipart/form-data",
			contentType: false,
    	    cache: false,
        	processData:false,
			success: function(data, textStatus, jqXHR)
		    {
					//window.location='?';
				
		    }  
	   });
	}
	function luluskanSemua(prodi) {
	$.ajax({
        	url: "pmb/ajx/pmblulus.luluskanajax.php",
	        type: 'GET',
			data: "ProdiID="+prodi+"&stat=1", 
			mimeType:"multipart/form-data",
			contentType: false,
    	    cache: false,
        	processData:false,
			success: function(data, textStatus, jqXHR)
		    {
					alert("Sebanyak " + data + " calon mahasiswa sudah diluluskan.");
					window.close();
				
		    }  
	   });
	}
	function luluskanQuota(prodi) {
	$.ajax({
        	url: "pmb/ajx/pmblulus.luluskanajax.php",
	        type: 'GET',
			data: "ProdiID="+prodi+"&stat=0", 
			mimeType:"multipart/form-data",
			contentType: false,
    	    cache: false,
        	processData:false,
			success: function(data, textStatus, jqXHR)
		    {
					alert("Sebanyak " + data + " calon mahasiswa sudah diluluskan.");
					window.close();
				
		    }  
	   });
	}
	function cetakLampiran(){
		lnk = "<?php echo $_SESSION['mnux']?>.xls.php";
    	win2 = window.open(lnk, "", "width=500, height=600, scrollbars, status");
    	if (win2.opener == null) childWindow.opener = self;
	}
  //-->
  </script>
<input type="button" value="Cetak Lampiran Kelulusan" onclick="javascript:cetakLampiran()" />
<input type="button" value="Hitung Grade" onclick="javascript:hitungGrade()" /> <span id="info"></span>
<?php
		// Hitung Target dan berapa yang sudah lulus...
		$thn = GetaField('pmbperiod', "KodeID='".KodeID."' and NA", 'N', "PMBPeriodID");
		$target = GetaField('pmbtarget', "PMBPeriodID = '$thn' and ProdiID", $_SESSION['ProdiID'], "Target");
		$Lulus = GetaField('pmb lulus', "lulus.ProdiID='$_SESSION[ProdiID]' and lulus.PMBPeriodID='$thn' and lulus.LulusUjian", 'Y',"count(DISTINCT(lulus.PMBID))");
		$sisa = $target - $Lulus;
	if ($_SESSION['_LevelID'] == 1) {
	echo "<input type=\"button\" value=\"Proses Lulus Semua\" onclick=\"javascript:luluskanSemua('$_SESSION[ProdiID]')\" />";
	echo "<input type=\"button\" value=\"Proses Lulus Sesuai Quota\" onclick=\"javascript:luluskanQuota('$_SESSION[ProdiID]')\" />";
	}
echo '<table class="table table-striped datatable">
	<thead>
		  <tr>
		  	  <th>No.</th>
			  <th>ID</th>
			  <th>Nama</th>
			  <th>Nilai Rapor</th>
			  <th>Nilai UN</th>
			  <th>Grade</th>
			  <th>Jurusan</th>
			  <th>Proses Lulus</th>  
			  <th>Status</th>                          
		  </tr>
  </thead>   
<tbody>';
	$dt = substr(date('Y'),-2);
	$thn = GetaField('pmbperiod',"NA",'N', "PMBPeriodID");
	$whr_prodi = " and (Pilihan1='$_REQUEST[ProdiID]' or Pilihan2='$_REQUEST[ProdiID]')";
	$whr_nilai = ($_REQUEST['pil']=='0' ? " and NilaiUjian < 65 " : " and NilaiUjian >= 65");
	$s = "SELECT PMBID,MhswID,AplikanID,Nama,LulusUjian,NilaiUjian,
				Pilihan1, Pilihan2, JurusanSekolah  from 
			pmb
			where PMBPeriodID='$thn' and LulusUjian='N' 
			$whr_prodi group by PMBID";
	$r = _query($s);$no=0;
	while ($w = _fetch_array($r)){
		$no++;
		$w1 = GetFields('aplikan',"PMBID='$w[PMBID]' and AplikanID", $w['AplikanID'], "NilaiRapor,NilaiSekolah");
		$labelattr = ($w['LulusUjian']=='Y'? "label-success":"");
		
		$status = ($w['LulusUjian']=='Y'? $w['Pilihan1']:"Belum Lulus, Q:".$sisa);
		$w['_Pilihan1'] = GetaField('prodi',"ProdiID",$w['Pilihan1'], "Nama");
		$w['_Pilihan2'] = GetaField('prodi',"ProdiID",$w['Pilihan2'], "Nama");
		// Sisa quota pil 1
		$target1 = GetaField('pmbtarget', "PMBPeriodID = '$thn' and ProdiID", $w['Pilihan1'], "Target");
		$Lulus1 = GetaField('pmb lulus', "lulus.ProdiID='$w[Pilihan1]' and lulus.PMBPeriodID='$thn' and lulus.LulusUjian", 'Y',"count(DISTINCT(lulus.PMBID))");
		$sisa1 = $target1 - $Lulus1;
		// Sisa quota pil 2
		$target2 = GetaField('pmbtarget', "PMBPeriodID = '$thn' and ProdiID", $w['Pilihan2'], "Target");
		$Lulus2 = GetaField('pmb lulus', "lulus.ProdiID='$w[Pilihan2]' and lulus.PMBPeriodID='$thn' and lulus.LulusUjian", 'Y',"count(DISTINCT(lulus.PMBID))");
		$sisa2 = $target2 - $Lulus2 + 0;
		
		$opt = '<option></option>';
		$opt .= ($w['_Pilihan1'] !=''   ? "<option value='$w[Pilihan1]'>1. $w[_Pilihan1] $sisa1 </option>" : "");
		$opt .= ($w['_Pilihan2'] !=''   ? "<option value='$w[Pilihan2]'>2. $w[_Pilihan2] $sisa2 </option>" : "");
		$opt .= "<option value='N'>Tidak Lulus</option>";
		$w['_JurusanSekolah'] = GetaField('spmb.cama_sma_jurusan',"smajur_id",$w['JurusanSekolah'],"smajur_nama");
		echo "<tr>
					<td>".$no.".</td>
					<td><b>".$w['PMBID']."</b><hr>".$w['AplikanID']."</td>
					<td>".$w['Nama']."</td>
					<td>".$w1['NilaiRapor']."</td>
					<td>".$w1['NilaiSekolah']."</td>
					<td>".$w['NilaiUjian']."</td>
					<td>".(empty($w['_JurusanSekolah']) ? $w['JurusanSekolah']:$w['_JurusanSekolah'])."</td>
					<td><select name='Kelulusan' onchange=\"javascript:PenentuanLulus('$w[PMBID]',this, '#Status$w[PMBID]')\">$opt</select></td>
					<td><div id='Status$w[PMBID]'> <span class='label ".$labelattr."'>".$status."</span></div></td>
			  </tr>";

	}
?>
</tbody>
</table>
<?php } 



