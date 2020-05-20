<div class="container">
<?php
$_arrUsr = array(100, 120);
$KodeID = GetSetVar('KodeID');
$LevelID = GetSetVar('LevelID', 1);

// *** Main ***
TampilkanJudul('Administrasi User');
$gos = (empty($_REQUEST['gos']))? 'DftrUsr' : $_REQUEST['gos'];
$gos();


// *** Functions ***
function DftrUsr() {
  global $_arrUsr;
  $_arr = implode(',', $_arrUsr);
  if (!empty($_SESSION['LevelID'])) {
    $optlvl = GetOption2('level', "concat(LevelID, '. ', Nama)", 'LevelID', $_SESSION['LevelID'], 
      "Tampak = 'Y' and Accounting = 'N' and not (LevelID in ($_arr))", 'LevelID');
    $a = "<p>
      <table class='table table-striped' width=700>
      <form action='?' method=POST>
      <input type=hidden name='mnux' value='$_SESSION[mnux]' />
      <tr><td class=ul1 colspan=10>
          Level: <select name='LevelID' onChange='this.form.submit()' class='form-control'>$optlvl</select>
          <a href='?mnux=$_SESSION[mnux]&gos=UsrEdt&LevelID=$_SESSION[LevelID]&md=1'>[+ Tambah User]</a>
          <a href='?mnux=$_SESSION[mnux]&gos='>[Refresh]</a>
          </td>
          </form></tr>

      <tr><th class=ttl>#</th>
          <th class=ttl>Kode</th>
          <th class=ttl>Nama</th>
          <th class=ttl>Program Studi</th>
          <th class=ttl>NA</th>
          <th class=ttl>DEL</th>
      </tr>";
    $TabelUser = GetaField('level', 'LevelID', $_SESSION['LevelID'], 'TabelUser');
    $s = "select t.Login, t.Nama, t.ProdiID, t.NA
      from $TabelUser t
      where LevelID='$_SESSION[LevelID]' and KodeID='$_SESSION[KodeID]'
      order by t.Login";
    $r = _query($s);
    $n = 0;
    
    while ($w = _fetch_array($r)) {
      $n++;
      $c = ($w['NA'] == 'Y')? 'class=nac' : 'class=ul';
      $a .= "<tr>
        <td class=inp width=20>$n</td>
        <td $c><a href='?mnux=$_SESSION[mnux]&gos=UsrEdt&LevelID=$_SESSION[LevelID]&md=0&Lgn=$w[Login]' class='btn btn-info'>
        $w[Login]</a></td>
        <td $c>$w[Nama]</td>
        <td $c>$w[ProdiID]&nbsp;</td>
        <td $c align=center width=5><img src='img/book$w[NA].gif'></td>
	<td $c align=center width=5><a href='?mnux=$_SESSION[mnux]&gos=dltUsrCek&LevelID=$_SESSION[LevelID]&Lgn=$w[Login]'><img src='img/del.gif'></td>
        </tr>";
    }
    echo $a ."</table></p>";
  }
}
function UsrEdt() {
  $md = $_REQUEST['md'] +0;
  $TabelUser = GetFields('level', 'LevelID', barasiah($_REQUEST['LevelID']), 'Nama, TabelUser');
  if ($md == 0) {
    $w = GetFields($TabelUser['TabelUser'], 'Login', barasiah($_REQUEST['Lgn']), '*');
    $jdl = "Edit User: $TabelUser[Nama]";
    $strlogin = "<input type=hidden name='Login' value='$w[Login]'><b>$w[Login]</b>";
  }
  else {
    $w = array();
    $w['Login'] = '';
    $w['Nama'] = '';
    $w['LevelID'] = $_REQUEST['LevelID'];
    $w['Telephone'] = '';
    $w['Password'] = '';
    $w['Handphone'] = '';
    $w['Email'] = '';
    $w['Alamat'] = '';
    $w['Kota'] = '';
    $w['Propinsi'] = '';
    $w['Negara'] = '';
    $w['ProdiID'] = '';
    $w['NA'] = 'N';
    $jdl = "Tambah User: $TabelUser[Nama]";
    $strlogin = "<input type=text name='Login' value='' size=30 maxlength=20>";
  }
  $na = ($w['NA'] == 'Y')? 'checked' : '';
  $snm = session_name(); $sid = session_id();
  $cb_prodi = GetCheckboxes('prodi', 'ProdiID', "concat(ProdiID, ' - ', Nama) as PRD", 'PRD', $w['ProdiID'], ',');
  $c1 = 'class=inp'; $c2 = 'class=ul';
  // tampilkan
  CheckFormScript('Login,Nama');
    echo $_SESSION['_Login'];
  echo "<p><table class=box cellspacing=1 cellpadding=4>
  <form action='?' method=POST onSubmit='return CheckForm(this)'>
  <input type=hidden name='mnux' value='$_SESSION[mnux]' />
  <input type=hidden name='BypassMenu' value='1' />
  <input type=hidden name='gos' value='UsrSav' />
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='LevelID' value='$_REQUEST[LevelID]' />
  <input type=hidden name='OldPwd' value='$w[Password]' />
  
  <tr><th class=ttl colspan=3>$jdl</th></tr>
  <tr><td $c1>Kode Login</td>
      <td $c2>$strlogin</td>
      <th class=ttl>Hak Akses Prodi:</th>
      </tr>
  <tr><td $c1>Nama User</td>
      <td $c2><input type=text name='Nama' value='$w[Nama]' size=40 maxlength=50></td>
      <td $c2 rowspan=12 valign=top>$cb_prodi</td>
      </tr>
  <tr><td $c1>Password</td><td $c2><input type=password name='Password' value='$w[Password]' size=20 maxlength=10></td></tr>

  <tr><td $c1>Telepon</td><td $c2><input type=text name='Telephone' value='$w[Telephone]' size=40 maxlength=50></td></tr>
  <tr><td $c1>Handphone</td><td $c2><input type=text name='Handphone' value='$w[Handphone]' size=40 maxlength=50></td></tr>
  <tr><td $c1>Email</td><td $c2><input type=text name='Email' value='$w[Email]' size=40 maxlength=50></td></tr>
  <tr><td $c1>Alamat</td><td $c2><textarea name='Alamat' cols=30 rows=4>$w[Alamat]</textarea></td></tr>
  <tr><td $c1>Kota</td><td $c2><input type=text name='Kota' value='$w[Kota]' size=40 maxlength=50></td></tr>
  <tr><td $c1>Propinsi</td><td $c2><input type=text name='Propinsi' value='$w[Propinsi]' size=40 maxlength=50></td></tr>
  <tr><td $c1>Negara</td><td $c2><input type=text name='Negara' value='$w[Negara]' size=40 maxlength=50></td></tr>
  <tr><td $c1>NA (tidak aktif)?</td><td $c2><input type=checkbox name='NA' value='Y' $na></td></tr>
  <tr><td $c2 colspan=2 align=center>
      <input type=submit name='Simpan' value='Simpan'>
      <input type=reset name='Reset' value='Reset'>
      <input type=button name='Batal' value='Batal' onClick=\"location='?mnux=sysusr'\">
      </td></tr>

  </form></table></p>";
}
function UsrSav() {
  $md = $_REQUEST['md']+0;
  $TabelUser = GetFields('level', 'LevelID', $_REQUEST['LevelID'], 'Nama, TabelUser');
  $Login = $_REQUEST['Login'];
  $Nama = sqling($_REQUEST['Nama']);
  $Password = $_REQUEST['Password'];
  $OldPwd = $_REQUEST['OldPwd'];
  $Telephone = $_REQUEST['Telephone'];
  $Handphone = $_REQUEST['Handphone'];
  $Email = $_REQUEST['Email'];
  $Alamat = sqling($_REQUEST['Alamat']);
  $Kota = sqling($_REQUEST['Kota']);
  $Propinsi = sqling($_REQUEST['Propinsi']);
  $Negara = sqling($_REQUEST['Negara']);
  $arrProdiID = array();
  $arrProdiID = $_REQUEST['ProdiID'];
  $ProdiID = (empty($arrProdiID))? '' : implode(',', $arrProdiID);
  $NA = (empty($_REQUEST['NA']))? 'N' : $_REQUEST['NA'];
  if ($md == 0) {
    $pwd = ($OldPwd == $Password)? '' : ", Password=PASSWORD('$Password')";
    $s = "update $TabelUser[TabelUser] set Nama='$Nama', Telephone='$Telephone', Handphone='$Handphone',
      Email='$Email', Alamat='$Alamat', Kota='$Kota', Propinsi='$Propinsi', Negara='$Negara', NA='$NA',
      ProdiID='$ProdiID' $pwd
      where Login='$Login' ";
    $r = _query($s);
    if (!empty($pwd)) echo Konfirmasi('Perubahan Password', 'Telah terjadi perubahan password.').'<br>';
    BerhasilSimpan("?mnux=$_SESSION[mnux]", 1);
  }
  else {
    $ada = GetFields($TabelUser['TabelUser'], 'Login', $Login, 'Login, Nama');
    if (empty($ada)) {
      $s = "insert into $TabelUser[TabelUser] (Login, KodeID, LevelID, Nama, Password, Telephone, Handphone, Email, 
        Alamat, Kota, Propinsi, Negara, ProdiID, NA)
        values('$Login', '$_SESSION[KodeID]', '$_REQUEST[LevelID]', '$Nama', PASSWORD('$Password'), '$Telephone', '$Handphone', '$Email',
        '$Alamat', '$Kota', '$Propinsi', '$Negara', '$ProdiID', '$NA')";
      $r = _query($s);
      BerhasilSimpan("?mnux=$_SESSION[mnux]", 1);
    }
    else echo ErrorMsg('Gagal Simpan', "Kode Login: <b>$Login</b> telah dipakai oleh user: <b>$ada[Nama]</b>.<br>
      Gunakan Kode Login yang lain.").'<br>';
  }
}

function dltUsrCek(){
	$Login = $_REQUEST['Lgn'];
	$Akses = GetaField('level', 'LevelID', $_REQUEST['LevelID'], 'Nama');
	echo Konfirmasi("Delete User", "Yakin Anda ingin menghapus user : <br />
									Login : <b>$Login</b><br />
									Akses : <b>$Akses</b><br /><br />
									<input type=button name='hapus' value='Delete User' onClick=\"location='?mnux=sysusr&gos=dltUsr&Lgn=$Login'\">");
}

function dltUsr(){
  if ($_SESSION['_Login'] == $_REQUEST['Lgn']) {
    echo '<script>alert("anda tidak dapat menghapus diri anda sendiri")</script>';
  }else{  
	$s = "delete from karyawan where Login = '$_REQUEST[Lgn]'";
	$r = _query($s);
 }
	DftrUsr();
}

?>
</div>