<?php 
session_start();

// ********* Parameter
$gos 	= GetSetVar('gos');

// Ready
$_gos 	= (empty($gos) ? "TampilkanCama":$gos);
$_gos();

// ===== GO
function TampilkanCama(){
TampilkanJudul("Penentuan Kelulusan Calon Mahasiswa Undangan");
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
	function cetakLampiran(){
		lnk = "<?php echo $_SESSION['mnux']?>.xls.php";
    	win2 = window.open(lnk, "", "width=500, height=600, scrollbars, status");
    	if (win2.opener == null) childWindow.opener = self;
	}
  //-->
  </script>
<input type="button" value="Cetak Lampiran Kelulusan" onclick="javascript:cetakLampiran()" />
<?php
echo '<table class="table table-striped datatable">
	<thead>
		  <tr>
		  	  <th>No.</th>
			  <th>ID</th>
			  <th>Nama</th>
			  <th>Tanggal Lahir</th>
              <th>Foto</th>
			  <th>Nilai Rapor</th>
			  <th>Nilai UN</th>
			  <th>Grade</th>
			  <th>Proses Lulus</th>  
			  <th>Status</th>                          
		  </tr>
  </thead>   
<tbody>';
	$dt = substr(date('Y'),-2);
	$s = "SELECT p.PMBID,p.MhswID,p.AplikanID,p.Nama,p.TanggalLahir,a.Foto,p.UkuranJaket,p.LulusUjian,a.NilaiRapor,a.NilaiSekolah, p.NilaiUjian,
				pr1.Nama as _Pilihan1, pr2.Nama as _Pilihan2, p.Pilihan1, p.Pilihan2  from 
			pmb p left outer join aplikan a on a.AplikanID=p.AplikanID and p.PMBID=a.PMBID
					left outer join pmbperiod pp on pp.PMBPeriodID=p.PMBPeriodID
					left outer join prodi pr1 on pr1.ProdiID = p.Pilihan1
					left outer join prodi pr2 on pr2.ProdiID = p.Pilihan2
			where pp.NA='N' and p.UangKesehatan!='Y' and p.AplikanID not like '$dt%'";
	$r = _query($s);$no=0;
	while ($w = _fetch_array($r)){
		$no++;
		$labelattr = ($w['LulusUjian']=='Y'? "label-success":"");
		$status = ($w['LulusUjian']=='Y'? $w['Pilihan1']:"Tidak Lulus");
		$foto = "http://spmb.bunghatta.ac.id/foto_file/small_".$w['Foto'];
		$opt = '<option></option>';
		$opt .= ($w['_Pilihan1'] !='' ? "<option value='$w[Pilihan1]'>1. $w[_Pilihan1]</option>" : "");
		$opt .= ($w['_Pilihan2'] !='' ? "<option value='$w[Pilihan2]'>2. $w[_Pilihan2]</option>" : "");
		$opt .= "<option value='N'>Tidak Lulus</option>";
		echo "<tr>
					<td>".$no.".</td>
					<td><b>".$w['PMBID']."</b></td>
					<td>".$w['Nama']."</td>
					<td>".TanggalFormat($w['TanggalLahir'])."</td>
					<td>";//<img src='".$foto."' width=50>
					echo "</td>
					<td>".$w['NilaiRapor']."</td>
					<td>".$w['NilaiSekolah']."</td>
					<td>".$w['NilaiUjian']."</td>
					<td><select name='Kelulusan' onchange=\"javascript:PenentuanLulus('$w[PMBID]',this, '#Status$w[PMBID]')\">$opt</select></td>
					<td><div id='Status$w[PMBID]'> <span class='label ".$labelattr."'>".$status."</span></div></td>
			  </tr>";
	}
?>
</tbody>
</table>
<?php } 



