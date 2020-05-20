<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 14 Nov 2008

// *** Parameters ***
$MhswID = ($_SESSION['_LevelID'] !=120)? GetSetVar('MhswID') : $_SESSION['_Login'];
$mhsw = GetFields('mhsw', "MhswID = '$MhswID' and KodeID", KodeID, "*");

// *** Main ***
TampilkanJudul("Transkrip Nilai Mahasiswa");
TampilkanAmbilMhswID($MhswID, $mhsw);

if ($MhswID == '') {
  echo Konfirmasi("Masukkan Parameter",
    "Masukkan NIM/NPM dari Mahasiswa pindahan.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.");
}
// Cek apakah mahasiswanya ketemu atau tidak
elseif (empty($mhsw)) {
  echo ErrorMsg("Error",
    "Mahasiswa dengan NIM/NPM: <b>$MhswID</b> tidak ditemukan.<br />
    Masukkan NIM/NPM yang sebenarnya.
    <hr size=1 color=silver />
    Hubungi Sysadmin untuk informasi lebih lanjut.");
}
/*
elseif ($mhsw['Keluar'] == 'Y') {
  echo ErrorMsg("Error",
    "Mahasiswa dengan NIM/NPM: <b>$MhswID</b> telah keluar/lulus.<br />
    Anda sudah tidak dapat mengubah konversi.
    <hr size=1 color=silver />
    Hubungi Sysadmin untuk informasi lebih lanjut.");
} */
else {
  // Cek apakah punya hak akses terhadap mhsw dari prodi ini?
  if (strpos($_SESSION['_ProdiID'], $mhsw['ProdiID']) === false) {
    echo ErrorMsg("Error",
      "Anda tidak memiliki hak akses terhadap mahasiswa ini.<br />
      Mahasiswa: <b>$MhswID</b>, Prodi: <b>$mhsw[ProdiID]</b>.<br />
      Hubungi Sysadmin untuk informasi lebih lanjut.");
  }
  // hak akses oke
  else {
      $gos = (empty($_REQUEST['gos']))? 'DftrMK' : $_REQUEST['gos'];
      $gos($MhswID, $mhsw);
  }
}

// *** Functions ***
function TampilkanAmbilMhswID($MhswID, $mhsw) {
  $stawal = GetaField('statusawal', 'StatusAwalID', $mhsw['StatusAwalID'], 'Nama');
  $status = GetaField('statusmhsw', 'StatusMhswID', $mhsw['StatusMhswID'], 'Nama');
  if (empty($mhsw['PenasehatAkademik'])) {
    $pa = '<sup>Belum diset</sup>';
  }
  else {
    $dosenpa = GetFields('dosen', "Login='$mhsw[PenasehatAkademik]' and KodeID", KodeID, "Nama, Gelar");
    $pa = "$dosenpa[Nama] <sup>$dosenpa[Gelar]</sup>";
  } 
  $txtCari = ($_SESSION['_LevelID'] !=120)? "<input type=text name='MhswID' value='$MhswID' size=20 maxlength=50 />
  				<input type=submit name='btnCari' value='Cari' />" : "$MhswID";
    
  echo <<<ESD
  <table class=box cellspacing=1 align=center width=600>
  <form name='frmMhsw' action='?' method=POST>
  <input type=hidden name='mnux' value='$_SESSION[mnux]' />
  <input type=hidden name='gos' value='' />
  
  <tr><td class=wrn width=2 rowspan=4></td>
      <td class=inp width=80>NIM/NPM:</td>
      <td class=ul width=200>
	  $txtCari
          </td>
      <td class=inp width=80>Mahasiswa:</td>
      <td class=ul>$mhsw[Nama]&nbsp;</td>
      </tr>
  <tr><td class=inp>Status Mhsw:</td>
      <td class=ul>$status <sup>$stawal</sup></td>
      <td class=inp>Dosen PA:</td>
      <td class=ul>$pa</td>
	  </tr>
  <tr><td class=ul colspan=4 align=center>
	  <input type=button name='btnSKSLulus' value='Cetak SKS Lulus'
        onClick="fnCetakSKSLulus('$MhswID')" />
	  <input type=button name='btnSKSTidakLulus' value='Cetak SKS Tidak Lulus'
        onClick="fnCetakSKSTidakLulus('$MhswID')" />
	  </td></tr>
  </form>
  </table>
  <script>
	  function fnCetakSKSLulus(MhswID)
	  {	var _rnd = randomString();
        lnk = "$_SESSION[mnux].skslulus.php?MhswID="+MhswID+"&_rnd="+_rnd;
        win2 = window.open(lnk, "", "width=700, height=500, scrollbars");
        if (win2.opener == null) childWindow.opener = self;
	  }
	  function fnCetakSKSTidakLulus(MhswID)
      {	var _rnd = randomString();
        lnk = "$_SESSION[mnux].skstidaklulus.php?MhswID="+MhswID+"&_rnd="+_rnd;
        win2 = window.open(lnk, "", "width=700, height=500, scrollbars");
        if (win2.opener == null) childWindow.opener = self;
	  }
  </script>
ESD;
  RandomStringScript();
}
function DftrMK($MhswID, $mhsw) {
  $s = "select k.*
    from krs k
      left outer join khs h on h.KHSID = k.KHSID and h.KodeID = '".KodeID."'
    where k.MhswID = '$MhswID'
    order by k.TahunID, k.MKKode";
  $r = _query($s); $_tahun = 'alksdjfasdf-asdf';
  echo <<<ESD
  <table class=box cellspacing=1 width=600 align=center>
ESD;
  $hdr = "<tr><th class=ttl width=20>Nmr</th>
    <th class=ttl width=80>Kode</th>
    <th class=ttl>Matakuliah</th>
    <th class=ttl width=30>SKS</th>
    <th class=ttl width=30>Nilai</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    if ($_tahun != $w['TahunID']) {
      $_tahun = $w['TahunID'];
      echo "<tr>
        <td class=ul1 colspan=10>
          <font size=+1>$_tahun</font>
        </td></tr>";
      echo $hdr;
      $n = 0;
    }
    $n++;
    
	$GradeNilai = ($w['Final'] == 'Y')? $w['GradeNilai'] : '*'; 
	
    echo <<<ESD
    <tr>
      <td class=inp>$n</td>
      <td class=ul>$w[MKKode]</td>
      <td class=ul>$w[Nama]</td>
      <td class=ul align=right>$w[SKS]</td>
      <td class=ul align=center>$GradeNilai</td>
    </tr>
ESD;
  }
  echo <<<ESD
  </form>
  </table>
ESD;
}

?>
