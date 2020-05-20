<?php
session_start();

include_once "../dwo.lib.php";
include_once "../db.mysql.php";
include_once "../connectdb.php";
include_once "../parameter.php";
include_once "../cekparam.php";
include_once "../header_pdf.php";
include_once "../class.ezpdf.php";
include_once "../util.lib.php";

// *** Parameters ***
$pmbid = $_REQUEST['PMBID'];
$wid = $_REQUEST['WID'];
$gelombang = GetFields('pmbperiod', "KodeID='".KodeID."' and NA", 'N', "*");

$pdf =& new Cezpdf('a4', 'portrait');
$pdf->selectFont('../font/Times-Roman.afm');	

// Tampilkan datanya
if($pmbid == 0 and $wid == 0)
{	
  $arrUrut = array('Nomer PMB~p.PMBID asc, p.Nama', 'Nomer PMB (balik)~p.PMBID desc, p.Nama', 'Nama~p.Nama');
  // Urutan
  $_urut = $arrUrut[$_SESSION['_pmbUrut']];
  $__urut = explode('~', $_urut);
  $urut = "order by ".$__urut[1];
  // Filter formulir
  $whr = array();
  if (!empty($_SESSION['_pmbFrmID'])) $whr[] = "p.PMBFormulirID='$_SESSION[_pmbFrmID]'";
  if (!empty($_SESSION['_pmbPrg']))   $whr[] = "p.ProgramID = '$_SESSION[_pmbPrg]' ";
  if (!empty($_SESSION['_pmbNama']))  $whr[] = "p.Nama like '%$_SESSION[_pmbNama]%'";
  if (!empty($_SESSION['_pmbNomer'])) $whr[] = "p.PMBID like '%$_SESSION[_pmbNomer]%'";
  
  $_whr = implode(' and ', $whr);
  $_whr = (empty($_whr))? '' : 'and '.$_whr;
  
  $s1 = "select p.PMBID, p.LulusUjian, w.WawancaraID from 
			pmb p left outer join wawancara w on p.PMBID = w.PMBID 
      and p.KodeID = w.KodeID and w.Tanggal = (select max(Tanggal) 
      from wawancara where PMBID=p.PMBID group by PMBID) 
			where p.KodeID = '".KodeID."' 
			and p.PMBPeriodID='$gelombang[PMBPeriodID]'
			$_whr
			$urut";
  $r1 = _query($s1);
  $errorarr = array();
  while($w1 = _fetch_array($r1))
  {	$error = CetakIsi($w1[PMBID], $w1[WawancaraID], $gelombang, $pdf);
  
	if($w1[LulusUjian] != 'Y') $errorarr[] = $w1[PMBID];
	
  }
  if(!empty($errorarr))
  {	
  	CetakListTidakBisaPrint($errorarr, $pdf);
  }
}
else 
{	$error = CetakIsi($pmbid, $wid, $gelombang, $pdf); 
	if(!empty($error)) CetakListTidakBisaPrint(array($error), $pdf);
}
$pdf->stream();

// *** Functions ***

function CetakIsi($pmbid, $wid, $gelombang, $pdf)
{	$lbr1 = 0;

	//HeaderLogo('', $pdf);
	//$pdf->newPage();
	
	$s = "select pmb.*, prd.Nama as PRD, prg.Nama as PRG 
			from pmb left outer join prodi prd on pmb.ProdiID=prd.ProdiID
					 left outer join program prg on pmb.ProgramID=prg.ProgramID
			where PMBID='$pmbid'";
	$r = _query($s);
	$n = _num_rows($r);
	$w = _fetch_array($r);
		
	//$HasilRekomendasi = GetaField('wawancara', "WawancaraID='$wid' and KodeID", KodeID, 'HasilWawancara');	
	$pmbformulir = GetFields('pmbformulir', "PMBFormulirID='$w[PMBFormulirID]' and KodeID", KodeID, "USM, Wawancara");
	
	//if(empty($HasilRekomendasi)) return $pmbid;
	//else
	if ($w[LulusUjian] == 'Y')
	{	
		CetakSuratRekomendasi($w, $gelombang, $NomerSurat, $pdf);
		$pdf->eznewPage();
	}
	return;
}

function CetakSuratRekomendasi($pmb, $gelombang, $NomerSurat, $pdf)
{	$NomerSurat = GetaField('pmb', "PMBID='$pmb[PMBID]' and KodeID", KodeID, 'NomerSurat');
	if(empty($NomerSurat)) $NomerSurat = GetNextNomerNomerSurat($gelombang['PMBPeriodID'], $pmb['PMBID']);
	$tahun = $gelombang['Tahun'];
	$tahunnext = $tahun+1;
	$identitas = getFields('identitas', "Kode", KodeID, '*');
	
	$db_data[] = array('Isi'=>'');
	$db_data[] = array('Isi'=>'');
	$db_data[] = array('Isi'=>'');
	$db_data[] = array('Isi'=>'');
	$db_data[] = array('Isi'=>'');
	$db_data[] = array('Isi'=>'');
	$db_data[] = array('Isi'=>"<b>No         : $NomerSurat</b>");
	$db_data[] = array('Isi'=>'<b>Perihal    : Pengumuman Hasil Seleksi</b>');
	$db_data[] = array('Isi'=>'');
	$db_data[] = array('Isi'=>'Kepada Yth.');
	$db_data[] = array('Isi'=>'<b>Sdr/i '.$pmb[Nama].'</b>');
	$db_data[] = array('Isi'=>'Di Tempat');
	$db_data[] = array('Isi'=>'');
	$db_data[] = array('Isi'=>'');
	$db_data[] = array('Isi'=>'Dengan Hormat,');
	$db_data[] = array('Isi'=>'');
	$db_data[] = array('Isi'=>"Dengan ini kami sampaikan bahwa berdasarkan Hasil Seleksi Ujian masuk Tahun Ajaran / Akademik $tahun/$tahunnext ".$identitas[Nama]." pada tanggal ".GetDateInWords($gelombang['UjianMulai']).", maka saudara yang namanya tersebut di atas dinyatakan :"); 
	$db_data[] = array('Isi'=>'');
	$db_data[] = array('Isi'=>'                                                                            <b>LULUS</b>');
	$db_data[] = array('Isi'=>'');
	$db_data[] = array('Isi'=>'Selanjutnya saudara/i diberikan kesempatan belajar di '.$identitas[Nama].' dan diwajibkan untuk:');
	$db_data[] = array('Isi'=>"   a. Melakukan Pendaftaran Ulang di Kampus ".$identitas[Nama]." paling lambat s/d tanggal ".GetDateInWords($gelombang['BayarSelesai'])."");
	$db_data[] = array('Isi'=>'   b. Menyelesaikan Administrasi dan mengambil Slip Pembayaran di '.$identitas[Nama].'');
	$db_data[] = array('Isi'=>"   c. Segera mengisi / mengambil KRS (Kartu Rencana Studi) Semester I $tahun/$tahunnext di Administrasi / Akademik ".$identitas[Nama]."");
	$db_data[] = array('Isi'=>'');
	$db_data[] = array('Isi'=>'');
	$db_data[] = array('Isi'=>'Bila memerlukan informasi dan pelayanan akademik dapat menghubungi :');
	$db_data[] = array('Isi'=>'<b>Telp '.$identitas[Telepon].'</b>');
	$db_data[] = array('Isi'=>'');
	
	$KotaIdentitas = GetaField('identitas', "Kode", KodeID, 'Kota');
	$db_data[] = array('Isi'=>'');
	$db_data[] = array('Isi'=>"$KotaIdentitas, ".GetDateInWords(date('Y-m-d')));
	$db_data[] = array('Isi'=>'');
	$db_data[] = array('Isi'=>'');
	$db_data[] = array('Isi'=>'');
	$db_data[] = array('Isi'=>'');
	$db_data[] = array('Isi'=>'');
	
	$PejabatAkademik = GetAField('pejabat', "KodeJabatan='KABAA' and KodeID", KodeID, "Nama");
	$db_data[] = array('Isi'=>"<b>$PejabatAkademik</b>");
	$db_data[] = array('Isi'=>'Bagian Akademik');
	
	$col_names = array('Isi'=>'');
	
	$title = '';
	
	$rowoptions = array('width'=>500,
						'lineCol'=>array(1,1,1),
						'innerLineThickness'=>0,
						'outerLineThickness'=>0,
						'shaded'=>0,
						'rowGap'=>0,
						'fontSize'=>12);
	
	$pdf->ezTable($db_data, $col_names, $title, $rowoptions);
}

function GetNextNomerNomerSurat($gel, $pmbid)
{	$_NomerSuratDigit = 2;
  // Buat nomer baru
  $nmr = GetaField('pmb',
    "PMBPeriodID='$gel' and KodeID", KodeID, "max(left(NomerSurat, $_NomerSuratDigit))");
  $nmr++;
  $nmr = str_pad($nmr, $_NomerSuratDigit, '0', STR_PAD_LEFT);
  
  $tahun = GetaField('pmbperiod', "KodeID='".KodeID."' and NA", 'N', "Tahun");
  
  // Ini hanya akan berjalan dengan baik bilai format gelombang adalah 4 digit tahun + 1 digit gelombang
  
  
  $baru = $nmr."/".date('d').".".date('m')."/".KodeID."/".$tahun;
  
  $s = "update pmb set NomerSurat='$baru' where PMBID='$pmbid' and KodeID='".KodeID."'";
  $r = _query($s);
  
  return $baru;
}

function CetakListTidakBisaPrint($arr, $pdf)
{	$count=0;
	foreach($arr as $PMBID)
	{	$count++;
		$PMB = GetFields('pmb p left outer join prodi prd on p.ProdiID=prd.ProdiID
								left outer join wawancara w on p.PMBID = w.PMBID and p.KodeID = w.KodeID and w.Tanggal = (select max(Tanggal) from wawancara where PMBID=p.PMBID group by PMBID) ', 
						  "p.PMBID='$PMBID' and p.KodeID", 
						  KodeID, 
						  'p.PMBID, p.Nama, p.ProgramID, prd.Nama as PRD, p.LulusUjian, w.HasilWawancara');
		$db_data[] = array('No'=>$count,
							'PMBID'=>$PMB[PMBID],
							'Nama'=>$PMB[Nama],
							'Prodi'=>$PMB[PRD],
							'Program'=>$PMB[ProgramID],
							'Lulus'=>$PMB[LulusUjian],
							'Wawancara'=>$PMB[HasilWawancara]
							);
	}
	
	$col_names = array('No'=>'No.',
					   'PMBID'=>'PMBID',
					   'Nama'=>'Nama',
					   'Prodi'=>'Program Studi',
					   'Program'=>'Program',
					   'Lulus'=>'Lulus?',
					   'Wawancara'=>'Wawancara?'
					   );
	
	$title = '<b>Calon mahasiswa yang tidak bisa dicetak surat kelulusannya karena belum Lulus atau belum mempunyai hasil wawancara</b>';
	
	$rowoptions = array('width'=>500,
						'innerLineThickness'=>1,
						'outerLineThickness'=>1,
						'titleFontSize'=>10,
						'options'=>array('No'=>array('justification'=>'right','width'=>12,'link'=>''),
										 'PMBID'=>array('justification'=>'center','width'=>80,'link'=>''),
										 'Prodi'=>array('justification'=>'left','width'=>60,'link'=>''),
										 'Program'=>array('justification'=>'center','width'=>20,'link'=>'')
										)
						);
	
	$pdf->ezTable($db_data, $col_names, $title, $rowoptions);
}


function HeaderLogo($jdl, $p, $orientation='P', $jdltambahan='')
{	$pjg = 120;
    $top = 800;
	$logo = (file_exists("../img/logo.jpg"))? "../img/logo.jpg" : "img/logo.jpg";
    $identitas = GetFields('identitas', 'Kode', KodeID, 'Nama, Alamat1, Telepon, Fax');
	//$fp = fopen($logo, 'rb');
	//$data = fread($fp, filesize($logo));
	//$p->addImage($data, 12, 8, 18);
	//$p->imagecreatefrom png($logo);
	$img = ImageCreatefromjpeg($logo);
    $p->addImage($img, 30, 765, 65);
	
    $p->AddText($pjg, $top, 10, $identitas['Yayasan']);
    $p->AddText($pjg, $top, 12, $identitas['Nama']);
    
	//Judul
	if($orientation == 'L')
	{
		$p->AddText($pjg+20, $top, 16, $jdl);
	}
	else
	{	$p->AddText($pjg+300, $top, 12, $jdl);
	}
	
	$p->AddText($pjg, $top-12, 8, $identitas['Alamat1']);
    
	if($orientation == 'L')
	{
		$p->AddText($pjg+20, $top-12, 18, $jdltambahan);
	}
	else
	{	
		$p->AddText($pjg+80, $top-12, 14, $jdltambahan);
	}

	$p->AddText($pjg, $top-24, 8, "Telp. ".$identitas['Telepon'].", Fax. ".$identitas['Fax']);
    
	if($orientation == 'L') $length = 775;
	else $length = 575;
    $p->line(20, $top-40, $length, $top-40);
}

?>
