<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 26/11/2008

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Konfirmasi Penjadwalan Ujian", 1);

// *** Parameters ***
$jid = GetSetVar('jid');

$_jdwlRuang = GetSetVar('_jdwlRuang');
$_jdwlTgl_d = GetSetVar('_jdwlTgl_d', date('d'));
$_jdwlTgl_m = GetSetVar('_jdwlTgl_m', date('m'));
$_jdwlTgl_y = GetSetVar('_jdwlTgl_y', date('Y'));
$_SESSION['_jdwlTgl'] = "$_jdwlTgl_y-$_jdwlTgl_m-$_jdwlTgl_d";

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'fnKonfirmasi' : $_REQUEST['gos'];
$gos($jid);

// *** Functions ***
function fnKonfirmasi($jid) {
  TampilkanJudul("Jadwalkan $_SESSION[_jdwlU]");
  $w = GetFields('jadwal', 'JadwalID', $jid, '*');
  $d = GetFields('dosen', "Login = '$w[DosenID]' and KodeID", KodeID, "Nama, Gelar");
  $tgl = FormatTanggal($_SESSION['_jdwlTgl']);
  echo <<<ESD
  <table class=box width=100%>
  <form name='frmUJIAN' action='../$_SESSION[mnux].edit.php' method=POST>
  <input type=hidden name='jid' value='$jid' />
  <input type=hidden name='gos' value='fnSimpan' />
  <input type=hidden name='_jdwlRuang' value='$_SESSION[_jdwlRuang]' />
  <input type=hidden name='_jdwlTgl' value='$_SESSION[_jdwlTgl]' />
  <input type=hidden name='_jdwlU' value='$_SESSION[_jdwlU]' />
  
  <tr><td class=inp>Kode:</td>
      <td class=ul>$w[MKKode]&nbsp;</td>
      <td class=inp>Matakuliah:</td>
      <td class=ul>$w[Nama]&nbsp;</td>
      </tr>
  <tr><td class=inp>SKS:</td>
      <td class=ul>$w[SKS] <sup>sks</sup></td>
      <td class=inp>Kelas:</td>
      <td class=ul>$w[NamaKelas]&nbsp;</td>
      </tr>
  <tr><td class=inp>Prodi:</td>
      <td class=ul>$w[ProdiID] <sup>$w[ProgramID]</sup></td>
      <td class=inp>Dosen:</td>
      <td class=ul>$d[Nama] <sup>$d[Gelar]</sup></td>
      </tr>
  
  <tr><th class=ttl colspan=4>Dijadwalkan Di:</th></tr>
  <tr><td class=inp>Tanggal:</td>
      <td class=ul>$tgl</td>
      <td class=inp>Ruang:</td>
      <td class=ul>$_SESSION[_jdwlRuang]&nbsp;</td>
      </tr>
  <tr><td class=inp>Jam Mulai:</td>
      <td class=ul><input type=text name='JamMulai' size=5 maxlength=5 /></td>
      <td class=inp>Selesai:</td>
      <td class=ul><input type=text name='JamSelesai' size=5 maxlength=5 /></td>
      </tr>
  <tr><td class=ul colspan=4 align=center>
      <input type=submit name='btnSimpan' value='Jadwalkan' />
      <input type=button name='btnTutup' value='Tutup' 
        onClick="window.close()" />
      </td></tr>
  </form>
  </table>
ESD;
}
function fnSimpan($jid) {
  $_jdwlRuang = sqling($_REQUEST['_jdwlRuang']);
  $_jdwlTgl = sqling($_REQUEST['_jdwlTgl']);
  $_jdwlU = sqling($_REQUEST['_jdwlU']);
  $JamMulai = sqling($_REQUEST['JamMulai']);
  $JamMulai = str_replace('.', ':', $JamMulai);
  $JamSelesai = sqling($_REQUEST['JamSelesai']);
  $JamSelesai = str_replace('.', ':', $JamSelesai);
  
  // Cek jadwal
  $pesan = '';
  $oke = CheckRuang($_jdwlU, $_jdwlRuang, $_jdwlTgl, $JamMulai, $JamSelesai, $jid, $pesan);
  
  if ($oke) {
    $s = "update jadwal
      set ".$_jdwlU."RuangID = '$_jdwlRuang',
          ".$_jdwlU."Tanggal = '$_jdwlTgl',
          ".$_jdwlU."JamMulai = '$JamMulai',
          ".$_jdwlU."JamSelesai = '$JamSelesai',
          LoginEdit = '$_SESSION[_Login]',
          TglEdit = now()
      where JadwalID = '$jid' ";
    $r = _query($s);
    
    echo "<script>
    opener.RefreshAll();
    window.close();
    </script>";
  }
  else {
    echo ErrorMsg("Error",
      "<table class=bsc cellspacing=1 width=400>
      <tr><td class=ul1 colspan=5 align=center>
          Jadwal ujian bentrok.
          Berikut adalah jadwal yg bentrok:
          </td></tr>
      $pesan
      </table>
      <hr size=1 color=silver />
      Opsi:
      <input type=button name='btnKembali' value='Kembali' 
        onClick=\"location='../$_SESSION[mnux].edit.php?jid=$jid'\"/>
      <input type=button name='btnTutup' value='Tutup'
        onClick='window.close()' />");
  }
}
function CheckRuang($Ujian, $Ruang, $Tanggal, $Mulai, $Selesai, $jid, &$pesan) {
  $s = "select j.*
    from jadwal j
    where j.KodeID = '".KodeID."'
      and j.".$Ujian."RuangID = '$Ruang'
      and j.".$Ujian."Tanggal = '$Tanggal'
      and (('$Mulai:00' <= j.".$Ujian."JamMulai and j.".$Ujian."JamMulai <= '$Selesai:59')
      or  ('$Mulai:00' <= j.".$Ujian."JamSelesai and j.".$Ujian."JamSelesai <= '$Selesai:59'))
    order by j.MKKode";
  //echo ("<pre>$s</pre>");
  $r = _query($s);
  
  $jml = _num_rows($r);
  $n = 0;
  while ($w = _fetch_array($r)) {
    $n++;
    $pesan .= "<tr><td class=inp>$n</td>
      <td class=ul>$w[MKKode]</td>
      <td class=ul>$w[Nama]</td>
      <td class=ul>$w[NamaKelas]</td>
      <td class=ul1>$w[ProdiID] <sup>$w[ProgramID]</sup></td>
      </tr>";
  }
  return $jml == 0;
}
?>
