<?php

// *** Main ***
$sub = (empty($_REQUEST['sub']))? 'frmPribadi' : $_REQUEST['sub'];
$sub();

// *** Functions ***
function frmPribadi() {
  global $datamhsw, $mnux, $pref;
  $TanggalLahir =  GetDateOption($datamhsw['TanggalLahir'], 'TanggalLahir');
  $TanggalLahirIjazah = "<input type=text name='TanggalLahirIjazah' value='$datamhsw[TanggalLahirIjazah]' size=30 maxlength=50>";
  //GetRadio($_sql, $_name, $_disp, $_key, $_default='', $_pisah='<br>') {
  $Kelamin = GetRadio("select Kelamin, Nama from kelamin order by Kelamin",
    'Kelamin', 'Nama', 'Kelamin', $datamhsw['Kelamin']);
  $WargaNegara = GetRadio("select WargaNegara, Nama from warganegara order by WargaNegara",
    'WargaNegara', 'Nama', 'WargaNegara', $datamhsw['WargaNegara']);
  $Agama = GetOption2('agama', "concat(Agama, ' - ', Nama)", "Agama", $datamhsw['Agama'], '', 'Agama');
  
  // Kurikulum
  $s6 = "select KurikulumID,KurikulumKode,Nama
    from kurikulum
    where ProdiID = '$datamhsw[ProdiID]' order by Nama";
  $r6 = _query($s6);
    $optkurikulum = "<option value=''></option>";
    while($w6 = _fetch_array($r6))
    {  $ck = ($w6['KurikulumID'] == $datamhsw['KurikulumID'])? "selected" : '';
       $optkurikulum .=  "<option value='$w6[KurikulumID]' $ck>$w6[Nama]</option>";
    }
    $_inputKurikulum = "<select name='KurikulumID'>$optkurikulum</select>";

  // Propinsi
    $s8 = "select distinct(PropinsiID) as PropinsiID,NamaPropinsi from propinsi order by NamaPropinsi";
  $r8=_query($s8);
  $defPropinsi= GetaField('propinsi',"PropinsiID",$datamhsw[Propinsi],"NamaPropinsi");
    $optionProp = "<option value='$datamhsw[Propinsi]' selected>$defPropinsi</option>";
  while ($w8 = _fetch_array($r8)) {
  if ($w8['PropinsiID']==$datamhsw[Propinsi]) {
  $optionProp .= "<option value='$datamhsw[Propinsi]' selected>$w8[NamaPropinsi]</option>";
  }
  else{
  $optionProp .=  "<option value='$w8[PropinsiID]'>$w8[NamaPropinsi]</option>";
  }
  }
  $StatusSipil = GetOption2('statussipil', "concat(StatusSipil, ' - ', Nama)", "StatusSipil", $datamhsw['StatusSipil'], '', 'StatusSipil');
  $ck_forlapN = ($datamhsw['CekForlap']=='N')? "checked":"";
  $ck_forlapY = ($datamhsw['CekForlap']=='Y')? "checked":"";

  $ckPMB = GetaField('pmb', "MhswID", $datamhsw['MhswID'],"PMBID");
  if (!empty($ckPMB) && ($_SESSION['_LevelID']=='1' || $_SESSION['_LevelID']=='40')){
  	$update_mhsw='<tr><td class=ul colspan=2><b>Hanya bila ingin mengganti '.NPM.'</b></td></tr>';
  	$optprd = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'Nama', $datamhsw['ProdiID'], "KodeID='".KodeID."'", 'ProdiID');
  	$update_mhsw .= "<tr><td class=inp><b>Ganti ".NPM."</b></td>
      <td class=ul><input type=text name='gMhswID' value='$datamhsw[MhswID]' size=15 maxlength=15></td></tr>";
    $update_mhsw .= "<tr><td class=inp><b>Ganti Prodi</b></td>
      <td class=ul><select name='gProdiID'>$optprd</select></td></tr>
      <tr><td class=ul colspan=2><hr></td></tr>";
  }
  $update_mhsw .= "<tr><td class=inp><b>Ganti Kurikulum</b></td>
      <td class=ul>$_inputKurikulum</td></tr>
      <tr><td class=ul colspan=2><hr></td></tr>";
  echo "
  <table class=box cellspacing=1 cellpadding=4 width=600>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$_SESSION[mnux]'>
  <input type=hidden name='BypassMenu' value='1' />
  <input type=hidden name='submodul' value='pri' />
  <input type=hidden name='sub' value='PribadiSav' />
  <input type=hidden name='mhswid' value='$datamhsw[MhswID]'>

  $update_mhsw
  <tr><td class=ul colspan=2><b>Harap diisi sesuai dengan Ijazah Pendidikan sebelumnya</b></td></tr>

  <tr><td class=inp width=140>Nama</td>
      <td class=ul><input type=text name='Nama' value='$datamhsw[Nama]' size=40 maxlength=50></td></tr>
  <tr><td class=inp>Tempat Lahir</td>
      <td class=ul><input type=text name='TempatLahir' value='$datamhsw[TempatLahir]' size=40 maxlength=50></td></tr>
  <tr><td class=inp>Tanggal Lahir</td>
      <td class=ul>$TanggalLahir</td></tr>
  <tr><td class=inp>Tanggal Lahir di Ijazah</td>
      <td class=ul>$TanggalLahirIjazah</td></tr>
  <tr><td class=inp>Jenis Kelamin</td>
      <td class=ul>$Kelamin</td></tr>
  <tr><td class=inp rowspan=2>Warga Negara</td>
      <td class=ul>$WargaNegara</td></tr>
  <tr><td class=ul>Jika asing, sebutkan: <input type=text name='Kebangsaan' value='$datamhsw[Kebangsaan]' size=20 maxlength=50></td></tr>
  <tr><td class=inp>Agama</td>
      <td class=ul><select name='Agama'>$Agama</select></td></tr>
  <tr><td class=inp>Status Sipil</td>
      <td class=ul><select name='StatusSipil'>$StatusSipil</select></td></tr>

  <tr><td class=inp>Alamat</td>
      <td class=ul><input type=text name='Alamat' value='$datamhsw[Alamat]' size=50 maxlength=200></td></tr>
  <tr><td class=inp>RT</td><td class=ul><input type=text name='RT' value='$datamhsw[RT]' size=10 maxlength=5>
      RW <input type=text name='RW' value='$datamhsw[RW]' size=10 maxlength=5></td></tr>
  <tr><td class=inp>Kota/Kabupaten</td>
      <td class=ul><input type=text name='Kota' value='$datamhsw[Kota]' size=20 maxlength=50></td>
      </tr>
  <tr><td class=inp>Kode Pos</td>
      <td class=ul><input type=text name='KodePos' value='$datamhsw[KodePos]' size=20 maxlength=50></td></tr>
  <tr><td class=inp>Propinsi</td>
      <td class=ul><select name='Propinsi'>$optionProp</select></td></tr>
  <tr><td class=inp>Negara</td>
      <td class=ul><input type=text name='Negara' value='$datamhsw[Negara]' size=40 maxlength=50></td></tr>

  <tr><td class=inp>Telepon</td><td class=ul><input type=text name='Telepon' value='$datamhsw[Telepon]' size=20 maxlength=50>
      Handphone <input type=text name='Handphone' value='$datamhsw[Handphone]' size=20 maxlength=50></td></tr>
  <tr><td class=inp>E-mail</td>
      <td class=ul><input type=text name='Email' value='$datamhsw[Email]' size=50 maxlength=50></td></tr>
  <tr><td class=inp>Cek Forlap</td>
      <td class=ul>Sudahkah anda cek data anda di <a href='http://forlap.ristekdikti.go.id/mahasiswa' target='_blank'>http://forlap.ristekdikti.go.id/mahasiswa</a> ?<br />
      (Sebelum dicek anda belum bisa melanjutkan akses ke menu lain.)<br>
      <input type=radio name='CekForlap' value='Y' $ck_forlapY> Sudah <input type=radio name='CekForlap' value='N' $ck_forlapN> Belum<br /><hr />
      <p style='text-align:justify'>Sangat disarankan untuk memeriksa data anda di Forlap, Kesalahan nama dan lain-lain tentang data diri anda akan menyulitkan nantinya bagi anda dalam mencari pekerjaan. Sebagian besar perusahaan memeriksa kebenaran data dari Forlap. Apabila terdapat kesalahan di Forlap, segera laporkan ke Pustikom.</p>
      </td></tr>
  <tr><td colspan=2 align=center><input type=submit name='Simpan' value='Simpan'>
    <input type=reset value='Reset'></td></tr>
  </form></table></p>";
}
function PribadiSav() {
  $Nama = FixQuotes($_REQUEST['Nama']);
  $Nama = sqling($Nama);
  $MhswID = ($_SESSION['_LevelID']==120)? $_SESSION['_Login'] : sqling($_REQUEST['mhswid']);
  $TempatLahir = sqling($_REQUEST['TempatLahir']);
  $TanggalLahir = "$_REQUEST[TanggalLahir_y]-$_REQUEST[TanggalLahir_m]-$_REQUEST[TanggalLahir_d]";
  $TanggalLahirIjazah = sqling($_REQUEST['TanggalLahirIjazah']);
  $WargaNegara = sqling($_REQUEST['WargaNegara']);
  $Agama = sqling($_REQUEST['Agama']);
  $StatusSipil = sqling($_REQUEST['StatusSipil']);
  $Kebangsaan = sqling($_REQUEST['Kebangsaan']);
  $Kelamin = sqling($_REQUEST['Kelamin']);
  $Alamat = sqling($_REQUEST['Alamat']);
  $Kota = sqling($_REQUEST['Kota']);
  $KodePos = sqling($_REQUEST['KodePos']);
  $RT = sqling($_REQUEST['RT']);
  $RW = sqling($_REQUEST['RW']);
  $Propinsi = sqling($_REQUEST['Propinsi']);
  $Negara = sqling($_REQUEST['Negara']);
  $Telepon = sqling($_REQUEST['Telepon']);
  $Handphone = sqling($_REQUEST['Handphone']);
  $Email = sqling($_REQUEST['Email']);
  $CekForlap = sqling($_REQUEST['CekForlap']);
  $gProdiID = sqling($_REQUEST['gProdiID']);
  $gMhswID = sqling($_REQUEST['gMhswID']);
  $KurikulumID = sqling($_REQUEST['KurikulumID']);

  $whrTgl = " TanggalLahir='$TanggalLahir', TanggalLahirIjazah='$TanggalLahirIjazah', ";
  // Simpan
  $s = "update mhsw set Nama='$Nama',
    TempatLahir='$TempatLahir', $whrTgl 
    Agama='$Agama', StatusSipil='$StatusSipil',
    Kelamin='$Kelamin', WargaNegara='$WargaNegara', Kebangsaan='$Kebangsaan',
    Alamat='$Alamat', RT='$RT', RW='$RW',KurikulumID='$KurikulumID',
    Kota='$Kota', KodePos='$KodePos', Propinsi='$Propinsi', Negara='$Negara',
    Telepon='$Telepon', Handphone='$Handphone', Email='$Email', LoginEdit='$_SESSION[_Login]', TanggalEdit=now(), CekForlap='$CekForlap'
    where MhswID='$MhswID' ";
  $r = _query($s);
  if (!empty($gMhswID) || !empty($gProdiID) && $gMhswID!=$MhswID){
  	$s = "update mhsw set MhswID='$gMhswID', Login='$gMhswID', ProdiID='$gProdiID' where MhswID='$MhswID'";
  	$r = _query($s);
  	$s = "update pmb set MhswID='$gMhswID', ProdiID='$gProdiID' where MhswID='$MhswID'";
  	$r = _query($s);
  	$s = "update khs set MhswID='$gMhswID', ProdiID='$gProdiID',MaxSKS=24 where MhswID='$MhswID'";
  	$r = _query($s);
  	$_SESSION['mhswid'] = $gMhswID;
  }
  BerhasilSimpan("?mnux=$_SESSION[mnux]&submodul=pri", 100);
}

?>
