<?php

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Konfirmasi Pengembalian");

// *** Parameters ***
$md = $_REQUEST['md']+0;
$id = $_REQUEST['id']+0; // Jika edit, maka gunakan id ini
$bck = $_REQUEST['bck'];

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'Edit' : $_REQUEST['gos'];
$gos($md, $id, $bck);

// *** Functions ***
function Edit($md, $id, $bck) {
if ($md == 1) {
    $jdl = "Konfirmasi Pengembalian";
	$timE = date('Y-m-d');
    $w = GetFields("pustaka_sirkulasi2 s2 
					left outer join pustaka_sirkulasi s on s.SirkulasiID=s2.SirkulasiID
					left outer join app_pustaka1.biblio b on b.biblio_id=s2.BibliografiID","s2.Sirkulasi2ID", $id, 
					"b.title as Judul, s2.Sirkulasi2ID, s.SirkulasiID, DATEDIFF('$timE',s2.TanggalHarusKembali) as Selisih, s2.TanggalHarusKembali,s.AnggotaID");
    $ro = 'readonly=true disabled=true';
  }
  else die(ErrorMsg('Error',
    "Mode edit <b>$md</b> tidak ditemukan.<br />
    Hubungi Sysadmin untuk informasi lebih detail.
    <hr size=1 color=silver />
    Opsi: <input type=button name='Tutup' value='Tutup'
      onClick=\"window.close()\" />"));

$mhs = GetaField('pustaka_anggota',"AnggotaID", $w['AnggotaID'],"InstitusiID");
$setup = GetaField("pustaka_setup", "KodeID", KodeID, "Denda$mhs");
$denda = 0;
if ($w['Selisih']>0) {
	$denda = $w['Selisih'] * $setup + 0;
}
  echo "<p><table class=box cellspacing=1 width=100%>
  <form action='../$_SESSION[mnux].pinjaman.edit.php' method=POST>
  <input type=hidden name='gos' value='Simpan' />
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='SirkulasiID' value='$w[SirkulasiID]' />
  <input type=hidden name='Sirkulasi2ID' value='$w[Sirkulasi2ID]' />
  <input type=hidden name='bck' value='$bck' />
  <input type=hidden name='Denda' value='$denda' />
  <input type=hidden name='AnggotaID' value='$_SESSION[_pustakaAnggotaID]' />
  
  <tr><th class=ttl colspan=2>$jdl</th></tr>
  <tr><td class=inp>ID Anggota:</td>
      <td class=ul1><input type=text name='AnggotaIDs' value='$_SESSION[_pustakaAnggotaID]' size=30 $ro /></td>
      </tr>
  <tr><td class=inp>Judul Buku:</td>
      <td class=ul1>$w[Judul]</td>
      </tr>
<tr><td class=inp>Tanggal Harus Kembali:</td>
      <td class=ul1>".TanggalFormat($w['TanggalHarusKembali'])."</td>
      </tr>
	  <tr><td class=inp>Keterlambatan:</td>
      <td class=ul1>$w[Selisih] hari</td>
      </tr>
	  <tr><td class=inp>Denda:</td>
      <td class=ul1>Rp ".number_format($denda,0)."</td>
      </tr>
  <tr><td class=inp>Jumlah Dibayar:</td>
      <td class=ul1><input type=text name='Dibayar' value='' size=30 /></td>
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
  	$SirkulasiID = $_REQUEST['SirkulasiID'];
	$Sirkulasi2ID = $_REQUEST['Sirkulasi2ID'];
	$Denda = $_REQUEST['Denda'];
	$AnggotaID = $_REQUEST['AnggotaID'];
	
  // Simpan
	if ($md == 1) {
    $s = "UPDATE pustaka_sirkulasi2 set Status='Kembali',Denda='$Denda', Dibayar='$_REQUEST[Dibayar]',
				TanggalKembali=now(),LoginEdit='$_SESSION[_Login]', TanggalEdit=now() 
				where Sirkulasi2ID='$Sirkulasi2ID'";
    $r = _query($s);
	$ww = GetaField('pustaka_sirkulasi2', "Sirkulasi2ID", $Sirkulasi2ID, "Eksemplar");
// =======================================	dari sini
	$s = "UPDATE app_pustaka1.loan set
			return_date = now(),
			is_return = 1,
			is_lent = 0
			where
			item_code = '$ww' and member_id = '$AnggotaID'";
	$r = _query($s);
	echo $s;
// ====================================== sampai sini
	
	$cekSirkulasi = GetaField("pustaka_sirkulasi2", "Status='Pinjam' and SirkulasiID", $SirkulasiID, "count(Sirkulasi2ID)") + 0;
	if ($cekSirkulasi == 0) {
		$s = "UPDATE pustaka_sirkulasi set Status='Kembali', TanggalKembali=now(),LoginEdit='$_SESSION[_Login]', TanggalEdit=now() where SirkulasiID='$SirkulasiID'";
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
    opener.location='../?mnux=$BCK&gos=sirkulasi&aID=$_SESSION[_pustakaAnggotaID]';
    self.close();
    return false;
  }
  ttutup();
</SCRIPT>
SCR;
}
?>
