<?php

// *** Parameters ***
$TahunID = GetSetVar('TahunID');
$ProdiID = GetSetVar('ProdiID');

// *** Main ***
TampilkanJudul("Laporan Keuangan");
TampilkanHeaderLaporanKeuangan();
$gos = (empty($_REQUEST['gosx']))? 'DftrLapKeu' : $_REQUEST['gosx'];
$gos();

// *** Functions ***
function TampilkanHeaderLaporanKeuangan() {
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
function DftrLapKeu() {
  $arrLap = array(
    'Rekapitulasi Biaya Mahasiswa~rekapbiaya',
    'Laporan Pembayaran Mahasiswa~mhswbayar',
	'Laporan Pembayaran Registrasi Mahasiswa~rekapbiayareg',
	'Laporan Pembayaran Uang Kuliah~mhswbayar2',
    'Rekap Pembayaran Bulanan~bayarbulan',
    'Daftar Pembayaran Per Akun~bayarakun',
	'Daftar Mahasiswa Yang Memiliki Angsuran~angsurmhsw',
	'Daftar Mahasiswa Yang Mendapatkan Potongan~beasiswa',
	'Rekap Pembayaran Mahasiswa XLS~bayarmhs.xls3',
	'Laporan Pembayaran Mahasiswa melalui OPS Pertanggal~bayarmhs.rekap.xls',
  'Rekap Pembayaran Mahasiswa melalui OPS~bayarmhs.rekap.xls2',
  'Rekap Pembayaran Mahasiswa Tabel Bank~bayartablebank',
  'Laporan Transaksi Virtual Account~trx_virtual_account'
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
    lnk = "$_SESSION[mnux]."+mdl+".php?TahunID=$_SESSION[TahunID]&ProdiID=$_SESSION[ProdiID]"+param+"&_rnd="+rnd;
    //window.location = lnk;
    win2 = window.open(lnk, "", "width=800, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  </script>
SCR;
}

?>
