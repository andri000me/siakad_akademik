<?php
session_start();
	include_once "../dwo.lib.php";
  	include_once "../db.mysql.php";
  	include_once "../connectdb.php";
  	include_once "../parameter.php";
  	include_once "../cekparam.php";	
	
		header("Content-type:application/vnd.ms-excel");
		header("Content-Disposition:attachment;filename=Lapkeu-Pembayaran-Mahasiswa-$ProdiID-$TahunID.xls");
		header("Expires:0");
		header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
		header("Pragma: public");
 
?><style> 
	td, th, body{font-family:"Courier New", Courier, monospace; vertical-align:text-top}
	th{ background-color: #09F; color:#FFF;}
	td .text{  mso-number-format:"\@"; }
    a{text-decoration:none}
    a:hover{text-decoration:none}</style>
<?php
// *** Parameters ***
$gel = GetSetVar('gel');

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
	 // data
	 $pin		= GetaField('pmbformjual', "NA='N' and PMBPeriodID='$gel' and KodeID", KodeID, "Count(PMBFormJualID)");
	 $lengkap	= GetaField('aplikan', "CetakBukti='Y' and PMBPeriodID='$gel' and KodeID", KodeID, "Count(AplikanID)");
	 $Lulus		= GetaField('pmb', "LulusUjian='Y' and PMBPeriodID='$gel' and KodeID", KodeID, "Count(AplikanID)");
	 $klnByr	= GetaField('pmbklinikbayar', "PMBPeriodID='$gel' and KodeID", KodeID, "Count(Distinct(PMBID))");
	 $klnTes	= GetaField('pmbkliniktes', "PMBPeriodID='$gel' and KodeID", KodeID, "Count(Distinct(PMBID))");
	 $dftUlang	= GetaField('pmb', "TotalBiaya>0 and TotalBiaya=TotalBayar and PMBPeriodID='$gel' and KodeID", KodeID, "Count(distinct(AplikanID))");
	 $Terdaftar	= GetaField('pmb', "MhswID is not Null and MhswID > 0 and PMBPeriodID='$gel' and KodeID", KodeID, "Count(AplikanID)");
	
}
function BuatRekapGel1($gel){}
function BuatRekapGel2($gel){}
function BuatRekapGel3($gel){}
function BuatRekapPIN($gel){}
function BuatRekapPerProdi($gel){
	echo "<table border = 1 align=center width=200%>
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
				<th class='ttl'>G1</th>
				<th class='ttl'>G2</th>
				<th class='ttl'>G3</th>
				<th class='ttl'>G4</th>
				<th class='ttl'>G5</th>
				<th class='ttl'>% (Target/Peminat)</th>
				<th class='ttl'>Lulus</th>
				<th class='ttl'>% (Target/Lulus)</th>
				<th class='ttl'>% (Pelamar/Lulus)</th>
				<th class='ttl'>Cek Kesehatan</th>
				<th class='ttl'>% (Target/Cek Kesehatan)</th>
				<th class='ttl'>% (Lulus/Cek Kesehatan)</th>
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
			<td colspan=21><h3>Fakultas $wf[Nama]</h3></td></tr>";

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
		$w['Bayar'] = GetaField('pmb bayar', "bayar.PMBPeriodID='$gel' and bayar.LulusUjian = 'Y' and bayar.TotalBiaya > 0 and bayar.TotalBiaya=bayar.TotalBayar and bayar.ProdiID", $w['ProdiID'], "count(DISTINCT(bayar.PMBID))");
		$w['Kesehatan'] = GetaField('pmbkliniktes tes', "tes.ProdiID = '$w[ProdiID]' and tes.PMBPeriodID",$gel,"count(DISTINCT(tes.PMBID))");
		$w['Daftar'] = GetaField('pmb daftar', "daftar.PMBPeriodID='$gel' and daftar.LulusUjian = 'Y' and daftar.MhswID > 0 and daftar.ProdiID", $w['ProdiID'],"count(DISTINCT(daftar.PMBID))");
		$LulusPassingGrade1 = GetaField('pmb', "Pilihan1='$w[ProdiID]' and NilaiUjian >= 65 and PMBPeriodID", $gel,"count(DISTINCT(PMBID))");
		$LulusPassingGrade2 = GetaField('pmb', "Pilihan2='$w[ProdiID]' and Pilihan1 != '$w[ProdiID]' and NilaiUjian >= 65 and PMBPeriodID", $gel,"count(DISTINCT(PMBID))");
		$JmlLulusPassingGrade = $LulusPassingGrade1 + $LulusPassingGrade2 + 0 ;
		$TdkLulusPassingGrade = GetaField('pmb', "(Pilihan2='$w[ProdiID]' or Pilihan1 = '$w[ProdiID]') and NilaiUjian < 65 and PMBPeriodID", $gel,"count(DISTINCT(PMBID))");
		$PeminatG1 = GetaField('pmb', "Pilihan1='$w[ProdiID]' and Hint = 'Gel1' and PMBPeriodID", $gel,"count(DISTINCT(PMBID))");
		$PeminatG2 = GetaField('pmb', "Pilihan1='$w[ProdiID]' and Hint = 'Gel2' and PMBPeriodID", $gel,"count(DISTINCT(PMBID))");
		$PeminatG3 = GetaField('pmb', "Pilihan1='$w[ProdiID]' and Hint = 'Gel3' and PMBPeriodID", $gel,"count(DISTINCT(PMBID))");
		$PeminatG4 = GetaField('pmb', "Pilihan1='$w[ProdiID]' and Hint = 'Gel4' and PMBPeriodID", $gel,"count(DISTINCT(PMBID))");
		$PeminatG5 = GetaField('pmb', "Pilihan1='$w[ProdiID]' and Hint = 'Gel5' and PMBPeriodID", $gel,"count(DISTINCT(PMBID))");
		
		$TargetPelamar = ($w['Pilihan1']/$w['Target']) * 100;
		$TargetLulus = ($w['Lulus']/$w['Target']) * 100;
		$PelamarLulus = ($w['Lulus']/$w['Pelamar']) * 100;
		$TargetKesehatan = ($w['Kesehatan']/$w['Target']) * 100;
		$LulusKesehatan = ($w['Kesehatan']/$w['Lulus']) * 100;
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
		
		$TotLulusPgrade += $JmlLulusPassingGrade;
		$TotLulusPgrade1 += $LulusPassingGrade1;
		$TotLulusPgrade2 += $LulusPassingGrade2;
		
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
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted'><b>$w[Pelamar]</td>
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted'><b>
						$JmlLulusPassingGrade</td>
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted; '><b>
						<a href=# onclick=\"javascript:penentuanLulus('$w[ProdiID]',0)\"><span style=\"color:darkred\">$TdkLulusPassingGrade</span></a></td>
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted'>$PeminatG1</td>
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted'>$PeminatG2</td>
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted'>$PeminatG3</td>
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted'><b>
						<a href=# onclick=\"javascript:penentuanLulus('$w[ProdiID]','g4')\">$PeminatG4</a></td>
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted'><b>
						<a href=# onclick=\"javascript:penentuanLulus('$w[ProdiID]','g5')\">$PeminatG5</a></td>
				<td align=center style='background-color:#6FF; border:#99FF99 1px dotted'>".number_format($TargetPelamar,0)." %</td>
				<td align=center style='background-color:#6FF; border:#99FF99 1px dotted'>".number_format($TargetGrade,0)." %</td>
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted'><b>
				<a href=# onclick=\"javascript:penentuanLulus('$w[ProdiID]','xls')\">$w[Lulus]</a></td>
				<td align=center style='background-color:#6FF; border:#99FF99 1px dotted'>".number_format($TargetLulus,0)." %</td>
				<td align=center >".number_format($PelamarLulus,0)." %</td>
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted'><b>$w[Kesehatan]</td>
				<td align=center style='background-color:#6FF; border:#99FF99 1px dotted'>".number_format($TargetKesehatan,0)." %</td>
				<td align=center >".number_format($LulusKesehatan,0)." %</td>
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
	echo "<tr>
				<td colspan=3><b>Total</td>
				<td align=center style='background-color:#FC0; border:#FC0 1px dotted'>$TotTarget</td>
				<td colspan=1>&nbsp;</td>
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted'><b>$TotLulusPgrade1</td>
				<td colspan=1>&nbsp;</td>
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted'><b>$TotLulusPgrade2</td>
				
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted'><b>$TotPelamar</td>
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted'><b>$TotLulusPgrade</td>
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted'><b>$TotTdkLulusPassingGrade</td>
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted'><b>$TotG4</td>
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted'><b>$TotG5</td>
				<td align=center style='background-color:#6FF; border:#99FF99 1px dotted'></td>
				<td align=center style='background-color:#6FF; border:#99FF99 1px dotted'>$PrsTargetGrade %</td>
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted'><b>$TotLulus</td>
				<td align=center style='background-color:#6FF; border:#99FF99 1px dotted'></td>
				<td align=center ></td>
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted'><b>$TotKesehatan</td>
				<td align=center style='background-color:#6FF; border:#99FF99 1px dotted'></td>
				<td align=center ></td>
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted'><b>$TotBayar</td>
				<td align=center style='background-color:#6FF; border:#99FF99 1px dotted'></td>
				<td align=center ></td>
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted'><b>$TotDaftar</td>
				<td align=center style='background-color:#6FF; border:#99FF99 1px dotted'></td>
				<td align=center ></td>
				<td align=center style='background-color:#09F; border:#99FF99 1px dotted'><b>$TotBelum</td>
				<td align=center style='background-color:#6FF; border:#99FF99 1px dotted'></td>
				<td align=center ></td>
			</tr>";
}
echo "<a href = '$_SESSION[mnux].xls.php' target='_blank'>";