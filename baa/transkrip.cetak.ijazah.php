<!-- 	
Author	: Arisal Yanuarafi
Start	: 28 Februari 2012 --><head><title>Cetak Transkrip Mahasiswa</title></head>

<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
<link href="cetak.css" rel="stylesheet" type="text/css">
<style>
table,font { font-family:face='Monotype Corsiva'; line-height:120%; }
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

	$mhs = GetFields('mhsw', "MhswID", $mhswid, '*');
	$mhstgl = GetFields('mhsw', "MhswID", $mhswid, "Kapital(TempatLahir) as TLH, date_format(TanggalLahir, '%d') as Tgl, date_format(TanggalLahir, '%m') as _BlnLahir, date_format(TanggalLahir, '%Y') as Thn");
	
	//Konversi Bulan Lhr
  if (($mhstgl[_BlnLahir])==1) {  		$bulan="Januari";  }
  else if (($mhstgl[_BlnLahir])==2) {  	$bulan="Februari";  }
    else if (($mhstgl[_BlnLahir])==3) {  $bulan="Maret";  }
    else if (($mhstgl[_BlnLahir])==4) {  $bulan="April";  }
    else if (($mhstgl[_BlnLahir])==5) {  $bulan="Mei";  }
    else if (($mhstgl[_BlnLahir])==6) {  $bulan="Juni";  }
    else if (($mhstgl[_BlnLahir])==7) {  $bulan="Juli";  }
    else if (($mhstgl[_BlnLahir])==8) {  $bulan="Agustus";  }
    else if (($mhstgl[_BlnLahir])==9) {  $bulan="September";  }
    else if (($mhstgl[_BlnLahir])==10) {  $bulan="Oktober";  }
    else if (($mhstgl[_BlnLahir])==11) {  $bulan="November";  }
    else if (($mhstgl[_BlnLahir])==12) {  $bulan="Desember";  }

	$tgl_wisuda = GetFields('wisuda', "NA", N, "date_format(TglWisuda, '%d') as Tgl, date_format(TglWisuda, '%m') as bln, date_format(TglWisuda, '%Y') as Thn");
	//Konversi Bulan Wisuda
  if (($tgl_wisuda[bln])==1) {        $bulanW="Januari";   }
  else if (($tgl_wisuda[bln])==2) {   $bulanW="Februari"; }
    else if (($tgl_wisuda[bln])==3) { $bulanW="Maret"; }
    else if (($tgl_wisuda[bln])==4) { $bulanW="April"; }
    else if (($tgl_wisuda[bln])==5) { $bulanW="Mei"; }
    else if (($tgl_wisuda[bln])==6) { $bulanW="Juni";}
    else if (($tgl_wisuda[bln])==7) { $bulanW="Juli"; }
    else if (($tgl_wisuda[bln])==8) { $bulanW="Agustus"; }
    else if (($tgl_wisuda[bln])==9) { $bulanW="September"; }
    else if (($tgl_wisuda[bln])==10) {$bulanW="Oktober"; }
    else if (($tgl_wisuda[bln])==11) {$bulanW="November"; }
    else if (($tgl_wisuda[bln])==12) {$bulanW="Desember"; }
	$nmrIjazah = GetaField ('wisudawan',"MhswID", $mhswid, 'NomerIjazah');
	$nmrTranskrip = GetaField ('wisudawan',"MhswID", $mhswid, 'NomerTranskrip');
	$program = GetaField ('mhsw',"MhswID", $mhswid, 'ProgramID');
	$fak = GetFields('prodi p, fakultas f', "f.FakultasID=p.FakultasID and p.ProdiID", $mhs[ProdiID], 'Kapital(f.Nama) as NamaFak');
	$jur = GetFields('prodi', "ProdiID", $mhs[ProdiID], 'Kapital(Nama) as Nama,Gelar,Kapital(Keterangan) as KET, NoSKBAN');
	$prog = GetFields('prodi p, jenjang j', "j.JenjangID=p.JenjangID and ProdiID", $mhs[ProdiID], 'j.Nama as NMProg');
	if ($prog[NMProg] =='S1') $_jenjang='Strata Satu';
	if ($prog[NMProg] =='D3') $_jenjang='Diploma Tiga';
 	$identitas = GetFields('identitas', "Kode", KodeID, 'Alamat1,Nama,Yayasan,Telepon,Fax,Website, Email');
    	echo "<table width=900 align=center><tr><td align=right><font size=2><b>No. $nmrIjazah</b></font></td></tr></table><br><table border=0 width=900 align=center height=80><tr><td width=70>";
		echo "<td align=center colspan=6 id=header><font size=6><strong>INSTITUT TEKNOLOGI PADANG<br>";
		echo "( I T P )<br></strong></font>";
echo "<br><center><font size=4 face='Monotype Corsiva'>dengan ini menyatakan bahwa</font></center><br>";
echo "<table border=0 width=900 align=center><tr>";
//nama mhsw
echo "<td align=center><font size=6 face='Monotype Corsiva'><b>$mhs[Nama]</b></font></td>";
//tahun masuk / nim
echo "<tr><td align=center><font size=3>No. BP. $mhs[MhswID]<br><br></font></td>";
//isi
if ($jur[Gelar]=='S.T.') {
$sebutan="diberikan gelar";
$_sebutan="gelar";
}
else {
$sebutan="memakai sebutan profesional";
$_sebutan="sebutan profesional";
}

echo "<tr><td align=center><font size=4 face='Monotype Corsiva'>Lahir di $mhstgl[TLH] Tanggal $mhstgl[Tgl] $bulan $mhstgl[Thn] telah menyelesaikan dengan baik dan memenuhi segala syarat pendidikan <br>pada Program Studi $_jenjang $jur[Nama], Fakultas $fak[NamaFak],<br>
status Terakreditasi nomor $jur[NoSKBAN]<br>Oleh sebab itu kepadanya berhak $sebutan<Br></font><br>
<font size=6 face='Monotype Corsiva'><b>$jur[KET] ($jur[Gelar])</b><br></font><br>
<font size=4 face='Monotype Corsiva'>beserta segala hak dan kewajiban yang melekat pada $_sebutan tersebut.<br>Diberikan di Padang pada tanggal $tgl_wisuda[Tgl] $bulanW $tgl_wisuda[Thn]</font>
</td>
</tr></table>";


	$prog2 = GetFields('prodi p, jenjang j', "j.JenjangID=p.JenjangID and ProdiID", $mhs[ProdiID], 'KAPITAL(j.Nama) as NMProg');
if ($mhs['StatusAwalID']=='P' && $prog2[NMProg]=='S1') {
$TotalSKSProdi = GetaField('prodi',"ProdiID",$mhs[ProdiID],'TotalSks');
$sksKonversi=GetaField('mk m,krskonversi k',"m.MKKode=k.MKKode and m.ProdiID = '$mhs[ProdiID]' and m.NA='N' and k.MhswID",$mhs[MhswID],'sum(m.SKS)');
$_sksKonversi=$sksKonversi+0;
}

		$Yudisium= GetaField('wisudawan', "MhswID", $mhswid, 'Predikat');
		$mhswTA=GetFields('wisudawan w,ta t',"t.MhswID=w.MhswID And w.MhswID",$mhs[MhswID],'UPPER(w.Judul) as JudulTA,w.Pembimbing,t.TglUjian');
	
	//Nama Ketua Jurusan
	 $strProdiID = '.'.$mhs[ProdiID].'.';
  	$pjbt = GetFields('pejabat', "KodeJabatan='Ketua' and KodeID",KodeID, "*");
	// Nama Dekan Fakultas
	$dekan = GetFields('pejabat', "KodeJabatan",$fak[NamaFak], "*");
	//Fakultas, Jurusan, Program Studi
	$fak2 = GetFields('prodi p, fakultas f', "f.FakultasID=p.FakultasID and p.ProdiID", $mhs[ProdiID], 'KAPITAL(f.Nama) as NamaFak');
	$jur2 = GetFields('prodi', "ProdiID", $mhs[ProdiID], 'KAPITAL(Nama) as NMJur,Gelar,Keterangan');
	// Tanggal Lulus Sidang
	$tglLLS= GetFields('ta', "MhswID", $mhswid, "date_format(TglUjian, '%d') as Tgl, date_format(TglUjian, '%m') as bln, date_format(TglUjian, '%Y') as Thn");
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
if ($SesiKanan==6) {
echo "<div class='page-break'></div><br /><br /><br /><br />";
}
	//Judul dan lain-lain
	
	echo "<br><font size=5>&nbsp;<br><table width=720 align=center border=0><tr><td>";
	$jbtKajur="$jur2[NMJur] $prog2[NMProg]";
	echo "<table width=280 valign=top>
	<tr><td><font size=4 face='Monotype Corsiva'>Fakultas $fak2[NamaFak], <br><font size=4 face='Monotype Corsiva'>Dekan, <br><font size=2>&nbsp;<br><font size=3>&nbsp;<br><font size=4>&nbsp;<br><font size=2>&nbsp;<br><font size=4 face='Monotype Corsiva'><b>$dekan[Nama]</b>
	<br><font size=4 face='Monotype Corsiva'>NIDN. $dekan[NIP]</td></tr>
	</table></td><td align=left>
	<table width=100 align=right><tr><td width=100%><font size=2>&nbsp;</td></tr>
	<td><table border=1 height=115 width=100><tr><td align=center><b>Pas Foto<br>4 x 6</b></td></tr></table></td></tr></table></td><td>
	<table valign=top align=right>
	<tr><td><font size=4 face='Monotype Corsiva'>Rektor,
	<br><font size=4><br></font><font size=2>&nbsp;<br><font size=3>&nbsp;<br><font size=4>&nbsp;<br><font size=2>&nbsp;<br><font size=4 face='Monotype Corsiva'><b>$pjbt[Nama]</b>
	<br><font size=4 face='Monotype Corsiva'>NIDN. $pjbt[NIP]</td></tr>
	</table></td></tr></table>";
	
?>
</body>