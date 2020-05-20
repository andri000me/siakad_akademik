 <?php
// Author: Emanuel Setio Dewo
// 28 November 2006
// www.sisfokampus.net
// email: setio.dewo@gmail.com

session_start();
include "../sisfokampus.php";
include "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";
include_once "parameter.php";
CetakDaftarPSSB();
include_once "disconnectdb.php";

function CetakDaftarPSSB() {
  global $_lf, $arrID;
  // buat file
  $nmf = HOME_FOLDER  .  DS . "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(15));
  $mxb = 50;
  $mxc = 130;
  
  $grs = str_pad('-', $mxc, '-').$_lf;
  $hdr = str_pad("** Hasil Seleksi PSSB Tahun $_SESSION[tahunpssb] **", $mxc, ' ', STR_PAD_BOTH).$_lf.$_lf.
    $grs.
    str_pad("No. ", 5).
    //str_pad("PMBID", 14).
    str_pad("PSSBID", 14).
    str_pad("Nama Siswa", 40).
    str_pad("Nama Sekolah", 40).
    str_pad("Kota Sekolah", 40).
    //str_pad("Rabat", 10).
    $_lf.$grs;
  // footer
  fwrite($f, $lf);
  $spasi = str_pad(' ', 80, ' ');
  $rektor = GetaField('pejabat', "KodeID='$_SESSION[KodeID]' and JabatanID", "REKTOR", "Nama");
  $tgl = date('d-m-Y');
  $ftr = $spasi . "Jakarta, $tgl" . $_lf.
    $spasi . "Rektor" . $_lf.$_lf.$_lf.
    $spasi . $rektor;
  // Tuliskan data
  //fwrite($f, $hdr);
  // Ambil data
  $s = "select LEFT(p.Nama, 40) as Nama, p.Diskon, p.ProdiID, p.PSSBID, 
    prd.Nama as PRD, f.Nama as FAK, prd.FakultasID,
    LEFT(sek.Nama, 40) as SKL, LEFT(sek.Kota, 40) as Kota
    from pssb p
      left outer join asalsekolah sek on p.AsalSekolah=sek.SekolahID
      left outer join prodi prd on p.ProdiID=prd.ProdiID
      left outer join fakultas f on prd.FakultasID=f.FakultasID
    where p.TahunID='$_SESSION[tahunpssb]'
    order by p.ProdiID, p.Nama";
  $r = _query($s);
  $n = 0; $prd = 'qwertyuiop'; $fak = 'qwertyuiop'; $_fak = $fak;
  $brs = 0; $hal = 0;
  $tot = 0;
  while ($w = _fetch_array($r)) {
    $tot++;
    if ($fak != $w['FakultasID']) {
      if ($fak != $_fak) {
        fwrite($f, $grs);
        fwrite($f, $ftr . $_lf);
        fwrite($f, chr(12));
      }
      $fak = $w['FakultasID'];
      fwrite($f, str_pad("** Fakultas $w[FAK] **", $mxc, ' ', STR_PAD_BOTH).$_lf);
      fwrite($f, $hdr);
      $brs = 0;
      $n = 0;
      $hal++;
    }
    if ($prd != $w['ProdiID']) {
      $prd = $w['ProdiID'];
      fwrite($f, $_lf. "   > " . $w['PRD'] . $_lf. $grs);
      $brs += 2;
    }
    if ($brs >= $mxb) {
      $brs = 0;
      fwrite($f, $grs);
      fwrite($f, chr(12));
      fwrite($f, $hdr);
    }
    $brs++; $n++;
    fwrite($f, str_pad($n, 5).
      str_pad($w['PSSBID'], 14).
      str_pad($w['Nama'], 40).
      str_pad($w['SKL'], 40).
      str_pad($w['Kota'], 40).
      //str_pad($w['Diskon']."%", 5, ' ', STR_PAD_LEFT).
      $_lf);
  }
  fwrite($f, $grs);
  fwrite($f, "Total PSSB : $tot" . $_lf);
  fwrite($f, $ftr . $_lf);
  fwrite($f, chr(12));
  fclose($f);
  // Tampilkan
  if (empty($_REQUEST['prn'])) {
    TampilkanFileDWOPRN($nmf, '');
  }
  else {
    include_once "dwoprn.php";
    DownloadDWOPRN($nmf);
  }
}
?>
