<?php

session_start();
	include_once "../dwo.lib.php";
  	include_once "../db.mysql.php";
  	include_once "../connectdb.php";
  	include_once "../parameter.php";
  	include_once "../cekparam.php";


// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'Cetak' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function Cetak() {
if ($_REQUEST['CetakID']=='buku') {
 $getAntrian = explode("~",$_SESSION['_antrianCetakID']);
 $urut = 0;
 foreach ($getAntrian as $n){
	 $urut++;
	 $id = GetaField('app_pustaka1.item',"item_id", $n, "biblio_id");
	 $kode = str_replace(" ","",GetaField('app_pustaka1.item',"item_id", $n, "item_code"));
	 $kode = str_replace("/","",$kode);
	 $kode = str_replace("-","",$kode);
	 $judul = GetaField('app_pustaka1.biblio',"biblio_id", $id, "left(title,180)");
	 //<img src='barcode.php?bar=$kode&gox=1'>
	 echo "<div style=\"
	 					margin-bottom:10px;
						margin-right:10px;
						padding:4px; 
	 					border:1px #999 solid; 
						width:180px;
						min-height:50px; 
						text-align:center;
						position:relative;
						float:left; \">
						<span style='font-size:10px; font-family:Arial'>UPT. PERPUSTAKAAN</span><br />
						<span style=\"font-family: 'free 3 of 9'; font-size:39px\">*$kode*</span>
						<div style='margin-top:0px;font-size:10px;font-family:Arial'>$kode<br />
						Universitas Bung Hatta</div></div>";
	if ($urut=='32') { 
	echo "<div style='page-break-before: always;'></div>";
	$urut = 0;
	}
 }
}
elseif ($_REQUEST['CetakID']=='anggota'){
	$getAntrian = explode("~",$_SESSION['_antrianAnggotaCetakID']);
 
 foreach ($getAntrian as $n){
 	$nama = GetaField('app_pustaka1.member',"member_id", $n, "member_name");
 	$qu = GetFields('app_pustaka1.member',"member_id", $n, "*");
 	//<img src='barcode.php?bar=$n&gox=2'>
 	$expire = date('Y-m-d', strtotime('+4 months'));
	 echo "<div style=\"
	 					margin-bottom:10px;
						margin-right:10px;
						float:left;
						padding:9px; 
	 					border:1px #999 solid; 
						width:280px; 
						position:relative;
						font-family: Arial;
						background: linear-gradient(#ddd, #fff);
						\">
		<img src='logo-small.png' style='float:left; margin:0 5px 10px 0; z-index=-1' height=30>
		<span style='font-size:13px'>Universitas Bung Hatta</span><br>
		<span style='font-size:15px'><strong>UPT. PERPUSTAKAAN</strong></span>
		<hr />
		<p align=center><strong>Kartu Anggota</p>
		<table style='font-size:12px'><tr><td width=70>No. Anggota </td><td width=170>: $n</td></tr>
		<tr><td>Nama </td><td>: $nama</td></tr>".($qu['member_type_id']==3 ? "<tr><td>Instansi </td><td>: $qu[inst_name]</td></tr>":"")."
		</table>
						<img src='barcode.php?bar=$n&gox=2'>
		<div style=\"width:45px;height:55px;position:absolute;right:5px;bottom:40px;border:#000 solid 1px; font-size:10px;padding:10px\">Foto 2x3</div>
						<span style='font-size:9px; font-weight: normal; float:right'>berlaku hingga $expire</span></div>";
	} // $qu[expire_date]
}
 // <img src='barcode.php?bar=$n&gox=2'>
}


?>
</body>
</html>