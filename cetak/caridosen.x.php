<?php
// Author: Emanuel Setio Dewo
// Start: 13 March 2006
include "../sisfokampus.php";

$prodi = $_REQUEST['prodi'];
$arrdsn = TRIM($_REQUEST['arrdsn'], '.');
$arrdsn = (empty($arrdsn))? '.' : '.'.$arrdsn.'.';


include "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";
include_once "parameter.php";

echo "<HTML>
  <HEAD>
  <TITLE>Cari Sekolah</TITLE>";
include_once "css.php";
echo "</HEAD>
  <BODY>";

// Tambahkan Script
echo <<<END
  <SCRIPT LANGUAGE="JavaScript1.2">
  <!--
  function Kembalikan(frm){
    creator.data.DosenID.value = frm.arraydosen.value;
    window.close();
  }
  function UbahDosen(nm){
    ck = "";
    if (nm.checked == true) {
      var nilai = dosen.arraydosen.value;
      if (nilai.match(nm.value+".") != nm.value+".") dosen.arraydosen.value += nm.value + ".";
    }
    else {
      var nilai = dosen.arraydosen.value;
      dosen.arraydosen.value = nilai.replace(nm.value+".", "");
    }
  }
  //-->
  </script>
END;


// Tampilkan Ruang
TampilkanJudul("Daftar Dosen");
$s = "select concat(d.Nama, ', ', d.Gelar) as DSN,
  d.Login, d.Nama, d.Gelar, d.ProdiID
  from dosen d
  where INSTR(d.ProdiID, '.$prodi.')>0
  order by d.Nama";
$r = _query($s);
$hdr = "<tr><th class=ttl>Ampukan</th>
  <th class=ttl>Kode</ttl>
  <th class=ttl>Dosen</ttl>
  </tr>";
echo "<form action='' name='dosen' method=POST>
  <p><table class=box cellspacing=1 cellpadding=4>
  <tr><td class=inp1>Dosen pengampu :</td>
  <td class=ul><input type=text name='arraydosen' value='$arrdsn' size=30 maxlength=200>
  <input type=button name='Pengampu' value='Dosen Pengampu' onClick='javascript:Kembalikan(dosen)'>
  <input type=button name='Batal' value='Batal' onClick='javascript:window.close()'>
  </td></tr>
  <tr><td colspan=2 class=ul>Tiap kode ruang diapit oleh tanda '.'</td></tr>
  </table></p>
  <p><table class=box cellspacing=1 cellpadding=4>
  $hdr";
$kampus = '';
while ($w = _fetch_array($r)) {
  $ck = (strpos($arrdsn, '.'.$w['Login'].'.') === false)? '' : 'checked';
  echo "<tr>
    <td class=ul><input type=checkbox name='Kode$w[Login]' value='$w[Login]' $ck onChange='javascript:UbahDosen(dosen.Kode$w[Login])'></td>
    <td class=ul>$w[Login]</td>
    <td class=ul>$w[DSN]</td>
    </tr>";
}
echo "</table></form></p>";


// Tampilkan footer
include_once "disconnectdb.php";
echo "</BODY>
</HTML>";

?>
