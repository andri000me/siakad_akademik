<?php

// *** Parameters ***
$TahunID = GetSetVar('TahunID');
$ProdiID = GetSetVar('ProdiID');

// *** Main ***
TampilkanJudul("Laporan Akademik");
TampilkanHeaderLaporanAkademik();
$gos = (empty($_REQUEST['gos']))? 'DftrLapAkd' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function TampilkanHeaderLaporanAkademik() {
  $optprd = GetProdiUser($_SESSION['_Login'], $_SESSION['ProdiID']);
  echo <<<ESD
  <table class=box cellspacing=1 align=center width=600>
  <form action='?' method=POST>
  <tr><td class=wrn width=1></td>
      <td class=inp width=60>Tahun Akd:</td>
      <td class=ul width=120 nowrap>
        <input type=text name='TahunID' value='$_SESSION[TahunID]' size=5 maxlength=6 />
        <input type=submit name='btnSet' value='Set' />
        </td>
      <td class=inp width=60>Prodi:</td>
      <td class=ul><select name='ProdiID' onChange='this.form.submit()'>$optprd</select></td>
  
  </form>
  </table>
ESD;
}
function DftrLapAkd() {
  $arrLap = array(
    'Rekapitulasi Jumlah Mahasiswa per Angkatan~statusmhsw0',
    'Rekapitulasi Jumlah Mahasiswa per Angkatan Berdasarkan Agama~agama',
    'Rekapitulasi Jumlah Mahasiswa per Angkatan Berdasarkan Pendapatan Ortu~pendapatanortu',
    'Rekapitulasi Jumlah Mahasiswa per Angkatan Berdasarkan NilaiUN~nilaiUN',
    '&raquo; Laporan Mahasiswa Aktif~statusmhsw~&sta=A',
    '&raquo; Laporan Mahasiswa Cuti~statusmhsw~&sta=C',
    '&raquo; Laporan Mahasiswa Drop Out~statusmhsw~&sta=D',
	'&raquo; Laporan Mahasiswa Keluar~statusmhsw~&sta=K',
	'&raquo; Laporan Mahasiswa Pasif~statusmhsw~&sta=P',
	'Daftar Mahasiswa Yang Sudah KRS~krsmhsw',
    'Daftar Mahasiswa Yang Belum KRS~krsmhsw0',
	'&raquo; Daftar Mahasiswa Berdasarkan Agama~agamamhsw', 
	'&raquo; Daftar Mahasiswa Berdasarkan Asal Sekolah~asalsekmhsw',
	'&raquo; Daftar Mahasiswa Berdasarkan Dosen PA~dosenpamhsw',
	'&raquo; Daftar Mahasiswa Berdasarkan Prodi~prodimhsw',
	'&raquo; Daftar Mahasiswa Berdasarkan Angkatan~angkmhsw',
	'&raquo; Rekap IP Semester Mahasiswa~ipsemester',
	'&raquo; Rekap IPK Wisudawan~ipwisudawan',
  '&raquo; Laporan KRS Mahasiswa~krs.semester',
  '&raquo; Laporan Masa Studi~masastudi',
  '&raquo; Rata-rata Kehadiran Mahasiswa~hadir.mhsw'
  );
  /*'&raquo; Laporan Statistik Kelas~statistikkelas',
  '&raquo; Laporan Nilai Semester dan Distribusi Matakuliah~statistikkelas2',
  '&raquo; Laporan Presensi~presensi'*/
  $i = 0;
  echo "<p><table class=box cellspacing=1 align=center width=600>";
  foreach ($arrLap as $arr) {
    $i++;
    $a = explode('~', $arr);
    $_a = "<a href='#$i' onClick=\"Prints('".$a[1]."', '".$a[2]."')\">";
    echo "<tr>
      <td class=inp width=10><a name='$i'></a>$i</td>
      <td class=ul1>$_a $a[0]</a></td>
      <td class=ul1 align=center width=10>$_a<img src='img/printer2.gif' /></a></td>
      </tr>";
  }
  echo "</table></p>";
  RandomStringScript();
  echo <<<SCR
  <script>
  function Prints(mdl, param) {
    var rnd = randomString();
    lnk = "$_SESSION[mnux]."+mdl+".php?TahunID=$_SESSION[TahunID]&ProdiID=$_SESSION[ProdiID]"+param+"&_rnd="+rnd;
    //window.location = lnk;
    win2 = window.open(lnk, "", "width=800, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  </script>
SCR;
}

?>
