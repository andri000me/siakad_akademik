<?php
// Author: Irvandy Goutama
// Start Date: 31 Januari 2009

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'HalamanUtama' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function HalamanUtama() {
	include_once "../dwo.lib.php";
	include_once "../db.mysql.php";
	include_once "../connectdb.php";
	include_once "../parameter.php";
	include_once "../cekparam.php";

  echo "<HTML xmlns=\"http://www.w3.org/1999/xhtml\">
  <HEAD><TITLE>$title</TITLE>
  <META content=\"Emanuel Setio Dewo\" name=\"author\">
  <META content=\"Sisfo Sekolah\" name=\"description\">
  <link rel=\"stylesheet\" type=\"text/css\" href=\"../themes/$_Themes/index.css\" />
  ";

  $KelasID = GetSetVar('KelasID');
  $kelas_tahun = GetSetVar('kelas_tahun');
  $kelas_sesi = GetSetVar('kelas_sesi', 1);
  $kelas_tahunajaran = GetSetVar('kelas_tahunajaran', date('Y'));
  $kelas_prodimk = GetSetVar('kelas_prodimk');
  TampilkanJudul("Daftar dan Laporan Jurusan");
  $tombols = '&nbsp;';
  $wheretahun = "TahunID='$_SESSION[kelas_tahun]'";
	
	if(empty($_SESSION['kelas_tahun']) or $_SESSION['kelas_tahun']=='')
	{ 	$optkelas = "<option value=''>--Isi Tahun Dulu--</option>";  
		$optsesi = "<option value=''>--Isi Tahun Dulu--<option>";
		$opttahunajaran = "<option value=''>--Isi Tahun Dulu--<option>";
		$optprodimk = "<option value=''>--Isi Tahun Dulu--<option>";
	}	
	else
	{	$optkelas = GetOption2('kelas', "Nama", 'Nama', $_SESSION['KelasID'], $wheretahun, 'KelasID');	
		if($optkelas=='' or empty($optkelas))
		{	$optkelas = "<option value=''>--Tidak ada kelas--</option>";
		}
		
		$arrTahun = array();
		for($i = date('Y')+2; $i >= 2000; $i--)
		{	$arrTahun[]=$i;
		}
		$opttahunajaran = GetOptionsFromArray($arrTahun, $_SESSION['kelas_tahunajaran'], 1);
		
		$arrNomer = array('7', '6', '5', '4', '3', '2', '1');
		$optsesi = GetOptionsFromArray($arrNomer, $_SESSION['kelas_sesi'], 1);
		
		$tahunajarannext = '/ '.($_SESSION['kelas_tahunajaran']+1);
	
		$optprodimk = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID', $_SESSION['kelas_prodimk'],
						"KodeID='".KodeID."'", 'ProdiID');
	}
  RandomStringScript();
  if (empty($_SESSION['kelas_tahun'])) {
    $kelas = array();
  }
  else {
    $kelas = GetFields("kelas", 
      "KelasID='$_SESSION[KelasID]' and TahunID='$_SESSION[kelas_tahun]' and KodeID", KodeID, 
      "Nama, KapasitasMaksimum, KapasitasSekarang");
    if (empty($kelas)) $kelas = array();
    else {
      
      $scripts = <<<ESD
      <script>
      
	  function fnCetakNilaiTengahSemester(KelasID, sesi, thnajaran) {
        var _rnd = randomString();
        lnk = "lapakd.statistikkelas2.php?gos=_CetakNilaiTengahSemester&KelasID="+KelasID+"&sesi="+sesi+"&thnajaran="+thnajaran+"&_rnd="+_rnd;
        win2 = window.open(lnk, "", "width=1000, height=800, scrollbars");
        if (win2.opener == null) childWindow.opener = self;
      }
	  function fnCetakNilaiAkhirSemester(KelasID, sesi, thnajaran) {
        var _rnd = randomString();
        lnk = "lapakd.statistikkelas2.php?gos=_CetakNilaiAkhirSemester&KelasID="+KelasID+"&sesi="+sesi+"&thnajaran="+thnajaran+"&_rnd="+_rnd;
        win2 = window.open(lnk, "", "width=1000, height=800, scrollbars");
        if (win2.opener == null) childWindow.opener = self;
      }
      </script>
ESD;
    }
  }
  $scripts2 = <<<ESD
      <script>
	  function fnCetakDistribusiMK(KelasID, prodimk) {
        var _rnd = randomString();
        lnk = "lapakd.statistikkelas2.php?gos=_CetakDistribusiMK&KelasID="+KelasID+"&prodimk="+prodimk+"&_rnd="+_rnd;
        win2 = window.open(lnk, "", "width=1000, height=800, scrollbars");
        if (win2.opener == null) childWindow.opener = self;
      }
      </script>
ESD;
  CheckFormScript('kelas_tahun,KelasID');
  echo <<<ESD
  <table class=box cellspacing=1 align=center width=600>
  <form name='frmHeader' action='?' method=POST onSubmit=\"return CheckForm(this)">
  <input type=hidden name='gos' value='' />
  
  <tr><td width=12></td>
	  <td class=inp width=120>Tahun Akademik:</td>
      <td class=ul width=190>
        <input type=text name='kelas_tahun' value='$_SESSION[kelas_tahun]' size=4 maxlength=6 />
        <input type=submit name='btnCari' value='Cari' />
      </td>
      <td class=inp width=130>Kapasitas Sekarang:</td>
      <td class=ul>
        <b>$kelas[KapasitasSekarang]</b> &nbsp;
      </td>
      </tr>
  <tr><td></td>
	  <td class=inp>Kelas:</td>
      <td class=ul><select name='KelasID' onChange="this.form.submit()">$optkelas</select></td>
      <td class=inp>Kapasitas Maksimum:</td>
      <td class=ul><b>$kelas[KapasitasMaksimum]</b> &nbsp;</td>
      </tr>
  </tr>	
  <tr><td>&nbsp</td></tr>
  <tr><td class=ul colspan=5 align=center><font color=green><i><b>DAFTAR DAN LAPORAN YANG DAPAT DICETAK</b></i></font>
	  </td></tr>
  <tr><td bgcolor=silver height=1 colspan=11></td></tr>
  <tr>
    <td class=inp>1</td>
	<td class=ul1 colspan=2>
	<a href='#' onClick="javascript:fnCetakNilaiTengahSemester('$_SESSION[KelasID]', '$_SESSION[kelas_sesi]', '$_SESSION[kelas_tahunajaran]')" />
		Daftar Nilai Tengah Semester</a></td>
	<td class=inp>Semester:</td>
      <td class=ul><select name='kelas_sesi' onChange="this.form.submit()">$optsesi</select></td>
  </tr>
  <tr>
	<td class=inp>2</td>
	<td class=ul1 colspan=2>
	<a href='#' onClick="javascript:fnCetakNilaiAkhirSemester('$_SESSION[KelasID]', '$_SESSION[kelas_sesi]', '$_SESSION[kelas_tahunajaran]')" />
		Daftar Nilai Akhir Semester</a></td>
	<td class=inp>Tahun Ajaran:</td>
      <td class=ul><select name='kelas_tahunajaran' onChange="this.form.submit()">$opttahunajaran</select> $tahunajarannext</td>
  </tr>
  <tr><td bgcolor=silver height=1 colspan=11></td></tr>
  <tr>
	<td class=inp>3</td>
	<td class=ul1 colspan=2>
	<a href='#' onClick="javascript:fnCetakDistribusiMK('$_SESSION[KelasID]', '$_SESSION[kelas_prodimk]')" />
		Distribusi Mata Kuliah</a></td>
	<td class=inp>Prodi:</td>
      <td class=ul><select name='kelas_prodimk' onChange="this.form.submit()">$optprodimk</select></td>
  </tr>
  <tr><td bgcolor=silver height=1 colspan=11></td></tr>
  
  </form>
  </table>
  </p>
ESD;
	echo "$scripts";
	echo "$scripts2";
}

function _CetakNilaiAkhirSemester()
{	session_start();
	
	include_once "../dwo.lib.php";
	include_once "../db.mysql.php";
	include_once "../connectdb.php";
	include_once "../parameter.php";
	include_once "../cekparam.php";
	  
	// *** Parameters ***
	include_once "../fpdf.php";
	
	$pdf = new FPDF();
	$pdf->SetTitle("Daftar Nilai Akhir Semester");
	$pdf->SetAutoPageBreak(true, 5);
	$pdf->AddPage();
	$pdf->SetFont('Helvetica', 'B', 9);
	
	// Tampilkan datanya
	HeaderLogo("", $pdf, 'P');
	AmbilNilaiSemester('DAFTAR NILAI AKHIR SEMESTER', $_REQUEST['KelasID'], $_REQUEST['sesi'], $_REQUEST['thnajaran'], $pdf);
	
	$pdf->Output();
}

function _CetakNilaiTengahSemester()
{	session_start();
	
	include_once "../dwo.lib.php";
	include_once "../db.mysql.php";
	include_once "../connectdb.php";
	include_once "../parameter.php";
	include_once "../cekparam.php";
	  
	// *** Parameters ***
	include_once "../fpdf.php";
	
	$pdf = new FPDF();
	$pdf->SetTitle("Daftar Nilai Tengah Semester");
	$pdf->SetAutoPageBreak(true, 5);
	$pdf->AddPage();
	$pdf->SetFont('Helvetica', 'B', 9);
	
	// Tampilkan datanya
	HeaderLogo("", $pdf, 'P');
	AmbilNilaiSemester('DAFTAR NILAI TENGAH SEMESTER', $_REQUEST['KelasID'], $_REQUEST['sesi'], $_REQUEST['thnajaran'], $pdf);
	
	$pdf->Output();
}

function AmbilNilaiSemester($jdl, $kelasid, $sesi, $thnajaran, $p)
{	// Buat headernya dulu
  $pjg = 185;
  $p->SetFont('Helvetica', 'B', 12);
  $p->Cell($pjg, 6, $jdl, 0, 0, 'C');
  $p->Ln(5);
  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell($pjg, 5, 'Semester '.$sesi.' TA '.$thnajaran.'/'.($thnajaran+1), 0, 0, 'C');
  $p->Ln(7);
  
  $NamaKelas = GetaField('kelas', 'KelasID', $kelasid, 'Nama');
  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell(125, 5, 'Kelas: '.$NamaKelas, 0, 0);
  $p->Cell(65, 5, 'Matakuliah: ....................................', 0, 0);
  $p->Ln(7);
  
  $p->SetFont('Helvetica', 'B', 9);
  $t = 4.5;
  
  $p->Cell(8, $t, 'No', 1, 0, 'C');
  $p->Cell(25, $t, 'NIM', 1, 0, 'C');
  $p->Cell(60, $t, 'Nama Mahasiswa', 1, 0, 'C');
  $p->Cell(18, $t, 'No Ujian', 1, 0, 'C');
  $p->Cell(30, $t, 'Paraf', 1, 0, 'C');
  $p->Cell(18, $t, 'Nilai UTS', 1, 0, 'C');
  $p->Cell(30, $t, 'Keterangan', 1, 0, 'C');
  $p->Ln($t);

  $s = "select MhswID, Nama from mhsw where KelasID='$kelasid' and KodeID='".KodeID."' order by Nama";
  $r = _query($s);
  $n = 0;
  while($w = _fetch_array($r))
  {	  $n++;
	  $p->SetFont('Helvetica', '', 9);
	  $p->Cell(8, $t, $n, 1, 0, 'C');
	  $p->Cell(25, $t, $w['MhswID'], 1, 0, 'C');
	  $p->Cell(60, $t, $w['Nama'], 1, 0, 'L');
	  $p->Cell(18, $t, '', 1, 0, 'C');
	  $p->Cell(30, $t, '', 1, 0, 'C');
	  $p->Cell(18, $t, '', 1, 0, 'C');
	  $p->Cell(30, $t, '', 1, 0, 'C');
	  $p->Ln($t);
  }
  
  $kota = GetaField('identitas', 'Kode', KodeID, 'Kota');
  $t = 3;
  $p->Ln($t);
  $p->SetFont('Helvetica', '', 7);
  $p->Cell(100, $t, 'Catatan:', 0, 0, 'L');
  $p->Ln($t);
  $p->Cell(8, $t, '', 0, 0);
  $p->Cell(85, $t, '* 1 lembar untuk BAA', 0, 0, 'L');
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell(60, $t, $kota.', .....................................', 0, 0, 'L');
  $p->Ln($t);
  
  $p->SetFont('Helvetica', '', 7);
  $p->Cell(8, $t, '', 0, 0);
  $p->Cell(85, $t, '* 1 lembar untuk Dosen Pengajar', 0, 0, 'L');
  $p->Ln($t);
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell(95, $t, '', 0, 0);
  $p->Cell(60, $t, 'Dosen Pengajar', 0, 0, 'L');
  $p->Ln($t);
  $p->SetFont('Helvetica', '', 7);
  $p->Cell(10, $t, 'Keterangan :', 0, 0);
  $p->Ln($t);
  
  $prodikelas = GetaField('kelas', 'KelasID', $kelasid, 'ProdiID');
  $s = "select Nama, NilaiMin, NilaiMax from nilai where ProdiID='$prodikelas' and KodeID='".KodeID."' order by Nama";
  $r = _query($s);
  
  while($w = _fetch_array($r))
  {	$p->Cell(8, $t, '', 0, 0);
	$p->Cell(85, $t, $w['Nama'].': '.$w['NilaiMin'].' s/d '.$w['NilaiMax'], 0, 0, 'L');
	$p->Ln($t);
  }
  $p->Ln(2*$t);
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell(95, $t, '', 0, 0);
  $p->Cell(85, $t, '(.........................................)', 0, 0);
}

function HeaderLogo($jdl, $p, $orientation='P')
{	$pjg = 110;
	$logo = (file_exists("../img/logo.jpg"))? "../img/logo.jpg" : "img/logo.jpg";
    $identitas = GetFields('identitas', 'Kode', KodeID, 'Nama, Alamat1, Telepon, Fax');
	$p->Image($logo, 12, 8, 18);
	$p->SetY(5);
    $p->SetFont("Helvetica", '', 8);
    $p->Cell($pjg, 5, "YAYASAN KESEJAHTERAAN ANAK BANGSA", 0, 1, 'C');
    $p->SetFont("Helvetica", 'B', 10);
    $p->Cell($pjg, 7, $identitas['Nama'], 0, 0, 'C');
    
	//Judul
	$p->SetFont("Helvetica", 'B', 16);
	$p->Cell(20, 7, '', 0, 0);
    $p->Cell($pjg, 7, $jdl, 0, 1, 'C');
	
    $p->SetFont("Helvetica", 'I', 6);
	$p->Cell($pjg, 3,
      $identitas['Alamat1'], 0, 1, 'C');
    $p->Cell($pjg, 3,
      "Telp. ".$identitas['Telepon'].", Fax. ".$identitas['Fax'], 0, 1, 'C');
    $p->Ln(3);
	if($orientation == 'L') $length = 275;
	else $length = 190;
    $p->Cell($length, 0, '', 1, 1);
    $p->Ln(2);
}

function _CetakDistribusiMK()
{	session_start();
	
	include_once "../dwo.lib.php";
	include_once "../db.mysql.php";
	include_once "../connectdb.php";
	include_once "../parameter.php";
	include_once "../cekparam.php";
	  
	// *** Parameters ***
	include_once "../fpdf.php";
	
	$pdf = new FPDF('L');
	$pdf->SetTitle("Distribusi MataKuliah");
	$pdf->SetAutoPageBreak(true, 5);
	$pdf->AddPage('L');
	$pdf->SetFont('Helvetica', 'B', 9);
	
	// Tampilkan datanya
	HeaderLogo("DISTRIBUSI MATA KULIAH MHSW. ANGK. ".$_SESSION['kelas_tahun'], $pdf, 'L');
	AmbilDistribusiMK('', $_REQUEST['KelasID']+0, $_REQUEST['prodimk'], $pdf);
	
	$pdf->Output();
}

function AmbilDistribusiMK($jdl, $kelasid, $prodimk, $p)
{	// Buat headernya dulu
  $pjg = 185;
  $p->SetFont('Helvetica', 'B', 12);
  $p->Cell($pjg, 6, $jdl, 0, 0, 'C');
  $p->Ln(5);
  
  $p->SetFont('Helvetica', 'B', 8);
  $t = 4.5;
  $lbrkls = 8;
  
  $p->Cell(8, $t, '', 'LT', 0, 'C');
  $p->Cell(60, $t, '', 'LT', 0, 'C');
  $p->Cell(9, $t, '', 'LT', 0, 'C');
  
  $arrKelas = array();
  
  if(empty($kelasid))
  {
	  if(empty($prodimk))
	  {		$s = "select p.* from prodi p where p.KodeID='".KodeID."' order by ProdiID"; 
			$r = _query($s);
		while($w = _fetch_array($r))
		{	$s1 = "select Nama from kelas where TahunID='$_SESSION[kelas_tahun]' and ProdiID='$w[ProdiID]' and KodeID='".KodeID."'";
			$r1 = _query($s1);
			$n1 = _num_rows($r1);
			
			if($n1 > 0)
			{	$p->Cell($lbrkls*$n1, $t, $w['ProdiID'], 1, 0, 'C');
				while($w1 = _fetch_array($r1))
				{	$arrKelas[] = $w1['Nama'];
				}
			}
		}
	  }
	  else
	  {		$s1 = "select Nama from kelas where TahunID='$_SESSION[kelas_tahun]' and ProdiID='$prodimk' and KodeID='".KodeID."'";
			$r1 = _query($s1);
			$n1 = _num_rows($r1);
		
			if($n1 > 0)
			{	$p->Cell($lbrkls*$n1, $t, $prodimk, 1, 0, 'C');
				while($w1 = _fetch_array($r1))
				{	$arrKelas[] = $w1['Nama'];
				}
			}
	   }
  }
  else
  {	  $prodimk2 = (empty($prodimk))? GetaField('kelas', 'KelasID', $kelasid, 'ProdiID') : $prodimk;
	  $p->Cell($lbrkls, $t, $prodimk2, 1, 0,'C');
	  $arrKelas[] = GetaField('kelas', 'KelasID', $kelasid, 'Nama');;
  }	
  
  $p->Cell(60, $t, '', 'LT', 0, 'C');
  $p->Cell(30, $t, '', 'LTR', 0, 'C');
  $p->Ln($t/2);

  
  $p->Cell(8, $t, 'No', 0, 0, 'C');
  $p->Cell(60, $t, 'Mata Kuliah', 0, 0, 'C');
  $p->Cell(9, $t, 'SKS', 0, 0, 'C');
  foreach($arrKelas as $kelas) $ncol++;
  $p->Cell($lbrkls*$ncol, $t, '', 0, 0, '');
  $p->Cell(60, $t, 'Alternatif Dosen', 0, 0, 'C');
  $p->Cell(30, $t, 'Keterangan', 0, 0, 'C');
  $p->Ln($t/2);
  
  
  $p->Cell(8, $t, '', 'LB', 0, 'C');
  $p->Cell(60, $t, '', 'LB', 0, 'C');
  $p->Cell(9, $t, '', 'LB', 0, 'C');
  $p->SetFont('Helvetica', '', 5);
  foreach($arrKelas as $kelas) 
  {	$p->Cell($lbrkls, $t, $kelas, 1, 0, 'C');
  }
  $p->Cell(60, $t, '', 'LB', 0, 'C');
  $p->Cell(30, $t, '', 'LBR', 0, 'C');
  $p->Ln($t);
  
  // Cek apakah ini adalah hanya tahun(4 digit) atau bukan
  if(!empty($_SESSION['kelas_tahun']))
  {	$whr_tahun = (strlen($_SESSION['kelas_tahun']) == 4)?  
					"LEFT(j.TahunID, 4)='$_SESSION[kelas_tahun]' and" :
					"j.TahunID = '$_SESSION[kelas_tahun]' and";
  }
  
  $s = "select distinct(mk.Nama), mk.SKS from jadwal j left outer join mk on j.MKID=mk.MKID and mk.KodeID='".KodeID."'
				where $whr_tahun j.KodeID='".KodeID."' order by mk.Nama";
  $r = _query($s);
  $n = 0;
  while($w = _fetch_array($r))
  {	  $n++;
	  $p->SetFont('Helvetica', '', 8);
	  $p->Cell(8, $t, $n, 1, 0, 'C');
	  $p->Cell(60, $t, $w['Nama'], 1, 0, 'L');
	  $p->Cell(9, $t, $w['SKS'], 1, 0, 'C');
	  foreach($arrKelas as $kelas)
	  {	$cari = GetaField('jadwal', "NamaKelas='$kelas' and Nama='$w[Nama]' and KodeID", KodeID, 'JadwalID');
		if(!empty($cari)) $p->SetFillColor(100, 100, 255);
		else $p->SetFillColor(255, 255, 255);
		$p->Cell($lbrkls, $t, '', 1, 0, 'C', true);
	  }
	  $listdosen = '';
	  $s1 = "select distinct(j.DosenID) from jadwal j where $whr_tahun j.Nama='$w[Nama]'and j.KodeID='".KodeID."' order by j.DosenID";
	  $r1 =_query($s1);
	  while($w1 = _fetch_array($r1))
	  {	$listdosen .= (empty($listdosen))? $w1[DosenID] : ','.$w[DosenID];	
	  }
	  $p->Cell(60, $t, $listdosen, 1, 0, 'L');
	  $p->Cell(30, $t, '', 1, 0, 'C');
	  $p->Ln($t);
  }

}

function GetOptionsFromArray($arr, $def, $blank=0)
{	$result = '';
	if($blank == 0) $result .= "<option value=''></option>";
	else $result .= '';
	foreach($arr as $a)
	{	$ck = ($a == $def)? 'selected' : '';
		$result .= "<option value='$a' $ck>$a</option>";
	}
	return $result;
}
?>
