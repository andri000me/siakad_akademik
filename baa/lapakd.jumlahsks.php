<?php
session_start();

include_once "../dwo.lib.php";
include_once "../db.mysql.php";
include_once "../connectdb.php";
include_once "../parameter.php";
include_once "../cekparam.php";
include_once "../header_pdf.php";

  function TampilkanFilterSKS(){
  global $arrID;
  $optprg = GetOption2("program", "concat(ProgramID, ' - ', Nama)", "ProgramID", $_SESSION['prid'], '', 'ProgramID');
  $optprd = GetOption2("prodi", "concat(ProdiID, ' - ', Nama)", "ProdiID", $_SESSION['prodi'], '', 'ProdiID');
  $optstst = GetOption2("statusmhsw", "concat(StatusMhswID, ' - ', Nama)", "StatusMhswID", $_SESSION['stts'], "StatusMhswID in ('A', 'P', 'C', 'T')", 'StatusMhswID');
    echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST onSubmit=\"return CheckForm(this)\">
  <input type=hidden name='mnux' value='akd.lap.jumlahsks'>
  <input type=hidden name='gos' value='daftar'>
  <tr><th class=ttl colspan=5>$arrID[Nama]</th></tr><tr>
    <td class=inp>Tahun Akademik : </td>
    <td class=ul><input type=text name='tahun' value='$_SESSION[tahun]' size=10 maxlength=50></td>
    <td class=inp>Jumlah SKS Minimal : </td>
    <td class=ul><input type=text name='jmlsks' value='$_SESSION[jmlsks]' size=10 maxlength=50></td>
    <td class=ul><input type=submit name='Cetak' value='Cetak'></td></tr>
    <td class=inp>Program</td><td class=ul><select name='prid' onChange='this.form.submit()'>$optprg</select></td>
    <td class=inp>Program Studi</td><td class=ul><select name='prodi' onChange='this.form.submit()'>$optprd</select></td></tr>
    <tr><td class=inp>Status Mahasiswa</td><td class=ul><select name='stts' onchange='this.form.submit()'>$optstst</select></td>
        <td class=inp>Angkatan : </td><td class=ul><input type=text name='angkatan' value='$_SESSION[angkatan]' size=10 maxlength=4></td></tr>
  </form></table></p>";
  }
  
  function daftar(){
  global $_lf;
  $whr = array();
  if (!empty($_SESSION['prodi'])) $whr[] = "mhsw.ProdiID='$_SESSION[prodi]'";
  if (!empty($_SESSION['prid'])) $whr[] = "mhsw.ProgramID='$_SESSION[prid]'";
  if (!empty($_SESSION['angkatan'])) $whr[] = "mhsw.TahunID='$_SESSION[angkatan]'";
  if (!empty($_SESSION['stts'])) $whr[] = "k.StatusMhswID='$_SESSION[stts]'"; else $whr[] = "k.StatusMhswID in ('A', 'P', 'C', 'T')";
   $_whr = implode(" and ", $whr);
  if (!empty($_whr)) $_whr = " and ". $_whr;
    $s = "select mhsw.MhswID, mhsw.Nama, k.IPS, mhsw.TotalSKS, mhsw.ProdiID, k.StatusMhswID from khs k 
          left outer join mhsw on k.MhswID = mhsw.MhswID
          where mhsw.TotalSKS >= $_SESSION[jmlsks] 
          and k.TahunID = '$_SESSION[tahun]' $_whr order by mhsw.ProdiID, mhsw.MhswID";
    
    $r = _query($s);
    
  $maxcol = 80;
  $nmf = "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(15).chr(27).chr(108).chr(20));
  $div = str_pad('-', $maxcol, '-').$_lf;
  
  $n = 0; $hal = 0;
  $brs = 56;
  $maxbrs = 49;
	
	$jumlahrec = _num_rows($r);
  $jumhal = ceil($jumlahrec/$maxbrs);
  $prodi = "";
	$first = 1;
  // Buat header
  /*$_prodi = GetaField('prodi', 'ProdiID', $_SESSION['prodi'], 'Nama');
  $_prid = GetaField('program', 'ProgramID', $_SESSION['prid'], 'Nama');
  $hdr = str_pad("*** Daftar Mahasiswa SKS Lebih Atau Sama Dengan $_SESSION[jmlsks] ***", $MaxCol, ' ', STR_PAD_BOTH) . $_lf.$_lf;
  $hdr .= "Tahun    : " . NamaTahun($_SESSION['tahun']) . $_lf; 
  $hdr .= "Program  : $_prid" . $_lf;
  $hdr .= "Prodi    : $_prodi" .$_lf;
  $hdr .= $div;
  $hdr .= "No.  NPM          Nama                         Total SKS".$_lf . $div;
  
  fwrite($f, $hdr);
  $jumlahrec = _num_rows($r);
  $jumhal = ceil($jumlahrec/$maxbrs);*/
  while ($w = _fetch_array($r)) {
  
		$_prodi = GetaField('prodi', 'ProdiID', $w['ProdiID'], 'Nama');
		
		
		if ($brs > $maxbrs) {
			if ($first == 0) {
				fwrite($f, $div.chr(12));
			}
			$hd = Headerxx($_SESSION['tahun'], $_prodi, $div, $maxcol, $hal);
			fwrite($f, $hd);
			$brs = 0;
			$first = 0;
			$prodi = $w['ProdiID'];
		} 		
		elseif ($prodi != $w['ProdiID']) {
        $prodi = $w['ProdiID'];
				if ($first == 0){
					fwrite($f, $div);
				}
				fwrite($f, chr(12));
				fwrite($f, Headerxx($_SESSION['tahun'], $_prodi, $div, $maxcol, $hal));
				$brs=0;
				$n=1;
      } 
    
    if ($w['ProdiID'] == 10) {
      $w['TotalSKS'] = SKSFK($w['MhswID']);
    } 
    if (!empty($w['TotalSKS'])) {
      $n++; $brs++;
      $Status = GetaField('statusmhsw', "StatusMhswID", $w['StatusMhswID'], 'Nama');
      $isi = str_pad($n.'.', 4, ' ') . ' ' .
        str_pad($w['MhswID'], 12) . ' '.
        str_pad($w['Nama'], 30) . ' '.
        str_pad($w['TotalSKS'], 13, ' ', STR_PAD_LEFT).' '.
        str_pad($Status, 13, ' ', STR_PAD_LEFT);
        fwrite($f, $isi.$_lf);
    }
  }
  fwrite($f, $div);
  fwrite($f, str_pad("Hal. : ".$hal.'/'.$jumhal, $maxcol, ' ', STR_PAD_LEFT).$_lf);
  fwrite($f, str_pad('Dicetak oleh : '.$_SESSION['_Login'],55,' ').str_pad('Dibuat : '.date("d-m-Y H:i"),29,' '));
  fwrite($f, chr(12));
  fclose($f);
  TampilkanFileDWOPRN($nmf, "akd.lap");
}  

function Headerxx($tahun, $prodi, $div, $maxcol, &$hal){
    global $_lf;
		$hal++;
	  $hdr = str_pad('*** DAFTAR JUMLAH SKS MAHASISWA **', $maxcol, ' ', STR_PAD_BOTH) . $_lf. $_lf. $_lf;
		$hdr .= "Tahun Akademik : " . NamaTahun($tahun) . $_lf;
		$hdr .= "Prodi          : $prodi" . str_pad('Halaman : ' . $hal, 42, ' ', STR_PAD_LEFT) . $_lf;
		$hdr .= "Minimal SKS    : $_SESSION[jmlsks]" . $_lf;
		$hdr .= $div;
		$hdr .= str_pad("NO", 6) . 
            str_pad("NIM", 12) . 
            str_pad("NAMA", 35) . 
            str_pad('TOTAL SKS', 17) . 
            str_pad('STATUS', 13) .
            $_lf;
		$hdr .= $div;
		
		return $hdr;
}

function SKSFK($mhswid){
  $s = "select krs.SKS
    from krsprc krs
      left outer join mk mk on krs.MKID=mk.MKID
      left outer join nilai n on krs.GradeNilai=n.Nama
    where krs.MhswID='$mhswid' 
      and krs.GradeNilai not in ('-', '')
      and n.Lulus = 'Y'
    group by krs.MKKode";
	
	$r = _query($s);
	
	while ($w = _fetch_array($r)){
	 $krs += $w['SKS'];
	}
	//var_dump($krs);
	if ($krs >= $_SESSION['jmlsks']) return $krs;
}

$tahun = GetSetVar('tahun');
$prodi = GetSetVar('prodi');
$prid = GetSetVar('prid', 'REG');
$jmlsks = GetSetVar('jmlsks', 140);
$stts = GetSetVar('stts');
$angkatan = GetSetVar('angkatan');
//echo $stts;
TampilkanJudul("Daftar Jumlah SKS Mahasiswa");
//TampilkanTahunProdiProgram('akd.lap.krsmhswbolos', 'Daftar');
TampilkanFilterSKS();
if (!empty($tahun)) Daftar(); 
  
?>
