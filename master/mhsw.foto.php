<?php

$mhswbck = GetSetVar('mhswbck');

function TampilkanHeader($w) {
  $foto = FileFotoMhsw($w, $w['Foto']);
  if ($_SESSION[_LevelID]==120) {
  echo "<p><table class=box cellspacing=2 cellpadding=4 width=600>
  <tr><td class=inp width=100>NPM</td>
      <td class=ul><b>$w[MhswID]</td>
      <td rowspan=7 class=box width=124 style='padding: 2pt' align=center valign=middle>
      <img src='$foto' height=120 /></td>
      </tr>

  <tr><td class=inp>Nama</td>
      <td class=ul><b>$w[Nama]</td></tr>
  <tr><td class=inp>Program</td>
      <td class=ul><b>$w[ProgramID]</td></tr>
  <tr><td class=inp>Program Studi</td>
      <td class=ul><b>$w[ProdiID]</td></tr>
  <tr><td class=inp>File Foto</td>
      <td class=ul>$w[Foto]</td></tr>
  <tr><td class=inp>Pilihan</td>
      <td class=ul>
        <input type=button name='Kembali' value='Kembali ke Data Mhsw'
          onClick=\"location='?mnux=master/mhsw.edt&mhswid=$w[MhswID]'\" />
      </td></tr>
  </table></p>";
  TampilkanUploadFoto($w);
  }
  else {
   echo "<p><table class=box cellspacing=2 cellpadding=4 width=600>
  <tr><td class=inp width=100>NPM</td>
      <td class=ul><b>$w[MhswID]</td>
      <td rowspan=7 class=box width=124 style='padding: 2pt' align=center valign=middle>
      <img src='$foto' height=120 /></td>
      </tr>

  <tr><td class=inp>Nama</td>
      <td class=ul><b>$w[Nama]</td></tr>
  <tr><td class=inp>Program</td>
      <td class=ul><b>$w[ProgramID]</td></tr>
  <tr><td class=inp>Program Studi</td>
      <td class=ul><b>$w[ProdiID]</td></tr>
  <tr><td class=inp>File Foto</td>
      <td class=ul>$w[Foto]</td></tr>
  <tr><td class=inp>Pilihan</td>
      <td class=ul>
        <input type=button name='Kembali' value='Kembali ke Data Mhsw'
          onClick=\"location='?mnux=master/mhsw.edt&mhswid=$w[MhswID]'\" />
      </td></tr>
  </table></p>";
  TampilkanUploadFoto($w);
  }
}
function TampilkanUploadFoto($w) {
  $MaxFileSize = '';
  echo "<p><form action='index.php' enctype='multipart/form-data' method=POST>
  <table class=box cellspacing=1 cellpadding=4 width=600>
  <input type=hidden name='mnux' value='$_SESSION[mnux]'>
  <input type=hidden name='gos' value='aplodFoto'>
  <input type=hidden name='mhswid' value='$w[MhswID]'>
  <tr><td class=inp width=100>File Foto</td>
    <td class=ul><input type=file name='foto'></td></tr>
  <tr><td class=ul colspan=2 align=center>
    <input type=submit name='Upload' value='Upload File Foto'></td></tr>
  </table></form></p>";
}
function aplodFoto() {
  $MhswID = $_REQUEST['mhswid'];
  $Nama = GetaField('mhsw',"MhswID", $MhswID, "Nama");
  	 
				// *** Upload File Foto	 
					$NamaKecil = strtolower($Nama);
					$NamaKecil = str_replace(' ','_',$NamaKecil);
					 $namafoto = 'foto_'.$MhswID.'_'.$NamaKecil.'.';
					$dir 		= 'foto/'.$namafoto;
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
				
				   // Periksa tipe MIME file
				   if (!in_array(($tipe), $exts) || ($_FILES['foto']['size']>1024288) || !in_array(($extensi), $exts2)) {
						die(errorMsg('Gagal',"Harap upload foto tipe file jpg/jpeg dengan ukuran yang diperkecil, maksimal ukuran foto 500KB"));
				   }
				else {
				   if (!move_uploaded_file($file, $dir.$extensi)) {
					   die(errorMsg('Gagal',"Kemungkinan salah format (format saat ini: $tipe, $extensi) atau Anda menggunakan koneksi yang sangat lambat."));
				   } else {
				   //identitas file asli
				  $im_src = imagecreatefromjpeg($dir.$extensi);
				  $src_width = imageSX($im_src);
				  $src_height = imageSY($im_src);
				
				  //Simpan dalam versi small 110 pixel
				  //Set ukuran gambar hasil perubahan
				  $dst_width = 150;
				  $dst_height = ($dst_width/$src_width)*$src_height;
				
				  //proses perubahan ukuran
				  $im = imagecreatetruecolor($dst_width,$dst_height);
				  imagecopyresampled($im, $im_src, 0, 0, 0, 0, $dst_width, $dst_height, $src_width, $src_height);
				
				  //Simpan gambar
				  imagejpeg($im,'foto/kecil/' . $namafoto . $extensi);

				  //identitas file asli
				  $im_src = imagecreatefromjpeg($dir.$extensi);
				  $src_width = imageSX($im_src);
				  $src_height = imageSY($im_src);
				
				  //Simpan dalam versi small 110 pixel
				  //Set ukuran gambar hasil perubahan
				  $dst_width = 650;
				  $dst_height = ($dst_width/$src_width)*$src_height;
				
				  //proses perubahan ukuran
				  $im = imagecreatetruecolor($dst_width,$dst_height);
				  imagecopyresampled($im, $im_src, 0, 0, 0, 0, $dst_width, $dst_height, $src_width, $src_height);
				
				  //Simpan gambar
				  imagejpeg($im,'foto/med_' . $namafoto . $extensi);
				  
				   $s = _query("UPDATE mhsw set 
									Foto='$namafoto$extensi',TanggalEdit=now()	 where MhswID='$MhswID'");
				   }

				   unlink('foto/'.$namafoto.$extensi);
				}
			
  }

$gos = (empty($_REQUEST['gos']))? 'donothing' : $_REQUEST['gos'];
$gos();
	 if ($_SESSION['_LevelID']==120) {
			$w = GetFields('mhsw', 'MhswID', $_SESSION['_Login'], '*');
		}
	else
		{
			$w = GetFields('mhsw', 'MhswID', $_REQUEST['mhswid'], '*');
		}
// *** Main ***
TampilkanHeader($w);
?>
