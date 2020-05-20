<?php
// Author: Emanuel Setio Dewo
// 21 April 2006
session_start();
include "../sisfokampus.php";
include "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";
include_once "parameter.php";
Cetak();
include_once "disconnectdb.php";

function Cetak() {
  global $_lf, $arrBulan;
  $Hondos = array();
  $Hondos = $_REQUEST['Hondos'];
  if (!empty($Hondos)) {
    $hdid = implode(',', $Hondos);
    $s = "select hd.*, concat(d.Nama, ', ', d.Gelar) as DSN, sd.Nama as StatusDSN,
        prd.Nama as PRD, d.NamaBank, d.NomerAkun
      from honordosen hd
        left outer join dosen d on hd.DosenID=d.Login
        left outer join statusdosen sd on d.StatusDosenID=sd.StatusDosenID
        left outer join prodi prd on hd.ProdiID=prd.ProdiID
      where hd.HonorDosenID in ($hdid)";
    $r = _query($s);
    // Buat file
    $nmf = HOME_FOLDER  .  DS . "tmp/$_SESSION[_Login].dwoprn";
    $f = fopen($nmf, 'w');
    // Tuliskan isinya
    while ($w = _fetch_array($r)) {
      $bln = $arrBulan[$w['Bulan']];
      fwrite($f, chr(27).chr(18).chr(27).chr(67).chr(66).
        $_lf.$_lf);
      fwrite($f, "Nama          : ". $w['DSN'] . $_lf);
      fwrite($f, "Status        : ". $w['StatusDSN'].$_lf);
      fwrite($f, "Program Studi : ". $w['PRD'].$_lf);
      fwrite($f, "Bank          : ". $w['NamaBank'] . ', '.$w['NomerAkun'].$_lf);
      fwrite($f, "Periode       : ". $bln. ' ' . $w['Tahun'].$_lf.$_lf);
      // Tunjangan
      $mrg = "   - ";
      fwrite($f, "TUNJANGAN".$_lf);
      fwrite($f, $mrg.str_pad("Jabatan #1", 20) . " : Rp. ". str_pad(number_format($w['TunjanganJabatan1']), 12, ' ', STR_PAD_LEFT).$_lf);   
      fwrite($f, $mrg.str_pad("Jabatan #2", 20) . " : Rp. ". str_pad(number_format($w['TunjanganJabatan2']), 12, ' ', STR_PAD_LEFT).$_lf);
      fwrite($f, $mrg.str_pad("SKS", 20) . " : Rp. ". str_pad(number_format($w['TunjanganSKS']), 12, ' ', STR_PAD_LEFT).$_lf);
      fwrite($f, $mrg.str_pad("Transport", 20) . " : Rp. ". str_pad(number_format($w['TunjanganTransport']), 12, ' ', STR_PAD_LEFT).$_lf);
      fwrite($f, $mrg.str_pad("Paket", 20) . " : Rp. ". str_pad(number_format($w['TunjanganTetap']), 12, ' ', STR_PAD_LEFT).$_lf);
      // Tuliskan total tunjangan
      $tun = $w['TunjanganJabatan1']+$w['TunjanganJabatan2']+$w['TunjanganSKS']+$w['TunjanganTransport']+$w['TunjanganTetap'];
      fwrite($f, str_pad(' ', 27) . "-----------------".$_lf);
      fwrite($f, str_pad("Total : ", 25, ' ', STR_PAD_LEFT). " : Rp. ". str_pad(number_format($tun), 12, ' ', STR_PAD_LEFT).$_lf);
      // Ambil tambahan
      $tambahan = TambahanHD($w, '>');
      if (!empty($tambahan)) {
        fwrite($f, "TAMBAHAN".$_lf);
        fwrite($f, $tambahan.$_lf);
      }
      // Ambil potongan
      $potongan = TambahanHD($w, '<');
      if (!empty($potongan)) {
        fwrite($f, "POTONGAN".$_lf);
        fwrite($f, $potongan.$_lf);
      }
      $tun = $w['TunjanganJabatan1']+$w['TunjanganJabatan2']+
        $w['TunjanganSKS']+$w['TunjanganTransport']+
        $w['TunjanganTetap'];
      $bruto = $tun + $w['Tambahan'] + $w['Potongan'];
      $pajak = $bruto * $w['Pajak']/100;
      $bersih = $bruto - $pajak;
      
      fwrite($f, str_pad(' ', 27) . "-----------------".$_lf);
      fwrite($f, str_pad("BRUTO ", 25) . " : Rp. " . str_pad(number_format($bruto), 12, ' ', STR_PAD_LEF).$_lf);
      fwrite($f, str_pad("Pajak ", 25) . " : Rp. " . str_pad(number_format($pajak), 12, ' ', STR_PAD_LEFT).$_lf);
      fwrite($f, str_pad(' ', 27) . "=================".$_lf);
      fwrite($f, str_pad("Gaji diterima ", 25) . " : Rp. " . str_pad(number_format($bersih), 12, ' ', STR_PAD_LEFT).$_lf);
      fwrite($f, str_pad(' ', 27) . "=================".$_lf.$_lf);
      // tampilkan daftar presensi
      $s1 = "select p.*, j.MKKode, j.Nama
        from presensi p
          left outer join jadwal j on p.JadwalID=j.JadwalID
        where p.HonorDosenID=$w[HonorDosenID]
        order by p.Tanggal";
      $r1 = _query($s1); $nomer = 0;
      fwrite($f, "Daftar Kehadiran Mengajar:".$_lf);
      while ($w1 = _fetch_array($r1)) {
        $nomer++;
        fwrite($f, str_pad($nomer, 3) . FormatTanggal($w1['Tanggal']) . "  $w1[MKKode]  $w1[Nama]".$_lf);
      } 
      // Selesai
      fwrite($f, chr(12));
      // Tambahkan counter cetak
      $scetak = "update honordosen set Cetak=Cetak+1 where HonorDosenID=$w[HonorDosenID]";
      $rcetak = _query($scetak);
    }
    
    // Tutup file
    fclose($f);
    TampilkanFileDWOPRN($nmf);      
  }
}
function TambahanHD($hd, $tnd) {
  global $_lf;
  $s = "select hdt.*
    from honordosentambahan hdt
    where HonorDosenID='$hd[HonorDosenID]' and Besar $tnd 0
    order by hdt.Nama";
  $r = _query($s);
  $a = ''; $mrg = "   - ";
  while ($w = _fetch_array($r)) {
    $a .= $mrg . str_pad($w['Nama'], 20) . " : Rp. " . 
      str_pad(number_format($w['Besar']), 12, ' ', STR_PAD_LEFT).$_lf;
  }
  return $a;
}
?>
