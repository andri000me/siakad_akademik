<?php 
session_start();
	/* 	Author	: Arisal Yanuarafi
		Start	: 17 Agustus 2014  */
	include_once "../dwo.lib.php";
  	include_once "../db.mysql.php";
  	include_once "../connectdb.php";
  	include_once "../parameter.php";
  	include_once "../cekparam.php";

$s = "SELECT AnggotaID, Nama, NA from pustaka_anggota where AnggotaID='$_REQUEST[AnggotaID]' limit 1";
$r = _query($s);
$jumlahpinjam = GetaField("pustaka_sirkulasi s left outer join pustaka_sirkulasi2 d on d.SirkulasiID = s.SirkulasiID", 
							"d.Status='Pinjam' and s.Status='Pinjam' and s.AnggotaID", $_REQUEST['AnggotaID'],"count(d.Sirkulasi2ID)")+0;
while ($w = _fetch_array($r)) {
	$status = ($w['NA']=='Y' ? "Tidak aktif":"Aktif");
	$NA = ($w['NA']=='Y' ? "$('#BibliografiID2').attr('disabled','disabled');":"");
	$_SESSION['_pustakaAnggotaID']=$w['AnggotaID'];
	echo "<script>$('#Namax').html('$w[Nama]');";
	echo "$('#Statusx').html('$status');";
	echo "$('#Pinjamx').html('$jumlahpinjam');";
	echo "$('#AnggotaID').val('$w[AnggotaID]');";
	echo "$('#AnggotaID').attr('disabled','disabled');";
	echo "$('#BibliografiID2').focus();$NA";
	echo "</script>";
}
	$s = "SELECT b.biblio_id as BibliografiID, b.title as Judul,s2.TanggalHarusKembali,s2.Sirkulasi2ID from pustaka_sirkulasi s
		left outer join pustaka_sirkulasi2 s2 on s2.SirkulasiID=s.SirkulasiID
		left outer join app_pustaka1.biblio b on b.biblio_id=s2.BibliografiID
		where s.AnggotaID='$_REQUEST[AnggotaID]' and s.Status='Pinjam' and s2.Status='Pinjam'";
	$r = _query($s);
	
	while ($w = _fetch_array($r)) {
		$status = ($w['NA']=='Y' ? "Tidak aktif":"Aktif");
		$biblioID = $w['BibliografiID'];
		$IDBuku .= "$w[BibliografiID]";
		$TextBuku .= "<tr><td>$biblioID <input type='button' value='Pengembalian' onclick=\"javascript:pengembalianBuku(1, '$w[Sirkulasi2ID]', '$_SESSION[mnux]')\" /></td><td>$w[Judul]<sup>THK: ".TanggalFormat($w['TanggalHarusKembali'])."</td></tr>";
		$_SESSION['_JumlahBuku']++;
	}

$header = "<table class=box cellspacing=1 align=center width=800><tr><th class=ttl>ID Buku</th><th class=ttl>Judul</th></tr>";
$footer = "</table>";
$daftarBuku = ($_SESSION['_JumlahBuku']>0) ? $header.$TextBuku.$footer : "";
$fokus = ($_SESSION['_JumlahBuku']>0) ? "$('#BibliografiID2').focus();":"$('#AnggotaID').focus();";

echo "$daftarBuku";
	echo "<script>$('#BibliografiID2').val('');";
	echo $fokus;
	echo "</script>";


