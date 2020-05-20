<?php 
session_start();
	/* 	Author	: Arisal Yanuarafi
		Start	: 17 Agustus 2014  */
	include_once "../dwo.lib.php";
  	include_once "../db.mysql.php";
  	include_once "../connectdb.php";
  	include_once "../parameter.php";
  	include_once "../cekparam.php";
if (empty($_SESSION['_pustakaAnggotaID'])) die("<script>alert('Isi ID Anggota terlebih dahulu...');$('#AnggotaID').focus();$('#BibliografiID2').val('');</script>");
$agt = GetaField('pustaka_anggota', "AnggotaID",$_SESSION['_pustakaAnggotaID'],"InstitusiID");
$setup = GetaField("pustaka_setup", "KodeID", KodeID, "JumlahPeminjaman$agt");
if ($_SESSION['_JumlahBuku'] < $setup) {
	$ar1 = $_REQUEST['item_code'];
	$_ar1 = explode("/", $ar1);
	$ar2 = $_ar1[0].'/'.str_replace("/", "-", $_ar1[1]);
	$ar3 = $_ar1[0].'/'.str_replace("-", "/", $_ar1[1]);
	$ar4 = $_ar1[0].'/'.str_replace("-", " - ", $_ar1[1]);

	$pola = array();
	preg_match('/(\d+)([a-zA-Z]+)(\d+)/', $ar1, $pola);

	$ar5 = $pola[1]."/".$pola[2]."/".$pola[3];
	$ar6 = $pola[1]."/".$pola[2]."-".$pola[3];
	$ar7 = $pola[1]."/".$pola[2]." - ".$pola[3];
	$ar8 = $pola[1]."/ ".$pola[2]." - ".$pola[3];
	$ar9 = $pola[1]."/ ".$pola[2]."-".$pola[3];
	$ar10 = $pola[1]."/ ".$pola[2]."/".$pola[3];

	$s = "SELECT b.biblio_id as BibliografiID, b.title as Judul, i.item_code 
			from app_pustaka1.item i left outer join app_pustaka1.biblio b on b.biblio_id=i.biblio_id
			where i.item_code in ('$ar1','$ar2','$ar3','$ar4','$ar5','$ar6','$ar7','$ar8','$ar9','$ar10') limit 1";
	$r = _query($s);
	
	while ($w = _fetch_array($r)) {
		$status = ($w['NA']=='Y' ? "Tidak aktif":"Aktif");
		$biblioID = $w['BibliografiID'];
		$_SESSION['_IDBuku'] .= ($_SESSION['_IDBuku']=='' ? "$w[item_code]" : "~$w[item_code]");
		$_SESSION['_TextBuku'] .= "<tr><td>$w[item_code]</td><td>$w[Judul]</td></tr>";
		$_SESSION['_JumlahBuku']++;
	}
}
else $alert = "alert('Batas peminjaman $setup buku saja...');";
$header = "<table class=box cellspacing=1 align=center width=800><tr><th class=ttl>ID Buku</th><th class=ttl>Judul</th></tr>";
$footer = "<tr><td colspan=2 align=right border=1 style='border-top:2px solid black;' bgcolor='#eee'><input type=button name='Tambah' value='Proses Peminjaman'
        onClick=\"javascript:Edt(1, '', '$_SESSION[mnux]')\" />
      <input type=button name='Refresh' value='Batal'
        onClick=\"window.location='?mnux=$_SESSION[mnux]'\" /></td></tr></table>";
$daftarBuku = ($_SESSION['_TextBuku']!='')?$header.$_SESSION['_TextBuku'].$footer:"";


	echo "$daftarBuku";
	echo "<script>$('#BibliografiID2').val('');";
	echo "$('#BibliografiID2').focus();";
	echo "$alert</script>";

