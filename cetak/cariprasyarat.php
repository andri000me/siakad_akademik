<?php
// Author: Emanuel Setio Dewo
// Start: 13 March 2006
include "../sisfokampus.php";
include "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";
include_once "parameter.php";

$MKID = $_REQUEST['MKID'];
$arrMK = GetFields('mk', 'MKID', $MKID, '*');
$arrprasyarat = TRIM($_REQUEST['Prasyarat'], '.');
$arrprasyarat = (empty($arrprasyarat))? '.' : '.'.$arrprasyarat.'.';

echo "<HTML>
  <HEAD>
  <TITLE>Prasyarat Matakuliah</TITLE>";
include_once "css.php";
echo "</HEAD>
  <BODY>";

// Tambahkan Script
echo <<<END
  <SCRIPT LANGUAGE="JavaScript1.2">
  <!--
  function Kembalikan(frm){
    creator.data.Prasyarat.value = frm.Prasyarat.value;
    window.close();
  }

  function UbahPrasyarat(nm){
    ck = "";
    if (nm.checked == true) {
      var nilai = data.Prasyarat.value;
      if (nilai.match(nm.value+".") != nm.value+".") data.Prasyarat.value += nm.value + ".";
    }
    else {
      var nilai = data.Prasyarat.value;
      data.Prasyarat.value = nilai.replace(nm.value+".", "");
    }
  }
  //-->
  </script>
END;


// Tampilkan Ruang
TampilkanJudul("Prasyarat Matakuliah");
$s = "select mk.MKID, mk.MKKode, mk.Nama, mk.SKS, mk.JenisMKID, 
  mk.Sesi, jmk.Nama as JMK
  from mk mk
    left outer join jenismk jmk on mk.JenisMKID=jmk.JenisMKID
  where mk.ProdiID='$arrMK[ProdiID]' and mk.KurikulumID='$arrMK[KurikulumID]'
    and mk.NA='N' and mk.MKID<>$MKID
  order by mk.Sesi, mk.MKKode";
$r = _query($s);
$hdr = "<tr><th class=ttl>Tambah</th>
  <th class=ttl>Kode</th>
  <th class=ttl>Nama</th>
  <th class=ttl>SKS</th>
  <th class=ttl>Jenis</th>
  </tr>";
echo "<form action='' name='data' method=POST>
  <p><table class=box cellspacing=1 cellpadding=4>
  <tr><td class=inp1>Matakuliah Prasyarat :</td>
  <td class=ul><input type=text name='Prasyarat' value='$arrprasyarat' size=30 maxlength=200>
  <input type=button name='Tambah' value='Tambahkan Prasyarat' onClick='javascript:Kembalikan(data)'>
  <input type=button name='Batal' value='Batal' onClick='javascript:window.close()'>
  </td></tr>
  <tr><td colspan=2 class=ul>Tiap kode matakuliah diapit oleh tanda '.'</td></tr>
  </table></p>

  <p><table class=box cellspacing=1 cellpadding=4>";
$sesi = 0;
while ($w = _fetch_array($r)) {
  if ($sesi <> $w['Sesi']) {
    $sesi = $w['Sesi'];
    echo "<tr><td class=ul colspan=6><b>Semester: $sesi</b></td></tr>";
    echo $hdr;
  }
  $ck = (strpos($arrprasyarat, '.'.$w['MKID'].'.') === false)? '' : 'checked';
  echo "<tr>
    <td class=ul><input type=checkbox name='MKID$w[MKID]' value='$w[MKID]' $ck onChange='javascript:UbahPrasyarat(data.MKID$w[MKID])'> $w[MKID]</td>
    <td class=ul>$w[MKKode]</td>
    <td class=ul>$w[Nama]</td>
    <td class=ul align=right>$w[SKS]</td>
    <td class=ul>$w[JMK]</td>
    </tr>";
}
echo "</table></form></p>";


// Tampilkan footer
include_once "disconnectdb.php";
echo "</BODY>
</HTML>";

?>
