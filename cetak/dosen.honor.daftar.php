<?php
// Author: Emanuel Setio Dewo
// 18 July 2006
// www.sisfokampus.net
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
  $mxb = 55;
  $mxc = 186;
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
  
  // buat file
  $nmf = HOME_FOLDER  .  DS . "tmp/$_SESSION[_Login].dwoprn";
  $f = fopen($nmf, 'w');
  fwrite($f, chr(27).chr(15));
  fwrite($f, chr(27).chr(108).chr(5));
  $spasihdr = str_pad(' ', 50);
  $TglCetak = date('d/m/Y H:i');
  $hdr = str_pad('** Daftar Honor Dosen **', $mxc, ' ', STR_PAD_BOTH).$g.
    str_pad("Periode  : $PeriodeMinggu, Bulan : $bulan, Tahun : $PeriodeTahun", $mxc/2).
      $spasihdr . "Tanggal : $TglCetak" . $g.
    str_pad("Semester : $_tahun", $mxc/2).
      $spasihdr . "Dicetak : $_SESSION[_Login]" . $g.
    str_pad("Prodi    : $_prodi", $mxc/2).
      $spasihdr . "Hal.    : =HAL=".$g.
    $grs.
    "No. Kode  Nama Dosen    Kode    Matakuliah               SKS Hdr       T.Jab1      T.Jab2".
                                           "       T.SKS   Transport".
                                           "     T.Paket    Tambahan".
                                           "    Potongan       Bruto".
                                           "       Pajak        Total".
    $g.$grs;
  
  // Data
  $s = "select hd.*, LEFT(d.Nama, 30) as DSN
    from honordosen hd
      left outer join dosen d on hd.DosenID=d.Login
      left outer join statusdosen sd on d.StatusDosenID=sd.StatusDosenID
    where hd.ProdiID='$prodi'
      and hd.TahunID='$tahun'
      and hd.Tahun='$PeriodeTahun'
      and hd.Bulan='$PeriodeBulan'
      and hd.Minggu='$PeriodeMinggu'
      and sd.HonorMengajar='Y' 
      and d.NA='N'
    group by d.Login
    order by d.Nama";
  $prd = ($_SESSION['prodi'] == '99')? "and INSTR(j.ProdiID, '.$_SESSION[prodi].')>0" : "and j.ProdiID='.$_SESSION[prodi].'";
  $s1 = "select hd.*, LEFT(d.Nama, 30) as DSN from  presensi prs 
      left outer join jadwal j on prs.JadwalID=j.JadwalID
      left outer join dosen d on prs.DosenID=d.Login
      left outer join statusdosen sd on d.StatusDosenID=sd.StatusDosenID
      left outer join prodi prd on d.Homebase=prd.ProdiID
      left outer join golongan gol on d.GolonganID=gol.GolonganID and d.KategoriID=gol.KategoriID and d.Homebase=gol.ProdiID
      left outer join ikatan ikt on d.IkatanID=ikt.IkatanID
      left outer join honordosen hd on d.Login=hd.DosenID and hd.prodiID='$prodi'
    where sd.HonorMengajar='Y' and d.NA='N'
      and hd.Tahun='$_SESSION[PeriodeTahun]' 
      and hd.Bulan='$_SESSION[PeriodeBulan]'
      and hd.Minggu='$_SESSION[PeriodeMinggu]'
      and prs.TahunID='$_SESSION[tahun]'
    $prd
    group by prs.DosenID";
  //echo "<pre>$s</pre>"; exit;
  $r = _query($s1); $_TOT = 0; $n = 0; $brs = 0; $h = 1;
  $_tj1 = 0; $_tj2 = 0; $_tsk = 0; $_ttr = 0; $_ttp = 0; $_tam = 0; $_tpo = 0; $_pjk = 0; $_bru = 0;
  fwrite($f, str_replace('=HAL=', $h, $hdr));
  while ($w = _fetch_array($r)) {
    $n++; $brs++;
    if ($brs >= $mxb) {
      $brs = 0;
      $h++;
      fwrite($f, str_pad("Oleh: $_SESSION[_Login]", $mxc/2).
        str_pad("Hal. $h", $mxc/2, ' ', STR_PAD_LEFT).$g);
      fwrite($f, chr(12));
      fwrite($f, str_replace('=HAL=', $h, $hdr));
    }
    $TOT = $w['TunjanganJabatan1'] + $w['TunjanganJabatan2'] +
      $w['TunjanganSKS'] + $w['TunjanganTransport'] +
      $w['TunjanganTetap'] + $w['Tambahan'] - $w['Potongan'];
    $TOT1 = $TOT - ($TOT * $w['Pajak']/100);
    $_bru += $TOT;
    $_TOT += $TOT1;
    $pjk = $TOT * $w['Pajak'] /100;
    $strpjk = number_format($pjk);
    $strTOT = number_format($TOT);
    $strTOT1 = number_format($TOT1);
    $tj1 = number_format($w['TunjanganJabatan1']);
    $tj2 = number_format($w['TunjanganJabatan2']);
    $tsk = number_format($w['TunjanganSKS']);
    $ttr = number_format($w['TunjanganTransport']);
    $ttp = number_format($w['TunjanganTetap']);
    $tam = number_format($w['Tambahan']);
    $tpo = number_format($w['Potongan']);

    $_tj1 += $w['TunjanganJabatan1'];
    $_tj2 += $w['TunjanganJabatan2'];
    $_tsk += $w['TunjanganSKS'];
    $_ttr += $w['TunjanganTransport'];
    $_ttp += $w['TunjanganTetap'];
    $_tam += $w['Tambahan'];
    $_tpo += $w['Potongan'];
    $_pjk += $pjk;
    fwrite($f, 
      str_pad($n, 4).
      str_pad($w['DosenID'], 6).
      str_pad($w['DSN'], 55).
      str_pad($tj1, 12, ' ', STR_PAD_LEFT).
      str_pad($tj2, 12, ' ', STR_PAD_LEFT).
      str_pad($tsk, 12, ' ', STR_PAD_LEFT).
      str_pad($ttr, 12, ' ', STR_PAD_LEFT).
      str_pad($ttp, 12, ' ', STR_PAD_LEFT).
      str_pad($tam, 12, ' ', STR_PAD_LEFT).
      str_pad($tpo, 12, ' ', STR_PAD_LEFT).
      str_pad($strTOT, 12, ' ', STR_PAD_LEFT).
      str_pad($strpjk, 12, ' ', STR_PAD_LEFT).
      str_pad($strTOT1, 12, ' ', STR_PAD_LEFT).
      $g);
    // Ambil detail matakuliah yg diampu
    $sj = "select p.*, j.MKKode, LEFT(j.Nama, 20) as Nama, j.NamaKelas, jj.Nama as JENJAD, j.SKSHonor, count(*) as JML
      from presensi p
        left outer join jadwal j on p.JadwalID=j.JadwalID
        left outer join jenisjadwal jj on j.JenisJadwalID=jj.JenisJadwalID
      where p.HonorDosenID='$w[HonorDosenID]'
      group by p.JadwalID";
    $rj = _query($sj); $nj = 0;
    while ($wj = _fetch_array($rj)) {
      $nj++; $brs++;
      if ($brs >= $mxb) {
        $brs = 0;
        $h++;
        fwrite($f, str_pad("Oleh: $_SESSION[_Login]", $mxc/2).
          str_pad("Hal. $h", $mxc/2, ' ', STR_PAD_LEFT).$g);
        fwrite($f, chr(12));
        fwrite($f, $hdr);
      }
      $tsks = number_format($wj['TunjanganSKS']);
      fwrite($f, '          '.
        str_pad($nj.'.', 4).
        str_pad($wj['JENJAD'], 10).
        str_pad($wj['MKKode'], 8).
        str_pad($wj['Nama'], 21) . 
        str_pad($wj['NamaKelas'], 5).
        str_pad($wj['SKSHonor'], 3, ' ', STR_PAD_LEFT). ' '.
        str_pad($wj['JML'], 3, ' ', STR_PAD_LEFT).
        $g);
    }
  }
  $tj1 = number_format($_tj1);
  $tj2 = number_format($_tj2);
  $tsk = number_format($_tsk);
  $ttr = number_format($_ttr);
  $ttp = number_format($_ttp);
  $tam = number_format($_tam);
  $tpo = number_format($_tpo);
  $pjk = number_format($_pjk);
  $TOT = number_format($_TOT);
  $bru = number_format($_bru);
  fwrite($f, $grs.
    str_pad("Total : ", 65, ' ', STR_PAD_LEFT).
      str_pad($tj1, 12, ' ', STR_PAD_LEFT).
      str_pad($tj2, 12, ' ', STR_PAD_LEFT).
      str_pad($tsk, 12, ' ', STR_PAD_LEFT).
      str_pad($ttr, 12, ' ', STR_PAD_LEFT).
      str_pad($ttp, 12, ' ', STR_PAD_LEFT).
      str_pad($tam, 12, ' ', STR_PAD_LEFT).
      str_pad($tpo, 12, ' ', STR_PAD_LEFT).
      str_pad($bru, 12, ' ', STR_PAD_LEFT).
      str_pad($pjk, 12, ' ', STR_PAD_LEFT).
      str_pad($TOT, 12, ' ', STR_PAD_LEFT).
      $g);
  for ($i=$brs+3; $i <= $mxb; $i++) fwrite($f, $g);
  fwrite($f, str_pad("Oleh: $_SESSION[_Login]", $mxc/2).
          str_pad("Hal. $h", $mxc/2, ' ', STR_PAD_LEFT).$g);
  fwrite($f, chr(12));
  // Tutup file
  fclose($f);
  include "dwoprn.php";
  DownloadDWOPRN($nmf);
}
?>
