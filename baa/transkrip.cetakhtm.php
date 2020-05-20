<!-- 	
Author	: Arisal Yanuarafi
Start	: 28 Februari 2012 --><head><title>Cetak Transkrip Mahasiswa</title></head>


<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
<link href="cetak.css" rel="stylesheet" type="text/css">
<style>
table,font { font-family:Times; line-height:100%; }
.header{ font-family:Times; font-size:32px; line-height:90%; }
.garis {height:0px; line-height:0px;}
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

//$namafile = "$mhswid.xls";
//header("Content-type:application/vnd.ms-excel");
//header("Content-Disposition:attachment;filename=$namafile");
//header("Expires:0");
//header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
//header("Pragma: public");

	$mhs = GetFields('mhsw', "MhswID", $mhswid, '*,left(TahunID,4) as TahunMasukMhsw');
	$mhstgl = GetFields('mhsw', "MhswID", $mhswid, "date_format(TanggalLahir, '%d') as Tgl, date_format(TanggalLahir, '%m') as _BlnLahir, date_format(TanggalLahir, '%Y') as Thn");
	
	//Konversi Bulan Lhr
  if (($mhstgl[_BlnLahir])==1) {  $bulan="JANUARI";  }
  else if (($mhstgl[_BlnLahir])==2) {  $bulan="FEBRUARI";  }
    else if (($mhstgl[_BlnLahir])==3) {  $bulan="MARET";  }
    else if (($mhstgl[_BlnLahir])==4) {  $bulan="APRIL";  }
    else if (($mhstgl[_BlnLahir])==5) {  $bulan="MEI";  }
    else if (($mhstgl[_BlnLahir])==6) {  $bulan="JUNI";  }
    else if (($mhstgl[_BlnLahir])==7) {  $bulan="JULI";  }
    else if (($mhstgl[_BlnLahir])==8) {  $bulan="AGUSTUS";  }
    else if (($mhstgl[_BlnLahir])==9) {  $bulan="SEPTEMBER";  }
    else if (($mhstgl[_BlnLahir])==10) {  $bulan="OKTOBER";  }
    else if (($mhstgl[_BlnLahir])==11) {  $bulan="NOVEMBER";  }
    else if (($mhstgl[_BlnLahir])==12) {  $bulan="DESEMBER";  }

	$tgl_wisuda = TanggalFormat(GetaField('wisuda', "NA", N, "TglWisuda"));
	$nmrIjazah = GetaField ('wisudawan',"MhswID", $mhswid, 'NomerIjazah');
	$nmrTranskrip = GetaField ('wisudawan',"MhswID", $mhswid, 'NomerTranskrip');
	$program = GetaField ('mhsw',"MhswID", $mhswid, 'ProgramID');
	$fak = GetFields('prodi p, fakultas f', "f.FakultasID=p.FakultasID and p.ProdiID", $mhs[ProdiID], 'f.Nama as NamaFak');
	$jur = GetFields('prodi', "ProdiID", $mhs[ProdiID], 'Nama,Gelar,UPPER(Keterangan) as KET');
	$prog = GetFields('prodi p, jenjang j', "j.JenjangID=p.JenjangID and ProdiID", $mhs[ProdiID], 'j.Nama as NMProg');
 	$identitas = GetFields('identitas', "Kode", KodeID, 'Alamat1,Nama,Yayasan,Telepon,Fax,Website, Email');
    	$logo = "http://sisfo.itp.ac.id/sisfo/img/logo.jpg";
		if ($prog[NMProg]=='D3') { $brtop="<br><br><br>"; }
		if ($prog[NMProg]=='S1' && $mhs[ProgramID]=='N') { $brtop=""; }
		else { $brtop="<br><br>"; }
    	echo "$brtop<table border=0 width=650 align=center height=80><tr><td width=70 rowspan=2 align=right>";
		echo "<img src=$logo width=50></td>";
		//echo "<font size=3><strong>$identitas[Yayasan]</strong></font><br>";
		echo "<td align=center colspan=6 class=header valign=bottom><strong>INSTITUT TEKNOLOGI PADANG</strong></td></tr>";
		echo "<tr valign=top><td align=center><font size=1>".$identitas['Alamat1'];
     	echo " Telp. ".$identitas['Telepon'].", Fax. ".$identitas['Fax'].", Website:".$identitas['Website'].", Email:".$identitas['Email']."</font>";
echo "</td></tr><tr valign=top class=garis><td align=center colspan=7 height=3><font size=1><hr style='border-bottom:double' width=650 align=center></td></tr></table>";
echo "<center><font size=4 id=header><strong>TRANSKRIP AKADEMIK</strong></font><br><font size=2><strong>No. $nmrTranskrip</center>";
echo "<br>";
echo "<table border=0 width=650 align=center><tr>";
//nama mhsw
echo "<td width=100 align=left><font size=1>Nama Mahasiswa</font></td>
<td width=5><font size=1>:</td>
<td width=270 align=left><strong><font size=1>$mhs[Nama], $jur[Gelar]</font></td>
<td width=10><font size=1>&nbsp;</td>";
//no ijazah
echo "<td width=80 align=left><font size=1>Nomor Ijazah</font></td>
<td width=5><font size=1>:</td>
<td width=140 align=left><strong><font size=1>$nmrIjazah</font></td></tr>";
//tahun masuk / nim
echo "<tr><td align=left><font size=1>Tahun Masuk / NIM</font></td>
<td><font size=1>:</td>
<td align=left><strong><font size=1>$mhs[TahunMasukMhsw] / $mhs[MhswID]</font></td>
<td><font size=1>&nbsp;</td>";
//tgl wisuda
echo "<td align=left><font size=1>Tanggal Wisuda</font></td>
<td ><font size=1>:</td>
<td align=left><font size=1>$tgl_wisuda</font></td></tr>";
//Tempat tgl lahir
echo "<tr><td align=left><font size=1>Tempat/Tanggal Lahir</font></td>
<td><font size=1>:</td>
<td align=left><font size=1>$mhs[TempatLahir] / $mhstgl[Tgl] $bulan $mhstgl[Thn]</font></td>
<td><font size=1>&nbsp;</td></tr>";
//Fakultas
echo "<tr><td align=left><font size=1>Fakultas</font></td>
<td><font size=1>:</td>
<td align=left><font size=1>$fak[NamaFak]</font></td>
<td ><font size=1>&nbsp;</td></tr>";
//Jurusan
echo "<tr><td align=left><font size=1>Jurusan/Prog.Studi</font></td>
<td><font size=1>:</td>
<td align=left><font size=1>$jur[Nama] $prog[NMProg]</font></td>
<td><font size=1>&nbsp;</td></tr>";
//Gelar Kesarjanaan
echo "<tr><td align=left><font size=1>Gelar Kesarjanaan</font></td>
<td><font size=1>:</td>
<td align=left><font size=1>$jur[KET] ($jur[Gelar])</font></td>
<td><font size=1>&nbsp;</td></tr>";
$sesi=0;
echo "</table><br>";
echo "<table border=0 width=650 align=center><tr><td>";
//tabel sisi kiri
echo "<table border=0 width=318 align=left><tr bgcolor=grey>";
echo "<td align=center><font size=2><strong>Kode</strong></td>";
echo "<td align=center><font size=2><strong>Matakuliah</strong></td>";
echo "<td align=center><font size=2><strong>SKS</strong></td>";
echo "<td align=center><font size=2><strong>Nilai</strong></td>";
echo "<td align=center><font size=2><strong>Mutu</strong></td></tr>";

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
$s = "select k.MKKode,m.Nama,m.SKS,k.GradeNilai,k.BobotNilai
    from mk m, krs k
    where m.KodeID = '".KodeID."'
      and m.ProdiID = '$mhs[ProdiID]'
	  And m.Sesi=$sesi
	  And m.MKID=k.MKID
	  And k.MhswID=$mhs[MhswID]
	  And k.Tinggi='*'
	  And k.GradeNilai !='T'
      and m.NA = 'N'
    order by m.MKKode";
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
      and m.NA = 'N'
	  and mpi.NA='N'
    order by m.MKKode";
}
$r=_query($s);
$jmlsks=0;
$jmlmutu=0;
$lg=0;
echo "<tr><td colspan=5><font size=2><strong>Semester $_sesi</font></td></tr>";
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
echo "</table>";

//tabel sisi kanan
echo "<table border=0 width=318 align=right><tr bgcolor=grey>";
echo "<td align=center><font size=2><strong>Kode</strong></td>";
echo "<td align=center><font size=2><strong>Matakuliah</strong></td>";
echo "<td align=center><font size=2><strong>SKS</strong></td>";
echo "<td align=center><font size=2><strong>Nilai</strong></td>";
echo "<td align=center><font size=2><strong>Mutu</strong></td></tr>";

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
$s = "select k.MKKode,m.Nama,m.SKS,k.GradeNilai,k.BobotNilai
    from mk m, krs k
    where m.KodeID = '".KodeID."'
      and m.ProdiID = '$mhs[ProdiID]'
	  And m.Sesi=$sesi
	  And m.MKID=k.MKID
	  And k.MhswID=$mhs[MhswID]
	  And k.Tinggi='*'
	  And k.GradeNilai !='T'
      and m.NA = 'N'
    order by m.MKKode";
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
      and m.NA = 'N'
	  and mpi.NA='N'
    order by m.MKKode";
}
$r=_query($s);
echo "<tr><td colspan=5><strong><font size=2>Semester $_sesi</font></td></tr>";
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
$ipk=($totalmutu/$totalsks)+0;
echo "</table></td></table><br />";
if ($SesiKanan==8 || $SesiKanan==6) {
echo "<div class='page-break'></div><br /><br /><br /><br /><br /><br />";
}
	$prog2 = GetFields('prodi p, jenjang j', "j.JenjangID=p.JenjangID and ProdiID", $mhs[ProdiID], 'KAPITAL(j.Nama) as NMProg');
if ($mhs['StatusAwalID']=='P' && $prog2[NMProg]=='S1' && $mhs[ProgramID]=='N') {
$TotalSKSProdi = GetaField('prodi',"ProdiID",$mhs[ProdiID],'TotalSks');
$sksKonversi=GetaField('mk m,krskonversi k',"m.MKKode=k.MKKode and m.ProdiID = '$mhs[ProdiID]' and m.NA='N' and k.MhswID",$mhs[MhswID],'sum(m.SKS)');
$_sksKonversi=$sksKonversi+0;
echo "<table width=650 align=center border=0>
		<tr><td><font size=2>Total SKS Program S1 = <strong>$TotalSKSProdi SKS</td></tr>
		<tr><td><font size=2>SKS Yang Diakui dari Jenjang Program Sebelumnya (D3) = <strong>$_sksKonversi SKS</td></tr>
		<tr><td><font size=2>SKS Yang diambil = <strong>$totalsks SKS</td></tr></table><br>";
}
echo "<table width=650 align=center border=0>
		<tr><td><font size=2><strong><u>Bobot Nilai</u>:</font></td></tr>
		<tr><td><font size=2>A = 4 (Istimewa), B = 3 (Baik), C = 2 (Cukup), D = 1 (Kurang)</font></td></tr></table>
		<br>
		<table width=600 align=center border=0>
		<tr><td width=170><font size=2>Total Angka Mutu </td><td width=3><font size=2>:</td><td><font size=2><b>$totalmutu</td></tr>
		<tr><td><font size=2>Total SKS </td><td><font size=1>:</td><td><font size=2><b>$totalsks</td></tr>
		<tr><td><font size=2>Indeks Prestasi Komulatif (IPK) </td><td><font size=2>:</td><td><font size=2><b>";
		printf ("%01.2f", $ipk);
		$Yudisium= GetaField('wisudawan', "MhswID", $mhswid, 'Predikat');
		echo "<tr><td><font size=2>Yudisium </td><td><font size=2>:</td><td><font size=2><b>$Yudisium</td></tr>";
		echo "</table>";
		$mhswTA=GetFields('wisudawan w,ta t',"t.MhswID=w.MhswID And w.MhswID",$mhs[MhswID],'UPPER(w.Judul) as JudulTA,w.Pembimbing,t.TglUjian');
	
	//Nama Ketua Jurusan
	 $strProdiID = '.'.$mhs[ProdiID].'.';
  	$pjbt = GetFields('pejabat', "LOCATE('$strProdiID',KodeJabatan) and KodeID",KodeID, "*");
	// Nama Dekan Fakultas
	$dekan = GetFields('pejabat', "KodeJabatan",$fak[NamaFak], "*");
	//Fakultas, Jurusan, Program Studi
	$fak2 = GetFields('prodi p, fakultas f', "f.FakultasID=p.FakultasID and p.ProdiID", $mhs[ProdiID], 'KAPITAL(f.Nama) as NamaFak');
	$jur2 = GetFields('prodi', "ProdiID", $mhs[ProdiID], 'KAPITAL(Nama) as NMJur,Gelar,Keterangan');
	// Tanggal Lulus Sidang
	$tglLLS= GetFields('wisudawan', "MhswID", $mhswid, "UPPER(Judul) as Judul, upper(Pembimbing) as Pembimbing,date_format(TglSidang, '%d') as Tgl, date_format(TglSidang, '%m') as bln, date_format(TglSidang, '%Y') as Thn");
	//Konversi Bulan Lulus Sidang
  if (($tglLLS[bln])==1) {        $bulanS="Januari";   }
  else if (($tglLLS[bln])==2) {   $bulanS="Februari"; }
    else if (($tglLLS[bln])==3) { $bulanS="Maret"; }
    else if (($tglLLS[bln])==4) { $bulanS="April"; }
    else if (($tglLLS[bln])==5) { $bulanS="Mei"; }
    else if (($tglLLS[bln])==6) { $bulanS="Juni";}
    else if (($tglLLS[bln])==7) { $bulanS="Juli"; }
    else if (($tglLLS[bln])==8) { $bulanS="Agustus"; }
    else if (($tglLLS[bln])==9) { $bulanS="September"; }
    else if (($tglLLS[bln])==10) {$bulanS="Oktober"; }
    else if (($tglLLS[bln])==11) {$bulanS="November"; }
    else if (($tglLLS[bln])==12) {$bulanS="Desember"; }
	//Judul dan lain-lain
	echo "<br><table width=650 align=center border=0>
	<tr><td width=130 valign=top><font size=2>Judul Tugas Akhir</td><td width=3 valign=top><font size=2 class='judul'>:</td><td valign=top><font size=2>$tglLLS[Judul]</td></tr>";
	echo "<tr><td width=130><font size=2>Nama Pembimbing</td><td width=3><font size=2>:</td><td><font size=2>$tglLLS[Pembimbing]</td></tr>";
	echo "<tr><td width=130><font size=2>Tanggal Lulus Sidang</td><td width=3><font size=2>:</td><td><font size=2>$tglLLS[Tgl] $bulanS $tglLLS[Thn]</td></tr></table>";
	echo "<br><table width=650 align=center border=0><tr><td>";
	$jbtKajur="$jur2[NMJur] $prog2[NMProg]";
	echo "<table width=270><tr><td width=100%><font size=2>&nbsp;</td></tr>
	<tr><td><font size=2 class='kapital'>Ketua Jurusan/Prog. Studi $jbtKajur,</font>
	<br><font size=2>&nbsp;<br><font size=2>&nbsp;<br><font size=2>&nbsp;<br><font size=2>&nbsp;<br><font size=2>&nbsp;<br><font size=2><b>$pjbt[Nama]</b>
	<br><font size=2>NIDN. $pjbt[NIP]</td></tr>
	</table></td><td>
	<table width=120><tr><td width=100%><font size=2>&nbsp;</td></tr>
	<td><table border=1 height=105 width=70><tr><td align=center><b>Pas Foto<br>3 x 4</b></td></tr></table></td></tr></table></td><td>
	<table><tr><td width=100%><font size=2>Padang, $tgl_wisuda</td></tr>
	<tr><td><font size=2>Dekan Fakultas $fak2[NamaFak],
	<br><font size=2>&nbsp;<br><font size=2>&nbsp;<br><font size=2>&nbsp;<br><font size=2>&nbsp;<br><font size=2>&nbsp;<br><font size=2><b>$dekan[Nama]</b>
	<br><font size=2>NIDN. $dekan[NIP]</td></tr>
	</table></td></tr></table>";
	
// Matakuliah Konversi (khusus mhs pindahan)
if ($mhs['StatusAwalID']=='P' && $prog2[NMProg]=='S1' && $mhs[ProgramID]=='N') {
echo "<div class='page-break'></div><br /><br /><br /><br />";
$ProdiAsalPT = GetaField('prodidikti',"ProdiDiktiID",$mhs[ProdiAsalPT],'Nama');
echo "<table width=650 align=center><tr><td colspan=3><font size=2><b>Lampiran Matakuliah Konversi dari :</b></td></tr>";
echo "<tr><td width=160><font size=2>Program Studi</td><td width=3><font size=2>:</td><td><font size=2>$ProdiAsalPT</td></tr>";
echo "<tr><td><font size=2>Jenjang</td><td width=3><font size=2>:</td><td><font size=2>$JenjangAsalPT</td></tr>";
echo "<tr><td><font size=2>Tahun Tamat</td><td width=3><font size=2>:</td><td><font size=2>$TahunTamat</td></tr>";
echo "<tr><td width=160><font size=2>Total SKS Konversi</td><td width=3><font size=2>:</td><td><font size=2>$_sksKonversi</td></tr>";
echo "<tr><td><font size=2>Perguruan Tinggi</td><td width=3><font size=2>:</td><td><font size=2>$AsalPT<br></td></tr>";
echo "<tr><td colspan=3><font size=1>&nbsp;</td></tr>";
echo "<tr><td colspan=3><font size=2><b>Daftar Matakuliah Hasil Konversi</b></td></tr></table>";
// Daftar Matakuliah Konversi
echo "<table border=0 width=650 align=center><tr><td>";
//tabel sisi kiri
echo "<table border=0 width=318 align=left><tr bgcolor=grey>";
echo "<td align=center><font size=2><strong>Kode</strong></td>";
echo "<td align=center><font size=2><strong>Matakuliah</strong></td>";
echo "<td align=center><font size=2><strong>SKS</strong></td>";
echo "<td align=center><font size=2><strong>Nilai</strong></td>";
echo "<td align=center><font size=2><strong>Mutu</strong></td></tr>";

$s = "select k.MKKode,m.Nama,m.SKS,k.Nilai,k.Bobot
    from mk m, krskonversi k
    where m.KodeID = '".KodeID."'
      and m.ProdiID = '$mhs[ProdiID]'
	  And m.MKKode=k.MKKode
	  And k.MhswID=$mhs[MhswID]
      and m.NA = 'N'
    order by m.MKKode limit 0,20";
$r=_query($s);
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
		echo "<td align=center><font size=1>$w[Nilai]</td>";
		$mutu=$w['SKS']*$w['Bobot']+0;
		$jmlsks=($jmlsks+$w[SKS])+0;
		$jmlmutu=($jmlmutu+$mutu)+0;
		echo "<td align=center><font size=1>$mutu</td></tr>";
	}
	echo "<tr><td colspan=2 align=right><font size=1><b>Jumlah :</td><td align=center><font size=1><b>$jmlsks</b></td><td>&nbsp;</td><td align=center><font size=1><b>$jmlmutu</td></tr>";

echo "</table>";

//tabel sisi kanan
echo "<table border=0 width=318 align=right><tr bgcolor=grey>";
echo "<td align=center><font size=2><strong>Kode</strong></td>";
echo "<td align=center><font size=2><strong>Matakuliah</strong></td>";
echo "<td align=center><font size=2><strong>SKS</strong></td>";
echo "<td align=center><font size=2><strong>Nilai</strong></td>";
echo "<td align=center><font size=2><strong>Mutu</strong></td></tr>";

$s = "select k.MKKode,m.Nama,m.SKS,k.Nilai,k.Bobot
    from mk m, krskonversi k
    where m.KodeID = '".KodeID."'
      and m.ProdiID = '$mhs[ProdiID]'
	  And m.MKKode=k.MKKode
	  And k.MhswID=$mhs[MhswID]
      and m.NA = 'N'
    order by m.MKKode limit 20,50";
$r=_query($s);
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
		echo "<td align=center><font size=1>$w[Nilai]</td>";
		$mutu=$w['SKS']*$w['Bobot']+0;
		$jmlsks=($jmlsks+$w[SKS])+0;
		$jmlmutu=($jmlmutu+$mutu)+0;
		echo "<td align=center><font size=1>$mutu</td></tr>";
	}
	echo "<tr><td colspan=2 align=right><font size=1><b>Jumlah :</td><td align=center><font size=1><b>$jmlsks</b></td><td>&nbsp;</td><td align=center><font size=1><b>$jmlmutu</td></tr></table></td></tr></table>";
}
?>
</body>