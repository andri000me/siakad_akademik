<?php

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Setup Penilaian Praktek Kerja", 1);

// *** Parameters ***
$PraktekKerjaID = GetSetVar('PraktekKerjaID');

// *** Main ***
TampilkanJudul("Setup Penilaian Praktek Kerja");
$gos = (empty($_REQUEST['gos']))? 'fnSetupPenilaian' : $_REQUEST['gos'];
$gos($PraktekKerjaID);

// *** functions ***
function fnSetupPenilaian($PraktekKerjaID) {
  // Data TA
  $praktekkerja = GetFields('praktekkerja', 'PraktekKerjaID', $PraktekKerjaID, '*');
  $mhsw = GetFields('mhsw', "MhswID='$praktekkerja[MhswID]' and KodeID", KodeID, '*');
  $pa = GetFields('dosen', "Login='$mhsw[PenasehatAkademik]' and KodeID", KodeID, "Nama, Gelar");
  $_pa = (empty($pa))? "&lsaquo; Belum diset &rsaquo;" : "$pa[Nama] <sup>$pa[Gelar]</sup>";
  $stawal = GetaField('statusawal', 'StatusAwalID', $mhsw['StatusAwalID'], 'Nama');
  $prodi = GetaField('prodi', "ProdiID='$mhsw[ProdiID]' and KodeID", KodeID, 'Nama');
  $prg = GetaField('program', "ProgramID='$mhsw[ProgramID]' and KodeID", KodeID, 'Nama');
  $pembimbing = GetPembimbing($praktekkerjaID, $praktekkerja);
  $optlulus = GetOption2('statuslulus', "Nama", "StatusLulusID", $praktekkerja['StatusLulusID'], '', 'StatusLulusID');
  $optnilai = GetOption2('nilai', "concat(Nama, ' ... ', Bobot)", 'Bobot desc', $praktekkerja['BobotNilai'],
    "ProdiID = '$mhsw[ProdiID]'", 'Bobot');
  // Tampilkan
  echo <<<ESD
  <table class=box cellspacing=1 width=100%>
  <form name='frmLulus' action='../$_SESSION[mnux].lulus.php' method=POST>
  <input type=hidden name='gos' value='fnSimpan' />
  <input type=hidden name='PraktekKerjaID' value='$PraktekKerjaID' />
  
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
  <tr><td class=inp>Profil Perusahaan:</td>
      <td class=ul colspan=3>$praktekkerja[NamaPerusahaan] <font size=1 color=teal>$praktekkerja[AlamatPerusahaan], $praktekkerja[KotaPerusahaan]</font></td>
      </tr>
  <tr><td class=inp>Deskripsi/abstrak:</td>
      <td class=ul colspan=3>$praktekkerja[Deskripsi]&nbsp;</td>
      </tr>
  <tr><td class=inp>Pembimbing:</td>
      <td class=ul valign=top>$pembimbing</td>
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
      <textarea name='Keterangan' cols=50 rows=2>$praktekkerja[Keterangan]</textarea>
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
function fnSimpan($PraktekKerjaID) {
  $praktekkerja = GetFields('praktekkerja', 'PraktekKerjaID', $PraktekKerjaID, '*');
  $prd = GetaField('mhsw', "MhswID='$praktekkerja[MhswID]' and KodeID", KodeID, "ProdiID");
  $BobotNilai = $_REQUEST['BobotNilai']+0;
  $StatusLulusID = sqling($_REQUEST['StatusLulusID']);
  $Keterangan = sqling($_REQUEST['Keterangan']);
  
  $Lulus = GetaField('statuslulus', 'StatusLulusID', $StatusLulusID, 'Lulus');
  $GradeNilai = GetaField('nilai', "ProdiID='$prd' and Bobot='$BobotNilai' and KodeID",
    KodeID, "Nama");
  // Simpan
  $s = "update praktekkerja
    set Lulus = '$Lulus',
        StatusLulusID = '$StatusLulusID',
        BobotNilai = '$BobotNilai',
        GradeNilai = '$GradeNilai',
        Keterangan = '$Keterangan',
        LoginEdit = '$_SESSION[_Login]',
        TanggalEdit = now()
    where PraktekKerjaID = '$PraktekKerjaID' ";
  $r = _query($s);
  
  $s = "update krs left outer join mk on krs.MKID=mk.MKID
	  set krs.BobotNilai='$BobotNilai', krs.GradeNilai='$GradeNilai'	
	  where krs.MhswID='$praktekkerja[MhswID]' and krs.TahunID='$praktekkerja[TahunID]' and mk.PraktekKerja='Y' and krs.KodeID='".KodeID."'";
  $r = _query($s);

  echo <<<ESD
  <script>
  opener.location = "../index.php?mnux=$_SESSION[mnux]&gos=";
  window.close();
  </script>
ESD;
}
function GetPembimbing($praktekkerjaID, $praktekkerja) {
  $_praktekkerja = GetFields('dosen', "Login='$praktekkerja[Pembimbing]' and KodeID", KodeID, "Nama, Gelar");
  $b = array();
  $b[] = "$_praktekkerja[Nama] <sup>$_praktekkerja[Gelar]</sup>";
 
  return $b[0];
}
?>
