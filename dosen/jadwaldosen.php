<?php

// *** Parameters ***
$TahunID = GetSetVar('TahunID');
$DosenID = $_SESSION['_Login'];
$dsn = GetFields('dosen', "Login='$DosenID' and KodeID", KodeID, "*");

// *** Main ***
TampilkanJudul("<font size=3><b>Jadwal Mengajar $dsn[Gelar1] $dsn[Nama] <sup>$dsn[Gelar]</sup></b></font>");
if (empty($dsn))
  die(ErrorMsg("Error",
    "Anda tidak berhak mengakses menu ini.<br />
    Modul ini khusus untuk dosen.
    <hr size=1 color=silver />
    Hubungi Sysadmin untuk informasi lebih lanjut."));

$gos = (empty($_REQUEST['gos']))? 'JadwalDosen' : $_REQUEST['gos'];
$gos($TahunID, $dsn);

// *** Functions ***
function JadwalDosen($TahunID, $dsn) {
  TampilkanPilihanTahun($TahunID, $dsn['Login']);
  
  $s = "select j.*,
      left(j.JamMulai, 5) as _JM, left(j.JamSelesai, 5) as _JS,
      p.Nama as NamaProdi, k.Nama as NMKelas
    from jadwal j
      left outer join prodi p on p.ProdiID = j.ProdiID and p.KodeID = '".KodeID."'
	  left outer join kelas k on k.KelasID = j.NamaKelas
    left outer join jadwaldosen jd on jd.JadwalID=j.JadwalID
    where j.TahunID = '$TahunID'
      and j.KodeID = '".KodeID."'
      and (j.DosenID = '$dsn[Login]' or jd.DosenID = '$dsn[Login]')
      group by j.JadwalID
    order by j.HariID, j.JamMulai, j.JamSelesai";
  $r = _query($s);
  
  $n = 0; $ttl = 0; $hr = -25;
  echo "<table class=box cellspacing=1 align=center width=700>";
  while ($w = _fetch_array($r)) {
    if ($hr != $w['HariID']) {
      $hr = $w['HariID'];
      $Hari = GetaField('hari', 'HariID', $hr, 'Nama');
      TampilkanHeaderTabel($Hari);
    }
    $n++;
    $ttl += $w['SKS'];
    echo <<<ESD
    <tr>
        <td class=inp>$n</td>
        <td class=ul><sup>$w[_JM]</sup>&#8594;<sub>$w[_JS]</td>
        <td class=ul>$w[MKKode]</td>
        <td class=ul>$w[Nama]</td>
        <td class=ul align=right>$w[SKS]</td>
        <td class=ul>$w[NMKelas]</td>
        <td class=ul>$w[RuangID]</td>
        <td class=ul>
          <sup>$w[ProgramID]</sup>
          <div align=right>
          <sub><abbr title='$w[NamaProdi]'>&#8594;$w[ProdiID]</abbr></sub>
          </div>
          </td>
ESD;
  }
  RandomStringScript();
  echo <<<ESD
    <tr><td class=ul1 colspan=4 align=right>Total SKS:</td>
        <td class=ul1 align=right><font size=+1>$ttl</font></td>
        <td class=ul1 colspan=4></td>
        </tr>
    </table>
    
    <script>
    <!--
    function CetakJadwal(thn, dsn) {
      var _rnd = randomString();
      lnk = "$_SESSION[mnux].cetak.php?TahunID="+thn+"&DosenID="+dsn+"&_rnd="+_rnd;
      win2 = window.open(lnk, "", "width=800, height=600, scrollbars, status");
      if (win2.opener == null) childWindow.opener = self;
    }
    //-->
    </script>
ESD;
}
function TampilkanHeaderTabel($Hari) {
  echo <<<ESD
  <tr><td class=ul1 colspan=8><font size=+1>$Hari</font></td></tr>
  <tr><th class=ttl width=20>Nmr.</th>
      <th class=ttl width=80>Jam Kuliah</th>
      <th class=ttl width=80>Kode</th>
      <th class=ttl>Matakuliah</th>
      <th class=ttl width=20>SKS</th>
      <th class=ttl width=60>Kelas</th>
      <th class=ttl width=60>Ruang</th>
      <th class=ttl width=80>Program</th>
ESD;
}
function TampilkanPilihanTahun($TahunID, $DosenID) {
  $btnCetak = ($TahunID == '')? '' : "<input type=button name='Cetak' value='Cetak Jadwal' onClick=\"CetakJadwal('$TahunID', '$DosenID')\" />";
  echo <<<ESD
  <table class=box cellspacing=1 align=center>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$_SESSION[mnux]' />
  <input type=hidden name='gos' value='' />
  <tr><td class=wrn width=1></td>
      <td class=inp>Tahun Akademik:</td>
      <td class=ul>
        <input type=text name='TahunID' value='$_SESSION[TahunID]' size=6 maxlength=5 />
        <input type=submit name='ST' value='Set Tahun' />
        $btnCetak
      </td></tr>
  </table>
ESD;
}
?>
