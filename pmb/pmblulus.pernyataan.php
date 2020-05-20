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
$PMBID = $_REQUEST['PMBID'];
$PMB = GetFields('pmb', "KodeID='".KodeID."' and PMBID", $PMBID, '*');

	
// Init PDF
$pdf = new TCPDF();
$pdf->SetTitle("Surat Pernyataan");
$pdf->SetLeftMargin(20);
$pdf->SetRightMargin(20);
$pdf->SetAutoPageBreak(true, 5);

// ** Tanpa Header dan Footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// ** Set Detail File **
$pdf->SetCreator('Arisal Yanuarafi, S.Kom');
$pdf->SetAuthor(KodeID);
$pdf->SetSubject('Surat Pernyataan '.$PMB['Nama']);
$pdf->SetFont('times', '', 10);

		$pdf->AddPage();
		HeaderLogo("MAHASISWA BARU TA. ".$tahun.'/'.$tahunnext, $pdf, 'P');
		BuatHeader($PMB,$pdf);
		BuatIsi($PMB, $pdf);
		BuatFooter($PMB, $pdf);
$pdf->Output();


// *** functions ***
function BuatIsi($m, $p) {
$mrg = 30;
$t=5;
$arr = array();
  $arr[] = array('Nama', ':', $m['Nama']);
  $arr[] = array('Nama Panggilan', ':', '');
  $arr[] = array('No. BP', ':', $m['MhswID']);
  $arr[] = array('Tempat / Tanggal Lahir', ':', $m['TempatLahir'].' / '.TanggalFormat($m['TanggalLahir']));
  $arr[] = array('Fakultas', ':', GetaField('prodi p left outer join fakultas f on f.FakultasID=p.FakultasID', "p.ProdiID='$m[ProdiID]' and p.KodeID", KodeID, "concat(f.Nama)"));
  $arr[] = array('Jurusan / Program Studi', ':', GetaField('prodi', "ProdiID='$m[ProdiID]' and KodeID", KodeID, 'Nama').' / '.
  			GetaField('prodi p left outer join jenjang j on p.JenjangID=j.JenjangID', "p.ProdiID='$m[ProdiID]' and p.KodeID", KodeID, "concat(j.Nama)"));
  $arr[] = array('Telp. / HP', ':',(!empty($m['Telepon']) and empty($m['Handphone']))? "$m[Telepon]" : (!empty($m['Telepon']) and !empty($m['Handphone']))? "$m[Telepon] / $m[Handphone]" : "$m[Handphone]");

  // Tampilkan
  $p->SetFont('times', '', 10);
  foreach ($arr as $a) {
  	$p->MultiCell(10, $t, '', 0, 'C', 0, 0, '', '', true);
	$p->MultiCell(50, $t, $a[0], 0, 'L', 0, 0, '', '', true);
	$p->MultiCell(4, $t, $a[1], 0, 'C', 0, 0, '', '', true);
	$p->MultiCell(120, $t, $a[2], 0, 'L', 0, 0, '' ,'', true);
	$p->Ln($t);
  }
  	$p->MultiCell(10, $t, '', 0, 'C', 0, 0, '', '', true);
	$p->MultiCell(50, $t,'Alamat', 0, 'L', 0, 0, '', '', true);
	$p->MultiCell(4, $t, ':', 0, 'C', 0, 0, '', '', true);
	$p->MultiCell(110, $t, $m['Alamat'], 0, 'L', 0, 0, '' ,'', true);
	$p->Ln($t+2);
	
	$arr = array();
	$arr[] = array('Orangtua', '', '');
	$arr[] = array('   - Ayah', ':', $m['NamaAyah']);
	$arr[] = array('   - Ibu', ':', $m['NamaIbu']);
	$arr[] = array('   - Alamat', ':', $m['AlamatOrtu']);
	$arr[] = array('   - Handphone', ':', $m['HandphoneOrtu']);
  // Tampilkan
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
	$Identitas = GetFields('identitas',"Kode",KodeID,"*");
 	// *** Tampilkan Pernyataan ***
	$p->Ln($t);
	$p->Write(5,'menyatakan bersedia untuk mematuhi semua peraturan/ketentuan yang berlaku di '.$Identitas['Nama'].', termasuk untuk tidak akan melakukan tindakan asusila, mengkonsumsi dan/atau mengedarkan narkotika dan obat-obat berbahaya (Narkoba). Seandainya saya tidak mematuhinya, saya bersedia menerima sanksi yang ditetapkan oleh '.$Identitas['Nama'].'.');
	$p->Ln(11);
	$p->Write(5,'Demikian surat pernyataan ini dibuat dengan sesungguhnya tanpa ada paksaan dari pihak manapun.');
	$p->Ln($t*2);
	// *** Mengetahui ***
	$p->MultiCell(50, $t, '', 0, 'L', 0, 1);
	$p->MultiCell(70, $t, 'Mengetahui,', 0, 'L', 0, 0, '', '', true);
	$p->MultiCell(40, $t, '', 0, 'C', 0, 0, '', '', true);
	$p->MultiCell(50, $t, $Identitas['Kota'].', '.TanggalFormat(date('Y-m-d')), 0, 'L', 0, 0, '' ,'', true);
	$p->Ln($t);
	$p->MultiCell(70, $t, 'Orang Tua/Wali', 0, 'L', 0, 0, '', '', true);
	$p->MultiCell(40, $t, '', 0, 'C', 0, 0, '', '', true);
	$p->MultiCell(50, $t, 'Yang menyatakan,', 0, 'L', 0, 0, '' ,'', true);
	$p->Ln($t);
	$p->MultiCell(50, $t, '', 0, 'L', 0, 0, '', '', true);
	$p->Ln($t*4+2);

	// *** Lokasi tanda tangan ***
	$p->SetFont('times', 'B', 10);
	$p->MultiCell(70, $t, '', 0, 'L', 0, 0, '', '', true);
	$p->MultiCell(40, $t, '', 0, 'C', 0, 0, '', '', true);
	$p->MultiCell(50, $t, $m['Nama'], 0, 'L', 0, 0, '' ,'', true);
	$p->Ln($t*2-3);


}


function BuatHeader($mhsw,$p) {
	$t=5;
	$p->MultiCell(130, $t, '', 0, 'L', 0, 0, '' ,'', true);
	if (file_exists('../../spmb/foto_file/'.$mhsw['Foto'])){
		//$p->Image('../../spmb/foto_file/'.$mhsw['Foto'], 146, 18, '', 32, '', '', '', false, 300, '', false, false, 1, false, false, false);
	}
	$p->Ln($t);
	$p->SetFont('times', '', 10);
	$p->MultiCell(200, $t, 'Saya yang bertanda tangan di bawah ini :', 0, 'L', 0, 0, '' ,'', true);
	$p->Ln($t*2);
}

function HeaderLogo($jdl, $p, $orientation='P')
{	$pjg = 130;
	$gelombang = GetFields('pmbperiod', "KodeID='".KodeID."' and NA", 'N', "*");
	$tahun = $gelombang['Tahun'];
	$tahunnext = $tahun+1;
	$logo = (file_exists("../img/logo.jpg"))? "../img/logo.jpg" : "img/logo.jpg";
    $identitas = GetFields('identitas', 'Kode', KodeID, 'Nama, Alamat1, Telepon, Fax');
	$p->Image($logo, 20, 4, 16);
	$p->SetY(5);
    $p->SetFont("times", '', 8);
    $p->SetFont("times", 'B', 12);
	$p->Cell(1, 7, '', 0, 0);
	$p->writeHTML("MAHASISWA BARU ".strtoupper($identitas['Nama'])."<br> TAHUN AKADEMIK ".$tahun.'/'.$tahunnext, true, false, false, true, 'C');

	
    
	/*//Judul
	if($orientation == 'L')
	{
		$p->SetFont("times", 'B', 16);
		$p->Cell(20, 7, '', 0, 0);
		$p->Cell($pjg, 7, $jdl, 0, 1, 'L');
	}
	else
	{	$p->SetFont("times", 'B', 12);
		$p->Cell(50, 7, $jdl, 0, 1, 'L');
	}*/
	/*
    $p->SetFont("times", 'I', 6);
	$p->Cell(20, 7, '', 0, 0, 'L');
	$p->Cell($pjg, 3,
      $identitas['Alamat1'], 0, 1, 'L');
	  $p->Cell(20, 7, '', 0, 0, 'L');
    $p->Cell($pjg, 3,
      "Telp. ".$identitas['Telepon'].", Fax. ".$identitas['Fax'], 0, 1, 'L');
    $p->Ln(3);
	if($orientation == 'L') $length = 275;
	else $length = 170;
	*/
	$p->Ln(3);
	
    $p->Ln(2);
	$p->writeHTML('<hr>', true, false, true, false, '');
	
}


?>
