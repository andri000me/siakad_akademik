<?php ob_start();
// Author: Arisal Yanuarafi, S.Kom
// Start : 26 September 2012

session_start();
  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  include_once "../tcpdf/tcpdf.php";
  $filt = substr($_SERVER['REMOTE_ADDR'],0,9);
  if ($_SESSION['_LevelID']==1) $MhswID = GetSetVar('MhswID');
	elseif ($_SESSION['_LevelID']==120) $MhswID = $_SESSION['_Login'];
	else die (errorMsg("Tidak berhak","Anda tidak berhak mengakses modul ini"));
if ($_SESSION['_Login']!='auth0rized') {
	$Nama			= FixQuotes($_REQUEST['Nama']);
	$Kelamin		= sqling($_REQUEST['Kelamin']);
	$Alamat			= FixQuotes($_REQUEST['Alamat']);
	$Alamat			= sqling($Alamat);
	$Telepon		= sqling($_REQUEST['Telepon']);
	$TempatLahir	= sqling($_REQUEST['TempatLahir']);
	$Handphone		= sqling($_REQUEST['Handphone']);
	$Email			= sqling($_REQUEST['Email']);
	$Website		= sqling($_REQUEST['Website']);
	$NamaAyah		= sqling($_REQUEST['NamaAyah']);
	$NamaIbu		= sqling($_REQUEST['NamaIbu']);
	$JudulTA		= FixQuotes($_REQUEST['JudulTA']);
	$PembimbingI	= sqling($_REQUEST['PembimbingI']);
	$PembimbingII	= sqling($_REQUEST['PembimbingII']);
	$TanggalLahirIjazah	= sqling($_REQUEST['TanggalLahirIjazah']);
	$TanggalDaftar	= sqling($_REQUEST['TanggalDaftar']);
	$TanggalMulai	= sqling($_REQUEST['TanggalMulai']);
	$TanggalSelesai	= sqling($_REQUEST['TanggalSelesai']);
	$TanggalSidang	= sqling($_REQUEST['TanggalSidang']);
	
	if (empty($Kelamin) || empty($Alamat) || empty($Handphone) || empty($Email) || empty($NamaAyah) ||  empty($NamaIbu) || empty($JudulTA) || empty($PembimbingI) || empty($PembimbingII) || empty($TanggalLahirIjazah) || empty($TanggalSidang) || empty($TanggalDaftar) || empty($TanggalMulai) || empty($TanggalSelesai)) {	
	$pesan = "Kelamin:$Kelamin"."<br>Alamat:$Alamat"."<br>Handphone:$Handphone"."<br>Email:$Email"."<br>NamaAyah:$NamaAyah"."<br>NamaIbu:$NamaIbu"."<br>Judul:$JudulTA"."<br>PembimbingI:$PembimbingI"."<br>Pembimbing2:$PembimbingII"."<br>TglLahir:$TanggalLahirIjazah"."<br>TanggalSidang:$TanggalSidang";
	die('Masih ada kolom yang belum diisi, harap dikoreksi kembali ... <br>'.$pesan);
	}
	else {
		if (($TanggalSidang <= $TanggalDaftar) || ($TanggalSelesai <= $TanggalMulai)) {
			die('Harap perhatikan Tanggal Daftar/Tanggal Mulai/Tanggal Selesai/Tanggal Sidang. Masukan data yang valid.');
		}

		if (ctype_upper(str_replace(" ", "", $Nama)) || ctype_lower(str_replace(" ", "", $Nama)) || ctype_upper(str_replace(" ", "", $JudulTA)) || ctype_lower(str_replace(" ", "", $JudulTA))) {
        	die('Harap dicek kembali Nama dan Judul Skripsi Anda, jangan menggunakan huruf besar semua atau huruf kecil semua pada Nama dan Judul Skripsi. Sesuaikan format penulisan dengan EYD.');
    	}
		$ta = GetaField("tugasakhir", "MhswID", $MhswID, 'TAID');
		$Predikat = Predikat($MhswID);
		$s = "UPDATE mhsw set Nama = '$Nama', Kelamin='$Kelamin',  TempatLahir='$TempatLahir',
								NamaAyah = '$NamaAyah',NamaIbu = '$NamaIbu',
								Alamat='$Alamat', Telephone='$Telepon', Handphone='$Handphone', Email='$Email', Website='$Website',
								Predikat = '$Predikat',
								TanggalLahirIjazah = '$TanggalLahirIjazah', 
								TAID='$ta' where MhswID='$MhswID'";
		$r = _query($s);
		$s = 'UPDATE mhsw set Nama="'.$Nama.'",
								NamaAyah = "'.$NamaAyah.'", NamaIbu="'.$NamaIbu.'"" where MhswID="'.$MhswID.'"';
		// Jika TA belum dimasukan
		$TahunID = GetaField('wisuda',"NA='N' and KodeID",KodeID,"TahunID");
		if (empty($ta)) {
			$s 	= 'INSERT INTO tugasakhir (TahunID, MhswID, TglDaftar, TglMulai, TglSelesai, TglUjian, Judul, Pembimbing, Pembimbing2,LoginBuat,TanggalBuat) values("'.$TahunID.'", "'.$MhswID.'",
																									"'.$TanggalDaftar.'",
																									"'.$TanggalMulai.'",
																									"'.$TanggalSelesai.'",
																									"'.$TanggalSidang.'", "'.$JudulTA.'",
																									"'.$PembimbingI.'", "'.$PembimbingII.'",
																									"'.$_SESSION['_Login'].'",now())';
			$r 	= _query($s);
			$ta = GetaField("tugasakhir", "MhswID", $MhswID, 'TAID');
			$s 	= "UPDATE mhsw set TAID='$ta' where MhswID='$MhswID'";
			$r 	= _query($s);
		}
		// Jika belum ada data TA
		else {
			$s 	= "UPDATE tugasakhir set TahunID='$TahunID', TglDaftar='$TanggalDaftar', TglMulai='$TanggalMulai', TglSelesai='$TanggalSelesai',TglUjian='$TanggalSidang', Pembimbing='$PembimbingI', Pembimbing2='$PembimbingII',
					LoginEdit='$_SESSION[_Login]',TanggalEdit=now()		 where TAID='$ta'";
			$r 	= _query($s);
			$s 	= 'UPDATE tugasakhir set Judul="'.$JudulTA.'" where TAID="'.$ta.'"';
			$r 	= _query($s);
		}		
	}

	// Upload Skripsi
	if (!empty($_FILES['skripsi']['name'])) {
				// *** Upload File Foto	 
					$Nama	= GetaField('mhsw',"MhswID",$MhswID,"Nama");
					$NamaKecil = strtolower($Nama);
					
					$NamaKecil = str_replace(' ','_',$NamaKecil);
					 $namaskripsi = 'skripsi_'.$MhswID.'_'.$NamaKecil.'.';
					$dir 		= '../file/skripsi/'.$namaskripsi;
					$file 		= $_FILES['skripsi']['tmp_name'];
					$tipe 		= $_FILES['skripsi']['type'];
					$arrtipe 	= explode('/', $tipe);
					$extensi 	= $arrtipe[1];
					$name 		= $_FILES['skripsi']['name'];
					$Berkas 	= $dir.$extensi;
					$extension = end(explode(".", $_FILES["skripsi"]["name"]));
				   // Mendefinisikan tipe MIME
					$exts2 = array('application/msword','application/msword, msword','doc','docx','vnd.openxmlformats-officedocument.wordprocessingml.document',
									'msword','application/pdf, pdf','application/pdf','pdf','application/zip, zip','zip');
					$exts = array('doc','docx','pdf','zip');

				$pesan = ($_SESSION['Login']=='0910013221023') ? "$file":"";
				$cek = (filesize($file) < 300) ? die('ukuran file skripsi terlalu kecil. File:'.$pesan):"";

				   // Periksa tipe MIME file
				   if (!in_array(($extensi), $exts2) || !in_array(($extension), $exts)) {
						die(errorMsg('Gagal',"Harap upload dokumen skripsi dengan tipe file doc/docx/pdf/zip. Saat ini: $extensi"));
				   }
				else {
				   if (!move_uploaded_file($file, $dir.$extension)) {
					   die(errorMsg('Gagal',"Kemungkinan salah format (format saat ini: $tipe, $extensi .. $dir.$extension) atau Anda menggunakan koneksi yang sangat lambat. Skripsi."));
				   }
				   else{
				   		$s = _query("UPDATE mhsw set 
								Skripsi='$namaskripsi$extension'	 where MhswID='$MhswID'");
				   }
				}
	}
	// Upload Foto Wisudawan
		if (!empty($_FILES['foto']['name'])) {
				// *** Upload File Foto	 
					$Nama	= GetaField('mhsw',"MhswID",$MhswID,"Nama");
					$NamaKecil = strtolower($Nama);
					
					$NamaKecil = str_replace(' ','_',$NamaKecil);
					 $namafoto = 'foto_'.$MhswID.'_'.$NamaKecil.'.';
					$dir 		= '../foto/wisudawan/'.$namafoto;
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
								  'image/psd',
								  'image/bmp',
								  'image/jp2',
								  'image/iff',
								  'image/vnd.wap.wbmp',
								  'image/xbm',
								  'image/vnd.microsoft.icon');
					$exts2 = array('jpg','jpeg');
				
				   // Periksa tipe MIME file
				   $mm = GetaField('mhsw', "MhswID", $MhswID, "NIMSementara");
				   if (!in_array(($tipe), $exts) || !in_array(($extensi), $exts2)) {
						die(errorMsg('Gagal',"Harap upload foto tipe file jpg/jpeg."));
				   }
				else {
				   if (!move_uploaded_file($file, $dir.$extensi)) {
					   die(errorMsg('Gagal',"Kemungkinan salah format (format saat ini: $tipe, $extensi) atau Anda menggunakan koneksi yang sangat lambat"));
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
				  imagejpeg($im,'../foto/wisudawan/kecil/' . "" . $namafoto . $extensi);
				  
				 //============ukuran sedang==============================
				  $im_src = imagecreatefromjpeg($dir.$extensi);
				  $src_width = imageSX($im_src);
				  $src_height = imageSY($im_src);
				
				  //Simpan dalam versi sedang 200 pixel
				  //Set ukuran gambar hasil perubahan
				  $dst_width = 500;
				  $dst_height = ($dst_width/$src_width)*$src_height;
				
				  //proses perubahan ukuran
				  $im = imagecreatetruecolor($dst_width,$dst_height);
				  imagecopyresampled($im, $im_src, 0, 0, 0, 0, $dst_width, $dst_height, $src_width, $src_height);
				
				  //Simpan gambar
				  imagejpeg($im,'../foto/wisudawan/sedang/' . $namafoto . $extensi);
				   
				   
				   $s = _query("UPDATE mhsw set 
									FotoWisuda='$namafoto$extensi'	 where MhswID='$MhswID'");
				   }
				}
}
if (($mhsw['StatusMhswID']=='C') || ($mhsw['StatusMhswID']=='P') || ($mhsw['StatusMhswID']=='K'))  {
die(ErrorMsg("Error",
    "Tidak dapat membuat permohonan wisuda, karena status Anda Cuti/Pasif/Keluar..."));
}
} // isset POST

			

// *** Parameters ***
$mhsw = GetFields('mhsw', "KodeID='".KodeID."' and MhswID", $MhswID, '*,Nama as NMKapital,TempatLahir as TTL, FotoWisuda');
$wisuda = GetFields('wisuda', "NA='N' and KodeID", KodeID, '*');

if (empty($mhsw['FotoWisuda'])) die(errorMsg("Belum ada Foto", "Silakan upload foto terlebih dahulu ..."));
if (empty($mhsw['Skripsi'])) die(errorMsg("Belum ada Skripsi", "Silakan upload skripsi terlebih dahulu ..."));

// Init PDF
$pdf = new TCPDF();
$pdf->SetTitle("Surat Keterangan Kebenaran Data");
$pdf->SetLeftMargin(20);
$pdf->SetRightMargin(20);
$pdf->SetAutoPageBreak(true, 5);

// ** Tanpa Header dan Footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// ** Set Detail File **
$pdf->SetCreator('Arisal Yanuarafi, S.Kom');
$pdf->SetAuthor(KodeID);
$pdf->SetSubject('Surat Keterangan Kebenaran Data: '.$wisuda['Nama']);
$pdf->SetFont('Helvetica', '', 10);

		$pdf->AddPage();
		BuatHeader($mhsw,$pdf);
		BuatIsi($mhsw, $pdf);
		BuatFooter($mhsw, $pdf);

		// Pindahkan
		$pdf->AddPage();
		BuatHeaderRekomendasi($mhsw,$pdf);
		BuatIsiRekomendasi($mhsw, $pdf);
		BuatFooterRekomendasi($mhsw, $pdf);

		$pdf->AddPage();
		BlankoFotoIjazah($mhsw,$pdf);
		
		$pdf->AddPage();
		BlankoFotoTranskrip($mhsw,$pdf);

		$pdf->AddPage();
		AlurPendaftaran($mhsw,$pdf);

		
$pdf->Output();

// *** functions ***
function BuatIsi($m, $p) {
  $s = "select * from nilai where ProdiID='$m[ProdiID]' and Lulus='N' and KodeID='".KodeID."'";
  $r = _query($s);
  $whr_gagal = '';
  while($w = _fetch_array($r))
  {	$whr_gagal .= " and GradeNilai != '$w[Nama]' ";
  }
  $s1 = "SELECT MAX(k.BobotNilai) as BobotNilai,k.SKS from krs k,mk m,kurikulum u where
                    k.NA='N'   
                    AND  m.MKKode=k.MKKode 
                    AND u.KurikulumID=m.KurikulumID 
                    AND u.Nama like 'Kurikulum Default' 
                    AND k.BobotNilai > 0
                    AND k.Tinggi = '*'
                    AND m.NA='N'
                    AND k.MhswID='$m[MhswID]' group by k.Nama";
    $r1 = _query($s1);$bobot=0;$sks=0;
    while ($w1 = _fetch_array($r1)) {
        $bobot += $w1['BobotNilai']*$w1['SKS'];
        $sks += $w1['SKS'];
    }
    $ipk = $bobot/$sks;
    $ipk = number_format($ipk,2);

    $ipk = ($m['ProdiID']=='PGSD') ? HitungIPKPGSD($MhswID) : $ipk;
    $sks = ($m['ProdiID']=='PGSD') ? HitungSKSPGSD($MhswID) : $sks;


$mrg = 30;
$t=5;
$arr = array();
  $arr[] = array('Nama', ':', $m['Nama']);
  $arr[] = array(NPM, ':', $m['MhswID']);
  $arr[] = array('Tempat / Tanggal Lahir', ':', $m['TTL'].' / '.$m['TanggalLahirIjazah']);
  $arr[] = array('Fakultas', ':', GetaField('prodi p left outer join fakultas f on f.FakultasID=p.FakultasID', "p.ProdiID='$m[ProdiID]' and p.KodeID", KodeID, "concat(f.Nama)"));
  $arr[] = array('Jurusan / Program Studi', ':', GetaField('prodi', "ProdiID='$m[ProdiID]' and KodeID", KodeID, 'Nama').' / '.
  			GetaField('prodi p left outer join jenjang j on p.JenjangID=j.JenjangID', "p.ProdiID='$m[ProdiID]' and p.KodeID", KodeID, "concat(j.Nama)"));
  $arr[] = array('Telp. / HP', ':',(!empty($m['Telepon']) and empty($m['Handphone']))? "$m[Telepon]" : (!empty($m['Telepon']) and !empty($m['Handphone']))? "$m[Telepon] / $m[Handphone]" : "$m[Handphone]");
  $arr[] = array('Alamat', ':', $m['Alamat']);
  $arr[] = array('Ayah', ':', $m['NamaAyah']);
  $arr[] = array('Ibu', ':', $m['NamaIbu']);
  

  
  // Tampilkan
  foreach ($arr as $a) {
  	$p->MultiCell(10, $t, '', 0, 'C', 0, 0, '', '', true);
	$p->MultiCell(50, $t, $a[0], 0, 'L', 0, 0, '', '', true);
	$p->MultiCell(4, $t, $a[1], 0, 'C', 0, 0, '', '', true);
	$p->MultiCell(120, $t, $a[2], 0, 'L', 0, 0, '' ,'', true);
	$p->Ln($t);
  }
	$p->Ln($t*2);
	$p->SetFont('Helvetica', 'B', 10);
  	$p->MultiCell(10, $t, '', 0, 'C', 0, 0, '', '', true);
	$p->MultiCell(50, 6,'Tugas Akhir', 0, 'L', 0, 0, '', '', true);
	$p->Ln(5);
	$p->SetFont('Helvetica', '', 10);

$html = GetFields('tugasakhir',"MhswID",$m['MhswID'],'Judul,Pembimbing,Pembimbing2');
$judul = str_replace("''", "'", $html['Judul']);
$judul = str_replace('""', '"', $judul);
// Judul Tugas Akhir
	$p->MultiCell(10, $t, '', 0, 'C', 0, 0, '', '', true);
	$p->MultiCell(50, $t,'a. Judul Tugas Akhir', 0, 'L', 0, 0, '', '', true);
	$p->MultiCell(4, $t, ':', 0, 'C', 0, 0, '', '', true);
	$p->SetFont('Helvetica', '', 9);
	$p->MultiCell(110, $t, $judul, 0, 'L', 0, 0, '' ,'', true);
	$p->Ln($t*4);
	
	$arr = array();
  	$arr[] = array('b. Nama Pembimbing I', ':', $html['Pembimbing']);
	$arr[] = array('c. Nama Pembimbing II', ':', $html['Pembimbing2']);
	$arr[] = array('d. Tanggal Lulus Sidang', ':', TanggalFormat(GetaField('tugasakhir',"MhswID",$m['MhswID'],"TglUjian")));
	
  // Tampilkan
  $p->SetFont('Helvetica', '', 10);
  foreach ($arr as $a) {
  	$p->MultiCell(10, $t, '', 0, 'C', 0, 0, '', '', true);
	$p->MultiCell(50, $t, $a[0], 0, 'L', 0, 0, '', '', true);
	$p->MultiCell(4, $t, $a[1], 0, 'C', 0, 0, '', '', true);
	$p->MultiCell(120, $t, $a[2], 0, 'L', 0, 0, '' ,'', true);
	$p->Ln($t);
  }
}

function BuatFooter($m, $p) {
	// *** Parameter ***
	$t=5;
	$AngkatanWisuda = GetaField('wisuda',"NA='N' and KodeID",KodeID,"Nama");
	$TanggalWisuda = TanggalFormat(GetaField('wisuda',"NA='N' and KodeID",KodeID,"TglWisuda"));
	$Identitas = GetaField('identitas',"Kode",KodeID,"Nama");
 	// *** Tampilkan Pernyataan ***
	$p->Ln($t);
	$p->Write(5,'Dengan ini menyatakan sudah memeriksa semua data di atas dan memastikan kebenaranya. Saya juga memahami bahwa Kesalahan sekecil apapun tidak dapat ditolerir. Apabila dikemudian hari diketahui terdapat kesalahan, Saya bersedia menerima segala konsekwensinya.');
	$p->Ln(11);
	$p->Write(5,'Demikian surat pernyataan ini dibuat, sebagai syarat mengajukan permohonan mengikuti '.$AngkatanWisuda.' '.$Identitas.' yang akan diselenggarakan pada tanggal '.$TanggalWisuda.'.');
	$p->Ln($t*2);
	// *** Mengetahui ***
	$p->MultiCell(50, $t, '', 0, 'L', 0, 1);
	$p->MultiCell(70, $t, '', 0, 'L', 0, 0, '', '', true);
	$p->MultiCell(40, $t, '', 0, 'C', 0, 0, '', '', true);
	$p->MultiCell(50, $t, 'Calon Wisudawan', 0, 'L', 0, 0, '' ,'', true);
	$p->Ln($t*2);

	$p->SetFont('Helvetica', 'I', 7);
	$p->MultiCell(50, $t, '', 0, 'L', 0, 1);
	$p->MultiCell(70, $t, '', 0, 'L', 0, 0, '', '', true);
	$p->MultiCell(30, $t, '', 0, 'C', 0, 0, '', '', true);
	$p->MultiCell(50, $t, 'Materai Rp 6.000', 0, 'L', 0, 0, '' ,'', true);
	$p->Ln($t*3);
	
	// *** Lokasi tanda tangan ***
	$strProdiID = '.'.$m[ProdiID].'.';
 	$pjbt = GetaField('prodi', "ProdiID='$m[ProdiID]' AND KodeID",KodeID, "Pejabat");
	$p->SetFont('Helvetica', 'B', 10);
	$p->MultiCell(70, $t, '', 0, 'L', 0, 0, '', '', true);
	$p->MultiCell(40, $t, '', 0, 'C', 0, 0, '', '', true);
	$p->MultiCell(50, $t, $m['NMKapital'], 0, 'L', 0, 0, '' ,'', true);
	$p->Ln($t*2-3);

	// *** Tanda bintang ***
	$p->Ln($t);
	$p->Ln($t);
	$p->SetFont('Helvetica', 'I', 9);
	$p->MultiCell(170, $t, '*/ Surat ini dicetak dan ditandatangani untuk diserahkan bersamaan dengan syarat-syarat wisuda lainnya ke BAAK.', 0, 'L', 0, 0, '' ,'', true);
	//$p->Cell(200, 7, 'Form ini dicetak dan ditandatangani untuk diserahkan ke BAAK sebagai bukti bahwa ybs. telah mengisi formulir buku wisuda secara online.', '', 0, 'L', '');
}


function BuatHeader($mhsw,$p) {
	$p->SetFont('Helvetica', '', 8);
	$tbl = "<table><tr><td align='right'>Dokumen Nomor: W/01<hr></td></tr></table>";
	$p->writeHTML($tbl, true, false, false, false, '');
	$t=5;
	// *** Kepala Surat ***
	$p->MultiCell(15, $t, 'Hal.  :' , 0, 'L', 0, 0, '', '', true);
	$p->SetFont('Helvetica', 'B', 10);
	$p->MultiCell(95, $t, 'Pernyataan Kebenaran Data' , 0, 'L', 0, 0, '', '', true);
	$p->SetFont('Helvetica', '', 10);
	$p->MultiCell(30, $t, '', 0, 'C', 0, 0, '', '', true);
	$p->MultiCell(50, $t, TanggalFormat(date('Y-m-d')), 0, 'L', 0, 0, '' ,'', true);
	$p->Ln($t*3);
	
	// *** Kepada Yth ***
	$p->MultiCell(130, $t, 'Kepada Yth,', 0, 'L', 0, 0, '' ,'', true);
	$fotowisuda = (file_exists('../foto/wisudawan/kecil/'.$mhsw['FotoWisuda'])) ? '../foto/wisudawan/kecil/'.$mhsw['FotoWisuda']:'../foto/wisudawan/kecil/'.$mhsw['FotoWisuda'];
	$p->Image($fotowisuda, 146, 28, '', 32, '', '', '', false, 300, '', false, false, 1, false, false, false);
	$p->Ln($t);
	$p->MultiCell(130, $t, 'Rektor '.GetaField('identitas',"Kode",KodeID,'Nama'), 0, 'L', 0, 0, '' ,'', true);
	$p->Ln($t);
	$p->MultiCell(200, $t, 'c/q. Kepala BAAK', 0, 'L', 0, 0, '' ,'', true);
	$p->Ln($t*2);
	$p->MultiCell(200, $t, 'Dengan Hormat,', 0, 'L', 0, 0, '' ,'', true);
	$p->Ln($t*2);
	$p->MultiCell(200, $t, 'Saya yang bertanda tangan di bawah ini :', 0, 'L', 0, 0, '' ,'', true);
	$p->Ln($t*2);
}

// Blanko Foto Ijazah
function BlankoFotoIjazah($m, $p) {
	/*
	$p->SetFont('Helvetica', '', 8);
	$tbl = "<table><tr><td align='right'>Dokumen Nomor: W/03<hr></td></tr></table>";
	$p->writeHTML($tbl, true, false, false, false, '');
	$p->Image('../img/Blanko Pas Photo Utk Ijazah Wisuda 65 001.jpg', 10, 18, '', 270, '', '', '', false, 300, '', false, false, 0, false, false, false);
	*/
}

// Blanko Foto Transkrip
function BlankoFotoTranskrip($m, $p) {
	/*
	$p->SetFont('Helvetica', '', 8);
	$tbl = "<table><tr><td align='right'>Dokumen Nomor: W/04<hr></td></tr></table>";
	$p->writeHTML($tbl, true, false, false, false, '');
	$p->Image('../img/Blanko Pas Photo Untuk Transkrip Nilai Wisuda 65 001.jpg', 20, 28, '', 132, '', '', '', false, 300, '', false, false, 0, false, false, false);
	*/
}

// Alur Pendaftaran
function AlurPendaftaran($m, $p) {
	$p->SetFont('Helvetica', '', 8);
	$tbl = "<table><tr><td align='right'>Dokumen Nomor: W/05<hr></td></tr></table>";
	$p->writeHTML($tbl, true, false, false, false, '');
$t=5;
	// *** Kepala Surat ***
$AngkatanWisuda = GetaField('wisuda',"NA='N' and KodeID",KodeID,"Nama");
$tgl = GetFields('wisuda',"NA='N' and KodeID",KodeID,"TglMulai,TglSelesai");
	$p->MultiCell(15, $t, '' , 0, 'L', 0, 0, '', '', true);
	$p->SetFont('Helvetica', 'BU', 14);
	$p->MultiCell(95, $t, 'Tata Cara Pendaftaran '.$AngkatanWisuda , 0, 'L', 0, 0, '', '', true);
	$p->SetFont('Helvetica', '', 12);
	$p->MultiCell(30, $t, '', 0, 'C', 0, 0, '', '', true);
	$p->Ln($t*3);
	
	$p->writeHTML('<ol>
		<li>Pendaftaran dimulai tanggal '.TanggalFormat($tgl['TglMulai']).' sampai dengan '.TanggalFormat($tgl['TglSelesai']).
		'<li>Setelah mengisi formulir pendaftaran wisuda online, selanjutnya calon wisudawan menyelesaikan administrasi yang berkaitan dengan E-Jurnal, Toefl, Pustaka, Koperasi, Fakultas dan Labor. Keterangan perihal administrasi tersebut dapat dilihat pada pengumuman wisuda.</li>
		<li>Calon wisudawan menyerahkan bukti bebas administrasi Fakultas, dan bebas Labor ke Kasubag. Akademik Fakultas untuk divalidasi ke Portal Akademik</li>
		<li>Calon wisudawan menyerahkan Skripsi dalam bentuk <i>hard-copy</i> dan CD ke Pustaka</li>
		<li>Calon wisudawan menyelesaikan bebas administrasi di Perpustakaan dan Koperasi</li>
		<li>Calon wisudawan mencetak dan membawa Surat Rekomendasi Persyaratan Pendaftaran Wisuda ke Ketua Jurusan/Bagian dan Dekan untuk ditandatangani. (W/02)</li>
		<li>Calon wisudawan menempel foto pada masing-masing blanko Transkrip dan Ijazah. Ketentuan foto dapat dilihat pada pengumuman wisuda</li>
		<li>Calon wisudawan menandatangani Surat Pernyataan Kebenaran Data di atas Materai Rp 6.000,- .(W/01)</li>
		<li>Semua persyaratan dibawa ke BAAK Kampus Proklamator I Ulak Karang untuk diproses lebih lanjut. Dokumen yang dilampirkan sebagai berikut:
		<ul>
			<li>Fotocopy ijazah terakhir yang dilegalisir 1 lembar</li>
			<li>Transkrip Nilai dari Perguruan Tinggi Asal (bagi mahasiswa Transfer Dalam/Transfer Luar)</li>
			<li>Surat pernyataan kebenaran data (W/01) sebanyak 2 (dua) rangkap (seperti point 7)</li>
			<li>Surat rekomendasi persyaratan pendaftaran wisuda (W/02) sebanyak 2 (dua) rangkap</li>
			<li>Berita Acara ujian akhir dari program studi</li>
			<li>Semua bukti bebas administrasi</li>
		</ul>
		</li>
		</ol>',true, 0, true);
	$p->Ln($t*2);
}

// ================================================================================== REKOMENDASI ==
// ================================================================================== REKOMENDASI ====
function BuatIsiRekomendasi($m, $p) {
	$AngkatanWisuda = GetaField('wisuda',"NA='N' and KodeID",KodeID,"Nama");
  $s = "select * from nilai where ProdiID='$m[ProdiID]' and Lulus='N' and KodeID='".KodeID."'";
  $r = _query($s);
  $whr_gagal = '';
  while($w = _fetch_array($r))
  {	$whr_gagal .= " and GradeNilai != '$w[Nama]' ";
  }
  $Jurusan = GetaField('prodi', "ProdiID='$m[ProdiID]' and KodeID", KodeID, 'Nama').' / '.GetaField('prodi p left outer join jenjang j on p.JenjangID=j.JenjangID', "p.ProdiID='$m[ProdiID]' and p.KodeID", KodeID, "concat(j.Nama)");
  $s1 = "SELECT k.BobotNilai,k.SKS from krs k,mk m,kurikulum u where
                    k.NA='N'   
                    AND  m.MKKode=k.MKKode 
                    AND u.KurikulumID=m.KurikulumID 
                    AND u.Nama like 'Kurikulum Default' 
                    AND k.BobotNilai > 0
                    AND k.Tinggi = '*'
                    AND m.NA='N'
                    AND k.MhswID='$m[MhswID]' group by k.Nama";
    $r1 = _query($s1);$bobot=0;$sks=0;
    while ($w1 = _fetch_array($r1)) {
        $bobot += $w1['BobotNilai']*$w1['SKS'];
        $sks += $w1['SKS'];
    }
    $ipk = $bobot/$sks;
    $ipk = number_format($ipk,2);

$mrg = 30;
$t=5;
$p->Ln($t);
$p->Write(5,'Yang bertandatangan di bawah ini, Ketua Jurusan '.$Jurusan.'. Dengan ini menyampaikan bahwa:');
$p->Ln($t*2);
$arr = array();
  $arr[] = array('Nama', ':', $m['Nama']);
  $arr[] = array(NPM, ':', $m['MhswID']);
  $arr[] = array('Fakultas', ':', GetaField('prodi p left outer join fakultas f on f.FakultasID=p.FakultasID', "p.ProdiID='$m[ProdiID]' and p.KodeID", KodeID, "concat(f.Nama)"));
  $arr[] = array('Jurusan / Program Studi', ':', $Jurusan);
  
  // Tampilkan
  foreach ($arr as $a) {
  	$p->MultiCell(10, $t, '', 0, 'C', 0, 0, '', '', true);
	$p->MultiCell(50, $t, $a[0], 0, 'L', 0, 0, '', '', true);
	$p->MultiCell(4, $t, $a[1], 0, 'C', 0, 0, '', '', true);
	$p->MultiCell(120, $t, $a[2], 0, 'L', 0, 0, '' ,'', true);
	$p->Ln($t);
  }
}

function BuatFooterRekomendasi($m, $p) {
	// *** Parameter ***
	$t=5;
	$AngkatanWisuda = GetaField('wisuda',"NA='N' and KodeID",KodeID,"Nama");
	$TanggalWisuda = TanggalFormat(GetaField('wisuda',"NA='N' and KodeID",KodeID,"TglWisuda"));
	$Identitas = GetaField('identitas',"Kode",KodeID,"Nama");
 	// *** Tampilkan Persyaratan ***
	$p->Ln($t);
	$p->Write($t, 'Direkomendasikan sebagai calon wisudawan pada periode '.$AngkatanWisuda.'. Yang bersangkutan telah menyelesaikan semua persyaratan administrasi sebagai berikut:');
	$p->Ln($t*2);
	$arr = array();
  	$arr[] = array('1. Bebas Pustaka');
  	$arr[] = array('2. Bebas Koperasi');
  	$arr[] = array('3. Bukti Penyerahan Skripsi ke Pustaka');
  	$arr[] = array('4. Bukti Bebas Labor khusus bidang studi eksakta');
  	$arr[] = array('5. Bebas Administrasi Fakultas');
  // Tampilkan
  $p->SetFont('Helvetica', '', 10);
  foreach ($arr as $a) {
  	$p->MultiCell(10, $t, '', 0, 'C', 0, 0, '', '', true);
	$p->MultiCell(120, $t, $a[0], 0, 'L', 0, 0, '', '', true);
	$p->Ln($t);
  }

  	// *** Mengetahui ***
	$p->MultiCell(50, $t, '', 0, 'L', 0, 1);
	$p->MultiCell(70, $t, 'Mengetahui', 0, 'L', 0, 0, '', '', true);
	$p->MultiCell(40, $t, '', 0, 'C', 0, 0, '', '', true);
	$p->MultiCell(50, $t, 'Padang, .....................'.date('Y'), 0, 'L', 0, 0, '' ,'', true);
	$p->Ln($t);

	// *** Pejabat
	$pjbt = GetFields('prodi', "ProdiID='$m[ProdiID]' AND KodeID",KodeID, "Jabatan,Pejabat,FakultasID");
 	$dekan = GetFields('fakultas', "FakultasID='$pjbt[FakultasID]' AND KodeID",KodeID, "Pejabat,Jabatan");

	$p->MultiCell(70, $t, $dekan['Jabatan'].',', 0, 'L', 0, 0, '', '', true);
	$p->MultiCell(40, $t, '', 0, 'C', 0, 0, '', '', true);
	$p->MultiCell(50, $t, $pjbt['Jabatan'].',', 0, 'L', 0, 0, '' ,'', true);
	$p->Ln($t*4+2);
	
	// *** Lokasi tanda tangan ***
	$strProdiID = '.'.$m[ProdiID].'.';
	$p->SetFont('Helvetica', 'B', 10);
	$p->MultiCell(70, $t, $dekan['Pejabat'], 0, 'L', 0, 0, '', '', true);
	$p->MultiCell(40, $t, '', 0, 'C', 0, 0, '', '', true);
	$p->MultiCell(50, $t, $pjbt['Pejabat'], 0, 'L', 0, 0, '' ,'', true);
	$p->Ln($t*2-3);

	// *** Tanda bintang ***
	$p->Ln($t*2);
	$p->SetFont('Helvetica', 'I', 9);
	$p->MultiCell(170, $t, '*/ Surat ini dicetak dan ditandatangani untuk diserahkan bersamaan dengan syarat wisuda lainnya ke BAAK.', 0, 'L', 0, 0, '' ,'', true);
	$p->Ln($t*2);

	// define barcode style
	$p->MultiCell(130, $t, '', 0, 'C', 0, 0, '', '', true);
	$p->write1DBarcode(GetaField('ta', "MhswID", $m['MhswID'], 'TAID'), 'C93', '', '', 'R', 2, 0.4, $style, 'N');

	$p->Ln($t*4);
	$tbl = "<hr style='border: dotted 1px #000'>";
	$p->writeHTML($tbl, true, false, false, false, '');
	$p->Ln($t);
	$p->MultiCell(170, $t, 'Semua persyaratan wisuda yang sudah divalidasi oleh Fakultas diserahkan ke BAAK Tanggal: .....................', 0, 'L', 0, 0, '' ,'', true);
	$p->Ln($t);
	$p->SetFont('Helvetica', 'IU', 9);
	$p->MultiCell(170, $t, 'Diterima Oleh:', 0, 'L', 0, 0, '' ,'', true);

}


function BuatHeaderRekomendasi($mhsw,$p) {
	$p->SetFont('Helvetica', '', 8);
	$tbl = "<table><tr><td align='right'>Dokumen Nomor: W/02<hr></td></tr></table>";
	$p->writeHTML($tbl, true, false, false, false, '');
	$t=5;
	// *** Kepala Surat ***
	$AngkatanWisuda = GetaField('wisuda',"NA='N' and KodeID",KodeID,"Nama");
	$p->MultiCell(15, $t, 'Hal.  :' , 0, 'L', 0, 0, '', '', true);
	$p->SetFont('Helvetica', 'B', 10);
	$p->MultiCell(75, $t, 'Rekomendasi Persyaratan Pendaftaran ' . $AngkatanWisuda , 0, 'L', 0, 0, '', '', true);
	$p->SetFont('Helvetica', '', 10);
	$p->MultiCell(55, $t, '', 0, 'C', 0, 0, '', '', true);
	$p->MultiCell(50, $t, TanggalFormat(date('Y-m-d')), 0, 'L', 0, 0, '' ,'', true);
	$p->Ln($t*3);
	
	// *** Kepada Yth ***
	$p->MultiCell(130, $t, 'Kepada Yth,', 0, 'L', 0, 0, '' ,'', true);
	$p->Ln($t);
	$p->MultiCell(130, $t, 'Rektor '.GetaField('identitas',"Kode",KodeID,'Nama'), 0, 'L', 0, 0, '' ,'', true);
	$p->Ln($t);
	$p->MultiCell(200, $t, 'c/q. Ka. BAAK', 0, 'L', 0, 0, '' ,'', true);
	$p->Ln($t*2);
}


?>
