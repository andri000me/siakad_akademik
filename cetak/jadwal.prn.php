<?php

function SemuaJadwal1() {
  include "db.mysql.php";
  include "connectdb.php";
  include "dwo.lib.php";
  $tahun = $_REQUEST['tahun'];
  $prodi = $_REQUEST['prodi'];
  $prid = $_REQUEST['prid'];
  $thn = GetaField('tahun', "TahunID='$tahun' and ProgramID='$prid' and ProdiID", $prodi, "Nama");
  $prd = GetFields('prodi', 'ProdiID', $prodi, "FakultasID, Nama");
  $fak = GetaField('fakultas', 'FakultasID', $prd['FakultasID'], 'Nama');

  $lf = chr(13).chr(10);
  $mxc = 150;
  $mxb = 52;
  $grs = str_pad('-', $mxc, '-').$lf;
  $TGL = date('d-m-Y');
  
  $nmf = "tmp\$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(77).chr(27).chr(15));
  
  // query
  $s = "select j.*, LEFT(j.Nama, 35) as MK
    from jadwal j
    where j.TahunID='$tahun'
      and INSTR(j.ProdiID, '$prodi')>0
    order by j.HariID, j.JamMulai, j.MKKode";
  $r = _query($s);
  $n = 0;
  $hal = 1;
  $kol = 0;
  $isi = array();
  ResetArrayIsi($isi, $mxb);
  $hr = 'abcdefghijklmnopqrstuvwxyz';
  while ($w = _fetch_array($r)) {
    if ($n == 0 && $kol == 0) {
      $hdr = str_pad("*** DAFTAR JADWAL KULIAH/RESPONSI ***", $mxc, ' ', STR_PAD_BOTH). $lf.
        str_pad("Semester   : $thn ", 126) . 
        "Tanggal    : $TGL" .$lf.
        str_pad("Fak/Jur    : $fak/$prd[Nama]", 126) . 
        "Halaman    : $hal" .$lf .
        $grs .
        "Hari    Jam          Kode    Matakuliah                         Kls JEN RE ".
        "Hari    Jam          Kode    Matakuliah                         Kls JEN RE ". 
        $lf.$grs;
      fwrite($f, $hdr);
    }
    if ($hr != $w['HariID'] || ($n == 0)) {
      $hr = $w['HariID'];
      $_hr = GetaField('hari', 'HariID', $w['HariID'], 'Nama');
    }
    else {
      $_hr = str_pad(' ', 7);
    }
    $jm = substr($w['JamMulai'], 0, 5);
    $js = substr($w['JamSelesai'], 0, 5);
    $jam = "$jm-$js";
    $p = TRIM($w['ProgramID'], '.');
    $p = explode('.', $p);
    $p = substr($p[0], 0, 1);
    $isi[$n] .=
      str_pad($_hr, 8).
      str_pad($jam, 13).
      str_pad($w['MKKode'], 8).
      str_pad($w['MK'], 37).
      str_pad($w['NamaKelas'], 3).
      str_pad($w['JenisJadwalID'], 3).
      str_pad($p, 3);
    $n++;
    if ($n >= $mxb && $kol == 0) {
      $n = 0;
      $kol = 1;
    }
    if ($n >= $mxb && $kol ==1) {
      $n = 0;
      $kol = 0;
      $hal++;
      for ($i = 0; $i <= $mxb; $i++) fwrite($f, $isi[$i].$lf);
      fwrite($f, chr(12));
      ResetArrayIsi($isi, $mxb);
    }
  }
  for ($i = 0; $i <= $mxb; $i++) fwrite($f, $isi[$i].$lf);
  fclose($f);
  TampilkanFileDWOPRN($nmf);
}

function ResetArrayIsi1(&$isi, $mxb) {
  for ($i=0; $i < $mxb; $i++) {
    $isi[$i] = '';
  }
}

function JdwlperDosenCetak() {
  global $_lf;
  include "db.mysql.php";
  include "connectdb.php";
  include "dwo.lib.php";
  $tahun = $_REQUEST['tahun'];
  $prodi = $_REQUEST['prodi'];
  $prid = $_REQUEST['prid'];
  if (empty($tahun) && empty($prodi) && empty($prid)) 
    die (ErrorMsg("Data Tidak Lengkap",
      "Isikan Tahun, Program, dan Program Studi sebelum mencetak"));
  $_prodi = GetaField('prodi', "ProdiID", $prodi, 'Nama');
  $_prid = GetaField('program', 'ProgramID', $prid, 'Nama');
  // data
  $s = "select j.HariID,j.MKKode, j.Nama, j.NamaKelas, j.SKS, j.SKSAsli, mk.Sesi, j.DosenID, j.ProdiID,j.MKID,j.JadwalID,
    jj.Nama as JenisJadwal, concat(d.Nama, ', ', d.Gelar) as DSN, j.JenisJadwalID, j.RuangID, left(j.ProgramID,2) as Prog,
    h.Nama as HR, time_format(j.JamMulai, '%H:%i') as JM, time_format(j.JamSelesai, '%H:%i') as JS
    from jadwal j
      left outer join jenisjadwal jj on j.JenisJadwalID=jj.JenisJadwalID
      left outer join dosen d on j.DosenID=d.Login
      left outer join hari h on j.HariID=h.HariID
      left outer join mk mk on j.MKID=mk.MKID
    where j.TahunID='$tahun'
      and INSTR(j.ProdiID, '.$prodi.') > 0
    order by d.Nama, j.MKKode, j.NamaKelas, h.HariID";
  $r = _query($s);
  $_lf = chr(13).chr(10);
  $maxcol = 153;
  $maxbrs = 45;
  $brs = 0; $hal = 1;
  $nmf = HOME_FOLDER  .  DS . "tmp/$_SESSION[_Login].dwoprn";
		$f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(77).chr(27).chr(15).$_lf);
  $div = str_pad('-', $maxcol, '-').$_lf;
  $kddosen = '';
  $tgl = date("d-m-Y");
  $wkt = date("H:i:j");
  $jnstahun = NamaTahun($tahun);
  $FakNama  = GetFields("prodi p left outer join fakultas f on f.FakultasID = p.FakultasID","p.ProdiID",$prodi,
				"p.nama as pnama, f.nama as fnama");
							
  $hdr = str_pad("** Jadwal Kuliah Per Dosen **",$maxcol,' ',STR_PAD_BOTH ).$_lf.$_lf;
  $hdr .= str_pad("Semester",10,' ').str_pad(":",2,' ').str_pad($jnstahun,120,' ').str_pad("TGL  :",2,' ').str_pad($tgl,5,' ',STR_PAD_RIGHT).$_lf;
  $hdr .= str_pad("Fakultas",10,' ').str_pad(":",2,' ').str_pad($FakNama['fnama'].'/'.$FakNama['pnama'],120,' ').str_pad("HAL  :",2,' ').str_pad($hal,5,' ',STR_PAD_RIGHT).$_lf;
  $hdr .= $div;
  $hdr .= "NO. KODE NAMA DOSEN                                MATA KULIAH                                       HARI       JAM      JNS  SEKSI R/E SKS RUANG   JML".$_lf.$div;
  fwrite($f, $hdr);
    
	while ($w = _fetch_array($r)) {
	$jml = GetaField('jadwal',"INSTR(ProdiID, '.$prodi.') > 0 and DosenID",$w['DosenID'],"Sum(SKS)");
	if($brs > $maxbrs){
		$hal++; $brs = 1;
		fwrite($f,chr(12).$_lf);
		fwrite($f, $hdr);
	} 
    if ($dsn != $w['DSN']) {
      $dsn = $w['DSN'];
      $_dsn = $dsn;
	  $n++;
    } else $_dsn = '';
	if ($kddosen != $w['DosenID']) {
	  $kddosen = $w['DosenID'];
	  $_kddosen = $kddosen;
	} else {
	$_kddosen = '';
	$jml = '';
	}
	if ($n_ != $n) {
	  $n_ = $n;
	  $_n_ = $n_.".";
	} else $_n_ = '';
	 if ($kdmk != $w['MKKode']) {
      $kdmk = $w['MKKode'];
      $_kdmk = $kdmk;
    } else $_kdmk = '';
	if ($mk != $w['Nama']) {
	  $mk = $w['Nama'];
	  $_mk = $mk;
	} else $_mk = '';
	$prog = str_replace('.',"",$w['Prog']);
	$b++;
	$brs++;
	$isi = str_pad($_n_,4,' ').
	str_pad($_kddosen,5,' ').
	str_pad($_dsn,42,' ').
	str_pad($_kdmk,6,' ').' '.
	str_pad($_mk,42,' ').' '.
	str_pad($w['HR']." ",7,' ').' '.
	str_pad($w['JM']."-".$w['JS'],11,' ').' '.
	str_pad($w['JenisJadwalID'],4,' ').' '.
	
	str_pad($w['NamaKelas'],6,' ').' '.
	str_pad($prog,5,' ').
	str_pad($w['SKS'],2,' ',STR_PAD_RIGHT).' '.
	str_pad($w['RuangID'],7,' ').
	str_pad($jml,3,' ',STR_PAD_LEFT).
	$_lf;
    fwrite($f, $isi);
	}
	fwrite($f, $div);
	fwrite($f,str_pad("** AKHIR LAPORAN **",$maxcol,' ',STR_PAD_BOTH).$_lf);
	fwrite($f,chr(12).$_lf);
	//fwrite($f,str_pad("Dicetak oleh :",2,' ').str_pad($_SESSION['_Login'],7,' ').$_lf);
    fclose($f);
  	TampilkanFileDWOPRN($nmf);
	
}

function JdwlKuliahCetak() {
  global $_lf;
  include "db.mysql.php";
  include "connectdb.php";
  include "dwo.lib.php";
  $tahun = $_REQUEST['tahun'];
  $prodi = $_REQUEST['prodi'];
  $prid = $_REQUEST['prid'];
  if (empty($tahun) && empty($prodi) && empty($prid)) 
    die (ErrorMsg("Data Tidak Lengkap",
      "Isikan Tahun, Program, dan Program Studi sebelum mencetak"));
  $_prodi = GetaField('prodi', "ProdiID", $prodi, 'Nama');
  $_prid = GetaField('program', 'ProgramID', $prid, 'Nama');
  // data
  $s = "select j.MKKode, j.Nama, j.NamaKelas, j.SKS, j.SKSAsli, mk.Sesi, j.DosenID,j.Kapasitas, j.JumlahMhsw, j.JumlahMhswKRS,
    jj.Nama as JenisJadwal, concat(d.Nama, ', ', d.Gelar) as DSN, j.JenisJadwalID, j.RuangID, j.ProgramID as Program,
    h.Nama as HR, time_format(j.JamMulai, '%H:%i') as JM, time_format(j.JamSelesai, '%H:%i') as JS
    from jadwal j
      left outer join jenisjadwal jj on j.JenisJadwalID=jj.JenisJadwalID
      left outer join dosen d on j.DosenID=d.Login
      left outer join hari h on j.HariID=h.HariID
      left outer join mk mk on j.MKID=mk.MKID
    where j.TahunID='$tahun'
      and INSTR(j.ProdiID, '.$prodi.') > 0
	  and j.JenisJadwalID = 'K'
    order by j.MKKode, j.NamaKelas";
  $r = _query($s);
  $n = 0; $brs=0;
  $mk = '';
  $tgl = date("d-m-Y");
  $wkt = date("H:i:j");
  $maxbrs = 45;
  $maxcol = 150; $_lf = chr(13).chr(10);
  $nmf = HOME_FOLDER  .  DS . "tmp/$_SESSION[_Login].dwoprn";
		$f = fopen($nmf, 'w');
  		fwrite($f, chr(27).chr(77).chr(27).chr(15).$_lf);
  $div = str_pad('-', $maxcol, '-').$_lf;
  
  $jnstahun = NamaTahun($tahun);
  $FakNama  = GetFields("prodi p left outer join fakultas f on f.FakultasID = p.FakultasID","p.ProdiID",$prodi,
				"p.nama as pnama, f.nama as fnama");
							
  $hdr = str_pad("** DAFTAR JADWAL KULIAH **",$maxcol,' ',STR_PAD_BOTH ).$_lf.$_lf;
  $hdr .= str_pad("JENIS : KULIAH",$maxcol,' ',STR_PAD_BOTH ).$_lf.$_lf;
  $hdr .= str_pad("Semester",10,' ').str_pad(":",2,' ').str_pad($jnstahun,120,' ').str_pad("TGL  :",2,' ').str_pad($tgl,5,' ',STR_PAD_RIGHT).$_lf;
  $hdr .= str_pad("Fakultas",10,' ').str_pad(":",2,' ').str_pad($FakNama['fnama'].'/'.$FakNama['pnama'],120,' ').str_pad("JAM  :",2,' ').str_pad($wkt,5,' ',STR_PAD_RIGHT).$_lf;  $hdr .= $div;
  $hdr .= "NO.  KODE  NAMA MATA KULIAH                        SKS SEK R/E HARI    JAM         TRG ISI RUANG    NO.  NAMA DOSEN          ".$_lf.$div;
  fwrite($f, $hdr);
    
	while ($w = _fetch_array($r)) {
	$brs++;
	$_prog = trim($w['Program'], '.');
	$arrProg = explode('.', $_prog);
	//var_dump($arrProg);
	if (count($arrProg) >= 2) {
	  $prog = "R/E";
	} else {
	  $prog = substr($w['Program'], 1, 1);
	}
    if ($kdmk != $w['MKKode']) {
      $kdmk = $w['MKKode'];
      $_kdmk = $kdmk;
	  $n++;
    } else $_kdmk = '';
	if ($mk != $w['Nama']) {
	  $mk = $w['Nama'];
	  $_mk = $mk;
	} else $_mk = '';
	if ($n_ != $n) {
	  $n_ = $n;
	  $_n_ = $n_.".";
	} else $_n_ = '';
	if($brs > $maxbrs){
		$hal++; $brs = 1;
		fwrite($f,chr(12));
		fwrite($f, $hdr);
	}
    //$prog = ($prog == 'X') ? "E" : $prog;	
	//$prog = str_replace('.',"",$w['Prog']);
	$b++;
	$isi = str_pad($_n_,4,' ').
	str_pad($_kdmk,7,' ').
	str_pad($_mk,41,' ').
	str_pad($w['SKS'],2,' ',STR_PAD_LEFT).' '.
	str_pad($w['NamaKelas'],3,' ').' '.
	str_pad($prog,3,' ').' '.
	str_pad($w['HR'],7,' ').' '.
	str_pad($w['JM']."-".$w['JS'],4,' ').' '.
	str_pad($w['Kapasitas'],3,' ',STR_PAD_LEFT).
	str_pad($w['JumlahMhswKRS'],4,' ',STR_PAD_Left).'  '.
	str_pad($w['RuangID'],7,' ').' '.
	str_pad($w['DosenID'],5,' ').
	str_pad($w['DSN'],35,' ').
	$_lf;
    fwrite($f, $isi);
	}
	fwrite($f, $div);
	fwrite($f,str_pad("** AKHIR LAPORAN **",$maxcol,' ',STR_PAD_BOTH).$_lf);
    //fwrite($f,str_pad("Dicetak oleh :",2,' ').str_pad($_SESSION['_Login'],7,' ').$_lf);
	fwrite($f,chr(12).$_lf);
	fclose($f);
  	TampilkanFileDWOPRN($nmf);
}

function JdwlResponsiCetak() {
  global $_lf;
  include "db.mysql.php";
  include "connectdb.php";
  include "dwo.lib.php";
  $tahun = $_REQUEST['tahun'];
  $prodi = $_REQUEST['prodi'];
  $prid = $_REQUEST['prid'];
  if (empty($tahun) && empty($prodi) && empty($prid)) 
    die (ErrorMsg("Data Tidak Lengkap",
      "Isikan Tahun, Program, dan Program Studi sebelum mencetak"));
  $_prodi = GetaField('prodi', "ProdiID", $prodi, 'Nama');
  $_prid = GetaField('program', 'ProgramID', $prid, 'Nama');
  // data
  $s = "select j.MKKode, j.Nama, j.NamaKelas, j.SKS, j.SKSAsli, mk.Sesi, j.DosenID,j.Kapasitas, j.JumlahMhsw, j.JumlahMhswKRS,
    jj.Nama as JenisJadwal, concat(d.Nama, ', ', d.Gelar) as DSN, j.JenisJadwalID, j.RuangID, left(j.ProgramID, 2) as Prog,
    h.Nama as HR, time_format(j.JamMulai, '%H:%i') as JM, time_format(j.JamSelesai, '%H:%i') as JS
    from jadwal j
      left outer join jenisjadwal jj on j.JenisJadwalID=jj.JenisJadwalID
      left outer join dosen d on j.DosenID=d.Login
      left outer join hari h on j.HariID=h.HariID
      left outer join mk mk on j.MKID=mk.MKID
    where j.TahunID='$tahun'
      and INSTR(j.ProdiID, '.$prodi.') > 0
	  and j.JenisJadwalID = 'R'
    order by j.MKKode";
  $r = _query($s);
  $n = 0; $brs=0;
  $mk = '';
  $tgl = date("d-m-Y");
  $wkt = date("H:i:j");
  $maxbrs = 55;
  $maxcol = 150; $_lf = chr(13).chr(10);
  $nmf = HOME_FOLDER  .  DS . "tmp/$_SESSION[_Login].dwoprn";
		$f = fopen($nmf, 'w');
  		fwrite($f, chr(27).chr(77).chr(27).chr(15).$_lf);
  $div = str_pad('-', $maxcol, '-').$_lf;
  
  $jnstahun = NamaTahun($tahun);
  $FakNama  = GetFields("prodi p left outer join fakultas f on f.FakultasID = p.FakultasID","p.ProdiID",$prodi,
				"p.nama as pnama, f.nama as fnama");
							
  $hdr = str_pad("** DAFTAR JADWAL KULIAH **",$maxcol,' ',STR_PAD_BOTH ).$_lf.$_lf;
  $hdr .= str_pad("JENIS : RESPONSI",$maxcol,' ',STR_PAD_BOTH ).$_lf.$_lf;
  $hdr .= str_pad("Semester",10,' ').str_pad(":",2,' ').str_pad($jnstahun,120,' ').str_pad("TGL  :",2,' ').str_pad($tgl,5,' ',STR_PAD_RIGHT).$_lf;
  $hdr .= str_pad("Fakultas",10,' ').str_pad(":",2,' ').str_pad($FakNama['fnama'].'/'.$FakNama['pnama'],120,' ').str_pad("JAM  :",2,' ').str_pad($wkt,5,' ',STR_PAD_RIGHT).$_lf;  $hdr .= $div;
  $hdr .= "NO. KODE   NAMA MATA KULIAH                        SKS SEK R/E HARI    JAM         TRG ISI RUANG   NO   NAMA DOSEN           ".$_lf.$div;
  fwrite($f, $hdr);
    
	while ($w = _fetch_array($r)) {
	$brs++;
    if ($kdmk != $w['MKKode']) {
      $kdmk = $w['MKKode'];
      $_kdmk = $kdmk;
	  $n++;
    } else $_kdmk = '';
	if ($mk != $w['Nama']) {
	  $mk = $w['Nama'];
	  $_mk = $mk;
	} else $_mk = '';
	if ($n_ != $n) {
	  $n_ = $n;
	  $_n_ = $n_.".";
	} else $_n_ = '';
	if($brs > $maxbrs){
		$hal++; $brs = 1;
		fwrite($f,chr(12));
		fwrite($f, $hdr);
		} 
	$prog = str_replace('.',"",$w['Prog']);
	$b++;
	$isi = str_pad($_n_,4,' ').
	str_pad($_kdmk,7,' ').
	str_pad($_mk,42,' ').
	str_pad($w['SKS'],1,' ').' '.
	str_pad($w['NamaKelas'],3,' ').' '.
	str_pad($prog,3,' ').' '.
	str_pad($w['HR'],7,' ').' '.
	str_pad($w['JM']."-".$w['JS'],12,' ').' '.
	str_pad($w['Kapasitas'],5,' ',STR_PAD_RIGHT).
	str_pad($w['JumlahMhswKRS'],2,' ',STR_PAD_RIGHT).
	str_pad($w['RuangID'],7,' ').' '.
	str_pad($w['DosenID'],5,' ').
	str_pad($w['DSN'],35,' ').
	$_lf;
    fwrite($f, $isi);
	}
	fwrite($f, $div);
	fwrite($f,str_pad("** AKHIR LAPORAN **",$maxcol,' ',STR_PAD_BOTH).$_lf);
    //fwrite($f,str_pad("Dicetak oleh :",2,' ').str_pad($_SESSION['_Login'],7,' ').$_lf);
	fwrite($f,chr(12).$_lf);
	fclose($f);
  	TampilkanFileDWOPRN($nmf);
}

function JdwlSemuaCetak() {
  global $_lf;
  include "db.mysql.php";
  include "connectdb.php";
  include "dwo.lib.php";
  $tahun = $_REQUEST['tahun'];
  $prodi = $_REQUEST['prodi'];
  $prid = $_REQUEST['prid'];
  if (empty($tahun) && empty($prodi) && empty($prid)) 
    die (ErrorMsg("Data Tidak Lengkap",
      "Isikan Tahun, Program, dan Program Studi sebelum mencetak"));
  $_prodi = GetaField('prodi', "ProdiID", $prodi, 'Nama');
  $_prid = GetaField('program', 'ProgramID', $prid, 'Nama');
  // data
  $s = "select j.MKKode, j.Nama, j.NamaKelas, j.SKS, j.SKSAsli, mk.Sesi, j.DosenID,j.Kapasitas, j.JumlahMhsw, j.JumlahMhswKRS,
    jj.Nama as JenisJadwal, concat(d.Nama, ', ', d.Gelar) as DSN, j.JenisJadwalID, j.RuangID, left(j.ProgramID, 2) as Prog,
    h.Nama as HR, time_format(j.JamMulai, '%H:%i') as JM, time_format(j.JamSelesai, '%H:%i') as JS
    from jadwal j
      left outer join jenisjadwal jj on j.JenisJadwalID=jj.JenisJadwalID
      left outer join dosen d on j.DosenID=d.Login
      left outer join hari h on j.HariID=h.HariID
      left outer join mk mk on j.MKID=mk.MKID
    where j.TahunID='$tahun'
      and INSTR(j.ProdiID, '.$prodi.') > 0
    order by j.MKKode,j.JenisJadwalID";
  $r = _query($s);
  $n = 0; $brs=0; $hal = 1;
  $mk = '';
  $tgl = date("d-m-Y");
  $wkt = date("H:i:j");
  $maxbrs = 50;
  $maxcol = 150; $_lf = chr(13).chr(10);
  $nmf = HOME_FOLDER  .  DS . "tmp/$_SESSION[_Login].dwoprn";
		$f = fopen($nmf, 'w');
  		fwrite($f, chr(27).chr(77).chr(27).chr(15).$_lf);
  $div = str_pad('-', $maxcol, '-').$_lf;
  
  $jnstahun = NamaTahun($tahun);
  $FakNama  = GetFields("prodi p left outer join fakultas f on f.FakultasID = p.FakultasID","p.ProdiID",$prodi,
				"p.nama as pnama, f.nama as fnama");
							
  $hdr = str_pad("** DAFTAR JADWAL KULIAH **",$maxcol,' ',STR_PAD_BOTH ).$_lf.$_lf;
  $hdr .= str_pad("JENIS : KULIAH/RESPONSI",$maxcol,' ',STR_PAD_BOTH ).$_lf.$_lf;
  $hdr .= str_pad("Semester",10,' ').str_pad(":",2,' ').str_pad($jnstahun,120,' ').str_pad("TGL  :",2,' ').str_pad($tgl,5,' ',STR_PAD_RIGHT).$_lf;
  $hdr .= str_pad("Fakultas",10,' ').str_pad(":",2,' ').str_pad($FakNama['fnama'].'/'.$FakNama['pnama'],120,' ').str_pad("Jam  :",2,' ').str_pad($wkt,5,' ',STR_PAD_RIGHT).$_lf;  $hdr .= $div;
  $hdr .= "No.  KODE/NAMA MATA KULIAH                         SKS JEN SEK R/E HARI    JAM         TRG ISI RUANG   NO.  NAMA DOSEN          ".$_lf.$div;
  fwrite($f, $hdr);
    
	while ($w = _fetch_array($r)) {
	$brs++;
    if ($kdmk != $w['MKKode']) {
      $kdmk = $w['MKKode'];
      $_kdmk = $kdmk;
	  $n++;
    } else $_kdmk = '';
	if ($mk != $w['Nama']) {
	  $mk = $w['Nama'];
	  $_mk = $mk;
	} else $_mk = '';
	if($brs > $maxbrs){
		$hal++; $brs = 1;
		$n = 1;
		fwrite($f,chr(12));
		fwrite($f, $hdr);
		} 
	if ($n_ != $n) {
	  $n_ = $n;
	  $_n_ = $n_.".";
	} else $_n_ = '';
	$prog = str_replace('.',"",$w['Prog']);
	$b++;
	$isi = str_pad($_n_,4,' ').
	str_pad($_kdmk,7,' ').
	str_pad($_mk,41,' ').' '.
	str_pad($w['SKS'],2,' ').' '.
	str_pad($w['JenisJadwalID'],3,' ').
	str_pad($w['NamaKelas'],3,' ').' '.
	str_pad($prog,3,' ').' '.
	str_pad($w['HR'],7,' ').' '.
	str_pad($w['JM']."-".$w['JS'],6,' ').' '.
	str_pad($w['Kapasitas'],3,' ',STR_PAD_LEFT).
	str_pad($w['JumlahMhswKRS'],4,' ',STR_PAD_LEFT).
	str_pad($w['RuangID'],7,' ').' '.
	str_pad($w['DosenID'],5,' ').
	str_pad($w['DSN'],35,' ').
	$_lf;
    fwrite($f, $isi);
	}
	fwrite($f, $div);
	fwrite($f,str_pad("** AKHIR LAPORAN **",$maxcol,' ',STR_PAD_BOTH).$_lf);
    //fwrite($f,str_pad("Dicetak oleh :",2,' ').str_pad($_SESSION['_Login'],7,' ').$_lf);
	fwrite($f,chr(12).$_lf);
	fclose($f);
  	TampilkanFileDWOPRN($nmf);
}

    
?>
