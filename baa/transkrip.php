<?php ob_start();error_reporting(0); ?>
<?php
// Kostumisasi oleh: Arisal Yanuarafi
// Februari 2012


// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'HeaderTranskrip' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function HeaderTranskrip() {
	$MhswID = GetSetVar('MhswID');
  $MhswID = ($_SESSION['_LevelID'] !=120)? $MhswID : $_SESSION['_Login'];
  TampilkanJudul("Cetak Transkrip Nilai");
  $tombols = '&nbsp;';
  if (empty($_SESSION['MhswID'])) {
    $mhsw = array();
  }
  else {
    $mhsw = GetFields("mhsw m 
      left outer join prodi prd on m.ProdiID=prd.ProdiID and prd.KodeID='".KodeID."'
	  left outer join jenjang j on prd.JenjangID=j.JenjangID
      left outer join program prg on m.ProgramID=prg.ProgramID and prg.KodeID='".KodeID."'
      left outer join dosen d on m.PenasehatAkademik=d.Login and d.KodeID='".KodeID."'", 
      "m.MhswID='$MhswID' and m.KodeID", KodeID, 
      "m.MhswID, m.StatusAwalID, m.Nama, m.ProdiAsalPT,m.AsalPT,date_format(m.TglLulusAsalPT,'%Y') as TglTamat, m.ProgramID, m.ProdiID, m.PenasehatAkademik,
      d.Nama as NamaDosen, d.Gelar, j.Nama as _Jenjang,
      prd.Nama as _PRD, prg.Nama as _PRG, m.TempatLahir,date_format(m.TanggalLahir,'%d %M %Y') as TanggalLahir,m.NamaAyah");
    if (empty($mhsw)) $mhsw = array();
    else {
      if (empty($mhsw['NamaDosen'])) $mhsw['NamaDosen'] = "<font color=red>&times;</font> Belum diset";
	  $cekTA=GetaField('wisudawan',"MhswID",$mhsw[MhswID],'MhswID');
	
				$ctkTrSementara ="<form method=POST action='baa/transkrip.sementara.php' target='_blank'>
	 			<input type=hidden name='_TrMhswID' value='$mhsw[MhswID]' title='Nilai Sudah OK'>
				<input type=submit value='Transkrip Sementara'></form>
				";
				// Khusus PGSD
        $ctkTrSementara = ($mhsw['ProdiID']=='PGSD') ?"<form method=POST action='baa/transkrip.sementara.php' target='_blank'>
	 			<input type=hidden name='_TrMhswID' value='$mhsw[MhswID]' title='Nilai Sudah OK'>
				<input type=submit value='Transkrip Sementara'></form>" : $ctkTrSementara;
	
	  if ((!empty($cekTA)) && ($_SESSION['_LevelID']==1)) {
	  /*
	  if ($mhsw[StatusAwalID]=='P') {
	  $ProdiAsalPT = GetaField('prodidikti',"ProdiDiktiID",$mhsw[ProdiAsalPT],'Nama');
	  $PTAsal=GetaField('perguruantinggi','PerguruanTinggiID',$mhsw[AsalPT],'Nama');
	 $ctkTr ="<br><form method=POST action='$_SESSION[mnux].cetakhtm.php' target='_blank'>
	 			<input type=hidden name='mhswid' value='$mhsw[MhswID]'>
	 <table class=box>
	 <tr><td colspan=2 class=header><b>Data Perguruan Tinggi Asal:</b></td></tr>
	 <tr><td class=inp>Prodi</td><td class=ul><input type=text name='ProdiAsalPT' value='$ProdiAsalPT' size=30 maxlength=150></td></tr>
	 		<tr><td class=inp>Nama Perguruan Tinggi</td><td class=ul><input type=text name='AsalPT' value='$PTAsal' size=40 maxlength=150></td></tr>	
			<tr><td class=inp>Tahun Tamat</td><td class=ul><input type=text name='TahunTamat' value='$mhsw[TglTamat]' size=4 maxlength=4></td></tr>
			<tr><td class=inp>Jenjang</td><td class=ul><input type=text name='JenjangAsalPT' value='Diploma III (D3)' size=20 maxlength=150></td></tr>		
			<tr><td valign=top><input type=submit value='Cetak Transkrip Akademik'></form></td><td valign=top align=right><form method=POST action='$_SESSION[mnux].cetak.ijazah.php' target='_blank'>
	 			<input type=hidden name='mhswid' value='$mhsw[MhswID]'>
				<input type=submit value='Cetak Ijazah'></form></td></tr></table>";

				}
		else { $ctkTr ="<form method=POST action='$_SESSION[mnux].cetakhtm.php' target='_blank'>
	 			<input type=hidden name='mhswid' value='$mhsw[MhswID]'>
				<input type=submit value='Cetak Transkrip Akademik'></form></td><td valign=top><form method=POST action='$_SESSION[mnux].cetak.ijazah.php' target='_blank'>
	 			<input type=hidden name='mhswid' value='$mhsw[MhswID]'>
				<input type=submit value='Cetak Ijazah'></form>
				";
			} */
	$ctkTr = $ctkTr."<br><form methot=POST action='?'>";
	}
	else { $ctkTr=''; }
		
	RandomStringScript();
      $tombols = <<<ESD
	    </form>
		<tr valign=top><td colspan=4 align=center>$ctkTrSementara
		<table align=center><tr valign=top><td valign=top>$ctkTr</td></tr></table>
</td></tr>
      <script>
      function fnCetakTranskrip(MhswID, jen) {
        var _rnd = randomString();
        lnk = "$_SESSION[mnux].php?gos=_CetakTranskrip&MhswID="+MhswID+"&_rnd="+_rnd+"&jen="+jen;
        win2 = window.open(lnk, "", "width=700, height=500, scrollbars");
        if (win2.opener == null) childWindow.opener = self;
      }
	  function fnCetakTranskripHtm(MhswID) {
        var _rnd = randomString();
        lnk = "$_SESSION[mnux].cetakhtm.php?MhswID="+MhswID;
        win2 = window.open(lnk, "", "width=800, height=500, scrollbars");
        if (win2.opener == null) childWindow.opener = self;
      }
	        function fnEditMhs(MhswID) {
        lnk = "$_SESSION[mnux].edt.mhs.php?MhswID="+MhswID+"&gos=";
        win2 = window.open(lnk, "", "width=300, height=200, top=250,left=380,scrollbars");
        if (win2.opener == null) childWindow.opener = self;
      }
      </script>
ESD;
    }
  }
    $txtCari = ($_SESSION['_LevelID'] !=120)? "<input type=text name='MhswID' value='$MhswID' size=20 maxlength=50 />
  				<input type=submit name='btnCari' value='Cari' />" : "<b>$MhswID</b><input type=hidden name='MhswID' value='$MhswID' />
  				<input type=submit name='btnCari' value='Refresh' />";
   $edtDataMhsw = ($_SESSION[_LevelID]==1)? '&nbsp;<a href=# onClick='."javascript:fnEditMhs('$mhsw[MhswID]')".' /><img src='."img/edit.png".'></a>' : '';
    echo <<<ESD
  <table class=box cellspacing=1 align=center width=600>
  <form name='frmHeader' action='?' method=POST>
  <input type=hidden name='gos' value='' />
  
  <tr><td class=inp width=80>NPM:</td>
      <td class=ul width=220>
        $txtCari
		  
      </td>
      <td class=inp width=90>Nama Mhsw:</td>
      <td class=ul>
        <b>$mhsw[Nama]</b> $edtDataMhsw
      </td>
      </tr>
  <tr><td class=inp>Prodi:</td>
      <td class=ul>$mhsw[_PRD] <sup>$mhsw[_PRG]</sup>&nbsp;</td>
      <td class=inp>Penasehat Akd:</td>
      <td class=ul>$mhsw[NamaDosen] <sup>$mhsw[Gelar]</sup>&nbsp;</td>
      </tr>
	    <tr><td class=inp>Tempat Lahir:</td>
      <td class=ul>$mhsw[TempatLahir] <sup>$mhsw[TanggalLahir]</sup>&nbsp;</td>
      <td class=inp>Nama Orangtua:</td>
      <td class=ul>$mhsw[NamaAyah]</td>
      </tr>
  <tr>
	<td class=ul colspan=4 align=center>
      $tombols
	</td></tr>
  </table>
  </p>
ESD;
}

function BuatHeaderTranskrip($mhsw, $jen, $p) {
  $lbr = 190;
  $p->SetFont('Times', 'B', 14);
  
  if($jen < 2) $p->Cell($lbr, 8, "Transkrip Nilai Akademik", 0, 1, 'C');
  else if($jen == 2) $p->Cell($lbr, 8, "Transkrip Nilai Akademik Sementara", 0, 1, 'C');
  else $p->Cell($lbr, 8, "Transkrip Nilai Akademik", 0, 1, 'C');
  
  $s = "select DISTINCT(m.KonsentrasiID) as _KonsentrasiID, COUNT(k.KRSID) as _countKID  
			from krs k left outer join mk m on m.MKID=k.MKID and m.KodeID='".KodeID."'
			where k.MhswID='$mhsw[MhswID]' and m.KonsentrasiID!=0 and k.KodeID='".KodeID."'
			group by m.KonsentrasiID
			order by _countKID DESC";
  $r = _query($s);
  $w = _fetch_array($r);
  
  $konsentrasi = (empty($w['_KonsentrasiID']))? "-" : GetaField("konsentrasi", "KonsentrasiID='$w[_KonsentrasiID]' and KodeID", KodeID, "Nama");

  if (($mhsw['_BulanLahir'])==1) {
  $blnLhr="Januari";
  }
  else if (($mhsw[_BulanLahir])==2) {
  $blnLhr="Februari";
  }
    else if (($mhsw[_BulanLahir])==3) {
  $blnLhr="Maret";
  }
    else if (($mhsw[_BulanLahir])==4) {
  $blnLhr="April";
  }
    else if (($mhsw[_BulanLahir])==5) {
  $blnLhr="Mei";
  }
    else if (($mhsw[_BulanLahir])==6) {
  $blnLhr="Juni";
  }
    else if (($mhsw[_BulanLahir])==7) {
  $blnLhr="Juli";
  }
    else if (($mhsw[_BulanLahir])==8) {
  $blnLhr="Agustus";
  }
    else if (($mhsw[_BulanLahir])==9) {
  $blnLhr="September";
  }
    else if (($mhsw[_BulanLahir])==10) {
  $blnLhr="Oktober";
  }
    else if (($mhsw[_BulanLahir])==11) {
  $blnLhr="November";
  }
    else if (($mhsw[_BulanLahir])==12) {
  $blnLhr="Desember";
  }
  $arr = array();
  $arr[] = array("NPM", ':', $mhsw['MhswID'], 'Jenjang', ':', $mhsw['_Jenjang']);
  $arr[] = array('Nama', ':', $mhsw['Nama'], 'Program Studi', ':', $mhsw['_PRD']);
  $arr[] = array('Tempat/Tgl Lahir', ':', $mhsw['TempatLahir'] . ', ' . $mhsw['_TanggalLahir'] . ' ' . $blnLhr . ' ' . $mhsw['_TahunLahir'], 'Konsentrasi', ':', $konsentrasi);
  
  $t = 4;
  foreach ($arr as $a) {
    // Kolom 1
    $p->SetFont('Helvetica', '', 8);
    $p->Cell(23, $t, $a[0], 0, 0);
    $p->Cell(2, $t, $a[1], 0, 0);
    
    $p->SetFont('Helvetica', 'B', 8);
    $p->Cell(60, $t, $a[2], 0, 0);
    $p->Cell(40);
    // Kolom 2
    $p->SetFont('Helvetica', '', 8);
    $p->Cell(20, $t, $a[3], 0, 0);
    $p->Cell(2, $t, $a[4], 0, 0);
    
    $p->SetFont('Helvetica', 'B', 8);
    $p->Cell(50, $t, $a[5], 0, 0);
    
    $p->Ln($t);
  }
  $p->Ln(2);
  
  // Judul tabel
  $t = 6;
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell(10, $t, 'No.', 1, 0, 'C');
  $p->Cell(24, $t, 'Kode MK', 1, 0, 'C');
  $p->Cell(90, $t, 'Nama Mata Kuliah', 1, 0, 'C');
  $p->Cell(15, $t, 'SKS', 1, 0, 'C');
  $p->Cell(15, $t, 'Nilai', 1, 0, 'C');
  $p->Cell(15, $t, 'Bobot', 1, 0, 'C');
  $p->Cell(15, $t, 'Mutu', 1, 0, 'C');
  $p->Ln($t); 
}

function BuatIsiTranskrip0($mhsw, $p) {
  // Reset nilai tertinggi
  ResetNilaiTertinggi($mhsw);
  BuatNilaiTertinggi($mhsw);
  // Tampilkan isinya
  $s = "select k.KRSID, k.MKKode, k.Nama, k.BobotNilai, k.GradeNilai, k.SKS, k.Tinggi
    from krs k left outer join jadwal j on k.JadwalID=j.JadwalID
				left outer join jenisjadwal jj on jj.JenisJadwalID=j.JenisJadwalID
    where k.KodeID = '".KodeID."'
      and k.MhswID = '$mhsw[MhswID]'
	  and k.GradeNilai is not null
      and k.Final = 'Y'
    order by k.MKKode";
  $r = _query($s); $n = 0;
  
  $p->SetFont('Helvetica', '', 8);
  $t = 5; $_sks = 0; $_nxk = 0;
  
  while ($w = _fetch_array($r)) {
    $n++;
    $mutu = $w['SKS'] * $w['BobotNilai'];
    $_nxk += $mutu;
    $_sks += $w['SKS'];
    $p->Cell(10, $t, $n, 1, 0, 'C');
    $p->Cell(24, $t, $w['MKKode'], 1, 0);
    $p->Cell(90, $t, $w['Nama'], 1, 0);
    $p->Cell(15, $t, $w['SKS'], 1, 0, 'C');
    $p->Cell(15, $t, $w['GradeNilai'], 1, 0, 'C');
    $p->Cell(15, $t, $w['BobotNilai'], 1, 0, 'C');
    $p->Cell(15, $t, $mutu, 1, 0, 'C');
    $p->Ln($t);
  }
  // Tampilkan jumlahnya
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell(124, $t, 'JUMLAH:', 'LB', 0, 'R');
  $p->Cell(15, $t, $_sks, 'B', 0, 'C');
  $p->Cell(30, $t, '', 'B', 0);
  $p->Cell(15, $t, $_nxk, 'BR', 0, 'C');
  $p->Ln($t);
  $p->Ln(2);
}
function BuatIsiTranskrip1($mhsw, $p) {
  // Reset nilai tertinggi
  ResetNilaiTertinggi($mhsw);
  BuatNilaiTertinggi($mhsw);
  // Tampilkan isinya
  $s = "select k.KRSID, k.MKKode, k.Nama, k.BobotNilai, k.GradeNilai, k.SKS, k.Tinggi,
      j.JenisMKID, j.Urutan, j.Singkatan, j.Nama as JenisMK
    from krs k
      left outer join mk m on k.MKID=m.MKID and m.KodeID='".KodeID."'
      left outer join jenismk j on m.JenisMKID = j.JenisMKID and j.KodeID='".KodeID."'
	  left outer join jadwal jd on jd.JadwalID=k.JadwalID
	  left outer join jenisjadwal jj on jd.JenisJadwalID = jj.JenisJadwalID
	where k.KodeID = '".KodeID."'
      and k.MhswID = '$mhsw[MhswID]'
	  and k.GradeNilai!= 'T'
	  and k.GradeNilai!= '-'
	  and k.GradeNilai!= ''
      and k.Final = 'Y'
	  and k.GradeNilai is not null
      and k.Tinggi = '*'
    order by j.Urutan, k.MKKode";
  $r = _query($s); $n = 0;
  
  $t = 5; $_sks = 0; $_nxk = 0;
  $lbr = 184;
  $jenismkid = '-19721222';
  
  while ($w = _fetch_array($r)) {
    if ($jenismkid != $w['JenisMKID']) {
      $jenismkid = $w['JenisMKID'];
      $p->SetFont('Helvetica', 'B', 8);
      $p->Cell($lbr, $t, $w['JenisMK'] . ' (' . $w['Singkatan']. ')', 'LBR', 1);
      $n = 0;
    }
    $p->SetFont('Helvetica', '', 8);
    $n++;
    $mutu = $w['SKS'] * $w['BobotNilai'];
    $_nxk += $mutu;
    $_sks += $w['SKS'];
    $p->Cell(10, $t, $n, 'LB', 0, 'C');
    $p->Cell(24, $t, $w['MKKode'], 'B', 0);
    $p->Cell(90, $t, $w['Nama'], 'B', 0);
    $p->Cell(15, $t, $w['SKS'], 'B', 0, 'C');
    $p->Cell(15, $t, $w['GradeNilai'], 'B', 0, 'C');
    $p->Cell(15, $t, $w['BobotNilai'], 'B', 0, 'C');
    $p->Cell(15, $t, $mutu, 'BR', 0, 'C');
    $p->Ln($t);
  }
  // Tampilkan jumlahnya
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell(124, $t, 'JUMLAH:', 'LB', 0, 'R');
  $p->Cell(15, $t, $_sks, 'B', 0, 'C');
  $p->Cell(30, $t, '', 'B', 0);
  $p->Cell(15, $t, $_nxk, 'BR', 0, 'C');
  $p->Ln($t);
  $p->Ln(2);
}
function BuatIsiTranskrip2($mhsw, $p) {
  // Reset nilai tertinggi
  ResetNilaiTertinggi($mhsw);
  BuatNilaiTertinggi($mhsw);
  // Tampilkan isinya
  $s = "select k.KRSID, k.MKKode, k.Nama, MAX(k.NilaiAkhir),k.BobotNilai, k.GradeNilai, k.SKS, k.Tinggi
    from krs k left outer join jadwal j on k.JadwalID=j.JadwalID
				left outer join jenisjadwal jj on jj.JenisJadwalID=j.JenisJadwalID
    where k.KodeID = '".KodeID."'
      and k.MhswID = '$mhsw[MhswID]'
	  and k.Tinggi = '*'
      and k.GradeNilai != 'T'
      and k.GradeNilai != '-'
      and k.GradeNilai != ''
      and k.GradeNilai != 'E'
      and k.Final = 'Y'
      and k.TahunID not like 'Tra%'
    Group by k.MKKode
    Order by k.MKKode";
  $r = _query($s); $n = 0;
  
  $p->SetFont('Helvetica', '', 8);
  $t = 5; $_sks = 0; $_nxk = 0;
  
  while ($w = _fetch_array($r)) {
    $n++;
    $mutu = $w['SKS'] * $w['BobotNilai'];
    $_nxk += $mutu;
    $_sks += $w['SKS'];
    $p->Cell(10, $t, $n, 1, 0, 'C');
    $p->Cell(24, $t, $w['MKKode'], 1, 0);
    $p->Cell(90, $t, $w['Nama'], 1, 0);
    $p->Cell(15, $t, $w['SKS'], 1, 0, 'C');
    $p->Cell(15, $t, $w['GradeNilai'], 1, 0, 'C');
    $p->Cell(15, $t, $w['BobotNilai'], 1, 0, 'C');
    $p->Cell(15, $t, $mutu, 1, 0, 'C');
    $p->Ln($t);
  }
  // Tampilkan jumlahnya
  $p->SetFont('Helvetica', 'B', 9);
  $p->Cell(124, $t, 'JUMLAH:', 'LB', 0, 'R');
  $p->Cell(15, $t, $_sks, 'B', 0, 'C');
  $p->Cell(30, $t, '', 'B', 0);
  $p->Cell(15, $t, $_nxk, 'BR', 0, 'C');
  $p->Ln($t);
  $p->Ln(2);
}
function BuatFooterTranskrip($mhsw, $p) {
  $krs = GetFields('krs', "MhswID='$mhsw[MhswID]' and Tinggi='*' and GradeNilai != '-' and GradeNilai != 'T' and GradeNilai !='' And GradeNilai is not Null and KodeID",
    KodeID, "sum(SKS) as _SKS, sum(SKS*BobotNilai) as _NXK");
  $s = "select * from nilai where ProdiID='$mhsw[ProdiID]' and Lulus='N' and KodeID='".KodeID."'";
  $r = _query($s);
  $whr_gagal = '';
  while($w = _fetch_array($r))
  {	$whr_gagal .= " and GradeNilai != '$w[Nama]' ";
  }
  $SKSLulus = GetaField('krs', "MhswID='$mhsw[MhswID]' and Tinggi='*' $whr_gagal and GradeNilai is not Null And GradeNilai !='' AND GradeNilai != '-' and GradeNilai != 'E' and GradeNilai != 'T' and KodeID",
    KodeID, "sum(SKS)");	
  $_sks = $krs['_SKS']+0;
  $_nxk = $krs['_NXK']+0;
  // Buat footernya
  $MaxSesi = GetaField('khs',"MhswID",$mhsw[MhswID],'max(Sesi)');
  $ipk = ($SKSLulus > 0)? $_nxk / $SKSLulus : 0;
  $_ipk = number_format($ipk, 2);
  $predikat = GetaField("predikat", "ProdiID='$mhsw[ProdiID]' and IPKMin <= $_ipk and $_ipk <= IPKMax and KodeID", 
    KodeID, 'Nama');
  $identitas = GetFields('identitas', 'Kode', KodeID, '*');
  $tgl = date('d M Y');
  
  $prd = GetFields('prodi', "ProdiID='$mhsw[ProdiID]' and KodeID", KodeID, '*');
  $F = GetFields('fakultas', "FakultasID='$prd[FakultasID]' and KodeID", KodeID, '*');
  //$pjbt = GetFields('pejabat', "KodeJabatan='KETUA' and KodeID", KodeID, '*');
  
  $arr = array();
  $arr[] = array('Jumlah SKS yang lulus', ':', $SKSLulus . ' SKS');
  $arr[] = array('Jumlah SKS yang diperoleh', ':', $_sks . ' SKS', $identitas['Kota'] . ', '. $tgl);
  $arr[] = array('Jumlah SKS yang harus ditempuh', ':', $prd['TotalSKS'] . ' SKS', 'Dekan,');
  $arr[] = array('Jumlah Nilai Mutu (N x K)', ':', $_nxk);
  $arr[] = array();
  $arr[] = array('~Indeks Prestasi Kumulatif (IPK)', ':', $_ipk);

// Sembunyikan nama pejabat untuk sementara waktu
  //$arr[] = array('~Predikat Kelulusan', ':', $predikat, $F['Pejabat']);
  //$arr[] = array('', '', '', 'NIP. ' . $pjbt['NIP']);

  $arr[] = array('~Predikat Kelulusan', ':', $predikat, '');
  $arr[] = array('~', '', '', $F['Pejabat']);
  // Tampilkan
  $t = 4;
  foreach ($arr as $a) {
    $b = ($a[0][0] == '~')? 'B' : '';
    $a[0] = str_replace('~', '', $a[0]);
    $p->SetFont('Helvetica', $b, 9);
    $p->Cell(55, $t, $a[0], 0, 0);
    $p->Cell(3, $t, $a[1], 0, 0);
    $p->Cell(60, $t, $a[2], 0, 0);
    
    $p->Cell(10);
    $p->Cell(60, $t, $a[3], 0, 0);
    $p->Ln($t);
  }
}

function ResetNilaiTertinggi($mhsw) {
  $s = "update krs set Tinggi = '' where MhswID='$mhsw[MhswID]' and JadwalID<>'0' and KodeID='".KodeID."' ";
  $r = _query($s);
}

function BuatNilaiTertinggi($mhsw) {
  // Ambil semuanya dulu
  $s = "select k.KRSID, k.MKKode, k.BobotNilai, k.GradeNilai, k.SKS, k.Tinggi
    from krs k left outer join jadwal j on k.JadwalID=j.JadwalID
				left outer join jenisjadwal jj on jj.JenisJadwalID=j.JenisJadwalID
    where k.KodeID = '".KodeID."'
      and k.MhswID = '$mhsw[MhswID]'
	  and jj.Tambahan = 'N'
    order by k.MKKode";
  $r = _query($s);
  
  while ($w = _fetch_array($r)) {
    $ada = GetFields('krs', "Tinggi='*' and KRSID<>'$w[KRSID]' and MhswID='$mhsw[MhswID]' and MKKode", $w['MKKode'], '*');
    // Jika nilai sekarang lebih tinggi
    if ($w['BobotNilai'] > $ada['BobotNilai']) {
      $s1 = "update krs set Tinggi='*' where KRSID='$w[KRSID]' ";
      $r1 = _query($s1);
      // Cek yg lalu, kalau tinggi, maka reset
      if ($ada['Tinggi'] == '*') {
        $s1a = "update krs set Tinggi='' where KRSID='$ada[KRSID]' ";
        $r1a = _query($s1a);
      }
    }
    // Jika yg lama lebih tinggi, maka ga usah diapa2in
    else {
    }
  }
}

function _CetakTranskrip() {
  session_start();
  include_once "../dwo.lib.php";
  include_once "../db.mysql.php";
  include_once "../connectdb.php";
  include_once "../parameter.php";
  include_once "../cekparam.php";
  include_once "../header_pdf.php";
  
  // *** Init PDF
  $pdf = new PDF();
  $pdf->SetTitle("Transkrip Nilai");
  $pdf->AddPage();
  $lbr = 190;
  
  $MhswID = $_REQUEST['MhswID'];
  $mhsw = GetFields("mhsw m 
      left outer join prodi prd on m.ProdiID=prd.ProdiID and prd.KodeID='".KodeID."'
      left outer join jenjang j on j.JenjangID=prd.JenjangID
	  left outer join program prg on m.ProgramID=prg.ProgramID and prg.KodeID='".KodeID."'
      left outer join dosen d on m.PenasehatAkademik=d.Login and d.KodeID='".KodeID."'", 
      "m.MhswID='$_SESSION[MhswID]' and m.KodeID", KodeID, 
      "m.MhswID, m.Nama, m.ProgramID, m.ProdiID, m.PenasehatAkademik,
      m.TempatLahir, m.TanggalLahir,
      date_format(m.TanggalLahir, '%d') as _TanggalLahir,
	  date_format(m.TanggalLahir, '%m') as _BulanLahir,
	  date_format(m.TanggalLahir, '%Y') as _TahunLahir,
      d.Nama as NamaDosen, d.Gelar, j.Nama as _Jenjang,
      prd.Nama as _PRD, prg.Nama as _PRG");
  
  
  $jen = $_REQUEST['jen']+0;
  BuatHeaderTranskrip($mhsw, $jen, $pdf);
  $cetak = 'BuatIsiTranskrip'.$jen;
  $cetak($mhsw, $pdf);
  BuatFooterTranskrip($mhsw, $pdf);
  
  $pdf->Output();
}
?>
