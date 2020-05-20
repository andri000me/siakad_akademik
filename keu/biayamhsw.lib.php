<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 08 Sept 2008


function SetBIPOTID($MhswID, $ProdiID, $ProgramID) {
  $bipot = GetaField('bipot', "KodeID='".KodeID."' and NA='N' and `Def`='Y' 
    and ProgramID='$ProgramID' and ProdiID",
    $ProdiID, 'BIPOTID')+0;

  if ($bipot == 0) {
    echo "
    <table class=box width=100%>
    <tr><th class=wrn>Data master BIPOT tidak ditemukan. Hubungi Ka BAA/BAK</th></tr>
    </table>";
  }
  else {
    $s = "update mhsw set BIPOTID = '$bipot' where KodeID='".KodeID."' and MhswID='$MhswID' ";
    $r = _query($s);
    echo "
    <table class=box width=100%>
    <tr><th class=ttl>Data bipot telah diupdate secara otomatis.</th></tr>
    </table>";
  }
  return $bipot;
}
function VirtualBipotMhsw($MhswID, $BIPOTID) {
  // Ambil BIPOT-nya
  $s = "select * 
    from bipot2 
    where BIPOTID = '$BIPOTID'
      and Otomatis = 'Y'
      and NA = 'N'
    order by TrxID, Prioritas";
  $r = _query($s);
  $total = 0;
  $mhsw = GetFields('mhsw', "KodeID='".KodeID."' and MhswID", $MhswID,
    "BIPOTID, GradeNilai, StatusMhswID, StatusAwalID");
  while ($w = _fetch_array($r)) {
    $oke = true;
    // Apakah sesuai dengan status awalnya?
    $pos = strpos($w['StatusAwalID'], ".".$mhsw['StatusAwalID'].".");
    $oke = $oke && !($pos === false);

    // Apakah grade-nya?
    if ($oke) {
      if ($w['GunakanGradeNilai'] == 'Y') {
        $pos = strpos($w['GradeNilai'], ".".$mhsw['GradeNilai'].".");
        $oke = $oke && !($pos === false);
      }
    }
    
    // Simpan data
    if ($oke) {
      // Cek apakah memakai script atau tidak?
      if ($w['GunakanScript'] == 'Y') {
        // BipotGunakanScript($pmb, '', $w, $ada, 0);
      }
      // Jika tidak perlu pakai script
      else {
        // Jika tidak ada duplikasi, maka akan di-insert. Tapi jika sudah ada, maka dicuekin aja.
        $total += $w['Jumlah'];
      } // end else
    }   // end if $oke
  }     // end while
  return $total;
}
?>
