<?php 
// Author: Arisal Yanuarafi, S.Kom
// Start : 26 September 2012

session_start();
  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb2.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  include_once "../tcpdf/tcpdf.php";
if (($_SESSION['_LevelID']!='')) {
	if ($_SESSION['_LevelID']==1) $MhswID 	= GetSetVar('MhswID');
	elseif ($_SESSION['_LevelID']==121) $MhswID 	= $_SESSION['_Login'];
	else die (errorMsg("Tidak berhak","Anda tidak berhak mengakses modul ini"));
	
	
// *** Parameters ***
$mhsw = GetFields('mhsw m left outer join wisudawan w on w.MhswID=m.MhswID', "m.KodeID='".KodeID."' and m.MhswID", $MhswID, 'm.*,m.Nama as NMKapital,m.TempatLahir as TTL, m.FotoWisuda,w.WisudaID');
if (empty($mhsw['FotoWisuda'])) die(errorMsg("Belum ada Foto", "Silakan upload foto terlebih dahulu ..."));
$wisuda = GetFields('wisuda', "NA='N' and KodeID", KodeID, '*');

//if ($mhsw['StatusMhswID']=='L') {
//die(ErrorMsg("Error",
 //   "Anda sudah dinyatakan lulus..."));
//	}
	if (($mhsw['StatusMhswID']=='A') || ($mhsw['StatusMhswID']=='P') || ($mhsw['StatusMhswID']=='K'))  {
die(ErrorMsg("Error",
    "Tidak dapat membuat permohonan"));
	}

// Init PDF
$pdf = new TCPDF();
$pdf->SetTitle("Permohonan Mengambil Ijazah");
$pdf->SetLeftMargin(20);
$pdf->SetRightMargin(20);
$pdf->SetAutoPageBreak(true, 5);

// ** Tanpa Header dan Footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// ** Set Detail File **
$pdf->SetCreator('Arisal Yanuarafi, S.Kom');
$pdf->SetAuthor(KodeID);
$pdf->SetSubject('Permohonan Pengambilan Ijazah '.$wisuda['Nama']);
$pdf->SetFont('Helvetica', '', 10);

		$pdf->AddPage();
		BuatHeader($mhsw,$pdf);
		BuatIsi($mhsw, $pdf);
		BuatFooter($mhsw, $pdf);
$pdf->Output();
} // isset POST
else die(errorMsg('Terjadi kesalahan', "Periksa kembali data yang Anda kirimkan, pastikan ukuran file foto tidak melebihi 500KB.<hr>Opsi: <a href='?mnux'>Kembali</a>"));
// *** functions ***
function BuatIsi($m, $p) {
$mrg = 30;
$t=5;
$alumni = GetFields('alumni',"MhswID",$m['MhswID'],"*");
$arr = array();
  $arr[] = array('Nama', ':', $m['Nama']);
  $arr[] = array('No. BP', ':', $m['MhswID']);
  $arr[] = array('Tempat / Tanggal Lahir', ':', $m['TTL'].' / '.TanggalFormat($m['TanggalLahir']));
  $arr[] = array('Fakultas', ':', GetaField('prodi p left outer join fakultas f on f.FakultasID=p.FakultasID', "p.ProdiID='$m[ProdiID]' and p.KodeID", KodeID, "concat(f.Nama)"));
  $arr[] = array('Jurusan / Program Studi', ':', GetaField('prodi', "ProdiID='$m[ProdiID]' and KodeID", KodeID, 'Nama').' / '.
  			GetaField('prodi p left outer join jenjang j on p.JenjangID=j.JenjangID', "p.ProdiID='$m[ProdiID]' and p.KodeID", KodeID, "concat(j.Nama)"));
  $arr[] = array('Telp.', ':',(!empty($m['Telepon']) and empty($m['Handphone']))? "$m[Telepon]" : (!empty($m['Telepon']) and !empty($m['Handphone']))? "$m[Telepon] / $m[Handphone]" : "$m[Handphone]");
  $arr[] = array('Handphone', ':', $alumni['Handphone']);
  $arr[] = array('Email', ':', $alumni['Email']);
  $arr[] = array('Alamat', ':', $alumni['Alamat']);
  $arr[] = array('Golongan Darah', ':', $alumni['GolonganDarah']);
  $arr[] = array('Sudah Bekerja', ':', ($alumni['SudahBekerja']=='Y' ? "Sudah":"Belum"));
  $arr[] = array('Masa Tunggu Kerja', ':', $alumni['MasaTungguKerja'].' bulan');
  $arr[] = array('Gaji Pertama', ':', 'Rp '.$alumni['GajiPertama']);

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
	$p->MultiCell(50, 6,'Riwayat Pekerjaan', 0, 'L', 0, 0, '', '', true);
	$p->Ln(5);
	$p->SetFont('Helvetica', '', 10);

// Judul Tugas Akhir
	$kerja = _query("SELECT * from alumnikerja where MhswID='$m[MhswID]'");
	while ($alkerja = _fetch_array($kerja)) {
	$p->MultiCell(10, $t, '', 0, 'C', 0, 0, '', '', true);
	$p->MultiCell(160, $t,'- '.$alkerja['Nama'].' ( sebagai '.$alkerja['Jabatan'].'), Gaji pertama Rp '.$alkerja['GajiPertama'], 0, 'L', 0, 0, '', '', true);
	$p->Ln($t);
	}
	$p->Ln($t*4);
	
	
}

function BuatFooter($m, $p) {
	// *** Parameter ***
	$t=5;
	$AngkatanWisuda = GetaField('wisuda',"WisudaID='$m[WisudaID]' and KodeID",KodeID,"Nama");
	$TanggalWisuda = TanggalFormat(GetaField('wisuda',"WisudaID='$m[WisudaID]' and KodeID",KodeID,"TglWisuda"));
	$Identitas = GetaField('identitas',"Kode",KodeID,"Nama");
	$updateCetak =  _query("UPDATE alumni set Cetak = Cetak +1 where MhswID='".$m['MhswID']."' limit 1");
 	// *** Tampilkan Pernyataan ***
	$p->Ln($t);
	$p->Write(5,'Dengan ini mengajukan permohonan untuk mendapatkan ijazah dan transkrip akademik. Saya merupakan wisudawan periode '.$AngkatanWisuda.' '.$Identitas.' yang telah diselenggarakan pada tanggal '.$TanggalWisuda.' lalu.');
	$p->Ln(11);
	$p->Write(5,'Demikian saya sampaikan, atas perhatian dan kesediaan Bapak diucapkan terima kasih.');
	$p->Ln($t*2);
	// *** Mengetahui ***
	$p->MultiCell(50, $t, '', 0, 'L', 0, 1);
	$p->MultiCell(70, $t, '', 0, 'L', 0, 0, '', '', true);
	$p->MultiCell(40, $t, '', 0, 'C', 0, 0, '', '', true);
	$p->MultiCell(50, $t, 'Hormat saya,', 0, 'L', 0, 0, '' ,'', true);
	$p->Ln($t);
	$p->MultiCell(50, $t, '', 0, 'L', 0, 0, '', '', true);
	$p->Ln($t*4+2);
	
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
	$p->MultiCell(170, $t, '*/ Form ini dicetak dan ditandatangani untuk diserahkan ke Fakultas sebagai bukti bahwa ybs. telah mengisi formulir alumni secara online.', 0, 'L', 0, 0, '' ,'', true);
	//$p->Cell(200, 7, 'Form ini dicetak dan ditandatangani untuk diserahkan ke BAAK sebagai bukti bahwa ybs. telah mengisi formulir buku wisuda secara online.', '', 0, 'L', '');
}


function BuatHeader($mhsw,$p) {
	$t=5;
	// *** Kepala Surat ***
	$p->MultiCell(15, $t, 'Hal.  :' , 0, 'L', 0, 0, '', '', true);
	$p->SetFont('Helvetica', 'B', 10);
	$p->MultiCell(55, $t, 'Formulir Alumni' , 0, 'L', 0, 0, '', '', true);
	$p->SetFont('Helvetica', '', 10);
	$p->MultiCell(55, $t, '', 0, 'C', 0, 0, '', '', true);
	$p->MultiCell(50, $t, TanggalFormat(date('Y-m-d')), 0, 'L', 0, 0, '' ,'', true);
	$p->Ln($t*3);
	
	// *** Kepada Yth ***
	$p->MultiCell(130, $t, 'Kepada Yth,', 0, 'L', 0, 0, '' ,'', true);
	$p->Image('../foto/wisudawan/'.$mhsw['FotoWisuda'], 146, 18, '', 32, '', '', '', false, 300, '', false, false, 1, false, false, false);
	$p->Ln($t);
	$p->MultiCell(130, $t, 'Rektor '.GetaField('identitas',"Kode",KodeID,'Nama'), 0, 'L', 0, 0, '' ,'', true);
	$p->Ln($t);
	$p->MultiCell(200, $t, 'c/q. Ka. BAAK', 0, 'L', 0, 0, '' ,'', true);
	$p->Ln($t*2);
	$p->MultiCell(200, $t, 'Dengan Hormat,', 0, 'L', 0, 0, '' ,'', true);
	$p->Ln($t*2);
	$p->MultiCell(200, $t, 'Saya yang bertanda tangan di bawah ini :', 0, 'L', 0, 0, '' ,'', true);
	$p->Ln($t*2);
}



?>
