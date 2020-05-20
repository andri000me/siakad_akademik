<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 03 Mei 2008

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("USM");
echo $_Themes;

// *** Parameters ***
$md = $_REQUEST['md']+0;
$id = sqling($_REQUEST['id']);
$bck = sqling($_REQUEST['bck']);

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'Edit' : $_REQUEST['gos'];
$gos($md, $id, $bck);

// *** Functions ***
function Edit($md, $id, $bck) {
  if ($md == 0) {
    $jdl = "Edit Komponen";
    $w = GetFields('pmbsyarat', "KodeID='".KodeID."' and PMBSyaratID", $id, "*");
    $_sya = "<input type=hidden name='id' value='$w[PMBSyaratID]' /><b>$w[PMBSyaratID]</b>";
  }
  elseif ($md == 1) {
    $jdl = "Tambah Komponen";
    $w = array();
    $w['NA'] = 'N';
    $_sya = "<input type=text name='id' size=10 maxlength=10 />";
  }
  else die(ErrorMsg('Error', "Mode edit tidak dikenali."));
  
  TampilkanJudul($jdl);
  // Parameters
  $na = ($w['NA'] == 'Y')? 'checked' : '';
  $prodi = AmbilProdi($w['ProdiID']);
  echo "<p><table class=bsc cellspacing=1 align=center width=100%>
  <form action='../$_SESSION[mnux].pras.edit.php' method=POST>
  <input type=hidden name='gos' value='Simpan' />
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='bck' value='$bck' />
  
  <tr><td class=inp>Kode:</td>
      <td class=ul1>$_sya</td>
      </tr>
  <tr><td class=inp>Prasyarat:</td>
      <td class=ul1><input type=text name='Nama' value='$w[Nama]'
        size=30 maxlength=50 /></td>
      </tr>
  <tr><td class=inp>Prodi:</td>
      <td class=ul1>
      $prodi
      </td></tr>
  <tr><td class=inp>Keterangan:</td>
      <td class=ul1>
        <textarea name='Keterangan' cols=50 rows=4>$w[Keterangan]</textarea>
      </td></tr>
  <tr><td class=inp>NA (tidak aktif)?</td>
      <td class=ul1>
      <input type=checkbox name='NA' value='Y' $na /> *) Beri centang jika tidak aktif
      </td>
      </tr>
  
  <tr><td class=ul1 colspan=2 align=center>
      <input type=submit name='Simpan' value='Simpan' />
      <input type=button name='Batal' value='Batal'
        onClick=\"window.close()\" />
      </td>
      </tr>
  </form>
  </table></p>";
}

function Simpan($md, $id, $bck) {
  TutupScript();
  $Nama = sqling($_REQUEST['Nama']);
  $p = array();
  $p = $_REQUEST['ProdiID'];
  if (!empty($p)) {
    $ProdiID = implode('.', $p);
    $ProdiID = (empty($ProdiID))? '' : '.'.$ProdiID.'.';
  }
  else $ProdiID = '';
  
  $Keterangan = sqling($_REQUEST['Keterangan']);
  $NA = (empty($_REQUEST['NA']))? 'N' : 'Y';

  if ($md == 0) {
    $s = "update pmbsyarat
      set Nama = '$Nama',
          ProdiID = '$ProdiID',
          Keterangan = '$Keterangan',
          NA = '$NA',
          LoginEdit = '$_SESSION[_Login]',
          TanggalEdit = now()
      where KodeID = '".KodeID."' and PMBSyaratID = '$id' ";
    $r = _query($s);
    echo "<script>ttutup('$_SESSION[mnux]');</script>";
  }
  elseif ($md == 1) {
    // Cek ID-nya dulu
    $ada = GetFields('pmbsyarat', "KodeID='".KodeID."' and PMBSyaratID", $id, '*');
    if (!empty($ada))
      die(ErrorMsg('Error', "Syarat dengan kode <b>$id</b> sudah ada.<br />
        Gunakan kode yang lain.
        <hr size=1 color=silver />
        <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" />"));
    $s = "insert into pmbsyarat
      (PMBSyaratID, KodeID, Nama, ProdiID,
      Keterangan, LoginBuat, TanggalBuat, NA)
      values
      ('$id', '".KodeID."', '$Nama', '$ProdiID',
      '$Keterangan', '$_SESSION[_Login]', now(), '$NA')";
    $r = _query($s);
    echo "<script>ttutup('$_SESSION[mnux]');</script>";
  }
  else die(ErrorMsg('Error', "Mode edit tidak ditemukan."));
}

function AmbilProdi($sel) {
  $s = "select ProdiID, Nama
    from prodi
    where KodeID = '".KodeID."'
    order by ProdiID";
  $r = _query($s);
  $a = '';
  while ($w = _fetch_array($r)) {
    $pos = strpos($sel, ".$w[ProdiID].");
    $ck = ($pos === false)? '' : 'checked';
    $a .= "<input type=checkbox name='ProdiID[]' value='$w[ProdiID]' $ck>$w[ProdiID]. $w[Nama]<br />";
  }
  return $a;
}

function TutupScript() {
echo <<<SCR
<SCRIPT>
  function ttutup(bck) {
    opener.location='../index.php?mnux='+bck;
    self.close();
    return false;
  }
</SCRIPT>
SCR;
}

?>
