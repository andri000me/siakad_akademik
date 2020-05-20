<?php
// Author: Emanuel Setio Dewo
// 25 June 2006
// www.sisfokampus.net

// *** Functions ***
session_start();

include_once "../dwo.lib.php";
include_once "../db.mysql.php";
include_once "../connectdb.php";
include_once "../parameter.php";
include_once "../cekparam.php";
include_once "../header_pdf.php";

function DaftarDosenAktif() {
  global $_lf;
  $mxc = 80; $hal = 1;
  $mxb = 55;
  $grs = str_pad('=', $mxc, '=').$_lf;
  $NamaProdi = (empty($_SESSION['prodi']))? "Semua" : GetaField('prodi', 'ProdiID', $_SESSION['prodi'], 'Nama');
  
	$hdr = str_pad("Daftar Dosen : Tahun $_SESSION[tahun]", $mxc, ' ', STR_PAD_BOTH) . $_lf.
				 str_pad("Program Studi: $NamaProdi", $mxc, ' ', STR_PAD_BOTH) . $_lf . $grs.
				 
				 str_pad('No.', 5). 
				 str_pad('Kode', 10). 
				 str_pad('Nama Dosen', 44).
				 str_pad('Status', 10).
				 $_lf . $grs;
  
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(15));
  $whrprd = (empty($_SESSION['prodi']))? '' : "and INSTR(j.ProdiID, '.$_SESSION[prodi].') > 0";
	$status = (empty($_SESSION['statusdosen'])) ? '' : "and d.StatusDosenID = '$_SESSION[statusdosen]'";
  $s = "select d.Login, concat(d.Nama, ', ', d.Gelar) as DSN, sd.Nama as STTDSN
    from jadwal j
      left outer join dosen d on j.DosenID=d.Login
      left outer join statusdosen sd on d.StatusDosenID=sd.StatusDosenID
    where j.NamaKelas<>'KLINIK' $whrprd $status
    group by d.Login";
  $r = _query($s); $n = 0; $brs = 0;
  fwrite($f, $hdr);
  $jumlahrec = _num_rows($r);
    $jumhal = ceil($jumlahrec/$mxb);
  while ($w = _fetch_array($r)) {
    $brs++;
    $n++;
    if ($brs > $mxb) {
      $brs = 0; $hal++;
      fwrite($f, $grs);
      fwrite($f,str_pad('Halaman : '.$hal."/".$jumhal,10,' ').$_lf);
      fwrite($f, chr(12));
      fwrite($f, $hdr);
    }
    fwrite($f, str_pad($n, 5).
      str_pad($w['Login'], 10).
      str_pad($w['DSN'], 44).
      str_pad($w['STTDSN'], 10).
      $_lf); 
  }
  fwrite($f, $grs);
  $_n = number_format($n);
  fwrite($f, "Jumlah dosen: $_n orang".$_lf); 
  fwrite($f, str_pad('Halaman : '.$hal."/".$jumhal,10,' ').$_lf);
  fwrite($f, chr(12));
  fclose($f);
  TampilkanFileDWOPRN($nmf, 'akd.lap');
}

function FilterStatus(){
	$statdos = GetOption2('statusdosen', "concat(StatusDosenID, ' - ', Nama)", 'StatusDosenID', $_SESSION['statusdosen'], '', 'StatusDosenID');
	echo "<p><form action='?' method='POST'>
				<input type='hidden' name='mnux' value='akd.lap.dosenaktif'>
				<input type='hidden' name='gos' value='DaftarDosenAktif'>
				<table class=box cellpadding=4 cellspacing=1>
				<tr><td class=inp>Status Dosen</td><td class=ul><select name=statusdosen onChange='this.form.submit()'>$statdos</select></td></tr>
				</table>
				</form></p>";
}

// *** Parameters ***
$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid');
$statusdosen = GetSetVar('statusdosen');

// *** Main ***
TampilkanJudul("Daftar Dosen Aktif Mengajar");
TampilkanTahunProdiProgram('akd.lap.dosenaktif', 'DaftarDosenAktif');
FilterStatus();
if (!empty($tahun)) DaftarDosenAktif();
?>
