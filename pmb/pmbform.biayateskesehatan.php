<?php 
session_start();

// ********* Parameter
$gos 		= GetSetVar('gosx');
$PMBID 		= GetSetVar('PMBID');
$AplikanID	= GetSetVar('AplikanID');
$cetak	= $_POST['cetak'];

// Variable SAV
$UangKesehatan = GetSetVar('UangKesehatan');
$Jumlah = GetSetVar('Jumlah');

// Ready
$_gos 	= (empty($gos) ? "TampilkanCama2":$gos);
$_gos();


// ===== GO
function TampilkanCama2(){
TampilkanJudul("Pembayaran Uang Tes Kesehatan Calon Mahasiswa");
// Function javascript
RandomStringScript();
echo <<<ESD
<script>
  <!--
  function BuktiBayarUangKesehatan(bayarid) {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].cetak.php?bayarid="+bayarid+"&_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=600, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  //-->
  </script>
ESD;
if ($_SESSION['UangKesehatan']=='Y' && !isset($_POST['PMBID'])) { 
		$cek = GetaField('pmbklinikbayar',"AplikanID='$_SESSION[AplikanID]' and PMBID", $_SESSION['PMBID'],"PMBKlinikBayarID");
		?><script>BuktiBayarUangKesehatan('<?php echo $cek ?>')</script><?php
	}
echo '<form method=post action=? style="margin:20px 0 20px 20px;"> Filter Nama/No. Pendaftaran <input type="text" name="PMBID" placeholder="No. Pendaftaran" value="'.$_SESSION['PMBID'].'"> <input type="submit" value="Cari"></form>';
echo '<table class="table table-striped datatable">
	<thead>
		  <tr>
		  	  <th>No.</th>
			  <th>ID Maru</th>
			  <th>Nomor Pendaftaran</th>
			  <th>Nama</th>
			  <th>Tanggal Lahir</th>
			  <th>Status</th>
			  <th>Proses</th>
			  <th>Cetak</th>                              
		  </tr>
  </thead>   
<tbody>';
	$s = "SELECT p.PMBID,p.AplikanID,p.Nama,p.TanggalLahir,a.Foto,p.UangKesehatan,pb.PMBKlinikBayarID,p.StatusAwalID,s.Nama as SA from 
			pmb p left outer join aplikan a on a.AplikanID=p.AplikanID and p.PMBID=a.PMBID
					left outer join pmbperiod pp on pp.PMBPeriodID=p.PMBPeriodID
                    left outer join pmbklinikbayar pb on pb.AplikanID=p.AplikanID and pb.PMBID=p.PMBID
                    left outer join statusawal s on s.StatusAwalID=p.StatusAwalID
			where pp.NA='N' and p.LulusUjian='Y' and p.LulusTesKesehatan != 'Y' and (p.PMBID like '%$_SESSION[PMBID]%' or p.Nama like '%$_SESSION[PMBID]%' or p.AplikanID like '%$_SESSION[PMBID]%')  ";
	$r = _query($s);$no=0;
	while ($w = _fetch_array($r)){
		$no++;
        $ada = GetaField('pmbklinikbayar',"AplikanID='$w[AplikanID]' AND Jumlah > 0 AND PMBID", $w['PMBID'],"PMBID");
		$labelattr = (!empty($ada)? "label-success":"");
		$status = (!empty($ada)? "Sudah":"Belum");
		$foto = "http://spmb.bunghatta.ac.id/foto_file/small_".$w['Foto'];
		$w['Nama'] = str_replace("'","",$w['Nama']); 
        $print = (!empty($ada)? "<a href='#' onclick=\"javascript:BuktiBayarUangKesehatan('$w[PMBKlinikBayarID]');\" ><img src='img/printer.gif' width=40></a>":"Belum Bayar");
		echo "<tr>
					<td>".$no.".</td>
					<td><b>".$w['PMBID']."</b></td>
					<td>".$w['AplikanID']."</td>
					<td>".$w['Nama']."</td>
					<td>".TanggalFormat($w['TanggalLahir'])."</td>
					<td><h4>$w[SA]</h4><span class='label ".$labelattr."'>".$status."</span>
					</td>
					<td><button class=\"btn btn-large btn-primary btn-round\" onclick=\"javascript:modalPopup('pmb/ajx/biayateskesehatan','Uang Tes Kesehatan','$w[PMBID]','','','')\" /><i class=\"icon icon-white icon-plus\"></i> Rekam Bayar</button></td>
					<td>$print</td>
			  </tr>";
	}
?>
</tbody>
</table>
<?php 

} 
	

function SAV(){
	$cek = GetaField('pmbklinikbayar',"AplikanID='$_SESSION[AplikanID]' and PMBID", $_SESSION['PMBID'],"PMBKlinikBayarID");
	$pmb = GetFields('pmb', "AplikanID='$_SESSION[AplikanID]' and PMBID", $_SESSION['PMBID'],"*");
	if ($_SESSION['UangKesehatan']=='Y') {
		$query = "UPDATE pmb set 
					UangKesehatan = '$_SESSION[UangKesehatan]'
				where PMBID='$_SESSION[PMBID]' and AplikanID='$_SESSION[AplikanID]'";
		$r = _query($query);
	if (empty($cek)) {
		$query = "INSERT into pmbklinikbayar(PMBID,AplikanID,KodeID,ProdiID,Tanggal, Jumlah, OK,
												PMBPeriodID,Nama, LoginBuat,TanggalBuat)
							values('$_SESSION[PMBID]','$_SESSION[AplikanID]','".KodeID."','$pmb[ProdiID]',now(), '$_SESSION[Jumlah]', '$_SESSION[OK]',
												'$pmb[PMBPeriodID]','".sqling($pmb['Nama'])."', '$_SESSION[_Login]',now())";
	}
	else
	{ $query = "UPDATE pmbklinikbayar set OK = '$_SESSION[UangKesehatan]', Jumlah = '$_SESSION[Jumlah]',
											LoginEdit = '$_SESSION[_Login]', TanggalEdit = now()
											 where PMBID='$_SESSION[PMBID]' and AplikanID='$_SESSION[AplikanID]'";
	}
	$r = _query($query);
	$gel = GetaField('aplikan','AplikanID',$_SESSION['AplikanID'],"PMBPeriodID");
  	SetStatusAplikan2('UK', $_SESSION['AplikanID'], $gel);
	}
  //echo "<pre>$s</pre>";
  BerhasilSimpan("?mnux=$_SESSION[mnux]&gosx=&PMBID=$pmb[PMBID]", 4000);
}


?>
