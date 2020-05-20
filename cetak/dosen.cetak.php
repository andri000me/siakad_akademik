<?php
include "../sisfokampus.php";
include "dosen.hdr.php";

function daftar(){
  global $_lf;
  $whr = array();
  if (!empty($_SESSION['dsnkeycr']) && !empty($_SESSION['dsncr'])) {
    $whr[] = "$_SESSION[dsnkeycr] like '%$_SESSION[dsncr]%'";
  }
  $where = implode(' and ', $whr);
  $where = (empty($where))? '' : "and $where";
  $hom = (empty($_SESSION['prodi'])) ? '' : "and Homebase = '$_SESSION[prodi]'";
    
  $s = "select * from dosen
    where KodeID='$_SESSION[KodeID]' $where $hom
    order by $_SESSION[dsnurt] ";
  $r = _query($s);
  
  $Nhom = GetaField("prodi","ProdiID",$_SESSION['prodi'],'Nama');
  $nmf = HOME_FOLDER  .  DS . "tmp/$_SESSION[_Login].dwoprn";
  $maxcol = 155;
	$f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(15).chr(77).chr(27).chr(108).chr(5));
  $div = str_pad('-', $maxcol, '-').$_lf;
  
  // parameter2
  $n = 0; $hal = 1; 
  $brs = 0;
  $maxbrs = 52;
  $_Tgl = date("d-m-Y"); 
  $hdr = str_pad("** LAPORAN DAFTAR DOSEN **",$maxcol, ' ',STR_PAD_BOTH).$_lf.$_lf.$_lf;
  $hdr .= str_pad("Urut Berdasarkan : ".$_SESSION['dsnurt'],30,' ').$_lf;
  $hdr .= str_pad("Homebase         : ".$_SESSION['prodi'] . " - ". $Nhom ,30,' ').$_lf;
  $hdr .= $div;
  $hdr .= "No.   KODE    NAMA                                GELAR              HOMEBASE   TELEPON      ALAMAT".$_lf.$div;
  $jumlahrec = _num_rows($r);
  $jumhal = ceil($jumlahrec/$maxbrs);
  fwrite($f, $hdr);
  while ($w = _fetch_array($r)){
    $n++;$brs++;
    if($brs > $maxbrs){
			  fwrite($f,$div);
				fwrite($f,str_pad('Halaman : '.$hal."/".$jumhal,10,' ').$_lf);
				$hal++; $brs = 1;
				fwrite($f, chr(12));
				fwrite($f, $hdr.$_lf);
		}
		$isi = str_pad($n.".",5,' ').' '.
		       str_pad($w['Login'],8,' ').
		       str_pad($w['Nama'],35,' ').' '.
		       str_pad($w['Gelar'],22, ' ').
		       str_pad($w['Homebase'],6,' ').
		       str_pad($w['Telephone'],15,' ').
		       str_pad($w['Alamat'],50,' ')
           .$_lf;
    fwrite($f, $isi);       		
  }
  fwrite($f, $div);
  fwrite($f, str_pad('Halaman : '.$hal."/".$jumhal,10,' ').$_lf);
	fwrite($f, str_pad("Dicetak oleh : " . $_SESSION['_Login'], 20, ' ') . str_pad("Dicetak Tgl : " . $_Tgl, 130,' ', STR_PAD_LEFT).$_lf.$_lf); 
  fwrite($f, str_pad("Akhir laporan", 149, ' ', STR_PAD_LEFT));
	fwrite($f, chr(12));
	fclose($f);
  TampilkanFileDWOPRN($nmf, "dosen");
}

$dsnsub = GetSetVar('dsnsub');
$dsnurt = GetSetVar('dsnurt', 'Login');
$dsnid = GetSetVar('dsnid');
$dsncr = GetSetVar('dsncr');
$dsnkeycr = GetSetVar('dsnkeycr');

Daftar();

?>
               
