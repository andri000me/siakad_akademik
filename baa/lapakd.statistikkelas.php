<?php
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
  <META content=\"Arisal Yanuarafi\" name=\"author\">
  <link rel=\"stylesheet\" type=\"text/css\" href=\"../themes/$_Themes/index.css\" />
  ";

  $KelasID = GetSetVar('KelasID');
  $kelas_tahun = GetSetVar('kelas_tahun');
  $kelas_jmlsesi = GetSetVar('kelas_jmlsesi', 6);
  $kelas_sesi = GetSetVar('kelas_sesi', 1);
  TampilkanJudul("Laporan Statistik Kelas");
  $tombols = '&nbsp;';
  $wheretahun = "TahunID='$_SESSION[kelas_tahun]'";
	
	if(empty($_SESSION['kelas_tahun']) or $_SESSION['kelas_tahun']=='')
	{ 	$optkelas = "<option value=''>--Isi Tahun Dulu--</option>";  
		$optjmlsesi = "<option value=''>--Isi Tahun Dulu--<option>";
		$optsesi = "<option value=''>--Isi Tahun Dulu--<option>";
	}	
	else
	{	$optkelas = GetOption2('kelas', "Nama", 'Nama', $_SESSION['KelasID'], $wheretahun, 'KelasID');	
		if($optkelas=='' or empty($optkelas))
		{	$optkelas = "<option value=''>--Tidak ada kelas--</option>";
		}
		
		$arrNomer = array('7', '6', '5', '4', '3', '2', '1');
		$optjmlsesi = GetOptionsFromArray($arrNomer, $_SESSION['kelas_jmlsesi'], 1);
		$optsesi = GetOptionsFromArray($arrNomer, $_SESSION['kelas_sesi'], 1);;
	}
	
  if (empty($_SESSION['kelas_tahun'])) {
    $kelas = array();
  }
  else {
    $kelas = GetFields("kelas", 
      "KelasID='$_SESSION[KelasID]' and TahunID='$_SESSION[kelas_tahun]' and KodeID", KodeID, 
      "Nama, KapasitasMaksimum, KapasitasSekarang");
    if (empty($kelas)) $kelas = array();
    else {
      RandomStringScript();
      $scripts = <<<ESD
      <script>
      function fnCetakPerbandinganIPS(KelasID, jmlsesi) {
        var _rnd = randomString();
        lnk = "lapakd.statistikkelas.php?gos=_CetakLaporanPerbandinganIPS&KelasID="+KelasID+"&jmlsesi="+jmlsesi+"&_rnd="+_rnd;
        win2 = window.open(lnk, "", "width=1000, height=800");
        if (win2.opener == null) childWindow.opener = self;
      }
	  function fnCetakPengambilanKHS(KelasID, sesi) {
        var _rnd = randomString();
        lnk = "lapakd.statistikkelas.php?gos=_CetakDaftarPengambilanKHS&KelasID="+KelasID+"&sesi="+sesi+"&_rnd="+_rnd;
        win2 = window.open(lnk, "", "width=1000, height=800, scrollbars");
        if (win2.opener == null) childWindow.opener = self;
      }
      </script>
ESD;
    }
  }
  CheckFormScript('kelas_tahun,KelasID');
  echo <<<ESD
  <table class=box cellspacing=1 align=center width=600>
  <form name='frmHeader' action='?' method=POST onSubmit=\"return CheckForm(this)">
  <input type=hidden name='gos' value='' />
  
  <tr><td width=12></td>
	  <td class=inp width=120>Tahun Akademik:</td>
      <td class=ul width=220>
        <input type=text name='kelas_tahun' value='$_SESSION[kelas_tahun]' size=4 maxlength=6 />
        <input type=submit name='btnCari' value='Cari' />
      </td>
      <td class=inp width=120>Kapasitas Sekarang:</td>
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
  <tr><td class=ul colspan=5 align=center><font color=green><i><b>DAFTAR DAN LAPORAN YANG DAPAT DICETAK:</b></i></font>
	  </td></tr>
  <tr><td bgcolor=silver height=1 colspan=5></td></tr>
  <tr>
	<td class=inp>1</td>
	<td class=ul1 colspan=2>
	<a href='#$i' onClick="javascript:fnCetakPerbandinganIPS('$_SESSION[KelasID]', '$_SESSION[kelas_jmlsesi]')" />Laporan Perbandingan IPS</a></td>
	<td class=inp>Jumlah Semester:</td>
      <td class=ul><select name='kelas_jmlsesi' onChange="this.form.submit()">$optjmlsesi</select></td>
  </tr>
  <tr><td bgcolor=silver height=1 colspan=5></td></tr>
  <tr>
	<td class=inp>2</td>
	<td class=ul1 colspan=2>
	<a href='#' onClick="javascript:fnCetakPengambilanKHS('$_SESSION[KelasID]', '$_SESSION[kelas_sesi]')" />Daftar Pengambilan KHS</a></td>
	<td class=inp>Semester Ke:</td>
      <td class=ul><select name='kelas_sesi' onChange="this.form.submit()">$optsesi</select></td>
  </tr>
  <tr><td bgcolor=silver height=1 colspan=5></td></tr>
     
  
  </form>
  </table>
  </p>
ESD;
	echo "$scripts";
}

function _CetakDaftarPengambilanKHS()
{	session_start();
	
	include_once "../dwo.lib.php";
	include_once "../db.mysql.php";
	include_once "../connectdb.php";
	include_once "../parameter.php";
	include_once "../cekparam.php";
	  
	// *** Parameters ***
	include_once "../fpdf.php";
	
	$pdf = new FPDF('L');
	$pdf->SetTitle("Pengambilan KHS");
	$pdf->SetAutoPageBreak(true, 5);
	$pdf->AddPage('L');
	$pdf->SetFont('Helvetica', 'B', 9);
	
	// Tampilkan datanya
	HeaderLogo("DAFTAR PENGAMBILAN KHS", $pdf, 'L');
	AmbilPengambilan($_REQUEST['KelasID'], $_REQUEST['sesi'], $pdf);
	
	$pdf->Output();
}

function AmbilPengambilan($kelasid, $sesi, $p)
{	// Buat headernya dulu
  $NamaKelas = GetaField('kelas', 'KelasID', $kelasid, 'Nama');
  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell(90, 4, 'Kelas: '.$NamaKelas, 0, 0);
  $p->Cell(120, 4, 'Semester: '.$sesi, 0, 0);
  $p->Ln(5);
  
  $p->SetFont('Helvetica', 'B', 8);
  $t = 6;
  
  $p->Cell(8, $t, 'No', 1, 0, 'C');
  $p->Cell(60, $t, 'Nama Mahasiswa', 1, 0, 'C');
  $p->Cell(15, $t, 'SKS', 1, 0, 'C');
  $p->Cell(15, $t, 'IPS', 1, 0, 'C');
  $p->Cell(45, $t, 'Tanggal', 1, 0, 'C');
  $p->Cell(80, $t, 'Paraf', 1, 0, 'C');
  $p->Ln($t);

  // Ambil Isi dari KHS
  $s = "select MhswID, Nama from mhsw where KelasID='$kelasid' and KodeID='".KodeID."' order by Nama";
  $r = _query($s);
  $n = 0;
  $totarr = array(); $maxips = 0; $minips = 4;
  while($w = _fetch_array($r))
  {	  $n++;
	  $p->SetFont('Helvetica', '', 8);
	  $p->Cell(8, $t, $n, 1, 0, 'C');
	  $p->Cell(60, $t, $w['Nama'], 1, 0, 'L');
	  $khs = GetFields('khs', "MhswID='$w[MhswID]' and Sesi='$sesi' and KodeID", KodeID, 'IPS, SKS'); 
	  $p->Cell(15, $t, $khs['SKS'], 1, 0, 'C');
	  $totarr['SKS'] += $khs['SKS'];
	  $p->Cell(15, $t, $khs['IPS'], 1, 0, 'C');
	  $totarr['IPS'] += $khs['IPS'];
	  $minips = ($khs['IPS'] < $minips)? $khs['IPS'] : $minips;
	  $maxips = ($khs['IPS'] > $maxips)? $khs['IPS'] : $maxips;
	  $p->Cell(45, $t, '', 1, 0, 'C');
	  if($n%2 == 1)
	  {	$p->Cell(40, $t, $n.'.', 'LTR', 0, 'L');
		if(floor($n/2)==0) 
		{	$p->SetFillColor(200, 200, 200);
			$p->Cell(40, $t, '', 1, 0, 'L');
			$p->SetFillColor(255, 255, 255);
		}
		else $p->Cell(40, $t, '', 'LBR', 0, 'L');
	  }
	  else
	  {	$p->Cell(40, $t, '', 'LBR', 0, 'L');
		$p->Cell(40, $t, $n.'.', 'LTR', 0, 'L');
	  }
	  $p->Ln($t);
  }
  
  if($n > 0)
  {	  $p->SetFont('Helvetica', 'B', 8);
	  $p->Cell(8, $t, '', 1, 0, 'C');
	  $p->Cell(60, $t, 'Rata-rata', 1, 0, 'C');
	  $p->Cell(15, $t, number_format($totarr['SKS']/$n, 2), 1, 0, 'C');
	  $p->Cell(15, $t, number_format($totarr['IPS']/$n, 2), 1, 0, 'C');
	  $p->Cell(45, $t, '', 1, 0, 'C');
	  if($n%2 == 0)
	  {	 
		$p->SetFillColor(200, 200, 200);
		$p->Cell(40, $t, '', 1, 0, 'L');
		$p->SetFillColor(255, 255, 255);
		$p->Cell(40, $t, '', 'LBR', 0, 'L');
	  }
	  else
	  {	$p->SetFillColor(255, 255, 255);
		$p->Cell(40, $t, '', 'LBR', 0, 'L');
		$p->SetFillColor(200, 200, 200);
		$p->Cell(40, $t, '', 1, 0, 'L');
	  }
  }
  $p->Ln($t*2);
  
  $t = 5;
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell(8, $t, '', 0, 0, 'C');
  $p->Cell(60, $t, 'IPS Tertinggi', 1, 0, 'C');
  $p->Cell(30, $t, number_format($maxips, 2), 1, 0, 'C');
  $p->Cell(50, $t, '', 0, 0, 'C');
  $p->Cell(45, $t, 'Mengetahui,', 0, 0);
  $p->Ln($t);
  
  $p->Cell(8, $t, '', 0, 0, 'C');
  $p->Cell(60, $t, 'IPS Terendah', 1, 0, 'C');
  $p->Cell(30, $t, number_format($minips, 2), 1, 0, 'C');
  $p->Cell(45, $t, '', 0, 0, 'C');
  $p->Ln($t);
  
  $p->Cell(8, $t, '', 0, 0, 'C');
  $p->Cell(60, $t, 'IPS Rata-rata', 1, 0, 'C');
  $p->Cell(30, $t, number_format((($n > 0)? $totarr['IPS']/$n : 0), 2), 1, 0, 'C');
  $p->Cell(45, $t, '', 0, 0, 'C');
  $p->Ln($t);
}

function _CetakLaporanPerbandinganIPS()
{
	session_start();
	
	include_once "../dwo.lib.php";
	include_once "../db.mysql.php";
	include_once "../connectdb.php";
	include_once "../parameter.php";
	include_once "../cekparam.php";
	  
	// *** Parameters ***
	include_once "../fpdf.php";
	
	$pdf = new FPDF('L');
	$pdf->SetTitle("Laporan Perbandingan IPS");
	$pdf->SetAutoPageBreak(true, 5);
	$pdf->AddPage('L');
	$pdf->SetFont('Helvetica', 'B', 9);
	
	// Tampilkan datanya
	HeaderLogo("LAPORAN PERBANDINGAN IPS",$pdf, 'L');
	AmbilPerbandingan($_REQUEST['KelasID'], $_REQUEST['jmlsesi']+1, $pdf);
	
	$pdf->Output();
}
	
// *** Functions ***
function AmbilPerbandingan($kelasid, $tot, $p) {
  // Buat headernya dulu
  
  $NamaKelas = GetaField('kelas', 'KelasID', $kelasid, 'Nama');
  $p->SetFont('Helvetica', 'B', 10);
  $p->Cell(100, 4, 'Kelas: '.$NamaKelas, 0, 0);
  $p->Ln(5);
  
  $p->SetFont('Helvetica', 'B', 8);
  $t = 3.5;
  $leb1 = 10;
  $leb2 = 20;
  
  $p->Cell(8, $t, 'No', 1, 0, 'C');
  $p->Cell(60, $t, 'Nama Mahasiswa', 1, 0, 'C');
  $p->Cell($leb1, $t, '1', 1, 0, 'C');
  for($i=2; $i<$tot; $i++)
  {	$p->Cell($leb2, $t, 'Naik/Turun', 1, 0, 'C');
	$p->Cell($leb1, $t, $i, 1, 0, 'C');
  }
  $p->Cell($leb1, $t, 'IPK', 1, 0, 'C');
  $p->Ln($t);

  // Ambil Isi dari KHS
  $s = "select MhswID, Nama from mhsw where StatusMhswID='A' and KodeID='".KodeID."' order by Nama";
  $r = _query($s);
  $n = 0;
  $totarr = array();
  while($w = _fetch_array($r))
  {	  $n++;
	  $p->SetFont('Helvetica', '', 8);
	  $p->Cell(8, $t, $n, 1, 0, 'C');
	  $p->Cell(60, $t, $w['Nama'], 1, 0, 'L');
	  
	  $ips = GetaField('khs', "MhswID='$w[MhswID]' and Sesi='1' and KodeID", KodeID, 'IPS'); 
	  $totarr[1] += $ips;
	  $p->Cell($leb1, $t, $ips, 1, 0, 'C');
	  for($i=2; $i<$tot; $i++)
	  {	  
		  $ipstemp = GetaField('khs', "MhswID='$w[MhswID]' and Sesi='$i' and KodeID", KodeID, 'IPS'); 
		  $ipsdiff = $ipstemp-$ips;
		  if($ipsdiff < 0) $p->SetFillColor(255, 50, 50);
		  else if($ipsdiff > 0) $p->SetFillColor(50, 50, 255);
		  else $p->SetFillColor(200, 200, 200);
		  
		  $p->Cell($leb2, $t, $ipsdiff, 1, 0, 'C');	
		  $p->Cell($leb1, $t, $ipstemp, 1, 0, 'C');
		  $totarr[$i] +=$ipstemp;
		  $ips = $ipstemp;
	  }	  
	  $ipk = GetaField('khs', "MhswID='$w[MhswID]' and Sesi='1' and KodeID", KodeID, 'IP'); 
	  $p->Cell($leb1, $t, $ipk, 1, 0, 'C');
	  $totarr['ipk'] += $ipk;
	  $p->Ln($t);
  }
  
  if($n > 0)
  {
	  $p->Cell(8, $t, '', 0, 0, 'C');
	  $p->SetFont('Helvetica', 'B', 8);
	  $p->Cell(60, $t, 'Rata-Rata', 1, 0, 'C');  
	  $p->Cell($leb1, $t, number_format($totarr[1]/$n, 2), 1, 0, 'C');
	  for($i=2; $i<$tot; $i++)
	  {	  
		  $ipsdiff = ($totarr[$i]-$totarr[$i-1])/$n;
		  if($ipsdiff < 0) $p->SetFillColor(255, 50, 50);
		  else if($ipsdiff > 0) $p->SetFillColor(50, 50, 255);
		  else $p->SetFillColor(200, 200, 200);
		  
		  $p->Cell($leb2, $t, number_format($ipsdiff, 2), 1, 0, 'C');	
		  $p->Cell($leb1, $t, number_format($totarr[$i]/$n, 2), 1, 0, 'C');
	  }	  
	  $p->Cell($leb1, $t, number_format($totarr['ipk']/$n, 2), 1, 0, 'C');
	  $p->Ln($t);
  }
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