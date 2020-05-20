<?php

// *** Parameters ***
$ProdiID = GetSetVar('_pustakaProdiID');

// *** Main ***
TampilkanHeader();
$gos = (empty($_REQUEST['gos']) || $_REQUEST['gos']=='laporan')? 'Dftr' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function TampilkanHeader() {
  $optprd = GetProdiUser($_SESSION['_Login'], $_SESSION['_pustakaProdiID']);
  echo <<<ESD
  <table class=box cellspacing=1 align=center width=600>
  <form action='?' method=POST>
  <tr><td class=wrn width=1></td>
      <td class=inp width=60>Prodi:</td>
      <td class=ul><select name='_pustakaProdiID' onChange='this.form.submit()'>$optprd</select></td>
  
  </form>
  </table>
ESD;
}
function Dftr() {
  $arrLap = array(
    'Laporan Pengunjung &raquo; PDF~pengunjung.pdf',
	'Rekap Pengunjung &raquo; PDF~rekap.pengunjung.pdf',
  'Laporan Anggota &raquo; XLS~anggota.xls',
  'Laporan Anggota Baru&raquo; XLS~adm.anggota.xls',
	'Laporan Peminjaman &raquo; XLS~pinjam',
  'History Peminjaman &raquo; XLS~pinjam.history',
  'Laporan Keterlambatan &raquo; XLS~pinjam.telat',
  'Rekap Koleksi Pustaka berdasarkan GMD &raquo; XLS~rekap~0',
  'Rekap Koleksi Pustaka berdasarkan Bahasa &raquo; XLS~rekap~1',
  'Daftar Buku Pustaka &raquo; XLS~buku.daftar',
	'Laporan Pembayaran Denda &raquo; PDF~denda'
  );
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
    lnk = "$_SESSION[mnux].laporan."+mdl+".php?ProdiID=$_SESSION[_pustakaProdiID]&md="+param+"&_rnd="+rnd;
    //window.location = lnk;
    win2 = window.open(lnk, "", "width=800, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  </script>
SCR;
}

?>