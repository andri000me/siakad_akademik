<?php 
session_start();

// ********* Parameter
$gos 	= GetSetVar('gosx');
$PMBID 	= GetSetVar('PMBID');
$MhswID	= GetSetVar('MhswID');

// Variable SAV
$UkuranJaket = GetSetVar('UkuranJaket');
// Ready
$_gos 	= (empty($gos) ? "TampilkanCama":$gos);
$_gos();

// ===== GO
function TampilkanCama(){
TampilkanJudul("Pengukuran Jaket Mahasiswa");
// Function javascript
RandomStringScript();
echo <<<ESD
<script>
  <!--
  function CetakUkuranJaket(pmbid,aplid) {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].cetak.php?pmbid="+pmbid+"&aplid="+aplid+"&_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=600, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  //-->
  </script>
ESD;
echo "<a href='$_SESSION[mnux].xls.php' target='_blank'>Cetak XLS</a>";
echo '<table class="table table-striped datatable">
	<thead>
		  <tr>
		  	  <th>No.</th>
			  <th>ID</th>
			  <th>NPM</th>
			  <th>Nama</th>
			  <th>Tanggal Lahir</th>
              <th>Foto</th>
			  <th>Status</th>
			  <th>Proses</th>
			  <th>Cetak</th>                              
		  </tr>
  </thead>   
<tbody>';
	$s = "SELECT p.PMBID,p.MhswID,p.AplikanID,p.Nama,p.TanggalLahir,a.Foto,p.UkuranJaket from 
			pmb p left outer join aplikan a on a.AplikanID=p.AplikanID and p.PMBID=a.PMBID
					left outer join pmbperiod pp on pp.PMBPeriodID=p.PMBPeriodID
			where pp.NA='N' and p.LulusUjian='Y' and p.MhswID > 0";
	$r = _query($s);$no=0;
	while ($w = _fetch_array($r)){
		$no++;
		$labelattr = ($w['UkuranJaket']!=''? "label-success":"");
		$status = ($w['UkuranJaket']!=''? "Sudah":"Belum");
		$foto = "http://spmb.bunghatta.ac.id/foto_file/small_".$w['Foto'];
		echo "<tr>
					<td>".$no.".</td>
					<td><b>".$w['PMBID']."</b></td>
					<td>".$w['MhswID']."</td>
					<td>".$w['Nama']."</td>
					<td>".TanggalFormat($w['TanggalLahir'])."</td>
					<td><img src='".$foto."' width=50></td>
					<td><span class='label ".$labelattr."'>".$status."</span><br>Ukuran Jaket:
					<br><b>".$w['UkuranJaket']."</b>
					</td>
					<td><button class=\"btn btn-large btn-primary btn-round\" onclick=\"javascript:modalPopup('pmb/ajx/ukurjaket','Ukuran Jaket $w[Nama]','$w[PMBID]','','','')\" /><i class=\"icon icon-white icon-plus\"></i> Ukuran Jaket</button></td>
					<td><a href='#' onclick=\"javascript:CetakUkuranJaket('$w[PMBID]','$w[AplikanID]');\" ><img src='img/printer.gif' width=40></a></td>
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
  BerhasilSimpan("?mnux=$_SESSION[mnux]&gosx=&PMBID=$pmb[PMBID]", 500);
}

