<?php

// *** Parameters ***
// *** Main ***
$gos = (empty($_REQUEST['gosx']))? 'Dftr' : $_REQUEST['gosx'];
$gos();

// *** Functions ***
function Dftr() {
	$w = GetFields("pustaka_setup", "KodeID",KodeID,"*");
  echo "<p>
  <form action='?mnux=$_SESSION[mnux]&gos=setup' method=POST>
  <input type=hidden name='gosx' value='SAV'>
  <table class=box cellspacing=1 align=center width=600>";
    echo "<tr><th colspan=2>Mahasiswa</th></tr>
	<tr>
      <td class=inp >Denda per hari:</td>
      <td class=ul1>Rp <input type=text name='DendaMHS' value='$w[DendaMHS]' size=6 maxlength=7></td>
      </tr>
	  <tr>
      <td class=inp>Batas waktu peminjaman:</td>
      <td class=ul1><input type=text name='LamaPeminjamanMHS' value='$w[LamaPeminjamanMHS]' size=3 maxlength=3></td>
      </tr>
	  <tr>
      <td class=inp >Jumlah peminjaman maksimal:</td>
      <td class=ul1><input type=text name='JumlahPeminjamanMHS' value='$w[JumlahPeminjamanMHS]' size=2 maxlength=2></td>
      </tr>";
	  // Dosen
	  echo "<tr><th colspan=2>Dosen</th></tr>
	  <tr>
      <td class=inp >Denda per hari:</td>
      <td class=ul1>Rp <input type=text name='DendaDOS' value='$w[DendaDOS]' size=6 maxlength=7></td>
      </tr>
	  <tr>
      <td class=inp>Batas waktu peminjaman:</td>
      <td class=ul1><input type=text name='LamaPeminjamanDOS' value='$w[LamaPeminjamanDOS]' size=3 maxlength=3></td>
      </tr>
	  <tr>
      <td class=inp >Jumlah peminjaman maksimal:</td>
      <td class=ul1><input type=text name='JumlahPeminjamanDOS' value='$w[JumlahPeminjamanDOS]' size=2 maxlength=2></td>
      </tr>";
	  // Karyawan
	  echo "<tr><th colspan=2>Karyawan</th></tr>
	  <tr>
      <td class=inp >Denda per hari:</td>
      <td class=ul1>Rp <input type=text name='DendaKAR' value='$w[DendaKAR]' size=6 maxlength=7></td>
      </tr>
	  <tr>
      <td class=inp>Batas waktu peminjaman:</td>
      <td class=ul1><input type=text name='LamaPeminjamanKAR' value='$w[LamaPeminjamanKAR]' size=3 maxlength=3></td>
      </tr>
	  <tr>
      <td class=inp >Jumlah peminjaman maksimal:</td>
      <td class=ul1><input type=text name='JumlahPeminjamanKAR' value='$w[JumlahPeminjamanKAR]' size=2 maxlength=2></td>
      </tr>";
	  // Mhs Luar
	  echo "<tr><th colspan=2>Mahasiswa Luar</th></tr>
	  <tr>
      <td class=inp >Denda per hari:</td>
      <td class=ul1>Rp <input type=text name='DendaMHL' value='$w[DendaMHL]' size=6 maxlength=7></td>
      </tr>
	  <tr>
      <td class=inp>Batas waktu peminjaman:</td>
      <td class=ul1><input type=text name='LamaPeminjamanMHL' value='$w[LamaPeminjamanMHL]' size=3 maxlength=3></td>
      </tr>
	  <tr>
      <td class=inp >Jumlah peminjaman maksimal:</td>
      <td class=ul1><input type=text name='JumlahPeminjamanMHL' value='$w[JumlahPeminjamanMHL]' size=2 maxlength=2></td>
      </tr>
	  <tr><th colspan=2>Pejabat Perpustakaan</th></tr>
	  <tr>
      <td class=inp >Jabatan:</td>
      <td class=ul1><input type=text name='Jabatan' value='$w[Jabatan]' size=20 maxlength=40></td>
      </tr>
	  <tr>
      <td class=inp >Pejabat:</td>
      <td class=ul1><input type=text name='Pejabat' value='$w[Pejabat]' size=20 maxlength=40></td>
      </tr>
	  <tr><td class=ul1 colspan=2 align=center>
      <input type=submit name='Simpan' value='Simpan' />
      </td></tr>";
  echo "</table>
  </form></p>";
}
function SAV(){
	$s = "UPDATE pustaka_setup set 
			DendaMHS='$_REQUEST[DendaMHS]'+0, LamaPeminjamanMHS='$_REQUEST[LamaPeminjamanMHS]', JumlahPeminjamanMHS = '$_REQUEST[JumlahPeminjamanMHS]',
			DendaDOS='$_REQUEST[DendaDOS]'+0, LamaPeminjamanDOS='$_REQUEST[LamaPeminjamanDOS]', JumlahPeminjamanDOS = '$_REQUEST[JumlahPeminjamanDOS]',
			DendaKAR='$_REQUEST[DendaKAR]'+0, LamaPeminjamanKAR='$_REQUEST[LamaPeminjamanKAR]', JumlahPeminjamanKAR = '$_REQUEST[JumlahPeminjamanKAR]',
			DendaMHL='$_REQUEST[DendaMHL]'+0, LamaPeminjamanMHL='$_REQUEST[LamaPeminjamanMHL]', JumlahPeminjamanMHL = '$_REQUEST[JumlahPeminjamanMHL]'
			, Pejabat = '$_REQUEST[Pejabat]', Jabatan = '$_REQUEST[Jabatan]'";
	$r = _query($s);
	BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=setup&gosx=", 5000); 
}

?>
