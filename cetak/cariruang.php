<?php
// Author: Emanuel Setio Dewo
// Start: 13 March 2006
include "../sisfokampus.php";
$prodi = $_REQUEST['prodi'];
$arrrg = TRIM($_REQUEST['arrrg'], '.');
$arrrg = (empty($arrrg))? '.' : '.'.$arrrg.'.';


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
    creator.data.RuangID.value = frm.arrayruang.value;
    window.close();
  }

  function UbahRuang(nm){
    ck = "";
    if (nm.checked == true) {
      var nilai = ruang.arrayruang.value;
      if (nilai.match(nm.value+".") != nm.value+".") ruang.arrayruang.value += nm.value + ".";
    }
    else {
      var nilai = ruang.arrayruang.value;
      ruang.arrayruang.value = nilai.replace(nm.value+".", "");
    }
  }
  //-->
  </script>
END;


// Tampilkan Ruang
TampilkanJudul("Daftar Ruang");
$s = "select r.RuangID, r.Nama, r.Kapasitas, r.KampusID,
  r.Lantai, LEFT(r.Keterangan, 50) as KET
  from ruang r
  where INSTR(ProdiID, '.$prodi.')>0
    and r.NA='N'
  order by r.KampusID, r.RuangID";
$r = _query($s);
$hdr = "<tr><th class=ttl>Pakai</th>
  <th class=ttl>Kode</th>
  <th class=ttl>Nama</th>
  <th class=ttl>Lantai</th>
  <th class=ttl>Kapasitas</th>
  <th class=ttl>Keterangan</th>
  </tr>";
echo "<form action='' name='ruang' method=POST>
  <p><table class=box cellspacing=1 cellpadding=4>
  <tr><td class=inp1>Ruang yg dipakai:</td>
  <td class=ul><input type=text name='arrayruang' value='$arrrg' size=30 maxlength=200>
  <input type=button name='Gunakan' value='Gunakan Ruang' onClick='javascript:Kembalikan(ruang)'>
  <input type=button name='Batal' value='Batal' onClick='javascript:window.close()'>
  </td></tr>
  <tr><td colspan=2 class=ul>Tiap kode ruang diapit oleh tanda '.'</td></tr>
  </table></p>

  <p><table class=box cellspacing=1 cellpadding=4>";
$kampus = '';
while ($w = _fetch_array($r)) {
  if ($kampus <> $w['KampusID']) {
    $kampus = $w['KampusID'];
    $namakampus = GetaField('kampus', 'KampusID', $kampus, 'Nama');
    echo "<tr><td class=ul colspan=6><b>$namakampus</b></td></tr>";
    echo $hdr;
  }
  $ck = (strpos($arrrg, '.'.$w['RuangID'].'.') === false)? '' : 'checked';
  $ket = str_replace(chr(13), ", ", $w['KET']);
  echo "<tr>
    <td class=ul><input type=checkbox name='$w[RuangID]' value='$w[RuangID]' $ck onChange='javascript:UbahRuang(ruang.$w[RuangID])'></td>
    <td class=ul>$w[RuangID]</td>
    <td class=ul>$w[Nama]</td>
    <td class=ul>$w[Lantai]</td>
    <td class=ul align=right>$w[Kapasitas]</td>
    <td class=ul>$ket&nbsp;</td>
    </tr>";
}
echo "</table></form></p>";


// Tampilkan footer
include_once "disconnectdb.php";
echo "</BODY>
</HTML>";

?>
