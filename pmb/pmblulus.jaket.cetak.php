<?php

session_start();
	/* 	Author	: Arisal Yanuarafi
		Start	: 27 Apr 2014  */
	
	include_once "../dwo.lib.php";
  	include_once "../db.mysql.php";
  	include_once "../connectdb.php";
  	include_once "../parameter.php";
  	include_once "../cekparam.php";
//Variable
$PMBID = GetSetVar('pmbid');
$AplikanID = GetSetVar('aplid');
$w = GetFields("pmb", "AplikanID='$AplikanID' and PMBID", $PMBID,"*");
?>
<html>
<head>
<title>Cetak Blanko Almamater</title>
<style> 
	html,body,table,tr,th,td{font-family:'Arial'; font-size:14px;}
	table,hr{margin:0 auto; width:700px}
	h5{font-size:14px}
	h3{font-size:19px}
	.isi{font-size:14px}
	.prg{ text-align:center}
	.content{font-size:14px;width:700px; margin:0 auto; text-align:justify}
	.footer{margin-top:30px; width:700px;}
	.alamat{font-size:11px;margin-top:-10px;}
</style>
<style media='print'>
	.onlyscreen {
		display: none;
	}
	</style>
</head>
<body>
<table border="0">
	<tr>
		<td width="65"><img src="../img/020713114635_ubh.jpg" width="60" style="margin:0 auto" /></td>
        <td valign="bottom"><h5 style="line-height:0.01em; ">PENGUKURAN JAKET ALMAMATER</h5>
        	<h3 style="line-height:0.1em; margin-top:-6px; ">MAHASISWA BARU UNIVERSITAS BUNG HATTA</h3>
            <p class="alamat">Kampus I Jl. Sumatera Ulak Karang Padang, www.bunghatta.ac.id | rektorat@bunghatta.ac.id
            </p>
        </td>
    </tr>
</table>
<hr style="border-bottom:double #000 3px;" />
<p class="content" style="margin-top:20px">Saya yang bertandatangan dibawah ini :</p>
<table class="isi" border="0">
<?php 
	$arr = array();
  	$arr[] = array('Nama', ':', $w['Nama']);
	$arr[] = array('Tempat/Tgl. Lahir', ':', $w['TempatLahir'].' / '.TanggalFormat($w['TanggalLahir']));
	$arr[] = array('Nomor Pokok Mahasiswa', ':', $w['MhswID']);
	$arr[] = array('Fakultas', ':', GetaField('prodi p left outer join fakultas f on f.FakultasID=p.FakultasID', "p.ProdiID='$w[ProdiID]' and p.KodeID",KodeID,"f.Nama"));
	$arr[] = array('Jurusan', ':', GetaField('prodi', "ProdiID='$w[ProdiID]' and KodeID",KodeID,"Nama"));
	$arr[] = array('Alamat', ':', $w['Alamat']);
	$arr[] = array('Telpon/HP', ':', $w['Handphone']);
	$arr[] = array('Golongan Darah', ':', $w['GolonganDarah']);
	$arr[] = array('Status Mahasiswa', ':', GetaField('statusawal', "StatusAwalID",$w['StatusAwalID'],"Nama"));
	$arrPrestasiTambahan = explode('~', $w['PrestasiTambahan']);
	$arr[] = array('Prestasi', ':', "1. $arrPrestasiTambahan[0]<br>2. $arrPrestasiTambahan[1] <br>3. $arrPrestasiTambahan[2]");
	$arr[] = array('Ukuran Jaket', ':', $w['UkuranJaket']);
	$arr[] = array('Nama Ayah', ':', $w['Nama Ayah']);
	$arr[] = array('Nama Ibu', ':', $w['Nama Ibu']);
	$arr[] = array('Pekerjaan', ':', $w['PekerjaanAyah']);
?>
    	<?php
		foreach ($arr as $a) { ?>
        <tr>
    	<td width="200" class="isi" valign="top"><?php echo $a['0']?></td>
        <td width="10" class="isi" valign="top"><?php echo $a['1']?></td>
        <td class="isi" valign="top"><?php echo $a['2']?></td>
        </tr>
        <?php } ?>
</table>
<br />
<p class="content">Dengan ini menyatakan biodata ini sudah benar, apabila dikemudian hari terdapat kekeliruan/perubahan, maka akan diperbaiki kembali sebagaimana mestinya sesuai dengan ketentuan yang berlaku.<br /><br />Demikian blanko biodata ini saya buat, untuk dapat digunakan sebagaimana mestinya.</p>
<table border="0" style="margin-top:40px">
<tr>
<td width="400">&nbsp;</td>
<td height="100" valign="top">Padang, <?php echo TanggalFormat(date('Y-m-d')) ?><br />Mahasiswa yang bersangkutan,</td></tr>
<td width="400">&nbsp;</td>
<td><?php echo $w['Nama']?></td></tr>
</table>
<center><input type="button" value="Cetak" class="onlyscreen" onClick="window.print()" /></center>
</body>
</html>

