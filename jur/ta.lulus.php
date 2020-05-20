<?php

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Setup Kelulusan Skripsi/TA", 1);
CekBolehAksesModul();
// *** Parameters ***
$TAID = GetSetVar('TAID');

// *** Main ***
TampilkanJudul("Setup Kelulusan Skripsi/TA");
$gos = (empty($_REQUEST['gos']))? 'fnSetupKelulusan' : $_REQUEST['gos'];
$gos($TAID);

// *** functions ***
function fnSetupKelulusan($TAID) {
  // Data TA
  $ta = GetFields('ta', 'TAID', $TAID, '*');
  $mhsw = GetFields('mhsw', "MhswID='$ta[MhswID]' and KodeID", KodeID, '*');
  $pa = GetFields('dosen', "Login='$mhsw[PenasehatAkademik]' and KodeID", KodeID, "Nama, Gelar");
  $_pa = (empty($pa))? "&lsaquo; Belum diset &rsaquo;" : "$pa[Nama] <sup>$pa[Gelar]</sup>";
  $stawal = GetaField('statusawal', 'StatusAwalID', $mhsw['StatusAwalID'], 'Nama');
  $prodi = GetaField('prodi', "ProdiID='$mhsw[ProdiID]' and KodeID", KodeID, 'Nama');
  $prg = GetaField('program', "ProgramID='$mhsw[ProgramID]' and KodeID", KodeID, 'Nama');
  $pembimbing = GetPembimbing($TAID, $ta);
  $penguji = GetPenguji($TAID, $ta);
  $optlulus = GetOption2('statuslulus', "Nama", "StatusLulusID", $ta['StatusLulusID'], '', 'StatusLulusID');
  $optnilai = GetOption2('nilai', "concat(Nama, ' ... ', Bobot)", 'Bobot desc', $ta['BobotNilai'],
    "ProdiID = '$mhsw[ProdiID]'", 'Bobot');
  // Tampilkan
  echo <<<ESD
  <table class=box cellspacing=1 width=100%>
  <form name='frmLulus' action='../$_SESSION[mnux].lulus.php' method=POST>
  <input type=hidden name='gos' value='fnSimpan' />
  <input type=hidden name='TAID' value='$TAID' />
  
  <tr><td class=inp width=100>NIM/NPM:</td>
      <td class=ul>$mhsw[MhswID]&nbsp;</td>
      <td class=inp width=100>Mahasiswa:</td>
      <td class=ul>$mhsw[Nama]&nbsp;</td>
      </tr>
  <tr><td class=inp>Angkatan:</td>
      <td class=ul>$mhsw[TahunID] <sup>($stawal)</sup></td>
      <td class=inp>Program Studi:</td>
      <td class=ul>$prodi <sup>($prg)</sup></td>
      </tr>
  <tr><td class=inp>Penasehat Akd:</td>
      <td class=ul colspan=3>$_pa</td>
      </tr>
  <tr><td colspan=4 height=1 bgcolor=silver></td></tr>
  <tr><td class=inp>Judul:</td>
      <td class=ul colspan=3>$ta[Judul]</td>
      </tr>
  <tr><td class=inp>Deskripsi/abstrak:</td>
      <td class=ul colspan=3>$ta[Deskripsi]&nbsp;</td>
      </tr>
  <tr><td class=inp>Pembimbing:</td>
      <td class=ul valign=top>$pembimbing</td>
      <td class=inp>Penguji:</td>
      <td class=ul valign=top>$penguji</td>
      </tr>
  <tr><td class=inp>Nilai:</td>
      <td class=ul>
        <select name='BobotNilai'>$optnilai</select>
      </td>
      <td class=inp>Status Lulus:</td>
      <td class=ul>
        <select name='StatusLulusID'>$optlulus</select>
      </td>
      </tr>
  <tr><td class=inp>Keterangan:</td>
      <td class=ul colspan=3>
      <textarea name='Keterangan' cols=50 rows=2>$ta[Keterangan]</textarea>
      </td>
      </tr>
  <tr><td class=ul colspan=4 align=center>
      <input type=submit name='btnSimpan' value='Simpan' />
      <input type=button name='btnTutup' value='Tutup' onClick="window.close()" />
      </td></tr>
  </form>
  </table>
ESD;
}
function fnSimpan($TAID) {
  $ta = GetFields('ta', 'TAID', $TAID, '*');
  $prd = GetaField('mhsw', "MhswID='$ta[MhswID]' and KodeID", KodeID, "ProdiID");
  $BobotNilai = $_REQUEST['BobotNilai']+0;
  $StatusLulusID = sqling($_REQUEST['StatusLulusID']);
  $Keterangan = sqling($_REQUEST['Keterangan']);
  
  $Lulus = GetaField('statuslulus', 'StatusLulusID', $StatusLulusID, 'Lulus');
  $GradeNilai = GetaField('nilai', "ProdiID='$prd' and Bobot='$BobotNilai' and KodeID",
    KodeID, "Nama");
  // Simpan
  $s = "update ta
    set Lulus = '$Lulus',
        StatusLulusID = '$StatusLulusID',
        BobotNilai = '$BobotNilai',
        GradeNilai = '$GradeNilai',
        Keterangan = '$Keterangan',
        LoginEdit = '$_SESSION[_Login]',
        TanggalEdit = now()
    where TAID = '$TAID' ";
  $r = _query($s);
  
  $s = "update krs left outer join mk on krs.MKID=mk.MKID
		set krs.BobotNilai='$BobotNilai', krs.GradeNilai='$GradeNilai'
		where krs.MhswID='$ta[MhswID]' and mk.TugasAkhir='Y' and krs.KodeID='".KodeID."'";
  $r = _query($s);

  echo <<<ESD
  <script>
  opener.location = "../index.php?mnux=$_SESSION[mnux]&gos=";
  window.close();
  </script>
ESD;
}
function GetPembimbing($TAID, $ta) {
  $_ta = GetFields('dosen', "Login='$ta[Pembimbing]' and KodeID", KodeID, "Nama, Gelar");
  $b = array();
  $b[] = "1. $_ta[Nama] <sup>$_ta[Gelar]</sup>";
  $s = "select d.Nama, d.Gelar
    from tadosen td
      left outer join dosen d on d.Login = td.DosenID and d.KodeID = '".KodeID."'
    where td.Tipe = 0
      and td.TAID = '$TAID' ";
  $r = _query($s); $n = 1;
  while ($w = _fetch_array($r)) {
    $n++;
    $b[] = "$n. $w[Nama] <sup>$w[Gelar]</sup>";
  }
  return implode('<br />', $b);
}
function GetPenguji($TAID, $ta) {
  $_ta = GetFields('dosen', "Login='$ta[Penguji]' and KodeID", KodeID, "Nama, Gelar");
  $b = array();
  $b[] = "1. $_ta[Nama] <sup>$_ta[Gelar]</sup>";
  $s = "select d.Nama, d.Gelar
    from tadosen td
      left outer join dosen d on d.Login = td.DosenID and d.KodeID = '".KodeID."'
    where td.Tipe = 1
      and td.TAID = '$TAID' ";
  $r = _query($s); $n = 1;
  while ($w = _fetch_array($r)) {
    $n++;
    $b[] = "$n. $w[Nama] <sup>$w[Gelar]</sup>";
  }
  return implode('<br />', $b);
}
function CekBolehAksesModul() {
  $arrAkses = array(1, 20, 40, 41, 43, 56, 66, 440, 51);
  $key = array_search($_SESSION['_LevelID'], $arrAkses);
  if ($key === false)
    die(ErrorMsg('Error',
      "Anda tidak berhak mengakses modul ini.<br />
      Hubungi SysAdmin untuk informasi lebih lanjut."));
}
?>
