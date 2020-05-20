<?php
// Author: Emanuel Setio Dewo
// 20 September 2006
// http://www.sisfokampus.net

session_start();
include "../sisfokampus.php";
include "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";
include_once "parameter.php";
Cetak();
include_once "disconnectdb.php";

function Cetak() {
  global $arrBulan;
  $mxb = 15;
  $mxc = 112;
  $g = chr(13).chr(10);
  $grs = str_pad('-', $mxc, '-').$g;
    // parameter
  $tahun = $_SESSION['tahun'];
  $_tahun = NamaTahun($tahun);
  $DosenID = $_SESSION['DosenID'];
  $prodi = $_SESSION['prodi'];
  $_prodi = GetaField('prodi', 'ProdiID', $prodi, 'Nama');
  $PeriodeMinggu = $_SESSION['PeriodeMinggu'];
  $PeriodeBulan = $_SESSION['PeriodeBulan'];
  $PeriodeTahun = $_SESSION['PeriodeTahun'];
  $bulan = $arrBulan[$PeriodeBulan+0];
  // bank
  $arrBank = array("and d.NamaBank='INA PERDANA' ", "and d.NamaBank <> 'INA PERDANA' and d.NamaBank<>'' ");
  $arrNamaBank = array("INA PERDANA", "Lain2");
  
  // buat file
  $nmf = HOME_FOLDER  .  DS . "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27). chr(15));
  fwrite($f, chr(27). chr(108). chr(5));
  
  $SpasiHdr = str_pad(' ', 25);
  $tgl = date('d-m-Y H:i');
  $hdr1 = str_pad("** Daftar Rekap Honor Dosen per Bank ***", $mxc, ' ', STR_PAD_BOTH).$g.
    str_pad("Bulan: $bulan $PeriodeTahun", $mxc, ' ', STR_PAD_BOTH).$g.
    str_pad("Semester : $_tahun", $mxc/2). $SpasiHdr. 
      "Tanggal : $tgl" .$g.
    str_pad("Prodi    : $_prodi", $mxc/2). $SpasiHdr.
      "Dicetak : $_SESSION[_Login]" . $g;
  $hdr2 = $grs.
    "No. ".
    str_pad("Dosen", 35). str_pad("No.Rekening", 24). str_pad("Bank & Atas Nama", 24).
    str_pad("Jumlah", 15, ' ', STR_PAD_LEFT).
    $g.$grs;
    
//**** Cetakan berlangsung 2x *** //
for ($i=0; $i < 2; $i++) {
  // Data
  $_bank = $arrBank[$i];
  $s = "select hd.*, 
    LEFT(concat(d.Nama, ', ', d.Gelar), 34) as DSN, d.NamaBank, d.NamaAkun, d.NomerAkun,
    d.GolonganID, d.JabatanID, jb.Nama as JAB
    from honordosen hd
      left outer join dosen d on hd.DosenID=d.Login
      left outer join statusdosen sd on d.StatusDosenID=sd.StatusDosenID
      left outer join jabatan jb on d.JabatanID=jb.JabatanID
    where hd.TahunID='$tahun' $_bank
      and hd.Tahun='$PeriodeTahun'
      and hd.Bulan='$PeriodeBulan'
      and hd.Minggu='$PeriodeMinggu'
      and hd.ProdiID='$prodi'
      and sd.HonorMengajar='Y'
      and (hd.TunjanganJabatan1 +hd.TunjanganJabatan2 +hd.TunjanganSKS +hd.TunjanganTransport +hd.TunjanganTetap
        +hd.Tambahan -hd.Potongan) >0
    order by d.NamaBank, d.Nama";
  //echo "<pre>$s</p>";
  $r = _query($s); $n = 0; $_TOT = 0; $brs = 0; $h = 1;
  fwrite($f, str_replace('=HAL=', $h, $hdr1));
  fwrite($f, str_pad("Bank     : " . $arrNamaBank[$i], $mxc/2). $SpasiHdr. 
    "Halaman : $h". $g);
  fwrite($f, $hdr2);
  while ($w = _fetch_array($r)) {
    $brs++; $n++;
    if ($brs >= $mxb) {
      $brs = 1;
      // Tuliskan Total
      //$_totals = number_format($_TOT);
      //fwrite($f, $grs);
      //fwrite($f, str_pad("Total: ", 82, ' ', STR_PAD_LEFT).
      //str_pad($_totals, 20, ' ', STR_PAD_LEFT). $g);
      fwrite($f, str_pad("Bersambung...", $mxc, ' ', STR_PAD_LEFT));
      fwrite($f, chr(12));
      // Header
      fwrite($f, str_replace('=HAL=', $h, $hdr1));
      fwrite($f, "Bank     : " . $arrNamaBank[$i]. $g);
      fwrite($f, $hdr2);        
    }
    
    $TOT = $w['TunjanganJabatan1'] + $w['TunjanganJabatan2'] +
      $w['TunjanganSKS'] + $w['TunjanganTransport'] +
      $w['TunjanganTetap'] + $w['Tambahan'] - $w['Potongan'];
    $TOT1 = $TOT - ($TOT * $w['Pajak']/100);
    $_TOT += $TOT1;
    $_total = number_format($TOT1);
    fwrite($f, str_pad($n, 4, ' ').
      str_pad($w['DSN'], 35).
      str_pad($w['NomerAkun'], 24).
      str_pad($w['NamaAkun'], 24).
      str_pad($_total, 15, ' ', STR_PAD_LEFT).
      $g.
      str_pad(' ', 4, ' ').
      str_pad($w['DosenID']. ' '. $w['GolonganID'] . '.' . $w['JAB'], 35).
      $w['NamaBank'].
      $g.$g
      );
  }
  // Tuliskan Total
  $_totals = number_format($_TOT);
  fwrite($f, $grs);
  fwrite($f, str_pad("Total: ", 82, ' ', STR_PAD_LEFT).
    str_pad($_totals, 20, ' ', STR_PAD_LEFT). $g);
  fwrite($f, chr(12));
} // END Loop
  fclose($f);
  // Tampilkan
  include "dwoprn.php";
  DownloadDWOPRN($nmf);
}
?>
