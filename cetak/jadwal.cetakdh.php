<?php
// Author: Emanuel Setio Dewo
// 17 April 2006
// Selamat Ulang Tahun Ibu

session_start();
// *** Buat File ***
include "../sisfokampus.php";
include "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";
include_once "parameter.php";
Cetak();
include_once "disconnectdb.php";

function Cetak() {
  global $_lf, $arrUjian;
  // Parameters
  $ctk = $_REQUEST['ctk']+0;
  $jdwlid = $_REQUEST['JadwalID'];
  $jdwl = GetFields("jadwal", 'JadwalID', $jdwlid, "*");
  $pserial = GetaField("jadwal", "MKKode = '$jdwl[MKKode]' and TahunID='$jdwl[TahunID]' and NamaKelas='$jdwl[NamaKelas]' and JenisJadwalID='$jdwl[JenisJadwalID]' and JadwalSer", $jdwlid, "JadwalID")+0;
  if (empty($jdwl)) die("Data tidak ditemukan.");
  // Buat File
  $nmf = HOME_FOLDER  .  DS . "tmp/$jdwlid.dwoprn";
  $f = fopen($nmf, 'w');
  $hal = 0;
  $n = 0; $brs = 0;
  $maxbaris = 20;
	$dsntambah = (!empty($_REQUEST['DSN'])) ? GetaField('dosen', 'Login', $_REQUEST['DSN'], "concat(Nama, ' ', Gelar)") : '';
  //echo $dsntambah;
	$serial = ($jdwl['JadwalSer'] == 0)? $jdwlid : $jdwl['JadwalSer'];  
  // Daftar mhsw
  //GetArrayTable($sql, $key, $label, $separator=', ', $diapit=''
  //$statusmhswaktif = GetArrayTable("select StatusMhswID from statusmhsw where Nilai=1 order by StatusMhswID",
  //  "StatusMhswID", "StatusMhswID", ',', "'");
  // Hitung Jumlah Jadwal-ID Serialnya :
  $mTahunID = $jdwl[TahunID];
  $mMKKode = $jdwl[MKKode];
  $mNamaKelas = $jdwl[NamaKelas];
  $mJenisJadwalID = $jdwl[JenisJadwalID];
  $DaftarJadwalID=_query("select jadwalID from jadwal 
                            where TahunID='$mTahunID' and 
                                  MKKode='$mMKKode' and
                                  NamaKelas='$mNamaKelas' and
                                  JenisJadwalID='$mJenisJadwalID' order by JadwalID");  
  $ID = "(";
  while($rID=_fetch_array($DaftarJadwalID)) {
    if($ID=="(") $ID = $ID.$rID[JadwalID];
	else $ID = $ID.",".$rID[JadwalID];
  }
  $ID = $ID.")";
  //$DaftarTatapMuka=_query("select count(*) as TatapMuka from presensi where JadwalID in $ID");
  $pres = GetaField("presensi", "JadwalID", $jdwlid, "count(*)");
  if ($pserial != 0) $presserial = GetaField("presensi", "JadwalID", $pserial, "count(*)"); 
  $prestot = $pres + $presserial;
  //$RecordTatapMuka = _fetch_array($DaftarTatapMuka);
  $tatapmuka = $prestot;
  //echo $pserial;
  //$tatapmuka = GetaField("presensi", "JadwalSer", $jdwlid, "count()")
  $s = "select m.MhswID, m.Nama, krs.KRSID, 
    khs.Biaya, khs.Potongan, khs.Bayar, khs.Tarik
    from krs krs 
      left outer join mhsw m on krs.MhswID=m.MhswID
      left outer join khs khs on krs.KHSID=khs.KHSID
    where krs.JadwalID='$serial' and khs.Cetak='Y'
      and krs.StatusKRSID='A'
      and m.StatusMhswID in ('A')
    order by m.mhswid";
  $r = _query($s);
  $mrgisi = '         ';
  $jumrec = _num_rows($r);
  $jumhal = ceil($jumrec/$maxbaris);
  $ctk = $_REQUEST['ctk']+0;
  $nmujian = $arrUjian[$ctk];
  // Kuliah atau Ujian?
  if ($ctk == 0) {
    $RG = $jdwl['RuangID'];
    $JumlahRG = 1;
    $Batas = 1000;
  }
  else {
    $_RG = $jdwl[$nmujian."RuangID"];
    $RG = explode(',', $_RG);
    $JumlahRG = sizeof($RG);
    $_ruang1 = explode(':', $RG[0]);
    $Batas = (empty($_ruang1[1]))? 1000 : $_ruang1[1];
    //echo $Batas;
  }
  $RGKE = 0; $RG_ = 0;
	//echo $DSNTAM;
  $hdr = BuatHeader($jdwl, $ctk, $hal, $jumhal, $RG[$RGKE], $dsntambah);
  fwrite($f, $hdr);
  while ($w = _fetch_array($r)) {
    $RG_++;
    if ($RG_ > $Batas) {
      $RGKE++;
      $RG_ = 1;
      $_ruang1 = explode(':', $RG[0]);
      $Batas = (empty($_ruang1[1]))? 1000 : $_ruang1[1];
     // echo $Batas;
      $brs = $maxbaris +2;
      $n = 0;
    }
    if ($brs >= $maxbaris) {
      fwrite($f, chr(12));
      fwrite($f, BuatHeader($jdwl, $ctk, $hal, $jumhal, $RG[$RGKE], $dsntambah));
      $brs = 0;
    }
    $item = '';
    if ($ctk == 0) {
      $brs++; $n++;
      $item = $mrgisi . str_pad($n.'. ', 5) . str_pad($w['MhswID'], 12). $w['Nama'];
    }
    // Jika UTS/UAS
    else {
      $balance = -$w['Biaya'] -$w['Tarik'] +$w['Potongan'] +$w['Bayar'];
      if ($balance < 0) $NamaMhsw = "Anda masih memiliki hutang.";
      else $NamaMhsw = $w['Nama'];
      
      // jika UTS
      if ($ctk == 1) {
        $brs++; $n++;
        $item = $mrgisi . str_pad($n.'. ', 5) . str_pad($w['MhswID'], 12). str_pad($w['Nama'], 50);
      }
      elseif ($ctk == 2) {
//        $hadir = GetaField('presensimhsw', "KRSID", $w['KRSID'], "sum(Nilai)")+0;
		$qpres = _query("select sum(Nilai) as jmlhadir from presensimhsw where jadwalid = '$jdwlid' and mhswid='$w[MhswID]' group by mhswid");
		$dpres = _fetch_array($qpres);
		$hadir = $dpres[jmlhadir];
        $Hak = $_REQUEST['hak']+0;
        $brs2 = 0;
//        if ($jdwl['Kehadiran'] == 0) $jdwl['Kehadiran'] = 1;
        if ($Hak == 1){
          if (($tatapmuka > 0) && ($hadir/$tatapmuka > $jdwl['KehadiranMin']/100)) {}
          elseif (($hadir/$tatapmuka < $jdwl['KehadiranMin']/100)) {
            $brs++; $n++;
            $item = $mrgisi . str_pad($n.'. ', 5) . str_pad($w['MhswID'], 12). str_pad($w['Nama'], 30) . str_pad(number_format($hadir/$tatapmuka*100,0).'%',10,' ', str_pad_left);
          }        
        }
        else {
          if (($tatapmuka > 0) && ($hadir/$tatapmuka < $jdwl['KehadiranMin']/100)) {}
          elseif (($hadir/$tatapmuka >= $jdwl['KehadiranMin']/100)) {
            $brs++; $n++;
            $item = $mrgisi . str_pad($n.'. ', 5) . str_pad($w['MhswID'], 12). str_pad($w['Nama'], 30) . str_pad(number_format($hadir/$tatapmuka*100,0).'%',10,' ', str_pad_left);
          }
        }
      }
      if ($brs == 2) $item2 = "                   Jumlah Tatap Muka : " . $pres . 'x ';
      else $item2 = '';
    }
    //fwrite($f, $mrgisi . str_pad($n.'. ', 5) . $w['MhswID']. '   ' . str_pad($NamaMhsw, 50));
    if (!empty($item)) fwrite($f, $item . $item2 . $_lf.$_lf);
  }
  for ($i=$brs; $i<$maxbaris; $i++)
    fwrite($f, $mrgisi . str_pad(' ', 5) . "** Tidak diperkenankan menambah pada baris ini **" . $_lf.$_lf);
  fwrite($f, $_lf . "Dicetak Oleh : " . $_SESSION['_Login'] . ', ' . Date("d-m-Y H:i"));
  fwrite($f, chr(12));
  fclose($f);
  TampilkanFileDWOPRN($nmf);
}
function BuatHeader($jdwl, $ctk, &$hal, $jumhal, $rg='', $dosentambah='') {
  global $_lf, $arrUjian;
	$arrNamaUjian = array(0=>'', 1=>'( ** Ujian Tengah Semester ** )', 2=>'( ** Ujian Akhir Semester ** )');
  $hal++;
  // data program
  $prg = TRIM($jdwl['ProgramID'], '.');
  $_prg = explode('.', $prg);
  $prg = $_prg[0];
  // data prodi
  $prd = TRIM($jdwl['ProdiID'], '.');
  $_prd = explode('.', $prd);
  $prd = $_prd[0];
  $prodi = GetaField('prodi', "ProdiID", $prd, "Nama");
  $nmsesi = GetaField("tahun", "ProdiID='$prd' and ProgramID='$prg' and TahunID", $jdwl['TahunID'], 'Nama');
  // Dosen pengampu
  $arrdosen = explode('.', TRIM($jdwl['DosenID'], '.'));
  $strdosen = implode(',', $arrdosen);
	//echo $DSNTAM;
  if (!empty($dosentambah)) $dosen = $dosentambah;
	else $dosen = (empty($strdosen))? '' : GetArrayTable("select Nama from dosen where Login in ($strdosen) order by Nama", "Login", "Nama", '<br />');
  $mrg = str_pad(' ', 28);
  $mrg1 = ($ctk == 0)? str_pad(' ', 15) : '';
  $ctk = $_REQUEST['ctk']+0;
  if ($ctk == 0) {
    $hari = GetaField("hari", "HariID", $jdwl['HariID'], 'Nama');
    $JM = substr($jdwl['JamMulai'], 0, 5);
    $JS = substr($jdwl['JamSelesai'], 0, 5);
    $RG = $jdwl['RuangID'];
  }
  else {
    $nmujian = $arrUjian[$ctk];
    $hari = FormatTanggal($jdwl[$nmujian."Tanggal"]); 
    $JM = substr($jdwl[$nmujian."JamMulai"], 0, 5);
    $JS = substr($jdwl[$nmujian."JamSelesai"], 0, 5);
    //$RG = $jdwl[$nmujian."RuangID"];
    $_rg = explode(':', $rg);
    $RG = $_rg[0];
  }
  $Hak = $_REQUEST['hak'];
  $ss = ($Hak == 1) ? $_lf.Str_pad('Mahasiswa Tidak Berhak Ikut Ujian',81, ' ', STR_PAD_LEFT).$_lf : '';
  $RES = ($jdwl['JenisJadwalID'] == 'R') ? '(Responsi)' : '';
  $a = chr(27).chr(15) . $_lf.$_lf.$_lf.$_lf.
       str_pad($arrNamaUjian[$ctk], 80, ' ', STR_PAD_LEFT).
       $ss .
       $_lf.$_lf.
       $mrg . str_pad($nmsesi, 70) . $mrg1 . $hari . $_lf .
       $mrg . str_pad($prodi, 70) . $mrg1 . $JM . ' - ' . $JS .$_lf.
       $mrg . str_pad($jdwl['MKKode'].' - '.$jdwl['Nama'] . ' ' . 
         $mrg1 . $jdwl['NamaKelas']. 
         ' ('.$jdwl['SKS'].' SKS) ' .$RES, 70). $mrg1 . $RG.$_lf.
       $mrg . str_pad($jdwl['DosenID'] . " - " . $dosen, 70) . $mrg1 . $hal.'/'.$jumhal .
       $_lf.$_lf.$_lf.$_lf.$_lf.$_lf.$_lf;
  return $a;
}
?>
