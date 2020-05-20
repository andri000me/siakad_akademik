<?php
$TahunID = GetSetVar('_rapikanTahunID');
TampilkanJudul("Proses Sesi dan MaxSKS Mahasiswa");
	  $s = "select DISTINCT(TahunID) from tahun where KodeID='".KodeID."' order by TahunID DESC";
	  $r = _query($s);
	  $opttahun = "<option value=''></option>";
	  while($w = _fetch_array($r))
		{  $ck = ($w['TahunID'] == $_SESSION['_rapikanTahunID'])? "selected" : '';
		   $opttahun .=  "<option value='$w[TahunID]' $ck>$w[TahunID]</option>";
		}
	?>
    
    <form name='rapikan' action=?><table class="box"><tr><td class="inp">Tahun Akd:</td><td class="ul1"><select name="_rapikanTahunID"><?php echo $opttahun; ?></select></td><td class="ul1"><input type="submit" value="Proses" onClick="return confirm('Hitung ulang Sesi dan Maximum SKS Mahasiswa?')"></tr></table></form>
   <?php if (empty($_SESSION['_rapikanTahunID'])) echo "<center>Anda akan memproses data Semester & Maksimum SKS Mahasiswa secara massal.<br>
Tentukan dulu tahun akademik yang akan diproses.<br>
Hanya mahasiswa yang terdaftar di tahun akademik saja yang akan diproses.</center>"; ?>
    <?php
// Rapikan Sesi
if (!empty($TahunID)) {
	$m = "select MhswID from khs where TahunID='$TahunID' and MhswID in (1010018212010,1010018212061,1010018212066,1010018212070,1110018212011,1110018212037,1110018212046,1210018212090,1210018212095,1210018212096,1310018212015,1010018312033,1010018312074,1110018312037,1110018312039,1110018312043,1110018312045,1110018312054,1210018312004,1210018312011,1210018312027,1210018312028,1010018322007,1010018322010,1110018412004,1310018412012,1310018412013,1310018412022,1310018412023,1310018412027,1110018512002,1210018512004,1210018512005,1210018512009,1210018512010,1210018512014,1210018512018)";
	$n = _query($m);
	while ($o = _fetch_array($n)) {
		//$SKS2 = HitungSKSTranskrip($o['MhswID']);
		$ss = "SELECT * from krs where MhswID='".$o['MhswID']."' and TahunID <= '".$TahunID."' and TahunID not like '%Tra%' and TahunID !='' and BobotNilai > 0 ";
		$rr = _query($ss);
		$SKS =0;
		while ($ww = _fetch_array($rr)){
			$SKS += $ww['SKS'];
			$Bobot += ($ww['SKS']*$ww['BobotNilai']);
		}
		$IPK = $Bobot / $SKS;
		$IPK = number_format($IPK,2);
		//if ($SKS > 160) { $SKS = $SKS2; }
		$_sks = GetaField('krs',"TahunID='$TahunID' and MhswID",$o['MhswID'],"SUM(SKS)");
		$bobot = GetaField('krs',"TahunID='$TahunID' and MhswID",$o['MhswID'],"SUM(BobotNilai)");
		$IPS = $bobot / $_sks;
		$IPS = number_format($IPS,2);
		//$update = _query("UPDATE khs set TotalSKS='$SKS', SKS='$_sks', IP='$IPS' where MhswID='".$o['MhswID']."' and TahunID = '".$TahunID."'");
		$update = _query("UPDATE khs set IP='$IPK' where MhswID='".$o['MhswID']."' and TahunID = '".$TahunID."'");
	}
	$_SESSION['_rapikanTahunID']='';
	//end of Rapikan Sesi====================================**
	  BerhasilSimpan("?mnux=$_SESSION[mnux]", 5000); 
}
	?>