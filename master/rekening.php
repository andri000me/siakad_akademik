<?php
// Author: Emanuel Setio Dewo
// 16 March 2006

// *** Functions ***
function DftrRek() {
  $s = "select *
    from rekening
    where KodeID='$_SESSION[KodeID]' ";
  $r = _query($s);
  $nomer = 0;
  $link = "<tr><td class=ul colspan=5>
    <input type=button name='TambahRek' value='Tambah Rekening'
      onClick=\"location='?mnux=$_SESSION[mnux]&gos=RekEdt&md=1'\" />
    <input type=button name='Refresh' value='Refresh Data'
      onClick=\"location='?mnux=$_SESSION[mnux]&gos='\" />
    </td></tr>";
  echo "<p><table class=box cellspacing=1 cellpadding=4 width=800>
    $link
    <tr><th class=ttl>No.</th>
    <th class=ttl>No. Rekening</th>
    <th class=ttl>Nama</th>
    <th class=ttl>Bank</th>
	<th class=ttl>Cabang</th>
    <th class=ttl>NA</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $nomer++;
    $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
    echo "<tr><td class=inp width=30>$nomer</td>
      <td $c nowrap>
        <input type=button name='Edit' value='&raquo;'
          onClick=\"location='?mnux=$_SESSION[mnux]&gos=RekEdt&md=0&rekid=$w[RekeningID]'\" />
        $w[RekeningID]</a></td>
      <td $c nowrap>$w[Nama]</td>
      <td $c nowrap>$w[Bank]</td>
	  <td $c nowrap>$w[Cabang]</td>
      <td $c align=center width=20><img src='img/book$w[NA].gif'></td>
      </tr>";
  }
  echo "</table></p>";
}
function RekEdt() {
  $md = $_REQUEST['md']+0;
  if ($md == 0) {
    $rekid = $_REQUEST['rekid'];
    $w = GetFields('rekening', 'RekeningID', $rekid, '*');
    $jdl = "Edit Rekening";
    $norek = "<input type=hidden name='RekeningID' value='$w[RekeningID]'><b>$w[RekeningID]</b>";
  }
  else {
    $w = array();
    $w['RekeningID'] = '';
    $w['Nama'] = '';
    $w['Bank'] = '';
    $w['NA'] = 'N';
    $jdl = "Tambah Rekening";
    $norek = "<input type=text name='RekeningID' value='$w[RekeningID]' size=50 maxlength=50>";
  }
  $na = ($w['NA'] == 'Y')? 'checked' : '';
  CheckFormScript("RekeningID,Nama,Bank");
  // Tampilkan
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST name='rekening' onSubmit='return CheckForm(this)' />
  <input type=hidden name='mnux' value='$_SESSION[mnux]' />
  <input type=hidden name='gos' value='RekSav' />
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='BypassMenu' value='1' />
  
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp nowrap>Nomer Rekening:</td>
      <td class=ul1>$norek</td></tr>
  <tr><td class=inp>Nama:</td>
      <td class=ul1><input type=text name='Nama' value='$w[Nama]' size=50 maxlength=50></td></tr>
  <tr><td class=inp>Nama Bank:</td>
      <td class=ul><input type=text name='Bank' value='$w[Bank]' size=50 maxlength=50></td></tr>
  <tr><td class=inp>Lokasi Cabang:</td>
      <td class=ul><input type=text name='Cabang' value='$w[Cabang]' size=50 maxlength=100></td></tr>
  <tr><td class=inp>Tidak aktif?</td>
      <td class=ul><input type=checkbox name='NA' value='Y' $na></td></tr>
  <tr><td class=ul colspan=2 align=center>
      <input type=submit name='Simpan' value='Simpan' />
      <input type=reset name='Reset' value='Reset' />
      <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=$_SESSION[mnux]'\" />
      </td></tr>
  </form>
  </table></p>";
}
function RekSav() {
  $md = $_REQUEST['md']+0;
  $RekeningID = $_REQUEST['RekeningID'];
  $Nama = sqling($_REQUEST['Nama']);
  $Bank = sqling($_REQUEST['Bank']);
  $Cabang = sqling($_REQUEST['Cabang']);
  $NA = empty($_REQUEST['NA'])? 'N' : $_REQUEST['NA'];
  if ($md == 0) {
    $s = "update rekening set Nama='$Nama', Bank='$Bank', Cabang='$Cabang', NA='$NA'
      where RekeningID='$RekeningID' ";
    $r = _query($s);
  }
  else {
    $ada = GetFields('rekening', 'RekeningID', $RekeningID, '*');
    if (empty($ada)) {
      $s = "insert into rekening (RekeningID, KodeID, Nama, Bank, Cabang, NA)
        values('$RekeningID', '$_SESSION[KodeID]', '$Nama', '$Bank', '$Cabang', '$NA')";
      $r = _query($s);
    }
    else echo ErrorMsg('Rekening Tidak Dapat Disimpan',
      "<p>Nomer rekening sudah ada. Berikut adalah data rekening tersebut:</p>
      <p><table class=box cellspacing=1 cellpadding=4>
      <tr><td class=inp1>Nomer Rekening</td><td class=ul>$ada[RekeningID]</td></tr>
      <tr><td class=inp1>Kode Institusi</td><td class=ul>$ada[KodeID]</td></tr>
      <tr><td class=inp1>Nama Pemilik</td><td class=ul>$ada[Nama]</td></tr>
      <tr><td class=inp1>Nama Bank</td><td class=ul>$ada[Bank]</td></tr>
      <tr><td class=inp1>Tidak aktif?</td><td class=ul><img src='img/book$ada[NA].gif'></td></tr>
      </table></p>");
  }
  BerhasilSimpan("?mnux=$_SESSION[mnux]", 10);
}


// *** Parameters ***
$gos = (empty($_REQUEST['gos']))? 'DftrRek' : $_REQUEST['gos'];

// *** Main ***
TampilkanJudul("Rekening $arrID[Nama]");
$gos();
?>
