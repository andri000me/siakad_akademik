<?php

session_start();

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Ganti Foto Wisudawan");

// *** Main ***
$back = $_REQUEST['back'];
$MhswID = sqling($_REQUEST['MhswID']);
$mhsw = GetFields('mhsw', "KodeID='".KodeID."' and MhswID", $MhswID, '*');
if (empty($mhsw))
  die(ErrorMsg('Error',
    "Mahasiswa dengan NIM: <b>$MhswID</b> tidak ditemukan.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Tutup' onClick='window.close()' />"));


$gos = (empty($_REQUEST['gos']))? 'TampilkanFormulir' : $_REQUEST['gos'];
$gos($MhswID, $mhsw, $back);

// *** functions ***
function TampilkanFormulir($MhswID, $mhsw, $back) {
  $MaxFileSize = 500000;
  TampilkanJudul("Upload Foto Mahasiswa");
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='../master/gantifotowisuda.php' enctype='multipart/form-data' method=POST>
  <input type=hidden name='MAX_FILE_SIZE' value='$MaxFileSize' />
  <input type=hidden name='gos' value='Simpan' />
  <input type=hidden name='MhswID' value='$MhswID' />
  <input type=hidden name='back' value='$back' />
  
  <tr><td class=inp>NIM:</td>
      <td class=ul>$MhswID</td></tr>
  <tr><td class=inp>Nama:</td>
      <td class=ul>$mhsw[Nama]</td></tr>
  
  <tr><td class=inp width=100>File Foto</td>
    <td class=ul><input type=file name='foto' size=35></td></tr>
  <tr><td class=ul colspan=2 align=center>
    <input type=submit name='Upload' value='Upload File Foto' />
    <input type=button name='Batal' value='Batal' onClick='window.close()' />
    </td></tr>
  </form></table></p>";
}
function Simpan($MhswID, $mhsw, $back) {
  $file = $_FILES['foto']['tmp_name'];
  $arrNama = explode('.', $_FILES['foto']['name']);
  $tipe = $_FILES['foto']['type'];
  $arrtipe = explode('/', $tipe);
  $extensi = $arrtipe[1];
  	$Nama = GetaField('mhsw', "MhswID", $MhswID, 'Nama');
  	$NamaKecil 	= strtolower($Nama);
	$NamaKecil 	= str_replace(' ','_',$NamaKecil);
	$namafoto 	= 'foto_'.$MhswID.'_'.$NamaKecil.'.';
	$dir 		= '../foto/wisudawan/'.$namafoto;
  	$dest 		= $dir . $extensi;
  //echo $dest;
  // Mendefinisikan tipe MIME
				   $exts = array('image/gif',
								  'image/jpeg',
								  'image/jpg',
								  'image/png',
								  'application/x-shockwave-flash',
								  'image/psd',
								  'image/bmp',
								  'image/tiff',
								  'image/tiff',
								  'image/jp2',
								  'image/iff',
								  'image/vnd.wap.wbmp',
								  'image/xbm',
								  'image/vnd.microsoft.icon');
					$exts2 = array('jpg','jpeg');
				
				   // Periksa tipe MIME file
				   if (!in_array(($tipe), $exts) || ($_FILES['foto']['size']>1024288) || !in_array(($extensi), $exts2)) {
						die(errorMsg('Gagal',"Harap upload foto tipe file jpg/jpeg dengan ukuran yang diperkecil, maksimal ukuran foto 500KB"));
				   }
				else {
				   if (!move_uploaded_file($file, $dir.$extensi)) {
					   die(errorMsg('Gagal',"Ada kesalahan, hubungi SysAdmin."));
				   } else {
				   //identitas file asli
				  $im_src = imagecreatefromjpeg($dir.$extensi);
				  $src_width = imageSX($im_src);
				  $src_height = imageSY($im_src);
				
				  //Simpan dalam versi small 110 pixel
				  //Set ukuran gambar hasil perubahan
				  $dst_width = 110;
				  $dst_height = ($dst_width/$src_width)*$src_height;
				
				  //proses perubahan ukuran
				  $im = imagecreatetruecolor($dst_width,$dst_height);
				  imagecopyresampled($im, $im_src, 0, 0, 0, 0, $dst_width, $dst_height, $src_width, $src_height);
				
				  //Simpan gambar
				  imagejpeg($im,'../foto/wisudawan/kecil/' . "small_" . $namafoto . $extensi);
				   
				   
				   $s = _query("UPDATE mhsw set 
									FotoWisuda='$namafoto$extensi'	 where MhswID='$MhswID'");
				   }
				   TutupScript($back);
				}
			
}
function TutupScript($back) {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    opener.location='$back';
    self.close();
    return false;
  }
  ttutup();
</SCRIPT>
SCR;
}
?>
