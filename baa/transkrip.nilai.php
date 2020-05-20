<!-- 	
Author	: Arisal Yanuarafi
Start	: 15 Maret 2012 --><head><title>Cetak Daftar Nilai Mahasiswa</title></head>

<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
<link href="cetak.css" rel="stylesheet" type="text/css">
<style>
table,font { font-family:Times; line-height:100%; }
header{ font-family:Arial; font-size:7px;}
</style>
<body>
<?php
  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
$_uasProdi = $_REQUEST['_uasProdi2'];
$_uasProg  = $_REQUEST['_uasProg2'];
$_uasTahun = $_REQUEST['_uasTahun2'];
$mhswid=$_POST[mhswid];
$ProdiAsalPT=$_POST[ProdiAsalPT];
$AsalPT=$_POST[AsalPT];
$TahunTamat=$_POST[TahunTamat];
$JenjangAsalPT=$_POST[JenjangAsalPT];



$s = "Select IPS,MhswID,ProdiID from khs where TahunID='20111'";
$r = _query($s);
while ($khs=_fetch_array($r)) {
  $MaxSKS = GetaField('maxsks',
    "KodeID='".KodeID."' and NA = 'N'
    and DariIP <= $khs[IPS] and $khs[IPS] <= SampaiIP and ProdiID", 
    $khs['ProdiID'], 'SKS')+0;
	$ada = GetaField('khs',"MhswID='$khs[MhswID]' AND TahunID",20121,'KHSID');
	if (!empty($ada)) {
	mysql_query("update khs set MaxSKS='$MaxSKS' where MhswID='$khs[MhswID]' And TahunID='20121'");
	}
}
//$namafile = "$mhswid.xls";
//header("Content-type:application/vnd.ms-excel");
//header("Content-Disposition:attachment;filename=$namafile");
//header("Expires:0");
//header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
//header("Pragma: public");

	$mhs = GetFields('mhsw', "MhswID", $mhswid, '*');
	$TanggalLahir = TanggalFormat(GetaField('mhsw', "MhswID", $mhswid, "TanggalLahir"));
	
	$tgl_wisuda = TanggalFormat(GetaField('wisuda', "NA", N, "TglWisuda"));
	
	$nmrIjazah = GetaField ('wisudawan',"MhswID", $mhswid, 'NomerIjazah');
	$nmrTranskrip = GetaField ('wisudawan',"MhswID", $mhswid, 'NomerTranskrip');
	$program = GetaField ('mhsw',"MhswID", $mhswid, 'ProgramID');
	$fak = GetFields('prodi p, fakultas f', "f.FakultasID=p.FakultasID and p.ProdiID", $mhs[ProdiID], 'f.Nama as NamaFak');
	$jur = GetFields('prodi', "ProdiID", $mhs[ProdiID], 'Nama,Gelar,UPPER(Keterangan) as KET');
	$prog = GetFields('prodi p, jenjang j', "j.JenjangID=p.JenjangID and ProdiID", $mhs[ProdiID], 'j.Nama as NMProg');
 	$identitas = GetFields('identitas', "Kode", KodeID, 'Alamat1,Nama,Yayasan,Telepon,Fax,Website, Email');
    	$logo = "../img/logo.jpg";
    	echo "<table border=0 width=650 align=center height=80><tr><td width=70>";
		echo "<img src=$logo width=68></td>";
		//echo "<font size=3><strong>$identitas[Yayasan]</strong></font><br>";
		echo "<td align=center colspan=6 id=header><font size=5><strong>$identitas[Nama]</strong></font><font size=2><br>&nbsp;</font><font size=1>";
		echo $identitas['Alamat1'];
     	echo " Telp. ".$identitas['Telepon'].", Fax. ".$identitas['Fax'].", Website:".$identitas['Website'].", Email:".$identitas['Email']."</font>";
echo "</td></tr><tr><td align=center colspan=7><font size=-1><hr style='border-bottom:double' width=650 align=center></td></tr></table>";
echo "<center><font size=4 id=header><strong>DAFTAR NILAI</strong></font></center>";
echo "<table border=0 align=center><tr>";
//nama mhsw
echo "<td width=100 align=left><font size=1>Nama Mahasiswa</font></td>
<td width=5><font size=1>:</td>
<td align=left><font size=1>$mhs[Nama]</font></td>
";
//no ijazah
echo "</tr>";
//tahun masuk / nim
echo "<tr><td align=left><font size=1>Tahun Masuk / NIM</font></td>
<td><font size=1>:</td>
<td align=left><font size=1>$mhs[TahunID] / $mhs[MhswID]</font></td>
";
//tgl wisuda
echo "</tr>";
//Tempat tgl lahir
echo "<tr><td align=left><font size=1>Tempat/Tanggal Lahir</font></td>
<td><font size=1>:</td>
<td align=left><font size=1>$mhs[TempatLahir] / $TanggalLahir</font></td>
</tr>";
//Fakultas
echo "<tr><td align=left><font size=1>Fakultas</font></td>
<td><font size=1>:</td>
<td align=left><font size=1>$fak[NamaFak]</font></td>
</tr>";
//Jurusan
echo "<tr><td align=left><font size=1>Jurusan/Prog.Studi</font></td>
<td><font size=1>:</td>
<td align=left><font size=1>$jur[Nama] $prog[NMProg]</font></td>
</tr>";
//Gelar Kesarjanaan
echo "<tr><td align=left><font size=1>Gelar Kesarjanaan</font></td>
<td><font size=1>:</td>
<td align=left><font size=1>$jur[KET] ($jur[Gelar])</font></td>
</tr>";
$sesi=0;
echo "</table>";
echo "<table border=0 width=650 align=center><tr><td>";
//tabel sisi kiri
echo "<table border=0 width=318 align=left valign=top><tr bgcolor=grey>";
echo "<td align=center><font size=1><strong>Kode</strong></td>";
echo "<td align=center><font size=1><strong>Matakuliah</strong></td>";
echo "<td align=center><font size=1><strong>SKS</strong></td>";
echo "<td align=center><font size=1><strong>Nilai</strong></td>";
echo "<td align=center><font size=1><strong>Mutu</strong></td></tr>";

$totalmutu=0;
$totalsks=0;
if ($program=='R') {
	if ($prog[NMProg]=='S1') {	$SesiKiri=4;	$SesiKanan=8;	}
	else {						$SesiKiri=3;	$SesiKanan=6; 	}
}
else {
	if ($prog[NMProg]=='S1') {	$SesiKiri=2; 	$SesiKanan=4;	}
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
$s = "select k.MKKode,k.Nama,k.SKS,k.GradeNilai,k.BobotNilai
    from khs h, krs k
    where h.KodeID = '".KodeID."'
	  And h.Sesi=$sesi
	  And h.KHSID=k.KHSID
	  And k.MhswID=$mhs[MhswID]
	  And k.Tinggi='*'
	  And k.GradeNilai !='T'
	  And k.GradeNilai !='E'
	  and k.GradeNilai !='-'
	  and k.GradeNilai !=''
      and h.NA = 'N'
    order by k.MKKode";
}
else {
$sesiB=$prog[NMProg].' Reg B Sem '.$sesi;
$s = "select distinct(k.MKKode),m.Nama,m.SKS,k.GradeNilai,k.BobotNilai
    from mk m, krs k,mkpaket mp,mkpaketisi mpi
    where m.KodeID = '".KodeID."'
      and mp.ProdiID = '$mhs[ProdiID]'
	  And mp.Nama='$sesiB'
	  And mpi.MKPaketID=mp.MKPaketID
	  And m.MKID=mpi.MKID
	  And m.MKID=k.MKID
	  And k.MhswID=$mhs[MhswID]
	  And k.Tinggi='*'
	  And k.GradeNilai !='T'
	  And k.GradeNilai !='E'
	  and k.GradeNilai !='-'
	  and k.GradeNilai !=''
      and m.NA = 'N'
	  and mpi.NA='N'
    order by m.MKKode";
}
$r=_query($s);
$jmlsks=0;
$jmlmutu=0;
$lg=0;
$h=_num_rows($r);
if ($h>0){
echo "<tr><td colspan=5><font size=1><strong>Semester $_sesi</font></td></tr>";

	while ($w=_fetch_array($r)) {
	$lg++;
	if ($lg==1) {
		echo "<tr bgcolor=lightgrey valign=top>";
		$lg=$lg-2;
	}
	else {
	echo "<tr valign=top>";
	}
	
		echo "<td><font size=1>$w[MKKode]</td>";
		echo "<td><font size=1>$w[Nama]</td>";
		echo "<td align=center><font size=1>$w[SKS]</td>";
		echo "<td align=center><font size=1>$w[GradeNilai]</td>";
		$mutu=$w['SKS']*$w['BobotNilai']+0;
		$totalmutu=($totalmutu + $mutu)+0;
		$totalsks=($totalsks + $w['SKS'])+0;
		$jmlsks=($jmlsks+$w[SKS])+0;
		$jmlmutu=($jmlmutu+$mutu)+0;
		echo "<td align=center><font size=1>$mutu</td></tr>";
	}
	echo "<tr><td colspan=2 align=right><font size=1><b>Jumlah :</td><td align=center><font size=1><b>$jmlsks</b></td><td>&nbsp;</td><td align=center><font size=1><b>$jmlmutu</td></tr>";
}
}
echo "</table>";

//tabel sisi kanan
echo "<table border=0 width=318 align=right valign=top><tr bgcolor=grey>";
echo "<td align=center><font size=1><strong>Kode</strong></td>";
echo "<td align=center><font size=1><strong>Matakuliah</strong></td>";
echo "<td align=center><font size=1><strong>SKS</strong></td>";
echo "<td align=center><font size=1><strong>Nilai</strong></td>";
echo "<td align=center><font size=1><strong>Mutu</strong></td></tr>";

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
$s = "select k.MKKode,k.Nama,k.SKS,k.GradeNilai,k.BobotNilai
    from khs h, krs k
    where h.KodeID = '".KodeID."'
	  And h.Sesi=$sesi
	  And h.KHSID=k.KHSID
	  And k.MhswID=$mhs[MhswID]
	  And k.Tinggi='*'
	  And k.GradeNilai !='T'
	  And k.GradeNilai !='E'
	  and k.GradeNilai !='-'
	  and k.GradeNilai !=''
      and h.NA = 'N'
    order by k.MKKode";
}
else {
$sesiB=$prog[NMProg].' Reg B Sem '.$sesi;
$s = "select k.MKKode,m.Nama,m.SKS,k.GradeNilai,k.BobotNilai
    from mk m, krs k,mkpaket mp,mkpaketisi mpi
    where m.KodeID = '".KodeID."'
      and mp.ProdiID = '$mhs[ProdiID]'
	  And mp.Nama='$sesiB'
	  And mpi.MKPaketID=mp.MKPaketID
	  And m.MKID=mpi.MKID
	  And m.MKID=k.MKID
	  And k.MhswID=$mhs[MhswID]
	  And k.Tinggi='*'
	  And k.GradeNilai !='T'
	  And k.GradeNilai !='E'
	  and k.GradeNilai !='-'
	  and k.GradeNilai !=''
      and m.NA = 'N'
	  and mpi.NA='N'
    order by m.MKKode";
}
$r=_query($s);
$h=_num_rows($r);
if ($h>0){
echo "<tr><td colspan=5><strong><font size=1>Semester $_sesi</font></td></tr>";
$jmlsks=0;
$jmlmutu=0;
$lg=0;
	while ($w=_fetch_array($r)) {
		$lg++;
		if ($lg==1) {
			echo "<tr bgcolor=lightgrey valign=top>";
		$lg=$lg-2;
		}
		else {
			echo "<tr valign=top>";
		}
		echo "<td><font size=1>$w[MKKode]</td>";
		echo "<td><font size=1>$w[Nama]</td>";
		echo "<td align=center><font size=1>$w[SKS]</td>";
		echo "<td align=center><font size=1>$w[GradeNilai]</td>";
		$mutu=$w['SKS']*$w['BobotNilai']+0;
		$totalmutu=($totalmutu + $mutu)+0;
		$totalsks=($totalsks + $w['SKS'])+0;
		$jmlsks=($jmlsks+$w[SKS])+0;
		$jmlmutu=($jmlmutu+$mutu)+0;
		echo "<td align=center><font size=1>$mutu</td></tr>";
	}
	echo "<tr><td colspan=2 align=right><font size=1><b>Jumlah :</td><td align=center><font size=1><b>$jmlsks</b></td><td>&nbsp;</td><td align=center><font size=1><b>$jmlmutu</td></tr>";
	}
}
$ipk=($totalmutu/$totalsks)+0;
echo "</table></td></table><br />";
	$prog2 = GetFields('prodi p, jenjang j', "j.JenjangID=p.JenjangID and ProdiID", $mhs[ProdiID], 'KAPITAL(j.Nama) as NMProg');

echo "<table width=600 align=center border=0>
		<tr><td width=170><font size=2>Total Angka Mutu </td><td width=3><font size=2>:</td><td><font size=2><b>$totalmutu</td></tr>
		<tr><td><font size=2>Total SKS </td><td><font size=1>:</td><td><font size=2><b>$totalsks</td></tr>
		<tr><td><font size=2>Indeks Prestasi Komulatif (IPK) </td><td><font size=2>:</td><td><font size=2><b>";
		printf ("%01.2f", $ipk);
		$Yudisium= GetaField('wisudawan', "MhswID", $mhswid, 'Predikat');
		echo "</table>";
		$mhswTA=GetFields('wisudawan w,ta t',"t.MhswID=w.MhswID And w.MhswID",$mhs[MhswID],'UPPER(w.Judul) as JudulTA,w.Pembimbing,t.TglUjian');
	
	//Nama Ketua Jurusan
	 $strProdiID = '.'.$mhs[ProdiID].'.';
  	$pjbt = GetFields('pejabat', "LOCATE('$strProdiID',KodeJabatan) and KodeID",KodeID, "*");
	// Nama Dekan Fakultas
	$dekan = GetFields('pejabat', "KodeJabatan",$fak[NamaFak], "*");
	//Fakultas, Jurusan, Program Studi
	$fak2 = GetFields('prodi p, fakultas f', "f.FakultasID=p.FakultasID and p.ProdiID", $mhs[ProdiID], 'KAPITAL(f.Nama) as NamaFak');
	$jur2 = GetFields('prodi', "ProdiID", $mhs[ProdiID], 'KAPITAL(Nama) as NMJur,Gelar,Keterangan, Jabatan, Pejabat');
	// Tanggal Lulus Sidang
	$tglLLS= GetFields('ta', "MhswID", $mhswid, "date_format(TglUjian, '%d') as Tgl, date_format(TglUjian, '%m') as bln, date_format(TglUjian, '%Y') as Thn");
	//Konversi Bulan Lulus Sidang

	//Judul dan lain-lain
	echo "<table width=650 align=center border=0><tr><td>";
	$jbtKajur="$jur2[NMJur]";
	echo "<table width=300><tr><td width=100%><font size=2>&nbsp;</td></tr>
	<tr><td><font size=2 class='kapital'>Ketua Jurusan $jbtKajur,</font>
	<br><font size=2>&nbsp;<br><font size=2>&nbsp;<br><font size=2>&nbsp;<br><font size=2>&nbsp;<br><font size=2>&nbsp;<br><font size=2><b>$jur2[Pejabat]</b>
	<br><font size=2>NIDN. $pjbt[NIP]</td></tr>
	</table></td><td>
	<table width=20><tr><td width=100%><font size=2>&nbsp;</td></tr>
	<td><table border=0 height=185 width=100><tr><td align=center></td></tr></table></td></tr></table></td><td>";
	$TanggalSekarang = TanggalFormat(date('Y-m-d'));
	echo "
	<table><tr><td width=100%><font size=2>Padang, $TanggalSekarang</td></tr>
	<tr><td><font size=2>Dekan Fakultas $fak2[NamaFak],
	<br><font size=2>&nbsp;<br><font size=2>&nbsp;<br><font size=2>&nbsp;<br><font size=2>&nbsp;<br><font size=2>&nbsp;<br><font size=2><b>$dekan[Nama]</b>
	<br><font size=2>NIDN. $dekan[NIP]</td></tr>
	</table></td></tr></table>";
	
?>
</body>