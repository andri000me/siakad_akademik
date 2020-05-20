<?php
	//Author: Arisal Yanuarafi
	// Start: 18 Desember 2011
	// Email: arisal.yanuarafi@yahoo.com
	
$gos=GetSetVar('_Simpan');
if ($gos=='1') {
$ps=sqling($_REQUEST['pesanLogin']);
$krs=sqling($_REQUEST['KRS']);
$khs=sqling($_REQUEST['KHS']);
$kru=sqling($_REQUEST['KRU']);
		$_SESSION[_Simpan]='';
		$gos='';
		$s="update pesanlogin set Pesan='$ps',KRS='$krs',KHS='$khs',KRU='$kru',LoginEdit='$_SESSION[_Login]',TanggalEdit=now() where PesanID='1' ";
		$r=_query($s);
		BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=", 1000);

}
else {
TampilkanJudul("Pesan Login");
	Tampilkan();
}
	function Tampilkan()
	{	
	$PesanLogin = GetFields('pesanlogin', 'PesanID', '1', '*');
		echo "<h3></h3><table class=box align=center width=400>
		<form action='?mnux=$_SESSION[mnux]&gos=Simpan' method=POST>
		<tr><td class=inp>Informasi saat login:</td></tr>
		<tr><td><textarea name='pesanLogin' rows=10 cols=50>$PesanLogin[Pesan]</textarea>
		</td></tr>
		<tr><td><h3>Pesan di KRS</h3><textarea name='KRS' rows=3 cols=50>$PesanLogin[KRS]</textarea><br>Maksimum 250 karakter
		</td></tr>
		<tr><td><h3>Pesan di LHS</h3><textarea name='KHS' rows=3 cols=50>$PesanLogin[KHS]</textarea><br>Maksimum 250 karakter
		</td></tr>
		<tr><td><h3>Pesan di KRU</h3><textarea name='KRU' rows=3 cols=50>$PesanLogin[KRU]</textarea><br>Maksimum 250 karakter
		</td></tr>
		<tr><td class=inp>Terakhir diedit: $PesanLogin[TanggalEdit], oleh: $PesanLogin[LoginEdit]</td></tr>
		<tr><td align=center>
		<input type=hidden name='_Simpan' value='1'> 
		";
		$tblSimpan= "<input type=submit name='simpan' title='Simpan Pesan Login' value='Simpan' 
         />";
		echo "$tblSimpan</td></tr></form><tr><td></br><marquee>$PesanLogin[Pesan]</marquee></td></tr></table>";
	}
		
	
?>