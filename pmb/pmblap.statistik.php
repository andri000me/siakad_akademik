<?php

// *** Parameters ***
$_thn = date('Y').'1';
$gel = GetSetVar('gel',$_thn);

$tahun = substr($gel, 0, 4);
$prevtahun = $tahun-1;

$gos = (empty($_REQUEST['gos']))? 'BuatStatistik' : $_REQUEST['gos'];
$gos($tahun,$gel);

function BuatStatistik($tahun, $gel){
BuatHeader($tahun);
BuatRekapAll($gel);
BuatRekapGel1($gel);
BuatRekapGel2($gel);
BuatRekapGel3($gel);
BuatRekapPIN($gel);
BuatRekapPerProdi($gel);
}

function BuatHeader($tahun){
	TampilkanJudul("Rekap Penerimaan Mahasiswa Baru Tahun $tahun");
}
function BuatRekapAll($gel){
	$_thn = date('Y').'1';
	$thnlalu = (date('Y')-1).'1';
	$thnb1 = (date('Y')-1);
	$thnb2 = (date('Y')-2);
	$today = "$thnb1-".date('m-d H:i:s');

	echo "<div><form method='post' Target=?>
	<select name='gel' placeholder='Pilih Tahun' onchange=\"this.form.submit()\"><option value=''></option><option value='$_thn'>Tahun ini</option><option value='".$thnb1."1'>$thnb1</option><option value='".$thnb2."1'>$thnb2</option></select>
	</form></div>";
	 // data
	$reg = "and PMBFormulirID in (1)";
	$pasca = "and PMBFormulirID in (12,15,16)";
	 $pin		= GetaField('aplikan', "StatusAplikanID!='APL' and PMBPeriodID='$gel' $reg and KodeID", KodeID, "Count(AplikanID)");
	 $pinpasca	= GetaField('aplikan', "StatusAplikanID!='APL' and PMBPeriodID='$gel' $pasca and KodeID", KodeID, "Count(AplikanID)");
	 $pinlalu	= GetaField('aplikan', "StatusAplikanID!='APL' and PMBPeriodID='$thnlalu' and TanggalBuat < '$today' $reg and KodeID", KodeID, "Count(AplikanID)");
	 //$undangan	= GetaField('pmbformjual', "NA='N' and PMBPeriodID='$gel' and PMBFormulirID='13' and KodeID", KodeID, "Count(PMBFormJualID)");
	 echo "<center>Note: Tahun lalu sampai dengan $today</center>";
	 $thn = date('Y');
	 $lengkap	= GetaField('aplikan', "CetakBukti='Y' and PMBPeriodID='$gel' $reg and KodeID", KodeID, "Count(AplikanID)");
	 $Lulus		= GetaField('pmb', "LulusUjian='Y' and PMBPeriodID='$gel' $reg and KodeID", KodeID, "Count(AplikanID)");
	 $klnByr	= GetaField('pmbklinikbayar', "PMBPeriodID='$gel' and KodeID", KodeID, "Count(Distinct(PMBID))");
	 $klnByrlalu= GetaField('pmbklinikbayar', "PMBPeriodID='$thnlalu' and TanggalBuat < '$today' and KodeID", KodeID, "Count(Distinct(PMBID))");
	 $klnTes	= GetaField('pmbkliniktes', "PMBPeriodID='$gel' and KodeID", KodeID, "Count(Distinct(PMBID))");
	 $klnTeslalu= GetaField('pmbkliniktes', "PMBPeriodID='$thnlalu' and TanggalBuat < '$today' and KodeID", KodeID, "Count(Distinct(PMBID))");
	 $dftUlang	= GetaField('pmb', "TotalBayar>0  and PMBPeriodID='$gel' $reg and KodeID", KodeID, "Count(distinct(AplikanID))");
	 $Terdaftar	= GetaField('pmb', "MhswID is not Null and MhswID > 0 and PMBPeriodID='$gel' $reg and KodeID", KodeID, "Count(AplikanID)");
	 $Terdaftar2= GetaField('mhsw m, pmb p', "m.SemesterAwal='$thnlalu' and m.TanggalBuat <= '$today' and p.PMBFormulirID in (1) and p.PMBID=m.PMBID and m.KodeID", KodeID, "Count(m.MhswID)");
	 $peminat 	= $pin + $undangan + 0 ;
	echo "<table border = 1 align=center>
			<tr>
				<th colspan='3' class='ttl'>Reguler</th>
				<th class='ttl' rowspan='2'>&sum; Peminat</th>
				<th class='ttl' rowspan='2'>Lulus Seleksi</th>
				<th rowspan=2 class='ttl'>Daftar Ulang<br>Bayar U.Kuliah</th>
				<th rowspan=2 class='ttl'>Terdaftar<br>(NPM)</th>
			</tr>
			<tr>
				<th class='ttl' colspan=2>Beli PIN</th>
				<th class='ttl'>Syarat Online Lengkap</th>
			</tr>
			<tr>
				<td align=center>Tahun Lalu: $pinlalu</td><td><h5>Sekarang: $pin (S1) | $pinpasca (S2)</h5></td>
				<td align=center>$lengkap</td>
				<td align=center>$peminat</td>
				<td align=center><a href=# onclick=\"javascript:penentuanLulus('','Lulus')\"><h5>$Lulus</h5></a></td>
				<td align=center>$dftUlang</td>
				<td align=center><small>$Terdaftar2</small>/<b><a style='color:darkred' href=# onclick=\"javascript:penentuanLulus('$w[ProdiID]','all')\">$Terdaftar</a></td>
			</tr>
			</table>
			<center><a href='#' onclick=\"javascript:window.location = 'pmb/pmblap.statistik.xls.php'\">Download <img src='img/excel.png'></a> </center>";
}
function BuatRekapGel1($gel){}
function BuatRekapGel2($gel){}
function BuatRekapGel3($gel){}
function BuatRekapPIN($gel){}
function BuatRekapPerProdi($gel){
	echo "<style>td a{color:#FFF}</style><table border = 1 align=center width=200%>
			<tr>
				<th class='ttl'>No</th>
				<th class='ttl'>Program Studi</th>
				<th class='ttl'>Strata</th>
				<th class='ttl'>Target</th>
				<th class='ttl'>Pilihan1</th>
				<th class='ttl'>Grade &uarr;</th>
				<th class='ttl'>Pilihan2</th>
				<th class='ttl'>Grade &uarr;</th>
				<th class='ttl'>Jml. Peminat</th>
				<th class='ttl'>Jml Grade &uarr;</th>
				<th class='ttl'>Jml Grade &darr;</th>
				<th class='ttl'>% (Target/Peminat)</th>
				<th class='ttl'>% (Target/Grade)</th>
				<th class='ttl'>Lulus</th>
				<th class='ttl'>% (Target/Lulus)</th>
				<th class='ttl'>% (Pelamar/Lulus)</th>
				<th class='ttl'>Bayar Daftar Ulang</th>
				<th class='ttl'>% (Target/Bayar)</th>
				<th class='ttl'>% (Lulus/Bayar)</th>
				<th class='ttl'>Daftar Ulang (Mhs)</th>
				<th class='ttl'>% (Target/Daftar Ulang)</th>
				<th class='ttl'>% (Lulus/Daftar Ulang)</th>
				<th class='ttl'>Belum Daftar Ulang</th>
				<th class='ttl'>% (Target/Blm Daftar Ulang)</th>
				<th class='ttl'>% (Lulus/Blm Daftar Ulang)</th>
			</tr>";
/*$s = "SELECT p.Nama as Prodi, p.ProdiID, j.Nama	as Jenjang, t.Target, 
		count(distinct(pel.AplikanID)) as Pelamar,
		count(DISTINCT(lulus.PMBID)) as Lulus,
		count(DISTINCT(bayar.PMBID)) as Bayar,
		count(DISTINCT(tes.PMBID)) as Kesehatan,
		count(DISTINCT(daftar.PMBID)) as Daftar
		 from prodi p 
			left outer join jenjang j on j.JenjangID=p.JenjangID
			left outer join pmbtarget t on t.ProdiID=p.ProdiID and t.PMBPeriodID = '$gel'
			left outer join aplikan pel on (pel.Pilihan1 = p.ProdiID or pel.Pilihan2 = p.ProdiID) and pel.PMBPeriodID = '$gel'
			left outer join pmbkliniktes tes on tes.ProdiID = p.ProdiID and tes.PMBPeriodID = '$gel'
			left outer join pmb lulus on lulus.ProdiID=p.ProdiID and lulus.PMBPeriodID='$gel' and lulus.LulusUjian = 'Y'
			left outer join pmb bayar on bayar.ProdiID=p.ProdiID and bayar.PMBPeriodID='$gel' and bayar.LulusUjian = 'Y' and bayar.TotalBiaya > 0 and bayar.TotalBiaya=bayar.TotalBayar
			left outer join pmb daftar on daftar.ProdiID=p.ProdiID and daftar.PMBPeriodID='$gel' and daftar.LulusUjian = 'Y' and daftar.TotalBiaya > 0 and daftar.TotalBiaya=daftar.TotalBayar and daftar.MhswID > 0
			where p.NA='N' and p.FakultasID !='08' group by p.ProdiID order by p.FakultasID, p.Nama"; */

$sf = "SELECT * from fakultas where KodeID='".KodeID."' order by FakultasID";
$rf = _query($sf);
while ($wf = _fetch_array($rf)) {
	echo "<tr>
			<td colspan=21><h3>$wf[Nama]</h3></td></tr>";

$s1 = "SELECT p.Nama as Prodi, p.ProdiID, j.Nama	as Jenjang, t.Target from prodi p
		left outer join pmbtarget t on t.ProdiID=p.ProdiID and t.PMBPeriodID = '$gel'
		left outer join jenjang j on j.JenjangID=p.JenjangID
		 where p.NA='N' and p.KodeID='".KodeID."' and p.FakultasID='$wf[FakultasID]'";
$r = _query($s1); $no = 0;
	while ($w = _fetch_array($r)) {
		// ** Parameter
		$w['Pilihan1'] = GetaField('aplikan',"Pilihan1 = '$w[ProdiID]' and PMBPeriodID", $gel, "count(distinct(AplikanID))");
		$w['Pilihan2'] = GetaField('aplikan',"Pilihan2 = '$w[ProdiID]' and Pilihan1 != '$w[ProdiID]' and PMBPeriodID", $gel, "count(distinct(AplikanID))");
		$w['Pelamar'] = $w['Pilihan1'] + $w['Pilihan2'];
		$w['Lulus'] = GetaField('pmb lulus', "lulus.ProdiID='$w[ProdiID]' and lulus.PMBPeriodID='$gel' and lulus.LulusUjian", 'Y',"count(DISTINCT(lulus.PMBID))");
		$w['Bayar'] = GetaField('pmb bayar', "bayar.PMBPeriodID='$gel' and bayar.LulusUjian = 'Y' and bayar.TotalBayar > 0 and bayar.ProdiID", $w['ProdiID'], "count(DISTINCT(bayar.PMBID))");
		$w['Kesehatan'] = GetaField('pmbkliniktes tes', "tes.ProdiID = '$w[ProdiID]' and tes.PMBPeriodID",$gel,"count(DISTINCT(tes.PMBID))");
		$w['Daftar'] = GetaField('pmb daftar', "daftar.PMBPeriodID='$gel' and daftar.LulusUjian = 'Y' and daftar.MhswID > 0 and daftar.ProdiID", $w['ProdiID'],"count(DISTINCT(daftar.PMBID))");
		$LulusPassingGrade1 = GetaField('pmb', "Pilihan1='$w[ProdiID]' and NilaiUjian >= 65 and PMBPeriodID", $gel,"count(DISTINCT(PMBID))");
		$LulusPassingGrade2 = GetaField('pmb', "Pilihan2='$w[ProdiID]' and Pilihan1 != '$w[ProdiID]' and NilaiUjian >= 65 and PMBPeriodID", $gel,"count(DISTINCT(PMBID))");
		$JmlLulusPassingGrade = $LulusPassingGrade1 + $LulusPassingGrade2 + 0 ;
		$TdkLulusPassingGrade = GetaField('pmb', "(Pilihan2='$w[ProdiID]' or Pilihan1 = '$w[ProdiID]') and NilaiUjian < 65 and PMBPeriodID", $gel,"count(DISTINCT(PMBID))");
		
		$TargetPelamar = ($w['Pilihan1']/$w['Target']) * 100;
		$TargetGrade = ($LulusPassingGrade1/$w['Target']) * 100;
		$TargetLulus = ($w['Lulus']/$w['Target']) * 100;
		$PelamarLulus = ($w['Lulus']/$w['Pilihan1']) * 100;
		$TargetBayar = ($w['Bayar']/$w['Target']) * 100;
		$LulusBayar = ($w['Bayar']/$w['Lulus']) * 100;
		$TargetDaftar = ($w['Daftar']/$w['Target']) * 100;
		$LulusDaftar = ($w['Daftar']/$w['Lulus']) * 100;
		$w['Belum'] = $w['Bayar'] - $w['Daftar'];
		$TargetBelum = ($w['Belum']/$w['Target']) * 100;
		$LulusBelum = ($w['Belum']/$w['Lulus']) * 100;
		$no++;
		$TotTarget += $w['Target'];
		$TotPelamar += $w['Pelamar'];
		$TotLulus += $w['Lulus'];
		
		$TotPilihan1 += $w['Pilihan1'];
		
		$TotLulusPgrade += $JmlLulusPassingGrade;
		$TotLulusPgrade1 += $LulusPassingGrade1;
		$TotLulusPgrade2 += $LulusPassingGrade2;
		
		
		$TotTdkLulusPassingGrade += $TdkLulusPassingGrade;
		$TotKesehatan += $w['Kesehatan'];
		$TotBayar += $w['Bayar'];
		$TotDaftar += $w['Daftar'];
		$TotBelum += $w['Belum'];
		echo "<tr>
				<td >$no</td>
				<td >$w[Prodi]</td>
				<td >$w[Jenjang]</td>
				<td align=center style='background-color:#FC0; border:#FC0 1px dotted'>$w[Target]</td>
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted'><b><a href=# onclick=\"javascript:penentuanLulus('$w[ProdiID]','both')\">$w[Pilihan1]</a></td>
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted'><b>
						<a href=# onclick=\"javascript:penentuanLulus('$w[ProdiID]',1)\">$LulusPassingGrade1</a></td>
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted'><b>$w[Pilihan2]</td>
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted'><b>
						<a href=# onclick=\"javascript:penentuanLulus('$w[ProdiID]',2)\">$LulusPassingGrade2</a></td>
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted'><a href=# onclick=\"javascript:penentuanLulus('$w[ProdiID]',0)\"><h3 style=\"color:yellow\">$w[Pelamar]</a></h3></td>
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted'><b>
						<a href=# onclick=\"javascript:penentuanLulus('$w[ProdiID]',0)\">$JmlLulusPassingGrade</a></td>
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted; '><b>
						<a href=# onclick=\"javascript:penentuanLulus('$w[ProdiID]',0)\"><span style=\"color:darkred\">$TdkLulusPassingGrade</span></a></td>
				<td align=center style='background-color:#6FF; border:#99FF99 1px dotted'>".number_format($TargetPelamar,0)." %</td>
				<td align=center style='background-color:#6FF; border:#99FF99 1px dotted'>".number_format($TargetGrade,0)." %</td>
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted'><b>
				<a href=# onclick=\"javascript:penentuanLulus('$w[ProdiID]','xls')\">$w[Lulus]</a></td>
				<td align=center style='background-color:#6FF; border:#99FF99 1px dotted'>".number_format($TargetLulus,0)." %</td>
				<td align=center >".number_format($PelamarLulus,0)." %</td>
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted'><b>$w[Bayar]</td>
				<td align=center style='background-color:#6FF; border:#99FF99 1px dotted'>".number_format($TargetBayar,0)." %</td>
				<td align=center >".number_format($LulusBayar,0)." %</td>
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted'><b>$w[Daftar]</td>
				<td align=center style='background-color:#6FF; border:#99FF99 1px dotted'>".number_format($TargetDaftar,0)." %</td>
				<td align=center >".number_format($LulusDaftar,0)." %</td>
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted'><b>$w[Belum]</td>
				<td align=center style='background-color:#6FF; border:#99FF99 1px dotted'>".number_format($TargetBelum,0)." %</td>
				<td align=center >".number_format($LulusBelum,0)." %</td>
			</tr>";
	}
}
$PrsTargetGrade = number_format(($TotLulusPgrade1 / $TotTarget) * 100,0);
	echo "<tr>
				<td colspan=3><b>Total</td>
				<td align=center style='background-color:#FC0; border:#FC0 1px dotted'>$TotTarget</td>
				<td colspan=1>$TotPilihan1</td>
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted'><b>$TotLulusPgrade1</td>
				<td colspan=1>&nbsp;</td>
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted'><b>$TotLulusPgrade2</td>
				
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted'><b>$TotPelamar</td>
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted'><b>$TotLulusPgrade</td>
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted'><b>$TotTdkLulusPassingGrade</td>
				<td align=center style='background-color:#6FF; border:#99FF99 1px dotted'></td>
				<td align=center style='background-color:#6FF; border:#99FF99 1px dotted'>$PrsTargetGrade %</td>
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted'><b>$TotLulus</td>
				<td align=center style='background-color:#6FF; border:#99FF99 1px dotted'></td>
				<td align=center ></td>
				<td align=center ></td>
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted'><b>$TotBayar</td>
				<td align=center style='background-color:#6FF; border:#99FF99 1px dotted'></td>
				
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted'><b>$TotDaftar</td>
				<td align=center style='background-color:#6FF; border:#99FF99 1px dotted'></td>
				<td align=center ></td>
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted'><b>$TotBelum</td>
				<td align=center style='background-color:#6FF; border:#99FF99 1px dotted'></td>
				<td align=center ></td>
			</tr>";
}
?>
<script>
	function penentuanLulus(prodi,pilihan) {
		if (pilihan == '') {
		window.open('?mnux=pmb/pmblulus.newajax&ProdiID=' + prodi + '&pil=' + pilihan,"","scrollbars, status");
		}
		else {
		window.open('pmb/pmblap.statistik.detail.php?ProdiID=' + prodi + '&pil=' + pilihan,"","scrollbars, status");
		}
		window.focus();
	}
</script>