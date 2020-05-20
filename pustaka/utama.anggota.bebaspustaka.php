<?php

session_start();
	include_once "../dwo.lib.php";
  	include_once "../db.mysql.php";
  	include_once "../connectdb.php";
  	include_once "../parameter.php";
  	include_once "../cekparam.php";

// *** Parameters ***
$md = $_REQUEST['md']+0;
$id = $_REQUEST['id']; // Jika edit, maka gunakan id ini
$bck = $_REQUEST['bck'];

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'Edit' : $_REQUEST['gos'];
$gos($md, $id, $bck);

// *** Functions ***
function Edit($md, $id, $bck) {
  $StatusPeminjaman = GetaField('pustaka_sirkulasi s left outer join pustaka_sirkulasi2 s2 on s2.SirkulasiID=s.SirkulasiID', "s.AnggotaID", $id,"COUNT(s2.SirkulasiID)");
  $statusAnggota = GetaField('pustaka_anggota',"AnggotaID",$id, "NA");
  if ($StatusPeminjaman > 0 || $statusAnggota!='N') {
    die(ErrorMsg('Error',
    "Tidak bisa mencetak Kartu Bebas Pustaka. Berikut kemungkinan kesalahannya:
	<ul><li>Mahasiswa belum mengembalikan $StatusPeminjaman buku.</li>
	<li>Status Mahasiswa saat ini tidak aktif sebagai anggota pustaka.</li>
	</ul>
    <hr size=1 color=silver />
    Opsi: <input type=button name='Tutup' value='Tutup'
      onClick=\"window.close()\" />"));
  }
  else {
	  $p = GetFields('identitas',"Kode", KodeID,"*");
	  $m = GetFields('mhsw m left outer join prodi p on p.ProdiID=m.ProdiID', "m.MhswID", $id,"m.Nama as NM, m.ProdiID, p.Nama as PRD");
	  $pustaka = GetFields("pustaka_setup", "KodeID",KodeID,"*");
  echo "<p><table class=box cellspacing=1 width=500px style='border:1px solid #000; padding:10px;font-family:Trebuchet MS'>
  <tr><td colspan=2 align=center style='border-bottom:2px solid #000'><font size=+2>Perpustakaan $p[Nama]</font></td></tr>
  
  <tr><th class=ttl colspan=2><h4>Kartu Bebas Pustaka</h4></th></tr>
  <tr><td class=inp>NIM:</td>
      <td class=ul1>$id</td>
      </tr>
  <tr><td class=inp>Nama:</td>
      <td class=ul1>$m[NM]</td>
      </tr>
  <tr><td class=inp>Prodi:</td>
      <td class=ul1>$m[ProdiID] - $m[PRD]</td>
      </tr>
   <tr><td class=ttl colspan=2 style='padding-left:260px;padding-top:20px'>$p[Kota], ".TanggalFormat(date('Y-m-d'))."</td></tr>
   <tr><td class=ttl colspan=2 style='padding-left:260px;'>$pustaka[Jabatan],</td></tr>
   <tr><td class=ttl colspan=2 style='padding-left:260px;;padding-top:50px'>$pustaka[Pejabat]</td></tr>
  </table></p>";
  }
}
function TutupScript($BCK) {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    opener.location='../?mnux=$BCK&gos=anggota';
    self.close();
    return false;
  }
  ttutup();
</SCRIPT>
SCR;
}
?>
