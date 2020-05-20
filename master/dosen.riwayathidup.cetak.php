<?php
// Author  : Wisnu
// Email   : --
// Start   : 07/04/2009

session_start();

  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  include_once "../header_pdf.php";

// *** Parameters ***
$prodi = sqling($_REQUEST['prodi']);
$Logindsn = $_REQUEST['logindsn'];

$lbr = 190;

$pdf = new PDF();
$pdf->SetTitle("Daftar Riwayat Hidup Dosen");

$whr = array();
  if (!empty($_SESSION['dsnkeycr']) && !empty($_SESSION['dsncr'])) {
    if ($_SESSION['dsnkeycr'] == 'Login') {
      $whr[] = "d.$_SESSION[dsnkeycr] like '$_SESSION[dsncr]%'";
    } 
    else $whr[] = "d.$_SESSION[dsnkeycr] like '%$_SESSION[dsncr]%'";
  }
  $where = implode(' and ', $whr);
  $where = (empty($where))? '' : "and $where";
  $hom = (empty($_SESSION['prodi'])) ? '' : "and Homebase = '$_SESSION[prodi]'";

$s = "select d.Nama as _Namadosen, d.Login as _NIP, d.LulusanPT as _Lulus, d.Keilmuan as _Ilmu, d.Homebase as _Homebase,  
	s.Nama as _Namastatus,
	kl.Nama as _Kelamin,
	jj.keterangan as _Ket,
	p.Nama as _Namaprodi,
	j.Nama as _Namajab,
	d.TempatLahir as _TempatL, date_format(d.TanggalLahir,'%d %b %Y') as _TglLahir
	from dosen d 
	left outer join statusdosen s on d.StatusDosenID = s.StatusDosenID
	left outer join kelamin kl on d.KelaminID = kl.Kelamin
	left outer join jenjang jj on d.JenjangID = jj.JenjangID
	left outer join prodi p on d.Homebase = p.ProdiID
	left outer join jabatan j on d.JabatanID = j.JabatanID
	where d.KodeID = '".KodeID."' $where $hom
	order by d.Login";
$r = _query($s);
while ($w = _fetch_array($r)){
	$pdf->AddPage();
	BuatJudulLaporan($prodi, $pdf);
	BuatDaftarDosen($w, $prodi, $pdf);
}
$pdf->Output();

// *** Functions ***
function BuatJudulLaporan($prodi, $p) {
  global $lbr;
  $p->SetFont('Helvetica', 'B', 14);
  $p->Ln(3);
  $p->Cell($lbr, 5, "Daftar Riwayat Hidup Dosen", 0, 1, 'C');
  $p->Ln(10);
}

function BuatDaftarDosen($w, $prodi, $p) {
  global $lbr;
  $t = 5;
  $c1 = 65; $c2 = 5; $c3 = 80;
  $d1 = 25; $d2 = 5; $d3 = 50;
  $b = 0;
  
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($c1,$t,'Nama',$b,0);
  $p->Cell($c2,$t,':',$b,0,'C');
  $p->Cell($c3,$t,$w[_Namadosen],$b,1);
    
  $p->SetFont('Helvetica', '', 9);  
  $p->Cell($c1,$t,'NIP/NIS/NPP/NIK',$b,0);
  $p->Cell($c2,$t,':',$b,0,'C');
  $p->Cell($c3,$t,$w[_NIP],$b,1);

  $p->Cell($c1,$t,'Status dosen (Tetap/Tidak Tetap)',$b,0);
  $p->Cell($c2,$t,':',$b,0,'C');
  $p->Cell($c3,$t,$w[_Namastatus],$b,1);
  $p->SetFont('Helvetica', '', 9);
  
  $p->Cell($c1,$t,'Tempat dan tanggal lahir',$b,0);
  $p->Cell($c2,$t,':',$b,0,'C');
  $p->Cell($c3,$t,$w[_TempatL].", ".$w[_TglLahir],$b,1);
  $p->SetFont('Helvetica', '', 9);

  $p->Cell($c1,$t,'Jenis kelamin (P/W)',$b,0);
  $p->Cell($c2,$t,':',$b,0,'C');
  $p->Cell($c3,$t,$w[_Kelamin],$b,1);
  $p->SetFont('Helvetica', '', 9);

  $p->Ln(3);
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell(400,10,'Pendidikan',$b,1);

  $p->SetFont('Helvetica', '', 9);
  $p->Cell($c1,$t,'Jenjang pendidikan tertinggi',$b,0);
  $p->Cell($c2,$t,':',$b,0,'C');
  $p->Cell($c3,$t,$w[_Ket],$b,1);

  $p->SetFont('Helvetica', '', 9);
  $p->Cell($c1,$t,'Nama perguruan tinggi almamater',$b,0);
  $p->Cell($c2,$t,':',$b,0,'C');
  $p->Cell($c3,$t,$w[_Lulus],$b,1);
  
  /*$p->SetFont('Helvetica', '', 9);
  $p->Cell($c1,$t,'Pelatihan profesional yang pernah diikuti',$b,0);
  $p->Cell($c2,$t,':',$b,0,'C');
  $p->Cell($c3,$t,$w[_],$b,1);
  */
  
  $p->Ln(3);
  $p->Cell($c1,$t,'Jabatan fungsional akademik',$b,0);
  $p->Cell($c2,$t,':',$b,0,'C');
  $p->Cell($c3,$t,$w[_Namajab],$b,1);
  $p->SetFont('Helvetica', '', 9);
  
  $p->Ln(3);
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell(400,10,'Keahlian',$b,1);

  $p->SetFont('Helvetica', '', 9);
  $p->Cell($c1,$t,'Bidang keahlian',$b,0);
  $p->Cell($c2,$t,':',$b,0,'C');
  $p->Cell($c3,$t,$w[_Ilmu],$b,1);

  /*$p->SetFont('Helvetica', '', 9);
  $p->Cell($c1,$t,'Pengalaman kerja di bidang tersebut',$b,0);
  $p->Cell($c2,$t,':',$b,0,'C');
  $p->Cell($c3,$t,$w[_],$b,1);
  */
  
  $p->Ln(3);
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell(400,10,'Pengalaman mengajar dua tahun terakhir',$b,1);

	$tahun = date('Y');
	$tahun2 = $tahun-2;
	
	$d = "select j.*, m.Nama as _NamaMK from jadwal j left outer join mk m on m.MKID = j.MKID
			where j.DosenID = '".$w[_NIP]."' and substring(j.TahunID,4) >= '".$tahun2."' group by j.MKID";
	$pq = _query($d);
	$num = _num_rows($pq);
	$tt = ($num != 0)? ($t*$num) : $t; 
	
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($c1,$tt,'Mata kuliah',$b,0);
  $p->Cell($c2,$tt,':',$b,1,'C');

  //$p->Cell($c3,$t,$w[_],$b,1);
  
  $p->SetFont('Helvetica', '', 9);
  $p->Cell($c1,$t,'Program studi',$b,0);
  $p->Cell($c2,$t,':',$b,0,'C');
  $p->Cell($c3,$t,$w[_Namaprodi],$b,1);
  
	$d = "select SUM(j.JumlahMhsw) as _JumMhsw from jadwal j left outer join mk m on m.MKID = j.MKID
			where j.DosenID = '".$w[_NIP]."' and substring(j.TahunID,4) >= '".$tahun2."'";
	$dq = _query($d);
	$l = _fetch_array($dq);

  $p->SetFont('Helvetica', '', 9);
  $p->Cell($c1,$t,'Jumlah mahasiswa yang diasuh',$b,0);
  $p->Cell($c2,$t,':',$b,0,'C');
  $p->Cell($c3,$t,$l[_JumMhsw],$b,1);
   
  $p->Cell($lbr, 20, "Daftar Riwayat Hidup ini ditulis dengan sebenarnya.", 0, 1, 'L');
  $p->Cell($lbr, 5, "Mengetahui dan menyetujui isi", 0, 1, 'R');
  $p->Cell($lbr, 5, "Daftar Riwayat Hidup ini", 0, 1, 'R');
  $p->Cell($lbr, 5, "................, ........... 200...", 0, 1, 'R');
  $p->Cell($lbr, 30, $w[_Namadosen], 0, 1, 'R');
  
  $p->setXY($p->getX()+$c1+$c2,$p->getY()-(65+($t*$num)+($t*2)));
  	while ($m = _fetch_array($pq)){
		$p->Cell($c3,$t,$m[_NamaMK],$b,1);
		$p->setX($p->getX()+$c1+$c2);
	}

  
}

?>
