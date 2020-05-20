<!-- 	
Author	: Arisal Yanuarafi
Start	: 15 Maret 2012 -->
<style>
td { font-size:12px; }
</style>

<?php
if (!empty($_GET['mhswid'])) $mhswid=sqling($_GET['mhswid']);
else $mhswid = $_POST['mhswid'];
$_SESSION['_KurikulumID'] = $_POST['_KurikulumID'];
if ($_SESSION['_LevelID']==120) $mhswid = $_SESSION['_Login'];

	$mhs = GetFields('mhsw', "MhswID", $mhswid, '*');
	
	$program = GetaField ('mhsw',"MhswID", $mhswid, 'ProgramID');
	$fak = GetFields('prodi p, fakultas f', "f.FakultasID=p.FakultasID and p.ProdiID", $mhs[ProdiID], 'f.Nama as NamaFak');
	$jur = GetFields('prodi', "ProdiID", $mhs[ProdiID], 'Nama,Gelar,UPPER(Keterangan) as KET');
	$prog = GetFields('prodi p, jenjang j', "j.JenjangID=p.JenjangID and ProdiID", $mhs[ProdiID], 'j.Nama as NMProg');
 	
	$n=0;

echo "<form name='ProdiID' action='?' method=POST><table width=800 class=box align=center><tr><td class=inp>Kurikulum</td><td>";
   $s6 = "select KurikulumID,KurikulumKode,Nama
    from kurikulum
    where ProdiID = '$mhs[ProdiID]' order by Nama";
$r6 = _query($s6);
	  $optkurikulum = "<option value=''></option>";
	  while($w6 = _fetch_array($r6))
		{  $ck = ($w6['KurikulumID'] == $_SESSION['_KurikulumID'])? "selected" : '';
		   $optkurikulum .=  "<option value='$w6[KurikulumID]' $ck>$w6[Nama]</option>";
		}
	  $_inputKurikulum = "<select name='_KurikulumID' onChange='this.form.submit()'>$optkurikulum</select>";    
   echo"<input type=hidden name='mhswid' value='$mhswid'>$_inputKurikulum <font color=red> *) Tentukan Kurikulum Matakuliah</font></td></tr></table></form>";    
 if (!empty($_SESSION['_KurikulumID'])) {   
$kur = GetaField('kurikulum',"KurikulumID",$_SESSION[_KurikulumID],'KurikulumKode');
$tmbh = "Kurikulum $kur, $jur[Nama]";
TampilkanJudul("Daftar Matakuliah $tmbh");  
echo "<table border=0 width=800 align=center><tr><td>";
echo "<form action='?' method=POST>
 <input type=hidden name='gos' value='NilaiMhswSimpan' />";
 	if (!empty($_SESSION['_KurikulumID'])) $whrKurikulum="And KurikulumID='$_SESSION[_KurikulumID]'";
	else $whrKurikulum=='';
//tabel sisi kiri
echo "<table  class=bsc border=0 width=390 align=left><tr class=ttl>";
echo "<td class='ul1'>Kode</td>";
echo "<td class='ul1'>Matakuliah</td>";
echo "<td class='ul1'>SKS</td>";

$totalmutu=0;
$totalsks=0;
if ($program=='R') {
	if ($prog[NMProg]=='S1') {	$SesiKiri=4;	$SesiKanan=8;	}
	else {						$SesiKiri=3;	$SesiKanan=6; 	}
}
else {
	if ($prog[NMProg]=='S1') {	$SesiKiri=2; 	$SesiKanan=3;	}
	else {						$SesiKiri=3;	$SesiKanan=6;	}
}

while ($sesi<$SesiKiri) {
$sesi++;
if ($sesi==1) { $_sesi='I'; }
elseif ($sesi==2) { $_sesi='II'; }
elseif ($sesi==3) { $_sesi='III'; }
elseif ($sesi==4) { $_sesi='IV'; }
elseif ($sesi==5) { $_sesi='V'; }
if ($program=='R' || $prog[NMProg]=='D3') {
$s = "select m.MKKode,m.Nama,m.SKS,m.MKID
    from mk m
    where m.KodeID = '".KodeID."'
      and m.ProdiID = '$mhs[ProdiID]'
	  And m.Sesi=$sesi
      and m.NA = 'N'
	  $whrKurikulum
    order by m.MKKode";
}
else {
$sesiB=$prog[NMProg].' Reg B Sem '.$sesi;
$s = "select distinct(m.MKKode),m.Nama,m.SKS,m.MKID
    from mk m, mkpaket mp,mkpaketisi mpi
    where m.KodeID = '".KodeID."'
      and mp.ProdiID = '$mhs[ProdiID]'
	  And mp.Nama='$sesiB'
	  And mpi.MKPaketID=mp.MKPaketID
	  And m.MKID=mpi.MKID
	  and m.NA = 'N'
	  and mpi.NA='N'
	  $whrKurikulum
    order by m.MKKode";
}
$r=_query($s);
$jmlsks=0;
$jmlmutu=0;
$lg=0;
echo "<tr><td colspan=5><b>Semester $_sesi</b></td></tr>";
	while ($w=_fetch_array($r)) {
	$lg++;
	if ($lg==1) {
		echo "<tr bgcolor=lightgrey>";
		$lg=$lg-2;
	}
	else {
	echo "<tr>";
	}
	
		echo "<td class=ul1>$w[MKKode]</td>";
		echo "<td class=ul1>$w[Nama]</td>";
		echo "<td align=center class=ul1>$w[SKS]</td></tr>";
		

	}

}
echo "</table>";

//tabel sisi kanan
echo "<table class=bsc border=0 width=390 align=right><tr class=ttl>";
echo "<td align=center class=ul1>Kode</td>";
echo "<td align=center class=ul1>Matakuliah</td>";
echo "<td align=center class=ul1>SKS</td>";

while ($sesi<$SesiKanan) {
$sesi++;
if ($sesi==2) { $_sesi='II'; }
elseif ($sesi==3) { $_sesi='III'; }
elseif ($sesi==4) { $_sesi='IV'; }
elseif ($sesi==5) { $_sesi='V'; }
elseif ($sesi==6) { $_sesi='VI'; }
elseif ($sesi==7) { $_sesi='VII'; }
elseif ($sesi==8) { $_sesi='VIII'; }
if ($program=='R' || $prog[NMProg]=='D3') {
$s = "select m.MKKode,m.Nama,m.SKS,m.MKID
    from mk m
    where m.KodeID = '".KodeID."'
      and m.ProdiID = '$mhs[ProdiID]'
	  And m.Sesi=$sesi
      and m.NA = 'N'
	  $whrKurikulum
    order by m.MKKode";
}
else {
$sesiB=$prog[NMProg].' Reg B Sem '.$sesi;
$s = "select distinct(m.MKKode),m.Nama,m.SKS,m.MKID
    from mk m, mkpaket mp,mkpaketisi mpi
    where m.KodeID = '".KodeID."'
      and mp.ProdiID = '$mhs[ProdiID]'
	  And mp.Nama='$sesiB'
	  And mpi.MKPaketID=mp.MKPaketID
	  And m.MKID=mpi.MKID
	  and m.NA = 'N'
	  and mpi.NA='N'
	  $whrKurikulum
    order by m.MKKode";
}
$r=_query($s);
echo "<tr><td colspan=5><b>Semester $_sesi</b></td></tr>";
$jmlsks=0;
$jmlmutu=0;
$lg=0;
	while ($w=_fetch_array($r)) {
		$lg++;
		if ($lg==1) {
			echo "<tr bgcolor=lightgrey>";
		$lg=$lg-2;
		}
		else {
			echo "<tr>";
		}
		echo "<td>$w[MKKode]</td>";
		echo "<td>$w[Nama]</td>";
		echo "<td align=center class=ul1>$w[SKS]</td></tr>";
	
		
	}

}

echo "</table></td></table><br />";
}
	
?>
