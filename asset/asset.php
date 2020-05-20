<?php

include_once "asset.cari.php";
include_once "class/dwolister.class.php";  

// *** Functions ***
function DaftarAsset() {
tampilkancariasset('asset/asset', 1);
DaftarAst('asset/asset', "gos=AssetEdt&md=0&AssetID==AssetID=", "Nama,Jumlah,Satuan,TglBeli,HargaBeli,LokasiID,Pemakai");
}

function getAjax(){
  echo <<<EOF
  <script language='JavaScript'>
    <!--
    function genAsset(){
      var akun = $('#akun').val();
      var jenis = pad(3, $('#jenis').val(), '0');
      var tahun = $('#tahun').val();
      var lokasi = $('#lokasi').val();
      var ruang = $('#ruang').val();
      var sumberdana = $('#sumberdana').val();
      var assetkode = akun+"."+jenis+"."+tahun+"."+lokasi+"."+ruang+"."+sumberdana+".";
      $('#InventarisID').val(assetkode);
      
      
    }
    function pad(width, string, padding) { 
      return (width <= string.length) ? string : pad(width, padding + string, padding)
    }
  -->
  </script>
EOF;
}
  
function AssetEdt() {
	global $KodeID;
	$md = $_REQUEST['md']+0;
  getAjax();
	if($md==0) {
      $w = GetFields('asset', 'AssetID', $_REQUEST['AssetID'], '*');
	    $AssetID = "<input type=hidden name='AssetID' value='$w[AssetID]' size=20 malength=20>
	    <input name='InventarisID' id='InventarisID' value='$w[InventarisID]' size=25 malength=50> $w[InventarisID]";
 	    $jdl="Edit Data";
	}
	else{
      $w = array();
      $w['AssetID'] = GetaField('asset', 'KodeID', KodeID, "max(AssetID)+1");
	    $w['TglBeli']=date('y-m-d');
	    $w['TglSusut']=date('y-m-d');
      $AssetID = "<input type=text id='InventarisID' name='InventarisID' value='$w[InventarisID]' size=25 malength=50>";
	    $jdl="Tambah Data";
	}
      $lks = GetOption2('kampus', "concat(Kode, ' - ', Nama)", 'Kode', $w['LokasiID'], '',   'Kode');
      $sumberdana = GetOption2('sumberdana', "concat(SumberDanaID, ' - ', Nama)", 'SumberDanaID', $w['SumberDanaID'], '',   'SumberDanaID');
      $klp = GetOption2('kelompokasset', "concat(KelompokID, ' - ', Nama)", 'KelompokID', $w['KelompokID'], '', 'KelompokID');
      $vdr = GetOption2('vendor', "concat(VendorID, ' - ', Nama)", 'VendorID', $w['VendorID'], '', 'VendorID');
    $tglbeli=GetDateOption($w['TglBeli'], 'TglBeli');
    $tglsusut=GetDateOption($w['TglSusut'], 'TglSusut');
    $na = ($w['NA'] == 'Y')? 'checked' : '';
  CheckFormScript("AssetID, Nama");
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='mnux' value='asset/asset'>
  <input type=hidden name='gos' value='AssetSav'>
  <input type=hidden name='md' value='$md'>
  <tr><th class=ttl colspan=4>$jdl</th></tr>
  <tr><td class=inp>Kode Akun </td>
    <td class=ul colspan=4><input type=text name='KodeAkun' id='akun' value='$w[KodeAkun]' size=5 maxlength=3 onchange=\"genAsset();\"></td></tr>
  <tr><td class=inp>Jenis Asset </td>
    <td class=ul colspan=4><select name='KelompokID' id='jenis' onchange=\"genAsset();\">$klp</select></td></tr>
  <tr><td class=inp>Tahun Pengadaan</td>
    <td class=ul colspan=4><input type=text name='Tahun' id='tahun' value='$w[Tahun]' onchange=\"genAsset();\" size=5 maxlength=4></td></tr>
  <tr><td class=inp>Lokasi </td>
    <td class=ul colspan=4><select name='LokasiID' id='lokasi' onchange=\"genAsset();\">$lks</select></td></tr>
  <tr><td class=inp>Ruang </td>
    <td class=ul colspan=4><input type=text name='Ruang' id='ruang' value='$w[Ruang]' onchange=\"genAsset();\" size=8 maxlength=10></td></tr>
  <tr><td class=inp>Sumber Dana </td>
    <td class=ul colspan=4><select name='SumberDanaID' id='sumberdana' onchange=\"genAsset();\">$sumberdana</select></td></tr>
    <tr><td class=inp>Inventaris ID</td>
  <td class=ul colspan=4>$AssetID</td></tr>
  <tr><td class=inp>Nama</td>
  <td class=ul colspan=4><input type=text name='Nama' value='$w[Nama]' size=70 maxlength=80></td></tr>
  <tr><td class=inp>Tanggal Perolehan</td>
	  <td class=ul colspan=4>$tglbeli</td></tr>
  <tr><td class=inp>Tangal Disusutkan</td>
	  <td class=ul colspan=4>$tglsusut *) Tanggal asset bisa digunakan</td></tr>
  <tr><td class=inp>Q t y</td>
	  <td class=ul colspan=4><input type=text name='Jumlah' value='$w[Jumlah]' size=10 maxlength=6></td></tr>
  <tr><td class=inp>Satuan</td>
	  <td class=ul colspan=4><input type=text name='Satuan' value='$w[Satuan]' size=10 maxlength=8></td></tr>
  <tr><td class=inp>Harga Aset</td>
	  <td class=ul colspan=4><input type=text name='HargaBeli' value='$w[HargaBeli]' size=20></td></tr>  
	<tr><td class=inp>Harga Perolehan</td>
    <td class=ul colspan=4><input type=text name='HargaPerolehan' value='$w[HargaPerolehan]' size=20></td></tr> 
  <tr><td class=inp>Beban Penyusutan</td>
	  <td class=ul colspan=4><input type=text id='' name='BebanPenyusutan' value='$w[BebanPenyusutan]' size=10 maxlength=3> %</td>
   </tr>
  <tr><td class=inp>Umur Ekonomis</td>
    <td class=ul colspan=4><input type=text id='' name='UmurEkonomis' value='$w[UmurEkonomis]' size=10 maxlength=3> Tahun</td>
   </tr>
  <tr><td class=inp>Kondisi</td>
	  <td class=ul colspan=4><input type=text name='Kondisi' value='$w[Kondisi]' size=70 maxlength=80></td></tr>
  <tr><td class=inp>Pemakai</td>
	  <td class=ul colspan=4><input type=text name='Pemakai' value='$w[Pemakai]' size=70 maxlength=80></td></tr>
  <tr><td class=inp>No. Purchase Order(PO)</td>
	  <td class=ul colspan=4><input type=text name='PurchaseOrder' value='$w[PurchaseOrder]'></td></tr>
  <tr><td class=inp>Vendor</td>
	  <td class=ul colspan=4><select name='VendorID'>$vdr</select></td></tr>
  <tr><td class=inp>NA (tidak aktif)?</td>
  <td class=ul colspan=4><input type=checkbox name='NA' value='Y' $Na ></td></tr>
  <tr><td class=ul colspan=4><input type=submit name='Simpan' value='Simpan'>
  <input type=reset name='Reset' value='Reset'>
  <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=asset/asset'\"></td></tr>
  </form></table></p>";
}

function AssetSav() {
  global $DefaultGOS, $KodeID;
  $md       =sqling($_REQUEST['md']+0);
  $AssetID  =sqling($_REQUEST['AssetID']);
  $InventarisID  =sqling($_REQUEST['InventarisID']);
  $nama     =sqling($_REQUEST['Nama']);
  $tbl      ="$_REQUEST[TglBeli_y]-$_REQUEST[TglBeli_m]-$_REQUEST[TglBeli_d]";
  $tsst     ="$_REQUEST[TglSusut_y]-$_REQUEST[TglSusut_m]-$_REQUEST[TglSusut_d]";
  $jml      =$_REQUEST['Jumlah'];
  $stn      =$_REQUEST['Satuan'];
  $hrg      =$_REQUEST['HargaBeli'];
  $lks      =$_REQUEST['LokasiID'];

  $SumberDanaID      =$_REQUEST['SumberDanaID'];
  $Ruang      =$_REQUEST['Ruang'];
  $KodeAkun      =$_REQUEST['KodeAkun'];

  $HargaPerolehan    =$_REQUEST['HargaPerolehan'];
  $BebanPenyusutan   =$_REQUEST['BebanPenyusutan'];
  $UmurEkonomis      =$_REQUEST['UmurEkonomis'];

  $klp      =$_REQUEST['KelompokID'];
  $kds      =$_REQUEST['Kondisi'];

  $usr      =sqling($_REQUEST['Pemakai']);
  $po       =$_REQUEST['PurchaseOrder'];
  $vdr      =$_REQUEST['VendorID'];
  $Tahun    =$_REQUEST['Tahun'];

  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  if ($md == 0) {
  $s = "update asset set
  SumberDanaID='$SumberDanaID',
  Ruang='$Ruang',
  KodeAkun='$KodeAkun',
  InventarisID='$InventarisID', Nama='$nama',Tahun='$Tahun',  TglBeli='$tbl', TglSusut='$tsst', Jumlah='$jml', Satuan='$stn',  HargaBeli='$hrg', LokasiID='$lks', KelompokID='$klp', HargaPerolehan='$HargaPerolehan', BebanPenyusutan='$BebanPenyusutan', UmurEkonomis='$UmurEkonomis', Kondisi='$kds', Pemakai='$usr', PurchaseOrder='$po', VendorID='$vdr', LoginEdit='$_SESSION[_Nama]', TglEdit='$Actiondate', NA='$NA' WHERE AssetID='$AssetID'";
    $r = _query($s);
    $DefaultGOS();
  }
  else {
    $ada = GetFields('asset', "KodeID='".KodeID."' and AssetID", $AstID, '*');
    if (empty($ada)) {
      $s = "INSERT INTO asset (SumberDanaID,Ruang,KodeAkun,InventarisID, Nama, Tahun, TglBeli, TglSusut, Jumlah, Satuan, HargaBeli, LokasiID, KelompokID, BebanPenyusutan, HargaPerolehan, UmurEkonomis, Kondisi, Pemakai, PurchaseOrder, KodeID, LoginAdd, TglAdd, NA, VendorID)
      VALUES('$SumberDanaID','$Ruang','$KodeAkun','$InventarisID', '$nama', '$Tahun', '$tbl', '$tsst', '$jml', '$stn', '$hrg', '$lks', '$klp', '$BebanPenyusutan', '$HargaPerolehan', '$UmurEkonomis', '$kds', '$usr', '$po', '$_SESSION[KodeID]', '$_SESSION[_Nama]', '$Actiondate', '$NA','$vdr')";
      $r = _query($s);
      echo "<script>window.location = '?mnux=asset/asset'; </script>";
    }
    else {
      echo ErrorMsg("Gagal Simpan",
      "Data Asset <b>$JabatanID</b> sudah ada.<br />
      Anda tidak dapat memasukkan asset ini lebih dari 1 kali.");
      $DefaultGOS();
    }
  }
}

// *** Parameters ***
$asturt = GetSetVar('asturt', 'AssetID');
$lks = GetSetVar('LokasiID');
$klp = GetSetVar('KelompokID');
$Pemakai = GetSetVar('Pemakai');
$Tahun = GetSetVar('Tahun');
$astcr = GetSetVar('astcr');
$astkeycr = GetSetVar('astkeycr');
$astpage = GetSetVar('astpage');
$klp = GetSetVar('klp');
if ($astkeycr == 'Reset') {
  $astcr = '';
  $_SESSION['astcr'] = '';
  $astkeycr = '';
  $_SESSION['astkeycr'] = '';
}

$DefaultGOS = "DaftarAsset";
$gos = (empty($_REQUEST['gos']))? $DefaultGOS : $_REQUEST['gos'];
//$gos = (empty($_REQUEST['gos']))? 'cariasset' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Daftar Asset ");
$gos();


?>
