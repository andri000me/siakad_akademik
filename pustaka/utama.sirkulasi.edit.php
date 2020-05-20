<?php

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Konfirmasi Sirkulasi");

// *** Parameters ***
$md = $_REQUEST['md']+0;
$id = $_REQUEST['id']+0; // Jika edit, maka gunakan id ini
$bck = $_REQUEST['bck'];

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'Edit' : $_REQUEST['gos'];
$gos($md, $id, $bck);

// *** Functions ***
function Edit($md, $id, $bck) {
  if ($md == 0) {
    $jdl = "Konfirmasi Pengembalian";
    $w = GetFields('pustaka_anggota', 'AnggotaID', $id, '*');
    $ro = "readonly=true disabled=true";
  }
  elseif ($md == 1) {
    $jdl = "Konfirmasi Peminjaman";
    $w = array();
    $ro = 'readonly=true disabled=true';
	$buku = explode("~",$_SESSION['_IDBuku']);
	$buku1 = GetaField('app_pustaka1.item i left outer join app_pustaka1.biblio b on b.biblio_id=i.biblio_id',"i.item_code", $buku[0], "concat(i.item_code,' - ',b.title)");
	$buku2 = GetaField('app_pustaka1.item i left outer join app_pustaka1.biblio b on b.biblio_id=i.biblio_id',"i.item_code", $buku[1], "concat(i.item_code,' - ',b.title)");
	$buku3 = GetaField('app_pustaka1.item i left outer join app_pustaka1.biblio b on b.biblio_id=i.biblio_id',"i.item_code", $buku[2], "concat(i.item_code,' - ',b.title)");
	$mhs = GetaField('pustaka_anggota',"AnggotaID", $_SESSION['_pustakaAnggotaID'],"InstitusiID");
	$setup = GetaField("pustaka_setup", "KodeID", KodeID, "LamaPeminjaman$mhs");
	$tglkembali = date('Y-m-d',strtotime("+$setup days"));
	$tglHarusKembali = TanggalFormat($tglkembali);
  }
  else die(ErrorMsg('Error',
    "Mode edit <b>$md</b> tidak ditemukan.<br />
    Hubungi Sysadmin untuk informasi lebih detail.
    <hr size=1 color=silver />
    Opsi: <input type=button name='Tutup' value='Tutup'
      onClick=\"window.close()\" />"));

  echo "<p><table class=box cellspacing=1 width=100%>
  <form action='../$_SESSION[mnux].sirkulasi.edit.php' method=POST>
  <input type=hidden name='gos' value='Simpan' />
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='id' value='$id' />
  <input type=hidden name='bck' value='$bck' />
  <input type=hidden name='TanggalKembali' value='$tglkembali' />
  
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>ID Anggota:</td>
      <td class=ul1><input type=text name='AnggotaID' value='$_SESSION[_pustakaAnggotaID]' size=30 $ro /></td>
      </tr>
  <tr><td class=inp>Buku 1:</td>
      <td class=ul1>$buku1</td>
      </tr>
  <tr><td class=inp>Buku 2:</td>
      <td class=ul1>$buku2</td>
      </tr>
  <tr><td class=inp>Buku 3:</td>
      <td class=ul1>$buku3</td>
      </tr>
<tr><td class=inp>Tanggal Harus Kembali:</td>
      <td class=ul1><h1>$tglHarusKembali</h1></td>
      </tr>
  <tr><td class=ul1 colspan=2 align=center>
      <input type=submit name='Simpan' value='Simpan' />
      <input type=button name='Batal' value='Batal'
        onClick=\"window.close()\" />
      </td></tr>
  </form>
  </table></p>";
}
function Simpan($md, $id, $bck) {
  	$AnggotaID = $_REQUEST['AnggotaID'];
	
  // Simpan
  if ($md == 0) {
    $s = "update pustaka_anggota
      set Nama = '$Nama', Handphone = '$Handphone', Email='$Email', Alamat = '$Alamat', NamaInstitusi = '$NamaInstitusi',
          NA  = '$NA'
      where AnggotaID = '$id' ";
    $r = _query($s);
    TutupScript($bck);
  }
  elseif ($md == 1) {
    $s = "insert into pustaka_sirkulasi
      (AnggotaID, TanggalPinjam,Status, LoginBuat, TanggalBuat)
      values
      ('$_SESSION[_pustakaAnggotaID]',now(), 'Pinjam', '$_SESSION[_Login]',now())";
    $r = _query($s);
	$idx = mysql_insert_id();
	
	$buku = explode("~", $_SESSION['_IDBuku']);
	foreach ($buku as $b){
		$biblio_id = GetaField('app_pustaka1.item',"item_code", $b, "biblio_id");
		$s = "insert into pustaka_sirkulasi2
			  (SirkulasiID, BibliografiID,Eksemplar,TanggalHarusKembali, LoginBuat, TanggalBuat)
			  values
			  ('$idx','$biblio_id', '$b', '$_REQUEST[TanggalKembali]', '$_SESSION[_Login]',now())";
    	$r = _query($s);
		$s = "insert into app_pustaka1.loan
			  (item_code,member_id,loan_date,due_date,renewed,is_lent,is_return)
			  values
			  ('$b','$_SESSION[_pustakaAnggotaID]',now(),'$_REQUEST[TanggalKembali]',0,1,0)";
    	$r = _query($s);
	}
	
    TutupScript($bck);
  }
  else die(ErrorMsg('Error',
    "Mode edit <b>$md</b> tidak dikenali.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    Opsi: <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" />"));
  
}
function TutupScript($BCK) {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    opener.location='../?mnux=$BCK&gos=sirkulasi';
    self.close();
    return false;
  }
  ttutup();
</SCRIPT>
SCR;
}
?>