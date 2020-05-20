<?php

// *** Functions ***
function TampilkanDaftarTable() {
  $s = "select *
    from mastertable
    order by NamaTable";
  $r = _query($s);
  echo "<div class=bagian_kiri>";
  echo "<table class=box cellspacing=1 cellpadding=4 width=100%>
    <tr><td>&nbsp;</td><th class=ttl>Nama Table</th><td>&nbsp;</td></tr>";
  while ($w = _fetch_array($r)) {
    if ($w['ID'] == $_SESSION['IDTable']) {
      $ki = "&raquo;";
      $ka = "&laquo;";
      $c = "class=inp1";
    }
    else {
      $ki = ''; $ka = ''; $c = "class=ul";
    }
    echo "<tr><td width=5px>$ki</td>
      <td $c><a href='?mnux=master&IDTable=$w[ID]&NamaTable=$w[NamaTable]'>$w[Nama]</a></td>
      <td width=5px>$ka</td></tr>";
  }
  echo "</table></div>";
}
function GetTableProp($tbl, &$fld, &$typ, &$def) {
  $sf = "show columns from $tbl";
  $rf = _query($sf);
  while ($rwf = _fetch_array($rf)) {
    $fld[] = $rwf['Field'];
    $typ[] = $rwf['Type'];
    $def[] = $rwf['Default'];
  }
}
function DftrIsiTM($tm) {
  GetTableProp($tm['NamaTable'], $fld=array(), $typ=array(), $def=array());
  echo "<p><b>$tm[Nama]</b> &raquo; <a href=''>Tambah Data</a></p>";
  $s = "select *
    from $tm[NamaTable]
    order by $tm[Urut]";
  $r = _query($s);
  echo "<p><table class=box cellspacing=1 cellpadding=4>";
  // Tampilkan header
  echo "<tr>";
  for ($i=0; $i<sizeof($fld); $i++) echo "<th class=ttl>$fld[$i]</th>";
  echo "</tr>";

  // Tampilkan isi
  while ($w = _fetch_array($r)) {
    echo "<tr>";
    for ($i=0; $i < sizeof($fld); $i++) {
      $field = $fld[$i];
      if ($field == $tm['Kunci']) {
        echo "<td class=inp1 nowrap><a href='?mnux=master&gos=EditTM&ID=$tm[ID]&md=0&key=$w[$field]'><img src='img/edit.png'>
        $w[$field]</a></td>";
      }
      else {
        $isi = (empty($w[$field]))? '&nbsp;' : $w[$field];
        echo "<td class=ul>$isi</td>";
      }
    }
    echo "</tr>";
  }
  echo "</table></p>";
}
// untuk mengedit
function EditTM($tm) {
  $md = $_REQUEST['md']+0;
  GetTableProp($tm['NamaTable'], $fld=array(), $typ=array(), $def=array());
  $w = GetFields($tm['NamaTable'], $tm['Kunci'], $_REQUEST['Key'], '*');
  $NilaiKunci = $w[$tm['Kunci']];
  echo $NilaiKunci;
}

// *** Parameters ***
$IDTable = GetSetVar('IDTable');
$gos = (empty($_REQUEST['gos']))? 'DftrIsiTM' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Edit Master");
TampilkanDaftarTable();
if (!empty($IDTable)) {
  $tm = GetFields('mastertable', 'ID', $IDTable, '*');
  $gos($tm);
}
?>
