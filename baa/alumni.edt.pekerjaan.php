<?php

session_start();
include_once "../sisfokampus1.php";
HeaderSisfoKampus("Edit Pekerjaan");

// *** Parameters ***
$mhswid = GetSetVar('mhswid');

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'edtPekerjaan' : $_REQUEST['gos'];
$gos();

function edtPekerjaan() {
  $mhswid = sqling($_REQUEST['mhswid']);
  $md = $_REQUEST['md']+0;
  
  if ($md == 0) {
    $akid = $_REQUEST['akid']+0;
    $w = GetFields('alumnikerja', 'AlumniKerjaID', $akid, '*');
    $jdl = "Edit Pekerjaan";
	$statuskerjaT = ($w['StatusKerja']=='T'? "selected":'');
	$statuskerjaK = ($w['StatusKerja']=='K'? "selected":'');
	$statuskerjaN = ($w['StatusKerja']=='N'? "selected":'');
  }
  else {
    $w = array();
    $w['MulaiKerja'] = date('Y-m-d');
    $w['KeluarKerja'] = date('Y-m-d');
    $jdl = "Tambah Pekerjaan";
  }
  TampilkanJudul($jdl);
  $tglMulai = GetDateOption($w['MulaiKerja'], 'mul');
  $tglKeluar = GetDateOption($w['KeluarKerja'], 'kel');
  
  CheckFormScript("Nama,Jabatan,Kota");
  echo <<<ESD
  <table class=bsc cellspacing=1 width=100%>
  <form action='?' method=POST onSubmit="return CheckForm(this)">
  <input type=hidden name='mhswid' value='$mhswid' />
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='akid' value='$akid' />
  <input type=hidden name='gos' value='savPekerjaan' />
  
  <tr><td class=inp width=70>Mulai Bekerja:</td>
      <td class=ul>$tglMulai</td>
      <td class=inp width=70>Keluar:</td>
      <td class=ul>$tglKeluar</td>
      </tr>
  <tr><td class=inp>Perusahaan:</td>
      <td class=ul colspan=3>
      <input type=text name='Nama' value='$w[Nama]' size=40 maxlength=50 />
      </td></tr>
  <tr><td class=inp>Jabatan:</td>
      <td class=ul colspan=3>
      <input type=text name='Jabatan' value='$w[Jabatan]' size=40 maxlength=50 />
      </td></tr>
  <tr><td class=inp>Alamat:</td>
      <td class=ul colspan=3>
      <textarea name='Alamat' cols=40 rows=3>$w[Alamat]</textarea>
      </td></tr>
  <tr><td class=inp>Kota:</td>
      <td class=ul>
      <input type=text name='Kota' value='$w[Kota]' size=20 maxlength=50 />
      </td>
      <td class=inp>Kode Pos:</td>
      <td class=ul>
      <input type=text name='KodePos' value='$w[KodePos]' size=20 maxlength=50 />
      </td>
      </tr>
  <tr><td class=inp>Propinsi:</td>
      <td class=ul>
      <input type=text name='Propinsi' value='$w[Propinsi]' size=20 maxlength=50 />
      </td>
      <td class=inp>Negara:</td>
      <td class=ul>
      <input type=text name='Negara' value='$w[Negara]' size=20 maxlength=50 />
      </td>
      </tr>
  <tr><td class=inp>Telp/Fax:</td>
      <td class=ul>
      <input type=text name='Telepon' value='$w[Telepon]' size=10 maxlength=50 />/
      <input type=text name='Facsimile' value='$w[Facsimile]' size=10 maxlength=50 />
      </td>
      <td class=inp>Website:</td>
      <td class=ul>
      <input type=text name='Website' value='$w[Website]' size=20 maxlength=50 />
      </td>
      </tr>
   <tr><td class=inp>Gaji Pertama:</td>
      <td class=ul colspan=3>
      <input type=text name='GajiPertama' size=15 value='$w[GajiPertama]'>
      </td></tr>
  <tr><td class=inp>Status Kerja:</td>
      <td class=ul colspan=3>
      <select name='StatusKerja'>
	  		<option value=''></option>
			<option value='T' $statuskerjaT>Tetap</option>
			<option value='K' $statuskerjaK>Kontrak</option>
			<option value='N' $statuskerjaN>Tanpa Status</option>
		</select>
      </td></tr>
  <tr><td class=ul colspan=4 align=center>
      <input type=submit name='btnSimpan' value='Simpan' />
      <input type=button name='btnBatal' value='Batal'
        onClick="javascript:parent.toggleBox('divPekerjaan', 0);" />
      </td></tr>
      
  </form>
  </table>
  
  <script>
  </script>
ESD;
}
function savPekerjaan() {
  $mhswid = sqling($_REQUEST['mhswid']);
  $md = $_REQUEST['md']+0;
  $akid = $_REQUEST['akid']+0;
  
  $tglMulai = "$_REQUEST[mul_y]-$_REQUEST[mul_m]-$_REQUEST[mul_d]";
  $tglKeluar = "$_REQUEST[kel_y]-$_REQUEST[kel_m]-$_REQUEST[kel_d]";
  $Nama = sqling($_REQUEST['Nama']);
  $Jabatan = sqling($_REQUEST['Jabatan']);
  $Alamat = sqling($_REQUEST['Alamat']);
  $Kota = sqling($_REQUEST['Kota']);
  $KodePos = sqling($_REQUEST['KodePos']);
  $Propinsi = sqling($_REQUEST['Propinsi']);
  $Negara = sqling($_REQUEST['Negara']);
  $Telepon = sqling($_REQUEST['Telepon']);
  $Facsimile = sqling($_REQUEST['Facsimile']);
  $Website = sqling($_REQUEST['Website']);
  $GajiPertama = sqling($_REQUEST['GajiPertama']);
  $StatusKerja = sqling($_REQUEST['StatusKerja']);
  
  if ($md == 0) {
    $s = "update alumnikerja
      set Nama = '$Nama', Jabatan = '$Jabatan', Alamat = '$Alamat',
          Kota = '$Kota', KodePos = '$KodePos', Propinsi = '$Propinsi', Negara = '$Negara',
          Telepon = '$Telepon', Facsimile = '$Facsimile', Website = '$Website', GajiPertama='$GajiPertama',
          MulaiKerja = '$tglMulai', KeluarKerja = '$tglKeluar',StatusKerja='$StatusKerja',
          TanggalEdit = now(), LoginEdit = '$_SESSION[_Login]'
      where AlumniKerjaID = '$akid' ";
    $r = _query($s);
    Tutupin();
  }
  elseif ($md == 1) {
    $s = "insert into alumnikerja
      (MhswID, KodeID, MulaiKerja, KeluarKerja,
      Nama, Jabatan, Alamat, Kota, KodePos, Propinsi, Negara,
      Telepon, Facsimile, Website,
      TanggalBuat, LoginBuat)
      values
      ('$mhswid', '".KodeID."', '$tglMulai', '$tglKeluar',
      '$Nama', '$Jabatan', '$Alamat', '$Kota', '$KodePos', '$Propinsi', '$Negara',
      '$Telepon', '$Facsimile', '$Website',
      now(), '$_SESSION[_Login]')";
    $r = _query($s);
    Tutupin();
  }
}
function Tutupin() {
  echo <<<ESD
  <script>
  parent.toggleBox('divPekerjaan', 0);
  parent.location = "../index.php?mnux=$_SESSION[mnux]&gos=";
  </script>
ESD;
}

?>

</body>
</html>
