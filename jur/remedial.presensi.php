<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 28 Agustus 2008

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Presensi Remedial Mahasiswa", 1);

// *** Parameters ***
$pid = $_REQUEST['pid'];

// *** Main ***
TampilkanJudul("Presensi Remedial Mahasiswa");
$gos = (empty($_REQUEST['gos']))? 'DftrSiswa' : $_REQUEST['gos'];
$gos($pid);

// *** Functions ***
function DftrSiswa($pid) {
  $p = GetFields("presensiremedial pr 
    left outer join jadwalremedial jr on pr.JadwalRemedialID = jr.JadwalRemedialID
    left outer join dosen d on d.Login = jr.DosenID and d.KodeID='".KodeID."'
    left outer join hari h on h.HariID = date_format(pr.Tanggal, '%w')",
    "pr.PresensiRemedialID", $pid,
    "pr.*, jr.MKKode, jr.Nama, h.Nama as _HR,
    concat(d.Nama, ' <sup>', d.Gelar, '</sup>') as DSN,
    date_format(pr.Tanggal, '%d-%m-%Y') as _Tanggal,
    left(pr.JamMulai, 5) as _JM, left(pr.JamSelesai, 5) as _JS");
  TampilkanHeader($p);
  CekKRSMhsw($p);
  TampilkanPresensiMhsw($p);
}
function TampilkanHeader($p) {
  echo "<table class=box cellspacing=1 width=100%>
  <tr><td class=inp>Matakuliah:</td>
      <td class=ul>$p[Nama]<sup>$p[MKKode]</sup></td>
      <td class=inp>Dosen:</td>
      <td class=ul>$p[DSN]</td>
      </tr>
  <tr>
      <td class=inp>Tanggal:</td>
      <td class=ul>$p[_HR] $p[_Tanggal]
        </td>
      <td class=inp>Jam:</td>
      <td class=ul><sup>$p[_JM]</sup> &#8594; <sub>$p[_JS]</sub></td>
      </tr>
  </table>";
}
function CekKRSMhsw($p) {
  $def = GetFields('jenispresensi', 'Def', 'Y', '*');
  $s = "select KRSRemedialID, MhswID, JadwalRemedialID
    from krsremedial
    where JadwalRemedialID = '$p[JadwalRemedialID]'
    order by MhswID";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    $ada = GetFields('presensiremedialmhsw', "PresensiRemedialID = '$p[PresensiRemedialID]' and KRSRemedialID", $w['KRSRemedialID'], '*');
    if (empty($ada)) {
      $sp = "insert into presensiremedialmhsw
        (JadwalRemedialID, KRSRemedialID, PresensiRemedialID, 
        MhswID, JenisPresensiID, Nilai, NA)
        values
        ($p[JadwalRemedialID], $w[KRSRemedialID], $p[PresensiRemedialID],
        '$w[MhswID]', '$def[JenisPresensiID]', '$def[Nilai]', 'N')";
      $rp = _query($sp);
      // Hitung KRS
      $jml = GetaField('presensiremedialmhsw', 'KRSRemedialID', $w['KRSRemedialID'], "sum(Nilai)")+0;
      $sk = "update krsremedial
        set _Presensi = $jml
        where KRSRemedialID = $w[KRSRemedialID]";
      $rk = _query($sk);
    }
  }
}
function TampilkanPresensiMhsw($p) {
  $s = "select pm.*, mhsw.Nama
    from presensiremedialmhsw pm
      left outer join mhsw on mhsw.MhswID = pm.MhswID and mhsw.KodeID = '".KodeID."'
    where pm.PresensiRemedialID = '$p[PresensiRemedialID]'
    order by pm.MhswID";
  $r = _query($s);
  $def = GetFields('jenispresensi', 'Def', 'Y', '*');
  $opt0 = GetOption2('jenispresensi', "Nama", 'JenisPresensiID', $def['JenisPresensiID'], '', 'JenisPresensiID');
  
  echo "<table class=box cellspacing=1 width=100%>";
  echo "<script>
  function ttutup() {
    opener.location='../index.php?mnux=$_SESSION[mnux]&gos=';
    self.close();
    return false;
  }
  </script>";
  echo "<tr>
    <form action='../$_SESSION[mnux].presensi.php' method=POST>
    <input type=hidden name='gos' value='SimpanSemua' />
    <input type=hidden name='pid' value='$p[PresensiRemedialID]' />
    
    <td class=ul colspan=5>Set semua ke:
    <select name='Stt'>$opt0</select>
    <input type=submit name='SetStt' value='Set Status' />
    <input type=button name='Refresh' value='Refresh' 
      onClick=\"location='../$_SESSION[mnux].presensi.php?pid=$p[PresensiRemedialID]'\" />
    <input type=button name='Tutup' value='Tutup' onClick=\"ttutup()\" />
    </td>
    
    </form>
    </tr>";
  $n = 0;
  $arr = GetArrPre();
  while ($w = _fetch_array($r)) {
    $n++;
    $optpre = GetOptPre($arr, $w['JenisPresensiID']);
    echo "
      <tr><td class=inp width=10>$n</td>
          <td class=inp1 width=94><b>$w[MhswID]</b></td>
          <td class=ul1 width=260>$w[Nama]</td>
          <td class=ul><select id='PresensiMhsw_$w[PresensiRemedialMhswID]'
            onChange='javascript:SetPresensiMhsw($w[PresensiRemedialMhswID])'>$optpre</select></td>
      </tr>";
  }
  echo <<<SCR
  </table>
  <script>
  function SetPresensiMhsw(id) {
    var status = document.getElementById("PresensiMhsw_"+id).value;
    lnk = "../$_SESSION[mnux].presensi.save.php?id="+id+"&st="+status;
    win2 = window.open(lnk, "", "width=0, height=0, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  </script>
SCR;
}
function SimpanSemua($pid) {
  $Stt = sqling($_REQUEST['Stt']);
  $Nilai = GetaField('jenispresensi', 'JenisPresensiID', $Stt, 'Nilai');
  $s = "select *
    from presensiremedialmhsw
    where PresensiRemedialID = '$pid' ";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    // update
    $s0 = "update presensiremedialmhsw set JenisPresensiID = '$Stt', Nilai = '$Nilai'
      where PresensiRemedialMhswID = $w[PresensiRemedialMhswID]";
    $r0 = _query($s0);
    // Hitung & update ke KRS
    $jml = GetaField('presensiremedialmhsw', 'KRSRemedialID', $w['KRSRemedialID'], "sum(Nilai)")+0;
    // Update KRS
    $s1 = "update krsremedial
      set _Presensi = $jml
      where KRSRemedialID = $w[KRSRemedialID]";
    $r1 = _query($s1);
  }
  BerhasilSimpan("../$_SESSION[mnux].presensi.php?pid=$pid", 1);
}
function GetOptPre($arr, $id) {
  $opt = '';
  foreach($arr as $a) {
    $_a = explode('~', $a);
    $sel = ($id == $_a[0])? 'selected' : '';
    $opt .= "<option value='$_a[0]' $sel>$_a[1]</option>";
  }
  return $opt;
}
function GetArrPre() {
  $s = "select * from jenispresensi where NA='N' order by JenisPresensiID";
  $r = _query($s);
  $arr = array();
  $arr[] = '';
  while ($w = _fetch_array($r)) {
    $arr[] = "$w[JenisPresensiID]~$w[Nama]";
  }
  return $arr;
}
?>
