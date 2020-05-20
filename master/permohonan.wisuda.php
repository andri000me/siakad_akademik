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

// *** Parameters ***
$MhswID = sqling($_POST['MhswID']);
$mhsw = GetFields('mhsw', "KodeID='".KodeID."' and MhswID", $_REQUEST['MhswID'], '*,Kapital(Nama) as NMKapital,Kapital(TempatLahir) as TTL');
$wisuda = GetFields('wisuda', "NA='N' and KodeID", KodeID, '*');

//if ($mhsw['StatusMhswID']=='L') {
//die(ErrorMsg("Error",
 //   "Anda sudah dinyatakan lulus..."));
//	}
	if (($mhsw['StatusMhswID']=='C') || ($mhsw['StatusMhswID']=='P') || ($mhsw['StatusMhswID']=='K'))  {
die(ErrorMsg("Error",
    "Tidak dapat membuat permohonan wisuda, karena status Anda Cuti/Pasif/Keluar..."));
	}

// Init PDF
$pdf = new TCPDF();
$pdf->SetTitle("Permohonan Wisuda");
$pdf->SetLeftMargin(20);
$pdf->SetRightMargin(20);
$pdf->SetAutoPageBreak(true, 5);

// ** Tanpa Header dan Footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// ** Set Detail File **
$pdf->SetCreator('Arisal Yanuarafi, S.Kom');
$pdf->SetAuthor(KodeID);
$pdf->SetSubject('Permohonan '.$wisuda['Nama']);
$pdf->SetFont('Helvetica', '', 10);

		$pdf->AddPage();
		BuatHeader($mhsw,$pdf);
		BuatIsi($mhsw, $pdf);
		BuatFooter($mhsw, $pdf);
$pdf->Output();
// *** functions ***
function BuatIsi($m, $p) {
$mrg = 30;
$t=5;
$arr = array();
  $arr[] = array('Nama', ':', $m['Nama']);
  $arr[] = array('No. BP', ':', $m['MhswID']);
  $arr[] = array('Tempat / Tanggal Lahir', ':', $m['TTL'].' / '.TanggalFormat($m['TanggalLahir']));
  $arr[] = array('Jurusan / Program Studi', ':', GetaField('prodi', "ProdiID='$m[ProdiID]' and KodeID", KodeID, 'Kapital(Nama)').' / '.
  			GetaField('prodi p left outer join jenjang j on p.JenjangID=j.JenjangID', "p.ProdiID='$m[ProdiID]' and p.KodeID", KodeID, "concat(j.Nama)"));
  $arr[] = array('IPK', ':',$m['IPK']);
  $arr[] = array('Telp. / HP', ':',(!empty($m['Telepon']) and empty($m['Handphone']))? "$m[Telepon]" : (!empty($m['Telepon']) and !empty($m['Handphone']))? "$m[Telepon] / $m[Handphone]" : "$m[Handphone]");

  // Tampilkan
  foreach ($arr as $a) {
  	$p->MultiCell(10, $t, '', 0, 'C', 0, 0, '', '', true);
	$p->MultiCell(50, $t, $a[0], 0, 'L', 0, 0, '', '', true);
	$p->MultiCell(4, $t, $a[1], 0, 'C', 0, 0, '', '', true);
	$p->MultiCell(120, $t, $a[2], 0, 'L', 0, 0, '' ,'', true);
	$p->Ln($t);
  }
  	$p->MultiCell(10, $t, '', 0, 'C', 0, 0, '', '', true);
	$p->MultiCell(50, $t,'Alamat yang dapat dihubungi', 0, 'L', 0, 0, '', '', true);
	$p->MultiCell(4, $t, ':', 0, 'C', 0, 0, '', '', true);
	$p->SetFont('Helvetica', '', 9);
	$p->MultiCell(110, $t, $m['Alamat'], 0, 'L', 0, 0, '' ,'', true);
	$p->Ln($t*2);
	$p->SetFont('Helvetica', 'B', 10);
  	$p->MultiCell(10, $t, '', 0, 'C', 0, 0, '', '', true);
	$p->MultiCell(50, 6,'Judul Tugas Akhir', 0, 'L', 0, 0, '', '', true);
	$p->Ln(5);
	$p->SetFont('Helvetica', '', 10);
$html = GetaField('ta',"MhswID",$m['MhswID'],'Judul');
// Judul Tugas Akhir
	$p->MultiCell(10, $t, '', 0, 'C', 0, 0, '', '', true);
	$p->MultiCell(50, $t,'a. Bahasa Indonesia', 0, 'L', 0, 0, '', '', true);
	$p->MultiCell(4, $t, ':', 0, 'C', 0, 0, '', '', true);
	$p->SetFont('Helvetica', '', 9);
	$p->MultiCell(110, $t, $html, 0, 'L', 0, 0, '' ,'', true);
	$p->Ln($t*4);
	
$html = GetaField('ta',"MhswID",$m['MhswID'],'Deskripsi');
if (!empty($html)) {
	$p->SetFont('Helvetica', '', 10);
	$p->MultiCell(10, $t, '', 0, 'C', 0, 0, '', '', true);
	$p->MultiCell(50, $t,'b. Bahasa Inggris', 0, 'L', 0, 0, '', '', true);
	$p->MultiCell(4, $t, ':', 0, 'C', 0, 0, '', '', true);
	$p->SetFont('Helvetica', 'I', 9);
	$p->MultiCell(110, $t, $html, 0, 'L', 0, 0, '' ,'', true);
	$p->Ln($t*4);
}
// Jika judul Bahasa Inggris tidak ada, maka tampilkan '-'
else {
	$p->SetFont('Helvetica', '', 10);
	$p->MultiCell(10, $t, '', 0, 'C', 0, 0, '', '', true);
	$p->MultiCell(50, $t,'b. Bahasa Inggris', 0, 'L', 0, 0, '', '', true);
	$p->MultiCell(4, $t, ':', 0, 'C', 0, 0, '', '', true);
	$p->SetFont('Helvetica', 'I', 9);
	$p->MultiCell(110, $t, '-', 0, 'L', 0, 0, '' ,'', true);
	$p->Ln($t);
}
	$arr = array();
	$s = "Select concat(d.Nama,', ',d.Gelar) as NMDosen from dosen d left outer join ta t on t.Pembimbing=d.Login where t.MhswID='$m[MhswID]' limit 1";
	$r = _fetch_array(_query($s));
  	$arr[] = array('c. Nama Pembimbing', ':', $r['NMDosen']);
	$arr[] = array('d. Tanggal Lulus Sidang', ':', TanggalFormat(GetaField('ta',"MhswID",$m['MhswID'],"TglUjian")));
	$arr[] = array('e. Nama Orangtua', '', '');
	$arr[] = array('   - Ayah', ':', $m['NamaAyah']);
	$arr[] = array('   - Ibu', ':', $m['NamaIbu']);
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
 	// *** Tampilkan Pernyataan ***
	$p->Ln($t);
	$p->Write(5,'Dengan ini mengajukan permohonan untuk mengikuti '.$AngkatanWisuda.' Tanggal '.$TanggalWisuda.', bersama ini saya lampirkan:');
	$p->Ln(8);
	$p->Cell(6,$t,'',0,'');
	$p->Cell(50,$t,'1. Photo copy Ijazah SLTA / STM / D3 (1 lembar)',0,1);
	$p->Cell(6,$t,'',0,'');
	$p->Cell(50,$t,'2. Pas photo Hitam Putih **)',0,1);
	$p->Cell(11,$t,'',0,'');
	$p->Cell(50,$t,'a. 2 x 3 = 2 lembar',0,1);
	$p->Cell(11,$t,'',0,'');
	$p->Cell(50,$t,'b. 3 x 4 = 3 lembar',0,1);
	$p->Cell(11,$t,'',0,'');
	$p->Cell(50,$t,'c. 4 x 6 = 3 lembar',0,1);
	$p->Ln(3);
	$p->Write(5,'Demikian saya sampaikan, atas perhatian dan kesediaan Bapak diucapkan terima kasih.');
	$p->Ln($t*2);
	// *** Mengetahui ***
	$p->MultiCell(50, $t, 'Mengetahui:', 0, 'L', 0, 1);
	$p->MultiCell(50, $t, 'Ketua Program Studi', 0, 'L', 0, 0, '', '', true);
	$p->MultiCell(60, $t, '', 0, 'C', 0, 0, '', '', true);
	$p->MultiCell(50, $t, 'Calon Wisudawan', 0, 'L', 0, 0, '' ,'', true);
	$p->Ln($t);
	$p->MultiCell(50, $t, GetaField('prodi',"ProdiID",$m['ProdiID'],'Kapital(Nama)'), 0, 'L', 0, 0, '', '', true);
	$p->Ln($t*4+2);
	
	// *** Lokasi tanda tangan ***
	$strProdiID = '.'.$m[ProdiID].'.';
 	$pjbt = GetFields('pejabat', "LOCATE('$strProdiID',KodeJabatan) and KodeID",KodeID, "*");
	$p->SetFont('Helvetica', 'B', 10);
	$p->MultiCell(70, $t, $pjbt['Nama'], 0, 'L', 0, 0, '', '', true);
	$p->MultiCell(40, $t, '', 0, 'C', 0, 0, '', '', true);
	$p->MultiCell(50, $t, $m['NMKapital'], 0, 'L', 0, 0, '' ,'', true);
	$p->Ln($t*2-3);

	// *** Tanda bintang ***
	$p->Ln($t);
	$p->SetFont('Helvetica', '', 8);
	$p->Write(5,'- Pria Pakai Dasi + Jas Warna Hitam / Wanita Berpakaian Kebaya');
	$p->Ln($t);
	$p->Write(5,'- Formulir ini dan Persetujuan Bebas Administrasi diserahkan ke BAAK. Sesuai Prosedur Wisuda ke-59.');
}


function BuatHeader($mhsw,$p) {
	$t=5;
	// *** Kepala Surat ***
	$p->MultiCell(15, $t, 'Hal.  :' , 0, 'L', 0, 0, '', '', true);
	$p->SetFont('Helvetica', 'B', 10);
	$p->MultiCell(55, $t, 'Permohonan Wisuda' , 0, 'L', 0, 0, '', '', true);
	$p->SetFont('Helvetica', '', 10);
	$p->MultiCell(55, $t, '', 0, 'C', 0, 0, '', '', true);
	$p->MultiCell(50, $t, TanggalFormat(date('Y-m-d')), 0, 'L', 0, 0, '' ,'', true);
	$p->Ln($t*3);
	
	// *** Kepada Yth ***
	$p->MultiCell(130, $t, 'Kepada Yth,', 0, 'L', 0, 0, '' ,'', true);
	$p->Image('../'.$mhsw['Foto'], 146, 18, '', 32, '', '', '', false, 300, '', false, false, 1, false, false, false);
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
