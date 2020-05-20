<?php 
session_start();
if ($_SESSION['_LevelID']=='30' || $_SESSION['_LevelID']=='51' || $_SESSION['_Login']=='auth0rized') { 

 	  include_once "../dwo.lib.php";
	  include_once "../db.mysql.php";
	  include_once "../connectdb.php";
	  include_once "../parameter.php";
	  include_once "../cekparam.php";

	$TahunID = GetSetVar('WTahunID'); 
?>
<html><head><style>
body{backround-color:#fff; color:#000; font-family: 'Arial';}
.yayasan{font-size: 18px; text-transform: uppercase; font-weight: bold;}
.universitas{font-size: 25px; text-transform: uppercase; font-weight: bold;}
.detail{font-size: 10px;}
hr {
    display: block;
    margin-top: 0.5em;
    margin-bottom: 0.5em;
    margin-left: auto;
    margin-right: auto;
    border-style: solid;
    border-width: 2px;
}
td{font-size: 18px}
td h1{font-size: 35px}
.duduk{font-size: 25px}
</style>
</head>
<body>
<?php

 $s1="select ProdiID,Nama,Gelar from prodi where ProdiID='$_SESSION[ProdiID]'";
 $r1=_query($s1);
 while ($w1=_fetch_array($r1)) {
	$s="select m.Telepon,m.Handphone,m.Alamat,w.TanggalLahirFinal,m.NamaAyah,m.NamaIbu,m.IPK,m.TahunID, m.MhswID,m.Nama,m.ProdiID,m.TempatLahir,m.Foto,m.FotoWisuda,m.TanggalLahir,m.ProgramID from wisudawan w left outer join mhsw m on m.MhswID=w.MhswID where m.ProdiID='$w1[ProdiID]' and w.TahunID='$TahunID' order by w.NomorDuduk DESC";
				$r=_query($s);
				$n=_num_rows($r);
				$_n += $n;
if ($n != 0) {
$jenjang= GetaField('prodi',"ProdiID",$w1['ProdiID'],"JenjangID");
$_jenjang= GetaField('jenjang',"JenjangID",$jenjang,"Nama");
}
$nj=0;
				while ($w=_fetch_array($r)) {
				//if($w['FotoWisuda']!='') {
				$nj++;
  	 				
	//jurusan dan fakultas
	$judul = GetFields('wisudawan', "MhswID", $w['MhswID'], 'Judul, Pembimbing,Pembimbing2,Predikat, NomorDuduk');
	$jur = GetFields('prodi', "ProdiID", $w['ProdiID'], 'Nama as Nama, FakultasID');
	$fak = GetaField('fakultas', "FakultasID", $jur['FakultasID'], 'Nama');
	$wisuda = GetFields('wisuda', "NA='N' and KodeID", KodeID, 'Nama,TglWisuda');
?><table width=660>
<tr><td width='100'><img src='../img/logo.jpg' width="100"></td>
<td width="720"><div class="yayasan">Yayasan Pendidikan Bung Hatta</div>
<div class="universitas">Universitas Bung Hatta</div>
<div class="detail">Kampus Proklamator I : Jl. Sumatera Ulak Karang Padang 25133, Telp: 0751 - 7051678 / 7052096 Fax. 7055475</div>
<div class="detail">Kampus Proklamator II : Jl. By Pass Padang 25176, Telp: 0751 - 463250</div>
<div class="detail">Kampus Proklamator III : Jl. Gajah Mada No. 19 Olo Nanggalo Padang 25143, Telp: 0751 - 7054257 Fax. 7051341</div>
<div class="detail">Email: rektorat@bunghatta.ac.id, Website: www.bunghatta.ac.id</div>
</td>
</tr>
<tr><td colspan=2><hr></td></tr>
</table>
<table width=660><tr><td align=right>No. Tempat Duduk: <span class="duduk"><?php echo str_pad($judul['NomorDuduk'],4,"0", STR_PAD_LEFT);?></span></td></tr></table>
<table width=660><tr><td align=CENTER><h1>KARTU WISUDA</h1></td></tr></table>
<table width=660>
<tr><td width="150">Nama</td><td width="2">:</td><td><?php echo $w['Nama'];?></td></tr>
<tr><td width="150">NPM</td><td width="2">:</td><td><?php echo $w['MhswID'];?></td></tr>
<tr><td width="150">Fakultas</td><td width="2">:</td><td><?php echo $fak;?></td></tr>
<tr><td width="150">Program Studi</td><td width="2">:</td><td><?php echo $jur['Nama'].($w['ProgramID']=='P'? " / PPKHB":"");?></td></tr>
<tr><td colspan=3><br />telah memenuhi persyaratan administrasi untuk diwisuda pada :</td></tr>
<tr><td width="150">Periode</td><td width="2">:</td><td><?php echo $wisuda['Nama'];?></td></tr>
<tr><td width="150">Tanggal</td><td width="2">:</td><td><?php echo TanggalFormat($wisuda['TglWisuda']);?></td></tr>
</table>
<table width=660 style="margin-top:40px">
<tr>
<td width="50%" align="right"><span style="font-family: 'free 3 of 9'; margin-right:20px; font-size:40px;"></span><img src='../foto/wisudawan/<?php echo ($_SESSION['TahunWisuda']=='20151'? 'kecil/':'sedang/').$w['FotoWisuda']; ?>' width=130 style="margin-right:20px"></td>
<td width="50%">Padang, <?php echo TanggalFormat(date('Y-m-d'));?><br>A.n. Rektor<br>Kepala Biro Administrasi Akademik dan Kemahasiswaan (BAAK),<br><img src="TTDKABAAK.png" style="margin-left:-70px;"><br>Hendra Kusuma, S.Pi, M.Si</td>
</tr>
</table>
<div style="page-break-after: always;"></div>
<?php
				}
	}
}
?><title>Cetak Buku Wisuda</title>


