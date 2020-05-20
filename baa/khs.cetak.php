<?php
error_reporting(0);
session_start(); 

  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";

// *** Parameters ***
$lbr = 190;
$mrg = 10;

$TahunID = GetSetVar('TahunID');
$ProdiID = GetSetVar('ProdiID');
$ProgramID = GetSetVar('_khsProgramID');
$Angkatan = GetSetVar('Angkatan', date('Y'));
$MhswID = GetSetVar('MhswID');
//if ($_SESSION['_LevelID']==120) {die("Maaf, untuk saat ini LHS belum bisa ditampilkan.");}
// Cek S1 atau S2? 
$cek = GetaField('mhsw m left outer join prodi p on p.ProdiID=m.ProdiID',"m.MhswID", $MhswID, "p.FakultasID");

	include_once "../header_pdf_khs.php";

if ($_SESSION['_LevelID']==120) $MhswID = $_SESSION['_Login'];

if (!empty($MhswID)) {
  $whr_mhsw = "and h.MhswID = '$MhswID' ";
}
else {
  $whr_mhsw = "and LEFT(m.TahunID, 4) = LEFT('".$_SESSION['Angkatan']."', 4)";
}
$whr_programID = '';
if ($_SESSION['_LevelID']!=120) {
$whr_programID = "and m.ProgramID = '".$ProgramID."' and h.ProdiID = '".$_SESSION['ProdiID']."'";

}

// Init PDF
$pdf = new PDF();
$pdf->SetTitle("Kartu Hasil Studi");

// *** Main ***
$s = "select distinct(h.KHSID) as KHSID, h.MhswID, m.Nama, h.IP, h.IPS, h.Sesi,
      h.TahunID, m.ProgramID, m.ProdiID,
      prd.NamaSesi,
      prd.Nama as _PRD, prg.Nama as _PRG, t.Nama as _THN,
      if (d.Nama is NULL or d.Nama = '', 'Belum diset', d.Nama) as _PA, d.Gelar1,d.Gelar, 
      (h.Biaya - h.Bayar + h.Tarik - h.Potongan) as _Sisa
    from khs h
      left outer join prodi prd on prd.ProdiID = h.ProdiID and prd.KodeID = '".KodeID."'
      left outer join program prg on prg.ProgramID = h.ProgramID and prg.KodeID = '".KodeID."'
      left outer join tahun t on t.TahunID = h.TahunID and t.ProdiID = h.ProdiID and t.KodeID = '".KodeID."'
      left outer join mhsw m on m.MhswID = h.MhswID and m.KodeID = '".KodeID."'
      left outer join dosen d on d.Login = m.PenasehatAkademik and d.KodeID = '".KodeID."'
    where h.TahunID = '".$_SESSION['TahunID']."'
	 	".$whr_programID."
		".$whr_mhsw." group by h.KHSID
    order by h.MhswID";
$r = _query($s);
  //die($s);
while ($w = _fetch_array($r)) {
	$_SESSION['IPS'] = 0;
  $pdf->AddPage();
  BuatHeaderKHS($w, $pdf);
  BuatIsinya($w, $pdf);
  BuatFooter($w, $pdf);
  $pdf->Cell(157, 4, '', 0, 0);
  $pdf->Cell(33, 4, 'Arsip untuk Prodi', 'BTRL', 0);
  $pdf->Ln(3);
  $pdf->Cell(170, 4, '', 0, 0);
  $pdf->Cell(20, 4, '', 'B', 1);

  BuatHeaderKHS($w, $pdf);
  BuatIsinya($w, $pdf);
  BuatFooter($w, $pdf);
  $pdf->Cell(157, 4, '', 0, 0);
  $pdf->Cell(33, 4, 'Untuk Mahasiswa', 'BTRL', 0);
}

$pdf->Output();


// *** Functions ***
function BuatFooter($khs, $p) {
  global $mrg;
  $arrID = GetFields('identitas',"Kode",KodeID,"*");
	$IP = number_format($_SESSION['IPS'],2);
	$SKSPerolehan = $_sks;
  /*$SKSPerolehan = GetaField("krs k left outer join khs h on k.KHSID=h.KHSID and h.KodeID='".KodeID."'", "k.MhswID='$khs[MhswID]' And k.GradeNilai !=''
  							And k.GradeNilai !='-'
							And k.GradeNilai !='T'
							And k.GradeNilai is not Null
							 and k.Tinggi='*' and (h.Sesi <= $khs[Sesi] or k.KHSID=0) and k.KodeID",
    KodeID, "sum(k.SKS)"," group by k.MKKode"); */
  $SKSLulus = GetaField("krs k left outer join khs h on k.KHSID=h.KHSID and h.KodeID='".KodeID."'", "k.MhswID='$khs[MhswID]' AND k.GradeNilai is not Null  AND k.GradeNilai != '' and not k.GradeNilai='T' AND k.GradeNilai !='-' and not k.GradeNilai='E' and k.Tinggi='*' and (h.Sesi <= $khs[Sesi] or k.KHSID=0) and k.KodeID",
    KodeID, "sum(k.SKS)");	
  
  $MaxSKS = GetaField('maxsks',
    "KodeID='".KodeID."' and NA = 'N'
    and DariIP <= $IP and $IP <= SampaiIP and ProdiID", 
    $khs['ProdiID'], 'SKS')+0;
	$KHSIP = HitungIPK($khs['MhswID']);
	$ss = "SELECT * from krs where MhswID='".$khs['MhswID']."' and TahunID <= '".$khs['TahunID']."' and TahunID not like '%Tran%' group by Nama";
	$rr = _query($ss);
	while ($ww = _fetch_array($rr)){
		$SKS += $ww['SKS'];
		$Bobot += ($ww['SKS']*$ww['BobotNilai']);
	}
	$IPK = $Bobot / $SKS;
	$IPK = number_format($IPK,2);
	
	//$rr = _query("UPDATE khs set IP='$IP' where MhswID='".$khs['MhswID']."' and TahunID = '".$khs['TahunID']."'");

  // Pejabat
  $strProdiID = '.'.$_SESSION[ProdiID].'.';
  $pjbt = GetFields('prodi', "ProdiID",$khs['ProdiID'], "*");
    // Array Isi
  $tgl = TanggalFormat(date('Y-m-d'));
  $arr = array();
  $arr[] = array('Index Prestasi Semester', ':', $IP , $arrID['Kota'].', '.$tgl);
  $arr[] = array('Index Prestasi Kumulatif', ':', $IPK, "Mengetahui:");
  //$arr[] = array('Total SKS Lulus', ':', $SKSLulus+0, $pjbt['Jabatan']);
  //$arr[] = array('Total SKS Perolehan', ':', $SKSPerolehan+0);
  $arr[] = array('', '', '', $pjbt['Jabatan'].',');
  $arr[] = array('', '', '', '');
  $arr[] = array('Max SKS Semester Depan', ':', $MaxSKS);
  $arr[] = array('~IMG~');
  $arr[] = array('', '', '', $pjbt['Pejabat']);
  $arr[] = array('', '', '', 'NIP/NIDN: '.$pjbt['NIDN']);
  
  // Tampilkan
  $p->Ln(3);
  $t = 4;
  $p->SetFont('Helvetica', '', 9);
  foreach ($arr as $a) {
    $p->Cell($mrg);
    if ($a[0] == '~IMG~') {
      $fn = "../ttd/$pjbt[KodeJabatan].ttd.gif";
      if (file_exists($fn)) {
        $p->Cell(130);
        $p->Image($fn, null, null, 20);
        $p->Ln(1);
      }
      else $p->Ln($t);
    }
    else {
      $p->Cell($mrg);
      $p->Cell(50, $t, $a[0], 0, 0);
      $p->Cell(2, $t, $a[1], 0, 0, 'C');
      $p->Cell(15, $t, $a[2], 0, 0);
      $p->Cell(30, $t, '', 0, 0);
      $p->Cell(63, $t, $a[3], 0, 0);
      $p->Ln($t);
    }
  }
  
  //$p->SetFont('Helvetica', 'BIU', 8);
  //$p->Cell($mrg);
  //$p->Cell($lbr, $t, 'Keterangan:', 0, 1);
  if (!empty($PesanLogin['KHS'])){
    $p->SetFont('Helvetica', '', 8);
    $p->Cell($mrg);
    $PesanLogin = GetFields('pesanlogin', 'PesanID', '1', '*');
    $p->Cell($lbr, $t, $PesanLogin['KHS'], 0, 1);
  }
  /*$p->Cell($lbr, $t, "( - ) Nilai Matakuliah belum masuk dari jurusan/dosen.", 0, 1);
  $p->Cell($mrg);
  $p->Cell($lbr, $t, "( T ) Nilai belum lengkap.", 0, 1);
  $p->Cell($mrg);
  */
}
function BuatIsinya($khs, $p) {
  global $mrg;
  BuatHeaderDetail($p);
  $lihat      = ($_SESSION['_LevelID']==120? "and k.Final = 'Y'":"");
  $now = date('Y-m-d');
  $Final = GetaField('krs',"Final='N' and TahunID", $khs['TahunID'],"Final");
  if ($Final=='N'){
    $Finalisasi = GetaField('tahun',"TahunID",$khs['TahunID'],"TglNilai");
    if ($Finalisasi<$now){
      $update = "UPDATE krs set Final='Y' where TahunID='$khs[TahunID]'";
      $upd = _query($update);
    }
  }

  $evaluasi = GetaField('krs',"MhswID='$khs[MhswID]' and EvaluasiDosen='N' and 
                          TahunID",$khs['TahunID'],"count(KRSID)");
  if ($evaluasi>0 && $_SESSION['_LevelID']==120){
    $_SESSION['EvalTahunID']=$khs['TahunID'];
    echo "<script>alert('Anda belum menyelesaikan pengisian evaluasi dosen.');window.location='?mnux=evaluasi.dosen';</script>";
  }
  
  $s = "select k.*, left(k.Nama, 45) as MKNama, m.TugasAkhir, m.PraktekKerja,
      format((k.SKS * k.BobotNilai), 2) as NXK,
	  jj.Tambahan, jj.Nama as _NamaJenisJadwal, n.NilaiMin
    from krs k left outer join jadwal j on j.JadwalID=k.JadwalID
			   left outer join jenisjadwal jj on jj.JenisJadwalID=j.JenisJadwalID
			   left outer join nilai n on n.Bobot = k.BobotNilai and n.ProdiID = '".$khs['ProdiID']."'
			   left outer join mk m on m.MKID=k.MKID
    where k.TahunID = '".$khs['TahunID']."'
	and k.MhswID='".$khs['MhswID']."'
	and k.NA='N'
  $lihat
  	group by k.MKKode
    order by k.MKKode";
  $r = _query($s);
  $t = 4;
  $n = 0;
  $p->SetFont('Helvetica', '', 7);
  $_sks = 0; $_nxk = 0;
  while ($w = _fetch_array($r)) {
    $n++;
	if ($w['TugasAkhir']!='Y' && $w['PraktekKerja']!='Y') {
    	$_sks += $w['SKS'];
    	$_nxk += $w['NXK'];
		$_SESSION['IPS'] = $_nxk/$_sks;
	}
	elseif (($w['PraktekKerja']=='Y' || $w['TugasAkhir']=='Y') and $w['NilaiAkhir'] > 0) {
    	$_sks += $w['SKS'];
    	$_nxk += $w['NXK'];
		$_SESSION['IPS'] = $_nxk/$_sks;
	}
    $p->Cell($mrg);
    $p->Cell(10, $t, $n, 'LB', 0, 'R');
    $p->Cell(22, $t, $w['MKKode'], 'B', 0);
	$TagTambahan = ($w['Tambahan'] == 'Y')? "( ".$w['_NamaJenisJadwal']." )" : "";
    $p->Cell(89, $t, $w['MKNama'].' '.$TagTambahan, 'B', 0);
    $p->Cell(5, $t, $w['SKS'], 'B', 0, 'C');
    $p->Cell(14, $t, $w['GradeNilai'], 'B', 0, 'C');
	$p->Cell(13, $t, ($w['NilaiAkhir'] == "0.0")? $w['NilaiMin'] : $w['NilaiAkhir'], 'B', 0, 'C');
    $p->Cell(13, $t, $w['BobotNilai'], 'B', 0, 'R');
    $p->Cell(13, $t, $w['NXK'], 'B', 0, 'R');
    $p->Cell(2, $t, '', 'BR', 0);
    $p->Ln($t);
  }
  // Tampilkan jumlahnya
  $__nxk = number_format($_nxk, 2);
  $p->Cell($mrg);
  $p->Cell(104, $t, 'Jumlah :', 'LB', 0, 'R');
  $p->Cell(15, $t, $_sks, 'B', 0, 'R');
  $p->Cell(15, $t, '', 'B', 0);
  $p->Cell(45, $t, $__nxk, 'B', 0, 'R');
  $p->Cell(2, $t, '', 'BR', 0);
  $p->Ln($t);
  $update = _query("UPDATE khs set IPS='".$_SESSION['IPS']."' where KHSID='".$khs['KHSID']."'");
}
function BuatHeaderDetail($p) {
  global $mrg;
  $t = 4;
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell($mrg);
  $p->Cell(10, $t, 'No.', 'LBT', 0, R);
  $p->Cell(22, $t, 'Kode', 'BT', 0);
  $p->Cell(89, $t, 'Mata Kuliah', 'BT', 0);
  $p->Cell(5, $t, 'SKS', 'BT', 0, 'L');
  $p->Cell(14, $t, 'Nilai', 'BT', 0, 'C');
  $p->Cell(13, $t, 'Angka', 'BT', 0, 'R');
  $p->Cell(13, $t, 'Bobot', 'BT', 0, 'R');
  $p->Cell(13, $t, 'BxK', 'BT', 0, 'R');
  $p->Cell(2, $t, '', 'BTR', 0);
  $p->Ln($t);
}
function BuatHeaderKHS($khs, $p) {
  global $lbr, $mrg;
    $identitas = GetFields('identitas', "Kode", KodeID, '*');
    $logo = (file_exists("../img/logo.jpg"))? "../img/logo.jpg" : "img/logo.jpg";
    $p->Image($logo, $p->GetX()+30, $p->GetY()-5, 20);
    
    $p->SetFont("Helvetica", '', 10);
    $p->Cell($mrg);
    $p->Cell($lbr, 3, $identitas['Yayasan'], 0, 1, 'C');
    $p->SetFont("Helvetica", 'B', 12);
    $p->Cell($mrg);
    $p->Cell($lbr, 4, $identitas['Nama'], 0, 1, 'C');
    $p->SetFont("Helvetica", 'I', 7);
    $p->Cell($mrg);
    $p->Cell($lbr, 3, $identitas['Alamat1'], 0, 1, 'C');
    $p->Cell(1);
    $p->Cell($mrg);
    $p->Cell($lbr, 3, "Website: ".$identitas['Website'].", Email: ".$identitas['Email'], 0, 1, 'C');
    $p->Cell(1);
    $p->writeHTML("<hr>", true, false, true, false, '');
    $p->Ln(1);


  $t = 3;
  
  $p->SetFont('Helvetica', 'B', 12);
  $p->Cell($lbr, 7, "Laporan Hasil Studi Mahasiswa", 0, 1, 'C');
  $p->Ln(1);
  // parameter
  $prodi = $khs['_PRD'];
  $prg   = $khs['_PRG'];
  $thn   = substr($khs['TahunID'], 0, 4);
  $thn = $thn.'/'.($thn+1);
  $semester = (substr($khs['TahunID'], -1, 1) == 1? 'Semester Ganjil' : (substr($khs['TahunID'], -1, 1) == 2? 'Semester Genap' :'Semester Pendek'));
  
  $data = array();
  $data[] = array('Nama', ':', ucwords(strtolower($khs['Nama'])), 'Tahun Akademik', ':', $semester.' '.$thn);
  $data[] = array(NPM, ':', $khs['MhswID'], 'Program Studi', ':', $prodi);
  $data[] = array('Dosen PA', ':', $khs['Gelar1'].' '.ucwords(strtolower($khs['_PA'])).', '.$khs['Gelar'], '', '', '');
  // Tampilkan
  foreach ($data as $d) {
    $p->SetFont('Helvetica', '', 9);
    $p->Cell($mrg);
    $p->Cell(20, 4, $d[0], 0, 0);
    $p->Cell(2, 4, $d[1], 0, 0);
    
    $p->SetFont('Helvetica', 'B', 9);
    $p->Cell(68, 4, $d[2], 0, 0);
    
    $p->SetFont('Helvetica', '', 9);
    $p->Cell(26, 4, $d[3], 0, 0);
    $p->Cell(2, 4, $d[4], 0, 0);
    
    $p->SetFont('Helvetica', 'B', 9);
    $p->Cell(50, 4, $d[5], 0, 1);
  }
  $p->Ln(1);
  /*if ($khs['_Sisa'] > 0) {
    $_Sisa = number_format($khs['_Sisa']);
    $p->SetFont('Helvetica', 'B', 12);
    $p->SetTextColor(255, 255, 255);
    $p->SetFillColor(250, 0, 0);
    $p->Cell($lbr, $t+2, "Mahasiswa memiliki hutang sebesar: Rp. $_Sisa", 1, 1, 'C', true);
    $p->Ln(2);
    
    $p->SetFillColor(0);
    $p->SetTextColor(0, 0, 0);
  }*/
  
}
?>
