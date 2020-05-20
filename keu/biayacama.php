<?php

// *** Parameters ***
$Tahun = GetSetVar('Tahun');
$Semester = GetSetVar('Semester');
$KodeInstitusi = GetSetVar('KodeInstitusi', GetaField('identitas', "Kode", KodeID, "KodeInstitusi"));
$KodePembayaran = GetSetVar('KodePembayaran', 'SPP');

// *** Main ***
TampilkanJudul("File Bank Biaya Cama");
$gos = (empty($_REQUEST['gos']))? 'HeaderBiayaCama' : $_REQUEST['gos'];
$gos = str_replace(' ', '', $gos);
$gos($Tahun, $Semester, $KodeInstitusi, $KodePembayaran);


// *** Functions ***
function HeaderBiayaCama($Tahun, $Semester, $KodeInstitusi, $KodePembayaran) {
  $optsmt = GetOption2('semester', "concat(Semester, ' - ', Nama)", 'Semester', $Semester, '', 'Semester');
  CheckFormScript("Tahun,Semester,KodeInstitusi,KodePembayaran");
  echo "<p>
  <table class=box cellspacing=1 align=center>
  <form name='frmFileBank' action='?' method=POST onSubmit='return CheckForm(this)'>

  <tr><td class=wrn width=2 rowspan=3></td>
      <td class=inp>Kode Institusi:</td>
      <td class=ul1><input type=text name='KodeInstitusi' value='$KodeInstitusi' size=5 maxlength=10 /></td>
      <td class=inp>Kode Pembayaran:</td>
      <td class=ul1><input type=text name='KodePembayaran' value='$KodePembayaran' size=10 maxlength=20 /></td>
      </tr>
  <tr>
      <td class=inp>Gelombang:</td>
      <td class=ul1><input type=text name='Tahun' value='$Tahun' size=4 maxlength=4 /></td>
      <td class=inp>Semester:</td>
      <td class=ul1><select name='Semester'>$optsmt</select></td>
      </tr>
  <tr><td class=ul1 colspan=7>
      <input type=submit name='gos' value='Buat File Bank' />
      <input type=submit name='gos' value='Upload File dari Bank' />
      </td></tr>
  
  </form>
  </table>
  </p>";
}

function BuatFileBank($Tahun, $Semester, $KodeInstitusi, $KodePembayaran) {
  $s = "select p.PMBID, p.Nama
    from pmb p
    where p.KodeID = '".KodeID."' and p.PMBPeriodID = '".$Tahun.$Semester."'
      and (p.MhswID is NULL or p.MhswID = '')
    order by p.PMBID";

  //echo "<pre>$s</pre>";
  $r = _query($s);
  $n = 0;
  while ($w = _fetch_array($r)) {
    $n++;
    $_SESSION['_pmbPMBID_'.$n] = $w['PMBID'];
    $_SESSION['_pmbNama_'.$n] = $w['Nama'];
  }
  $_SESSION['_pmbJumlah'] = $n;
  $_SESSION['_pmbProses'] = 1;
  $_SESSION['_pmbTahun'] = $Tahun;
  $_SESSION['_pmbSemester'] = $Semester;
  $_SESSION['_pmbKodeInstitusi'] = $KodeInstitusi;
  $_SESSION['_pmbKodePembayaran'] = $KodePembayaran;
  
  // Parameter
  $tgl = date('dmY');
  $jml = str_pad($n, 5, '0', STR_PAD_LEFT);
  // Buat file penampung
  $fn = "tmp/biaya_".date('dmY').".txt";
  $_SESSION['_pmbFile'] = $fn;
  $f = fopen($fn, 'w');
  fwrite($f, "$KodeInstitusi|$KodePembayaran|$tgl|$jml\n");
  
  fclose($f);
  // Tampilkan Proses
  echo <<<SCR
  <p>
  <table class=box cellspacing=1 align=center width=500>
  <tr><td class=ttl>Sedang Memproses:</td></tr>
  <tr><td class=ul1 height=100>
      <iframe src='$_SESSION[mnux].proses.php' frameborder=0 width=100%>
      ...
      </iframe>
      </td>
      </tr>
  </table>
  </p>

  <script>_ProsesPMB()</script>
  
SCR;
}

function UploadFiledariBank($Tahun, $Semester, $KodeInstitusi, $KodePembayaran) {
  $max_file_size = 9000000;
  echo <<<FRM
  <p><table class=box cellspacing=1 align=center>
  <form enctype="multipart/form-data" action="?" method="POST">
  <input type=hidden name='MAX_FILE_SIZE' value='$max_file_size' />
  
  <tr><td class=inp>Nama File:</td>
      <td class=ul1><input type="file" name="filebank" size=40 /></td>
      </tr>
  <tr><td class=inp>Opsi:</td>
      <td class=ul1>
      <input type=submit name='gos' value='Upload File Bank' />
      <input type=button name='Batal' value='Batal'
        onClick="location='?mnux=$_SESSION[mnux]&gos='" />
      </td>
      </tr>
  
  </form>
  </table></p>
FRM;
}

function UploadFileBank() {
  $fn = "tmpbank/". basename($_FILES['filebank']['name']);

  if (move_uploaded_file($_FILES['filebank']['tmp_name'], $fn)) {
    ProsesFileBank($fn);
  }
  else {
    echo ErrorMsg('Error',
      "File tidak dapat diupload.<br />
      Ada kemungkinan ini adalah serangan upload.<br />
      Hubungi Sysadmin untuk informasi lebih lanjut.
      <hr size=1 color=silver />
      <input type=button name='Kembali' value='Kembali'
        onClick=\"location='?mnux=$_SESSION[mnux]&gos='\" />");
  }
}
function ProsesFileBank($fn) {
  $f = fopen($fn, 'r');
  $isi = fread($f, filesize($fn));
  fclose($f);
  // extract isinya
  $a = explode("\n", $isi);
  // parsing isinya
  $n = 0;
  foreach ($a as $_a) {
    $_a = TRIM($_a);
    if (!empty($_a)) {
      $n++;
      $_SESSION['_up_'.$n] = $_a;
      //echo "<pre>$_a</pre>\n";
    }
  }
  $_SESSION['_upJumlah'] = $n;
  $_SESSION['_upProses'] = 1;
  echo <<<SCR
  <p>
  <table class=box cellspacing=1 align=center width=500>
  <tr><th class=ttl>Sedang Memproses: ($_SESSION[_upJumlah])</th></tr>
  <tr><td class=ul1 height=300>
      <iframe src='$_SESSION[mnux].upload.php' frameborder=0 width=100% height=100%>
      ...
      </iframe>
      </td>
      </tr>
  </table>
  </p>
  
SCR;
}
?>
