<?php

$mhswbck = GetSetVar('mhswbck');
$DosenID = ($_SESSION['_LevelID']==100)? $_SESSION['_Login'] : $_GET['dosenid'];
$w = GetFields('dosen', "Login", $DosenID, '*');

function TampilkanHeader($w) {
  $foto = FileFotoDosen($w, $w['Foto']);
  echo "<p><table class=box cellspacing=2 cellpadding=4 width=600>

  <tr><td class=inp width=100>NIP/Login</td>
      <td class=ul><b>$w[Login]</td>
      <td rowspan=7 class=box width=124 style='padding: 2pt' align=center valign=middle>
      <img src='$foto' height=120 /></td>
      </tr>

  <tr><td class=inp>Nama</td>
      <td class=ul><b>$w[Nama]</td></tr>
    <tr><td class=inp>File Foto</td>
      <td class=ul>$w[Foto]</td></tr>
  <tr><td class=inp>Pilihan</td>
      <td class=ul>
        <input type=button name='Kembali' value='Kembali ke Data Dosen'
          onClick=\"location='?mnux=master/dosen&dosenid=$w[Login]'\" />
      </td></tr>
  </table></p>";
}
function TampilkanUploadFoto($w) {
  $MaxFileSize = 400960;
  echo "<p>
  <form action='index.php' enctype='multipart/form-data' method=POST>
  <table class=box cellspacing=1 cellpadding=4 width=600>
  <input type=hidden name='MAX_FILE_SIZE' value='$MaxFileSize' />
  <input type=hidden name='mnux' value='$_SESSION[mnux]'>
  <input type=hidden name='gos' value='aplodFoto'>
  <input type=hidden name='dosenid' value='$w[Login]'>
  <tr><td class=inp width=100>File Foto</td>
    <td class=ul><input type=file name='foto' size=35></td></tr>
  <tr><td class=ul colspan=2 align=center>
    <input type=submit name='Upload' value='Upload File Foto'></td></tr>
  </table></form></p>";
}
function aplodFoto() {
  $DosenID = $_REQUEST['dosenid'];
  $Nama = GetaField('dosen',"Login", $DosenID, "Nama");
  error_reporting(E_ALL);
				// *** Upload File Foto	 
					$NamaKecil = strtolower($Nama);
					$NamaKecil = str_replace(' ','_',$NamaKecil);
					 $namafoto = 'foto_'.$DosenID.'_'.$NamaKecil.'.';
					$dir 		= 'foto/dosen/'.$namafoto;
					$file 		= $_FILES['foto']['tmp_name'];
					$tipe 		= $_FILES['foto']['type'];
					$arrtipe 	= explode('/', $tipe);
					$extensi 	= $arrtipe[1];
					$name 		= $_FILES['foto']['name'];
					$Berkas 	= $dir.$extensi;
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
				unlink($Berkas);
				unlink('foto/dosen/kecil/' . $namafoto . $extensi);
				   // Periksa tipe MIME file
				   if (!in_array(($tipe), $exts) || ($_FILES['foto']['size']>1024288) || !in_array(($extensi), $exts2)) {
						die(errorMsg('Gagal',"Harap upload foto tipe file jpg/jpeg dengan ukuran yang diperkecil, maksimal ukuran foto 500KB"));
				   }
				else {
				   if (!move_uploaded_file($file, $dir.$extensi)) {
					   die(errorMsg('Gagal',"Kemungkinan salah format (format saat ini: $tipe, $extensi) atau Anda menggunakan koneksi yang sangat lambat. $dir.$extensi"));
				   } else {
				   //identitas file asli
				  $im_src = imagecreatefromjpeg($dir.$extensi);
				  $src_width = imageSX($im_src);
				  $src_height = imageSY($im_src);
				
				  //Simpan dalam versi small 110 pixel
				  //Set ukuran gambar hasil perubahan
				  $dst_width = 120;
				  $dst_height = ($dst_width/$src_width)*$src_height;
				
				  //proses perubahan ukuran
				  $im = imagecreatetruecolor($dst_width,$dst_height);
				  imagecopyresampled($im, $im_src, 0, 0, 0, 0, $dst_width, $dst_height, $src_width, $src_height);
				
				  //Simpan gambar
				  imagejpeg($im,'foto/dosen/kecil/' . $namafoto . $extensi);
				  
				   $s = _query("UPDATE dosen set 
									Foto='$namafoto$extensi'	 where Login='$DosenID'");
				   }
				}
			
  }
//chmod('foto/dosen',0644);
$gos = (empty($_REQUEST['gos']))? 'donothing' : $_REQUEST['gos'];
$gos();
$w = GetFields('dosen', 'Login', $DosenID, '*');

// *** Main ***
TampilkanHeader($w);
TampilkanUploadFoto($w);
?>
