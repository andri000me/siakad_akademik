<?php

session_start();
include_once "../sisfokampus1.php";
HeaderSisfoKampus("Mhsw Pindahan");

// *** Parameters ***
$MhswID = GetSetVar('MhswID');

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'DftrKRSLama' : $_REQUEST['gos'];
$gos($MhswID, $mhsw);

// *** Functions ***
function DftrKRSLama($MhswID, $mhsw) {
  $s = "select k.*
    from krs k
    where k.MhswID = '$MhswID'
    order by k.TahunID";
  $r = _query($s);
  
  echo "<table class=bsc cellspacing=1 width=100%>
    <tr>
        <th class=ttl width=20>Edit</th>
        <th class=ttl width=20>#</th>
        <th class=ttl width=80>Kode &rsaquo; SKS</th>
        <th class=ttl>Matakuliah</th>
        <th class=ttl width=30>Nilai</th>
        </tr>";
  $thn = 'al;ksdjf;asdfkja;sdf';
  while ($w = _fetch_array($r)) {
    if ($thn != $w['TahunID']) {
      $thn = $w['TahunID'];
      echo "<tr>
        <td class=ul1 colspan=5><font size=+1>$thn</td>
        </tr>";
    }
    $n++;
    if ($w['Setara'] == 'Y') {
      $btn = "<input type=button name='btnEditKRS' value='e' onClick=\"\" />";
    }
    else {
      $btn = "<abbr title='Bukan hasil konversi'>&times;</a>";
    }
    echo "<tr>
      <td class=ul align=center>$btn</td>
      <td class=inp>$n</td>
      <td class=ul1>$w[MKKode]<sup>$w[SKS]</sup></td>
      <td class=ul1>$w[Nama]</td>
      <td class=ul1 align=center>$w[GradeNilai]</td>
      </tr>";
  }
  echo "</table>";
}

// *** Functions ***

?>

</BODY>
</HTML>
