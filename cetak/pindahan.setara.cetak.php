<?php
session_start();
include "../sisfokampus.php";
include "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";
include_once "parameter.php";
CetakPindahan();
include_once "disconnectdb.php";

function CetakPindahan() {
  global $_lf;
  $s = "select mps.*, mk.Nama as NamaMK
    from mhswpindahansetara mps
    left outer join mk mk on mps.MKID=mk.MKID
    where MhswPindahanID='$_REQUEST[pin]'
    order by mps.SetaraKode";
  $r = _query($s);
  
  $maxcol = 135;
  $nmf = HOME_FOLDER  .  DS . "tmp/p.$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(15).chr(27).chr(108).chr(20));
  $div = str_pad('-', $maxcol, '-').$_lf;
  
  $n = 0; $hal = 0;
  $brs = 56;
  $maxbrs = 49;
  
  $dt = GetFields('mhswpindahan', 'MhswPindahanID', $_REQUEST['pin'], "Nama");
  $prd = GetaField('prodi', 'ProdiID', $_REQUEST['prodi'], 'Nama');
    $hdr = str_pad('*** DAFTAR PENYETARAAN MATA KULIAH PINDAHAN **', $maxcol, ' ', STR_PAD_BOTH) . $_lf. $_lf. $_lf;
		$hdr .= "Tahun Akademik : " . NamaTahun($_REQUEST['tahun']) . $_lf;
		$hdr .= "Prodi          : $prd" . $_lf;
		$hdr .= "Mahasiswa      : $dt[Nama]" . $_lf;
		$hdr .= $div;
		$hdr .= str_pad("NO", 5) . 
            str_pad("KODE", 10) . 
            str_pad("MATA KULIAH", 35) . 
            str_pad('SKS', 6) . 
            str_pad('NILAI', 6) .
            str_pad('GRADE', 6) .
            str_pad('>>>', 5) .
            str_pad("KODE", 8) . 
            str_pad("MATA KULIAH", 40) . 
            str_pad('SKS', 6) . 
            str_pad('GRADE', 4) .
            $_lf;
		$hdr .= $div;
  fwrite($f, $hdr);
  while ($w = _fetch_array($r)) {
    $n++;
    
    fwrite($f, str_pad($n, 5).
      str_pad($w['SetaraKode'], 10).
      str_pad($w['SetaraNama'], 37).
      str_pad($w['SetaraSKS'], 5).
      str_pad($w['NilaiAkhir'], 7).
      str_pad($w['SetaraGrade'], 4).
      str_pad('>>>>', 5) .
      str_pad($w['MKKode'], 8) .
      str_pad($w['NamaMK'], 40) .
      str_pad($w['SKS'], 8) .
      str_pad($w['GradeNilai'], 4) .
      $_lf);  
  }
  fwrite($f, $div);
  fwrite($f, $_lf . "Dicetak Oleh : " . $_SESSION['_Login'] . ', ' . Date("d-m-Y H:i"));
  fwrite($f, chr(12));
  fclose($f);
  TampilkanFileDWOPRN($nmf);
}

function HeaderPindahan($tahun, $prodi, $div, $maxcol, &$hal){
    global $_lf;
		$hal++;
	  $hdr = str_pad('*** DAFTAR PENYETARAAN MATA KULIAH PINDAHAN **', $maxcol, ' ', STR_PAD_BOTH) . $_lf. $_lf. $_lf;
		$hdr .= "Tahun Akademik : " . NamaTahun($tahun) . $_lf;
		$hdr .= "Prodi          : $prodi" . str_pad('Halaman : ' . $hal, 42, ' ', STR_PAD_LEFT) . $_lf;
		$hdr .= $div;
		$hdr .= str_pad("NO", 6) . 
            str_pad("KODE", 8) . 
            str_pad("MATA KULIAH", 35) . 
            str_pad('SKS', 6) . 
            str_pad('NILAI', 6) .
            str_pad('GRADE', 4) .
            str_pad('>>>', 5) .
            str_pad("KODE", 8) . 
            str_pad("MATA KULIAH", 35) . 
            str_pad('SKS', 6) . 
            str_pad('NILAI', 6) .
            str_pad('GRADE', 4) .
            $_lf;
		$hdr .= $div;
		
		return $hdr;
}
?>
