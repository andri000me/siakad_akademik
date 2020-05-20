<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 23 Agustus 2008

// *** Parameters ***

// *** Main ***
TampilkanJudul("Master Pejabat");
$gos = (empty($_REQUEST['gos']))? 'DftrPejabat' : $_REQUEST['gos'];
$gos();

// *** functions ***
function DftrPejabat() {
  PejabatScript();
  $s = "Select p.*
    from pejabat p
    where p.KodeID = '".KodeID."'
    order by p.Urutan";
  $r = _query($s);
  $n = 0;

  echo "<p><table class=box cellspacing=1 align=center width=800>";
  echo "<tr>
    <td class=ul1 colspan=6>
    <input type=button name='Refresh' value='Refresh' onClick=\"location='?mnux=$_SESSION[mnux]'\" />
    <input type=button name='Tambah' value='Tambah Pejabat'
      onClick=\"javascript:PjbtEdit(1, 0)\" />
    </td>
    </tr>";
  echo "<tr>
    <th class=ttl width=20 colspan=2>#</th>
    <th class=ttl width=80>Kode</th>
    <th class=ttl width=200>Jabatan</th>
    <th class=ttl>Nama Pejabat</th>
    <th class=ttl width=80>NIP</th>
    <th class=ttl width=80>TTD</th>
    </tr>";
  while ($w = _fetch_array($r)) {
    $n++;
    $fn = "ttd/$w[KodeJabatan].ttd.gif";
    $img_ttd = (file_exists($fn))? "<img src='$fn' width=80 height=80 />" : '&times;';
    echo "<tr>
      <td class=inp width=10>$w[Urutan]</td>
      <td class=cna$w[NA] width=10>
        <a href='#' onClick=\"javascript:PjbtEdit(0, $w[PejabatID])\" /><img src='img/edit.png' /></a>
        </td>
      <td class=cna$w[NA] width=80>$w[KodeJabatan]&nbsp;</td>
      <td class=cna$w[NA] width=200>$w[Jabatan]&nbsp;</td>
      <td class=cna$w[NA]>$w[Nama]&nbsp;</td>
      <td class=cna$w[NA] width=80>$w[NIP]&nbsp;</td>
      <td class=cna$w[NA] width=80 align=center>
        $img_ttd
        <hr size=1 color=silver />
        <a href='?mnux=$_SESSION[mnux]&gos=GantiTTD&PID=$w[PejabatID]'>Ganti TTD</a>
        </td>
      </tr>";
  }
  echo "</table></p>";
}
function GantiTTD() {
  $MaxFileSize = 500000;
  $PID = $_REQUEST['PID'];
  $w = GetFields('pejabat', 'PejabatID', $PID, '*');
  echo <<<ESD
  <p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' enctype='multipart/form-data' method=POST>
  <input type=hidden name='MAX_FILE_SIZE' value='$MaxFileSize' />
  <input type=hidden name='gos' value='SimpanTTD' />
  <input type=hidden name='PID' value='$PID' />
  <input type=hidden name='BypassMenu' value='1' />
  <input type=hidden name='KodeJabatan' value='$w[KodeJabatan]' />
  
  <tr><td class=inp>Nama:</td>
      <td class=ul>$w[Nama]</td></tr>
  <tr><td class=inp>Jabatan:</td>
      <td class=ul>$w[Jabatan]</td></tr>
  
  <tr><td class=inp width=100>File Foto</td>
    <td class=ul><input type=file name='foto' size=35></td></tr>
  <tr><td class=ul colspan=2 align=center>
      File gambar tanda tangan yang bisa diupload hanya yang berformat <b>gif</b>.<br />
      Ukuran gambar maximal: <b>80&times;80px</b>
      </td></tr>
  <tr><td class=ul colspan=2 align=center>
    <input type=submit name='btnUpload' value='Upload File Foto' />
    <input type=button name='btnBatal' value='Batal' onClick="location='?mnux=$_SESSION[mnux]&gos='" />
    </td></tr>
  </form></table></p>
ESD;
}
function SimpanTTD() {
  $PID = $_REQUEST['PID'];
  $KodeJabatan = sqling($_REQUEST['KodeJabatan']);
  
  $upf = $_FILES['foto']['tmp_name'];
  $arrNama = explode('.', $_FILES['foto']['name']);
  $tipe = $_FILES['foto']['type'];
  $arrtipe = explode('/', $tipe);
  $extensi = $arrtipe[1];
  if ($extensi != 'gif')
    die(ErrorMsg("Error",
      "File tanda tangan yang bisa diupload hanya yang berformat gif.<br />
      Hubunti Sysadmin untuk informasi lebih lanjut.
      <hr size=1 color=silver />
      <input type=button name='btnKembali' value='Kembali' onClick=\"location='?mnux=$_SESSION[mnux]&gos='\" />"));
  $dest = "ttd/" . $KodeJabatan . '.ttd.gif';
  //echo $dest;
  if (move_uploaded_file($upf, $dest)) {
    $_rand = rand();
    BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=&_rnd=".$_rand, 1);
  }
  else echo ErrorMsg("Gagal Upload Foto",
    "Tidak dapat meng-upload file foto.<br />
    Periksa file yg di-upload, karena besar file dibatasi cuma: <b>$_REQUEST[MAX_FILE_SIZE]</b> byte.");
  //print_r($_FILES);
}
function PejabatScript() {
  echo <<<SCR
  <script>
  function PjbtEdit(md, id) {
    lnk = "$_SESSION[mnux].edit.php?md="+md+"&id="+id;
    win2 = window.open(lnk, "", "width=400, height=400, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  </script>
SCR;
}
?>
