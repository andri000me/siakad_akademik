<?php
//created by: Arisal Yanuarafi
//created on 11 Agustus 2012
   include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  //Parameter
$Thn = GetSetVar('thn');
if (empty($_SESSION['thn'])) { $_SESSION['thn']=2012; $Thn = 2012; }
$_Thn = $Thn+1;
$TA = "$_SESSION[thn] / $_Thn";

//Header XLS
header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename=RAPP");
header("Expires:0");
header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
header("Pragma: public");

?>
<style>
html {font-family:'Trebuchet MS'; }
</style>
<title>RAPP</title><table style="border-collapse:collapse" border="1">
<tr valign="middle">
	<td colspan="7" align="center">
    	<b>RENCANA ANGGARAN PENERIMAAN DAN PENGELUARAN (RAPP)<BR />
        INSTITUT TEKNOLOGI PADANG TAHUN <?php echo $TA; ?></b>
    </td>
</tr>
<tr>
	<td>&nbsp;</td>
    <td colspan="6"><b>A. PENERIMAAN</b></td>
</tr>
<tr>
	<td align="center"><b>NO</b></td>
    <td align="center"><b>JENIS</b></td>
    <td align="center" colspan="3"><b>VOLUME</b></td>
    <td align="center"><b>H.SATUAN<BR />(Rp.)</b></td>
    <td align="center"><b>JUMLAH<BR />(Rp.)</b></td>
</tr>
<tr valign="top">
	<td align="center">I.</td>
    <td><b>DANA PENYELENGGARAAN PENDIDIKAN (DPP)</b></TD><td colspan="5">&nbsp;</td>
</tr>
<?php // Setting Perulangan Laporan untuk Masing-masing Fakultas
//====================================================================================================================================
$sx = "Select FakultasID from fakultas order by FakultasID DESC";
$rx = _query($sx);
$ABJAD_=array();
while ($wx = _fetch_array($rx)) {
$_total=0;
$_SESSION[fak]=$wx[FakultasID];
$_abjad = $_SESSION[fak];
if ($_abjad == 02) $ABJAD = "A";
elseif ($_abjad == 01) $ABJAD = "B";
else $ABJAD= "C";
?>
<tr valign="top">
	<td align="center">&nbsp;<?php echo $w[FakultasID].$w[FakultasID]; ?></td>
    <td><b>
    <ol type="<?php echo $ABJAD; ?>"><li>FAKULTAS
	<?php $FAKULTAS = GetaField('fakultas',"FakultasID",$_SESSION[fak],'upper(Nama)');
	echo $FAKULTAS; ?></li></ol>
    <ol type="a"><li>Uang Kuliah</li></ol></td>
    <td colspan="5">&nbsp;</td>
</tr>
<tr valign="top">
	<td align="center">&nbsp;</td>
	<td>
    <ul>
	    <b>SEMESTER GANJIL (I)
      </ul>
	</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
</tr>
<?php
$_total = 0;
$s = "Select distinct(mid(m.TahunID,3,2)) as th, left(m.TahunID,4) as Tahun from mhsw m, khs k, prodi p where m.MhswID=k.MhswID and k.TahunID='$_SESSION[thn]1' and p.FakultasID='$_SESSION[fak]' and p.ProdiID=k.ProdiID order by m.TahunID DESC";
$r = _query($s);
$no = 0;
while ($w = _fetch_array($r)) {
$s1 = "Select distinct(p.Nama) as Prodi,j.Nama as Program, pr.Nama as Reguler,k.ProdiID,p.JenjangID,m.ProgramID from khs k,mhsw m 
				left outer join prodi p on p.ProdiID=m.ProdiID
				left outer join jenjang j on j.JenjangID=p.JenjangID
				left outer join program pr on pr.ProgramID=m.ProgramID
				where k.TahunID='$_SESSION[thn]1' 
				and m.MhswID=k.MhswID 
				and m.TahunID like '$w[Tahun]%' 
				and p.FakultasID='$_SESSION[fak]' order by m.ProdiID ASC,m.ProgramID DESC, p.JenjangID DESC";
$r1 = _query($s1);

while ($w1 = _fetch_array($r1)) {
if ($w1['ProgramID']=='R') {

$s2 = "Select Count(m.MhswID) as JML from mhsw m,khs k
		where k.TahunID='$_SESSION[thn]1' and m.MhswID=k.MhswID 
		and k.ProdiID='$w1[ProdiID]'
		and k.ProgramID = '$w1[ProgramID]'
		and m.TahunID like '$w[Tahun]%'";
$r2 = _query($s2);
$w2 = _fetch_array($r2);
$SKS = GetaField("khs k, mhsw m", "k.TahunID='$_SESSION[thn]1' and m.MhswID=k.MhswID 
		and k.ProdiID='$w1[ProdiID]'
		and m.TahunID like '$w[Tahun]%' AND m.ProgramID",$w1[ProgramID],'sum(k.SKS)');
$_SKS = floor($SKS/$w2[JML])+0;
$_JML = $w2[JML];
$p = "SELECT bp.Jumlah,bp.PerSKS FROM bipot b
					left outer join bipot2 bp on bp.BIPOTID=b.BIPOTID AND bp.BIPOTNamaID='1'
					where b.Nama like '%$w[Tahun]%' 
					AND b.ProdiID='$w1[ProdiID]'
					And b.ProgramID='$w1[ProgramID]' limit 1";
$q = _query($p);
$t = _fetch_array($q);
if ($t[PerSKS]=='N') $_SKS=1;
$Total=$t[Jumlah]*$_SKS*$_JML;
if ($Total > 0) {
$no++;

$cetak1 = '<tr valign=top>
	<td align=center>&nbsp;</td>
	<td>
    <ul>
'.$no.' Mahasiswa NIM '.$w[th].' '.$w1[Prodi].' '.$w1[Program].' '.($w1[Reguler]).'
      </ul>
	</td>
    <td align=right width=45>'.$_JML.'</td>
    <td align=right width=45>'.$_SKS.'</td>
    <td align=right width=45>1</td>
    <td align=right>'.$t[Jumlah].'</td>
    <td align=right>'.$Total.'</td>
</tr>';
$_total = $Total+$_total;
echo $cetak1;
}
}
else {
//khusus reguler B
//Semester Ganjil Dulu.....
$jgnHitung = 0;
$smster = $w['Tahun'];
$s2 = "Select Count(m.MhswID) as JML from mhsw m,khs k
		where k.TahunID='$_SESSION[thn]1' and m.MhswID=k.MhswID 
		and k.ProdiID='$w1[ProdiID]'
		and m.ProgramID = '$w1[ProgramID]'
		and m.TahunID like '$w[Tahun]1'";
$r2 = _query($s2);
$w2 = _fetch_array($r2);
$SKS = GetaField("khs k, mhsw m", "k.TahunID='$_SESSION[thn]1' and m.MhswID=k.MhswID 
		and k.ProdiID='$w1[ProdiID]'
		and m.TahunID like '$w[Tahun]1' AND k.ProgramID",$w1[ProgramID],'sum(k.SKS)');
$_SKS=1;
$sesi = GetaField('tahun',"TahunID > '$w[Tahun]1' and TahunID <= '$_SESSION[thn]1' AND KodeID",KodeID,'count(distinct(TahunID))')+1;
$smt = "Sem. $sesi";
if (empty($w2[JML])) {
$s2 = "Select Count(m.MhswID) as JML from mhsw m,khs k
		where k.TahunID='$_SESSION[thn]1' and m.MhswID=k.MhswID 
		and k.ProdiID='$w1[ProdiID]'
		and m.ProgramID = '$w1[ProgramID]'
		and m.SemesterAwal like '$w[Tahun]1'";
$r2 = _query($s2);
$w2 = _fetch_array($r2);
}
$JML = $w2[JML];
//==========================================================================================================
$Jenjang = GetaField('prodi',"ProdiID",$w1[ProdiID],'JenjangID');
if ($smster < 2012) {
$Satuan = GetaField('biayamhswref',"JenjangID='$Jenjang' AND TahunID='20112' AND Sesi",$sesi,'Biaya');
}
else $Satuan = GetaField('biayamhswref',"JenjangID='$Jenjang' AND TahunID='20121' AND Sesi",$sesi,'Biaya');
if ($Satuan > 0 && $JML > 0) {
$no++;
$Total=$Satuan*$_SKS*$JML;
$cetak = '<tr valign=top>
	<td align=center>&nbsp;</td>
	<td>
    <ul>
'.$no.' Mahasiswa NIM '.$w[th].' '.$w1[Prodi].' '.$w1[Program].' (Reg. B) '.$smt.'
      </ul>
	</td>
    <td align=right width=45>'.$JML.'</td>
    <td align=right width=45>'.$_SKS.'</td>
    <td align=right width=45>1</td>
    <td align=right>'.$Satuan.'</td>
    <td align=right>'.$Total.'</td>
</tr>';
$_total = $Total+$_total;
echo $cetak;
}

if ($jgnHitung == 0) {
//khusus reguler B
//Sekarang Semester Genap .....
$smster = $w[Tahun]+1;
$s2 = "Select Count(m.MhswID) as JML from mhsw m,khs k
		where k.TahunID='$_SESSION[thn]1' and m.MhswID=k.MhswID 
		and k.ProdiID='$w1[ProdiID]'
		and m.ProgramID = '$w1[ProgramID]'
		and m.TahunID like '$w[Tahun]2'";
$r2 = _query($s2);
$w2 = _fetch_array($r2);
// Jika hasil kosong (jumlah mahasiswa) cari dengan alternatif semester awal mahasiswa
if (empty($w2[JML])) {
$s2 = "Select Count(m.MhswID) as JML from mhsw m,khs k
		where k.TahunID='$_SESSION[thn]1' and m.MhswID=k.MhswID 
		and k.ProdiID='$w1[ProdiID]'
		and m.ProgramID = '$w1[ProgramID]'
		and m.SemesterAwal like '$w[Tahun]2'";
$r2 = _query($s2);
$w2 = _fetch_array($r2);
}
$SKS = GetaField("khs k, mhsw m", "k.TahunID='$_SESSION[thn]1' and m.MhswID=k.MhswID 
		and k.ProdiID='$w1[ProdiID]'
		and m.TahunID like '$w[Tahun]2' AND k.ProgramID",$w1[ProgramID],'sum(k.SKS)');
$sesi = GetaField('tahun',"TahunID > '$w[Tahun]2' and TahunID <= '$_SESSION[thn]2' AND KodeID",KodeID,'count(distinct(TahunID))')+1;
$Jenjang = GetaField('prodi',"ProdiID",$w1[ProdiID],'JenjangID');
$inisial = $_SESSION[thn].'1';
$_inisial = $inisial+0;
if (($smster) < $_inisial) {
$Satuan = GetaField('biayamhswref',"JenjangID='$Jenjang' AND TahunID='20112' AND Sesi",$sesi,'Biaya');
}
else $Satuan = GetaField('biayamhswref',"JenjangID='$Jenjang' AND TahunID='20121' AND Sesi",$sesi,'Biaya');
$_SKS=1;
$JML = $w2[JML];
if (!empty($JML) && $Satuan > 0) {
$no++;
$Total=$Satuan*$_SKS*$JML;
$cetak = '<tr valign=top>
	<td align=center>&nbsp;</td>
	<td>
    <ul>
'.$no.' Mahasiswa NIM '.$w[th].' '.$w1[Prodi].' '.$w1[Program].' (Reg. B) Sem.'.$sesi.'
      </ul>
	</td>
    <td align=right width=45>'.$JML.'</td>
    <td align=right width=45>'.$_SKS.'</td>
    <td align=right width=45>1</td>
    <td align=right>'.$Satuan.'</td>
    <td align=right>'.$Total.'</td>
</tr>';
$_total = $Total+$_total;
echo $cetak;
}
}
}

  }
  ?>
<tr style="border:none 0"><td colspan="7"></td></tr>
<?php
 
}

// SEMESTER II
//=========================================================================
//=========================================================================
//=========================================================================
//=========================================================================
?>
<tr valign="top">
	<td align="center">&nbsp;</td>
	<td>
    <ul>
	    <b>SEMESTER GENAP (II)
      </ul>
	</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
</tr>

<?PHP $s = "Select distinct(mid(m.TahunID,3,2)) as th, left(m.TahunID,4) as Tahun from mhsw m, khs k, prodi p where m.MhswID=k.MhswID and k.TahunID='$_SESSION[thn]1' and p.FakultasID='$_SESSION[fak]' and p.ProdiID=k.ProdiID order by m.TahunID DESC";
$r = _query($s);
$no = 0;
while ($w = _fetch_array($r)) { 
$s1 = "Select distinct(p.Nama) as Prodi,j.Nama as Program, pr.Nama as Reguler,k.ProdiID,p.JenjangID,m.ProgramID from khs k,mhsw m 
				left outer join prodi p on p.ProdiID=m.ProdiID
				left outer join jenjang j on j.JenjangID=p.JenjangID
				left outer join program pr on pr.ProgramID=m.ProgramID
				where k.TahunID='$_SESSION[thn]1' 
				and m.MhswID=k.MhswID 
				and m.TahunID like '$w[Tahun]%' 
				and p.FakultasID='$_SESSION[fak]' order by ProdiID,ProgramID DESC";
$r1 = _query($s1);

while ($w1 = _fetch_array($r1)) {
if ($w1['ProgramID']=='R') {

$s2 = "Select Count(m.MhswID) as JML from mhsw m,khs k
		where k.TahunID='$_SESSION[thn]1' and m.MhswID=k.MhswID 
		and k.ProdiID='$w1[ProdiID]'
		and k.ProgramID = '$w1[ProgramID]'
		and m.TahunID like '$w[Tahun]%'";
$r2 = _query($s2);
$w2 = _fetch_array($r2);
$SKS = GetaField("khs k, mhsw m", "k.TahunID='$_SESSION[thn]1' and m.MhswID=k.MhswID 
		and k.ProdiID='$w1[ProdiID]'
		and m.TahunID like '$w[Tahun]%' AND m.ProgramID",$w1[ProgramID],'sum(k.SKS)');
$_SKS = floor($SKS/$w2[JML])+0;
$_JML = $w2[JML];
$p = "SELECT bp.Jumlah,bp.PerSKS FROM bipot b
					left outer join bipot2 bp on bp.BIPOTID=b.BIPOTID AND bp.BIPOTNamaID='1'
					where b.Nama like '%$w[Tahun]%' 
					AND b.ProdiID='$w1[ProdiID]'
					And b.ProgramID='$w1[ProgramID]' limit 1";
$q = _query($p);
$t = _fetch_array($q);
if ($t[PerSKS]=='N') $_SKS=1;
$Total=$t[Jumlah]*$_SKS*$_JML;
if ($Total > 0) {
$no++;

$cetak1 = '<tr valign=top>
	<td align=center>&nbsp;</td>
	<td>
    <ul>
'.$no.' Mahasiswa NIM '.$w[th].' '.$w1[Prodi].' '.$w1[Program].' '.($w1[Reguler]).'
      </ul>
	</td>
    <td align=right width=45>'.$_JML.'</td>
    <td align=right width=45>'.$_SKS.'</td>
    <td align=right width=45>1</td>
    <td align=right>'.$t[Jumlah].'</td>
    <td align=right>'.$Total.'</td>
</tr>';
$_total = $Total+$_total;
echo $cetak1;
}
}
else {
//khusus reguler B
//Semester Ganjil Dulu.....
$jgnHitung = 0;
$smster = $w['Tahun'];
$s2 = "Select Count(m.MhswID) as JML from mhsw m,khs k
		where k.TahunID='$_SESSION[thn]1' and m.MhswID=k.MhswID 
		and k.ProdiID='$w1[ProdiID]'
		and m.ProgramID = '$w1[ProgramID]'
		and m.TahunID like '$w[Tahun]1'";
$r2 = _query($s2);
$w2 = _fetch_array($r2);
$SKS = GetaField("khs k, mhsw m", "k.TahunID='$_SESSION[thn]1' and m.MhswID=k.MhswID 
		and k.ProdiID='$w1[ProdiID]'
		and m.TahunID like '$w[Tahun]1' AND k.ProgramID",$w1[ProgramID],'sum(k.SKS)');
$_SKS=1;
$sesi = GetaField('tahun',"TahunID > '$w[Tahun]1' and TahunID <= '$_SESSION[thn]1' AND KodeID",KodeID,'count(distinct(TahunID))')+2;
$smt = "Sem. $sesi";
if (empty($w2[JML])) {
$s2 = "Select Count(m.MhswID) as JML from mhsw m,khs k
		where k.TahunID='$_SESSION[thn]1' and m.MhswID=k.MhswID 
		and k.ProdiID='$w1[ProdiID]'
		and m.ProgramID = '$w1[ProgramID]'
		and m.SemesterAwal like '$w[Tahun]1'";
$r2 = _query($s2);
$w2 = _fetch_array($r2);
}
$JML = $w2[JML];
if (!empty($JML)) {
$Jenjang = GetaField('prodi',"ProdiID",$w1[ProdiID],'JenjangID');
if ($smster < 2012) {
$Satuan = GetaField('biayamhswref',"JenjangID='$Jenjang' AND TahunID='20112' AND Sesi",$sesi,'Biaya');
}
else $Satuan = GetaField('biayamhswref',"JenjangID='$Jenjang' AND TahunID='20121' AND Sesi",$sesi,'Biaya');
if ($Satuan > 0 && $JML > 0) {
$no++;
$Total=$Satuan*$_SKS*$JML;
$cetak = '<tr valign=top>
	<td align=center>&nbsp;</td>
	<td>
    <ul>
'.$no.' Mahasiswa NIM '.$w[th].' '.$w1[Prodi].' '.$w1[Program].' (Reg. B) '.$smt.'
      </ul>
	</td>
    <td align=right width=45>'.$JML.'</td>
    <td align=right width=45>'.$_SKS.'</td>
    <td align=right width=45>1</td>
    <td align=right>'.$Satuan.'</td>
    <td align=right>'.$Total.'</td>
</tr>';
$_total = $Total+$_total;
echo $cetak;
}
}
if ($jgnHitung == 0) {
//khusus reguler B
//Sekarang Semester Genap .....
$smster = $w[Tahun]+1;
$s2 = "Select Count(m.MhswID) as JML from mhsw m,khs k
		where k.TahunID='$_SESSION[thn]1' and m.MhswID=k.MhswID 
		and k.ProdiID='$w1[ProdiID]'
		and m.ProgramID = '$w1[ProgramID]'
		and m.TahunID like '$w[Tahun]2'";
$r2 = _query($s2);
$w2 = _fetch_array($r2);
if (empty($w2[JML])) {
$s2 = "Select Count(m.MhswID) as JML from mhsw m,khs k
		where k.TahunID='$_SESSION[thn]1' and m.MhswID=k.MhswID 
		and k.ProdiID='$w1[ProdiID]'
		and m.ProgramID = '$w1[ProgramID]'
		and m.SemesterAwal like '$w[Tahun]2'";
$r2 = _query($s2);
$w2 = _fetch_array($r2);
}
$SKS = GetaField("khs k, mhsw m", "k.TahunID='$_SESSION[thn]1' and m.MhswID=k.MhswID 
		and k.ProdiID='$w1[ProdiID]'
		and m.TahunID like '$w[Tahun]2' AND k.ProgramID",$w1[ProgramID],'sum(k.SKS)');
$sesi = GetaField('tahun',"TahunID > '$w[Tahun]2' and TahunID <= '$_SESSION[thn]1' AND KodeID",KodeID,'count(distinct(TahunID))')+2;
$Jenjang = GetaField('prodi',"ProdiID",$w1[ProdiID],'JenjangID');
$inisial = $_SESSION[thn].'1';
$_inisial = $inisial+0;
if (($smster) < $_inisial) {
$Satuan = GetaField('biayamhswref',"JenjangID='$Jenjang' AND TahunID='20112' AND Sesi",$sesi,'Biaya');
}
else $Satuan = GetaField('biayamhswref',"JenjangID='$Jenjang' AND TahunID='20121' AND Sesi",$sesi,'Biaya');
$_SKS=1;
$JML = $w2[JML];
if (!empty($JML) && $Satuan > 0) {
$no++;
$Total=$Satuan*$_SKS*$JML;
$cetak = '<tr valign=top>
	<td align=center>&nbsp;</td>
	<td>
    <ul>
'.$no.' Mahasiswa NIM '.$w[th].' '.$w1[Prodi].' '.$w1[Program].' (Reg. B) Sem.'.$sesi.'
      </ul>
	</td>
    <td align=right width=45>'.$JML.'</td>
    <td align=right width=45>'.$_SKS.'</td>
    <td align=right width=45>1</td>
    <td align=right>'.$Satuan.'</td>
    <td align=right>'.$Total.'</td>
</tr>';
$_total = $Total+$_total;
echo $cetak;
}
}
}


  }
 
}


//=========================================================================
//=========================================================================
echo '<tr bgcolor=#eee><td colspan=6 align=center><b>Sub Jumlah '.$ABJAD.' (Uang Kuliah)</td><td align=right><b>'.$_total.'</td></tr>';

// Mulai Hitung Uang Pendaftaran
// Hitung Jumlah Pendaftar yang membayar
	$m = GetaField('pmbformjual p, aplikan a,prodi pr',"p.PMBPeriodID like '%$_SESSION[thn]%' and p.Jumlah > 0 and a.AplikanID=p.AplikanID 
		and pr.ProdiID=a.ProdiID and pr.FakultasID='$_SESSION[fak]' and p.NA",N,'count(p.AplikanID)');
	//====================================================================================================================================
// Hitung pembayaran
	$n = GetFields('pmbformjual p, aplikan a,prodi pr',"p.PMBPeriodID like '%$_SESSION[thn]%' and p.Jumlah > 0 and a.AplikanID=p.AplikanID 
		and pr.ProdiID=a.ProdiID and pr.FakultasID='$_SESSION[fak]' and p.NA",N,'p.Jumlah');
	
	echo '<tr><td>&nbsp;</td><td>b. Uang Pendaftaran</td><td colspan=3 align=center>'.$m.'</td><td align=right>'.$n['Jumlah'].'</td>';
	//====================================================================================================================================
// Hitung Jumlah Uang Pendaftaran
	$o = $m * $n['Jumlah'] + 0;
	$_total =$_total + $o;
	echo '<td align=right>'.$o.'</td></tr>';

	//--------------------------------------------------------------------------------------------------------------------------------------
	// Uang PKN D.III
	echo '<tr><td>&nbsp;</td><td>c. Uang PKN(D.III)</td><td colspan=3 align=center></td><td align=center></td>';
	echo '<td align=center></td></tr>';
	$b = "Select distinct(left(m.TahunID,4)) as TH from khs k,mhsw m where m.MhswID=k.MhswID and k.TahunID like '$_SESSION[thn]%' order by m.TahunID DESC";
	$c = _query($b);
	$whr = array();
	while ($d = _fetch_array($c)) {
	$s = "Select bb.Jumlah from bipot2 bb,bipot b where b.ProgramID='R' and  b.Nama like '%$d[TH]%' and bb.BIPOTID=b.BIPOTID and bb.BIPOTNamaID='17'	
			 order by bb.Jumlah DESC limit 1";
	$r = _query($s);
	while ($w = _fetch_array($r)) {
			$_whr = implode(' and ', $whr);
 			$_whr = (empty($_whr))? '' : ' and ' . $_whr;
	$m = GetaField('khs k,mhsw m, prodi p', "m.MhswID=k.MhswID  and m.TahunID like '$d[TH]%' and
				m.ProgramID='R' and p.ProdiID=m.ProdiID and k.TahunID like '$_SESSION[thn]%' and p.FakultasID",$_SESSION[fak],'count(distinct(k.MhswID))');
	if ($m > 0) {
	echo '<tr><td>&nbsp;</td><td><ul>Mahasiswa BP '.$d[TH].'</ul></td><td align=right>'.$m.'</td><td>&nbsp;</td><td>2</td><td align=right>'.$w[Jumlah].'</td>';
	$o = $m * $w[Jumlah] * 2 +0;
	$_total =$_total + $o;
	echo '<td align=right>'.$o.'</td></tr>';
			$whr[] = "m.TahunID not like '$d[TH]%'";
	}		
	}
	
	}
	
		//====================================================================================================================================
// Uang PKN (DIII) dan Uang Tugas Akhir tidak dihitung
	echo '<tr><td>&nbsp;</td><td>d. Uang Tugas Akhir</td><td colspan=3 align=center>-</td><td align=center>-</td>';
	echo '<td align=center>-</td></tr>';
	
	//====================================================================================================================================
// Biaya Asuransi, Jaket dan Orientasi (Digabung)
	// 1. hitung jumlah mahasiswa baru
	$m = GetaField('khs k, mhsw m, prodi p', "k.MhswID=m.MhswID and p.ProdiID=m.ProdiID and k.TahunID like '$_SESSION[thn]%' and m.ProgramID='R' and m.TahunID like '$_SESSION[thn]%' 
			and p.FakultasID",
			$_SESSION['fak'],'count(distinct(k.MhswID))');
	//--------------------------------------------------------------------------------------------------------------------------------------
	// 2. Hitung Biaya Asuransi, Jaket dan Orientasi
	$n = GetFields ('bipot b, bipot2 bb',"b.NA='N' and b.ProgramID='R'  and b.Tahun like '%$_SESSION[thn]%' and b.Nama like '%$_SESSION[thn]%' and
			bb.BipotID=b.BipotID and bb.BipotNamaID='2' and bb.NA", N, 'bb.Jumlah');
	//--------------------------------------------------------------------------------------------------------------------------------------
	// 3. Hitung Total Biaya AJO
	echo '<tr><td>&nbsp;</td><td>e. Orientasi Akademik</td><td colspan=3 align=center>'.$m.'</td><td align=right>'.$n[Jumlah].'</td>';
	$o = $m * $n[Jumlah] +0;
	$_total =$_total + $o;
	echo '<td align=right>'.$o.'</td></tr>';
	
	//--------------------------------------------------------------------------------------------------------------------------------------
	// Uang Kemahasiswaan
	echo '<tr><td>&nbsp;</td><td>f. Uang Kemahasiswaan</td><td colspan=3 align=center></td><td align=center></td>';
	echo '<td align=center></td></tr>';
	$b = "Select distinct(left(m.TahunID,4)) as TH from khs k,mhsw m where m.MhswID=k.MhswID and k.TahunID like '$_SESSION[thn]%' order by m.TahunID DESC";
	$c = _query($b);
	$whr = array();
	while ($d = _fetch_array($c)) {
	$s = "Select bb.Jumlah from bipot2 bb,bipot b where b.ProgramID='R' and  b.Nama like '%$d[TH]%' and bb.BIPOTID=b.BIPOTID and bb.BIPOTNamaID='4'	
			 order by bb.Jumlah DESC limit 1";
	$r = _query($s);
	while ($w = _fetch_array($r)) {
			$_whr = implode(' and ', $whr);
 			$_whr = (empty($_whr))? '' : ' and ' . $_whr;
	$m = GetaField('khs k,mhsw m, prodi p', "m.MhswID=k.MhswID  and m.TahunID like '$d[TH]%' and
				m.ProgramID='R' and p.ProdiID=m.ProdiID and k.TahunID like '$_SESSION[thn]%' and p.FakultasID",$_SESSION[fak],'count(distinct(k.MhswID))');
	if ($m > 0) {
	echo '<tr><td>&nbsp;</td><td><ul>Mahasiswa BP '.$d[TH].'</ul></td><td align=right>'.$m.'</td><td>&nbsp;</td><td>2</td><td align=right>'.$w[Jumlah].'</td>';
	$o = $m * $w[Jumlah] * 2 +0;
	$_total =$_total + $o;
	echo '<td align=right>'.$o.'</td></tr>';
			$whr[] = "m.TahunID not like '$d[TH]%'";
	}		
	}
	
	}
	
	//--------------------------------------------------------------------------------------------------------------------------------------
	// Uang Registrasi
	echo '<tr><td>&nbsp;</td><td>g. Uang Registrasi</td><td colspan=3 align=center></td><td align=center></td>';
	echo '<td align=center></td></tr>';
	$b = "Select distinct(left(m.TahunID,4)) as TH from khs k,mhsw m where m.MhswID=k.MhswID and k.TahunID like '$_SESSION[thn]%' order by m.TahunID DESC";
	$c = _query($b);
	$whr = array();
	while ($d = _fetch_array($c)) {
	$s = "Select bb.Jumlah from bipot2 bb,bipot b where b.Nama like '%$d[TH]%' and bb.BIPOTID=b.BIPOTID and bb.BIPOTNamaID='3'	
			 limit 1";
	$r = _query($s);
	while ($w = _fetch_array($r)) {
			$_whr = implode(' and ', $whr);
 			$_whr = (empty($_whr))? '' : ' and ' . $_whr;
	$m = GetaField('khs k,mhsw m, prodi p', "m.MhswID=k.MhswID  and m.TahunID like '$d[TH]%' and
				m.ProgramID='R' and p.ProdiID=m.ProdiID and k.TahunID like '$_SESSION[thn]%' and p.FakultasID",$_SESSION[fak],'count(distinct(k.MhswID))');
	if ($m > 0) {
	echo '<tr><td>&nbsp;</td><td><ul>Mahasiswa BP '.$d[TH].'</ul></td><td align=right>'.$m.'</td><td>&nbsp;</td><td>2</td><td align=right>'.$w[Jumlah].'</td>';
	$o = $m * $w[Jumlah] * 2 +0;
	$_total =$_total + $o;
	echo '<td align=right>'.$o.'</td></tr>';
			$whr[] = "m.TahunID not like '$d[TH]%'";
	}		
	}
	
	}
	
	//--------------------------------------------------------------------------------------------------------------------------------------
	// Uang DPI
	echo '<tr><td>&nbsp;</td><td>h. Dana Peningkatan Infrastruktur (DPI)</td><td colspan=3 align=center></td><td align=center></td>';
	echo '<td align=center></td></tr>';
	$b = "Select distinct(left(m.TahunID,4)) as TH from khs k,mhsw m where m.MhswID=k.MhswID and k.TahunID like '$_SESSION[thn]%' order by m.TahunID DESC";
	$c = _query($b);
	$whr = array();
	while ($d = _fetch_array($c)) {
	$s = "Select bb.Jumlah from bipot2 bb,bipot b where b.ProgramID='R' and  b.Nama like '%$d[TH]%' and bb.BIPOTID=b.BIPOTID and bb.BIPOTNamaID='16'	
			 order by bb.Jumlah DESC limit 1";
	$r = _query($s);
	while ($w = _fetch_array($r)) {
			$_whr = implode(' and ', $whr);
 			$_whr = (empty($_whr))? '' : ' and ' . $_whr;
	$m = GetaField('khs k,mhsw m, prodi p', "m.MhswID=k.MhswID  and m.TahunID like '$d[TH]%' and
				m.ProgramID='R' and p.ProdiID=m.ProdiID and k.TahunID like '$_SESSION[thn]%' and p.FakultasID",$_SESSION[fak],'count(distinct(k.MhswID))');
	if ($m > 0) {
	echo '<tr><td>&nbsp;</td><td><ul>Mahasiswa BP '.$d[TH].'</ul></td><td align=right>'.$m.'</td><td>&nbsp;</td><td>2</td><td align=right>'.$w[Jumlah].'</td>';
	$o = $m * $w[Jumlah] * 2 +0;
	$_total =$_total + $o;
	echo '<td align=right>'.$o.'</td></tr>';
			$whr[] = "m.TahunID not like '$d[TH]%'";
	}		
	}
	
	}
	$SubTotal = $SubTotal + $_total;
	// TAMPILKAN SUB JUMLAH DANA PENYELENGGARAAN PENDIDIKAN FAKULTAS
	echo '<tr bgcolor=#eee><td colspan=6 align=center><b>JUMLAH '.$ABJAD.' (DANA PENYELENGGARAAN PENDIDIKAN F. '.$FAKULTAS.')
	</td><td align=right><b>'.number_format($_total).'</td></tr>';
	$ABJAD_[]=$ABJAD;
	}
	// TAMPILKAN JUMLAH SUB TOTAL DANA PENYELENGGARAAN PENDIDIKAN SEMUA FAKULTAS
				$_ABJ = implode(' + ', $ABJAD_);
		echo '<tr bgcolor=#eee><td colspan=6 align=center><b>JUMLAH I ('.$_ABJ.') DANA PENYELENGGARAAN PENDIDIKAN</td><td align=right><b>'.number_format($SubTotal).'</td></tr>';