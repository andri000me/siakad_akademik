<?php

echo $_SESSION['TahunID'];
// *** Parameters ***
$angk = GetSetVar('angk');
$prodi = GetSetVar('prodi');
$program = GetSetVar('program');
$TahunID = GetSetVar('TahunID');
$dari = GetSetVar('dari');
$sampai = GetSetVar('sampai');

// *** Main ***
TampilkanJudul("Status Pembayaran");
//TampilkanPilihanProdiAngkatan($_SESSION[mnux]);

  $optprd = GetProdiUser($_SESSION['_Login'], $_SESSION['prodi']);
  /*if (isset($prodi) and isset ($angk)){
  	$q = "select * from kelas where ProdiID = '".$prodi."' and TahunID = '".$angk."'";
	$m = _query($q);
		if (_num_rows($m) == 0) {
			$optkelas = "";
		} else {
		  $sel1 = ($kelas == 0)? 'selected' : '';
		  $sel2 = ($kelas == 'All')? 'selected' : '';
		  $optkelas ="<option value='0' $sel1>- Pilih -</option>
    <option value='All' $sel2>Semua</option>";
			while ($x = _fetch_array($m)){
				$sel = ($kelas == $x['KelasID'])? 'selected' : '';
				$optkelas .= "<option value='$x[KelasID]' $sel>$x[Nama]</option>";
			}
		}
	
	}*/
    $optprog  = GetOption2('program', "concat(ProgramID, ' - ', Nama)", 'ProgramID', $_SESSION['program'], "KodeID='".KodeID."'", 'ProgramID');
  
  echo "<p><table class=box cellspacing=1 width=100% align=center>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$mnux' />
  <input type=hidden name='gos' value='$gos' />
  <tr>
    <td class=inp>Tahun Akd</td>
    <td class=ul>
      <input type=text name='TahunID' value='$_SESSION[TahunID]' size=5 maxlength=5>
    </td>
    <td class=inp>Angkatan</td>
    <td class=ul>
      <input type=text name='angk' value='$_SESSION[angk]' size=5 maxlength=4>
    </td>
    <td class=inp>Prodi</td>
    <td class=ul>
      <select name='prodi' onChange='this.form.submit()'>$optprd</select>
    </td>
    <td class=inp>Program</td>
    <td class=ul>
      <select name='program' onChange='this.form.submit()'>
	  $optprog
	  </select>
    <input type=submit name='Tampilkan' value='Tampilkan'>
    </td>
  </tr>
  <tr>
  	<td colspan=10>
  		Mulai <input type=text name='dari' value='$_SESSION[dari]' size=5 maxlength=4> Limit <input type=text name='sampai' value='$_SESSION[sampai]' size=5 maxlength=4>
  	</td>
  </td>

  </form></table></p>";

$gos = (empty($_REQUEST['gos']))? "DaftarMhsw" : $_REQUEST['gos'];
$gos();

// *** Functions ***
function DaftarMhsw() {
  if ($_SESSION['angk'] == '' || $_SESSION['prodi'] == '' || $_SESSION['TahunID'] == '')
    echo Konfirmasi("Masukkan Parameter",
      "Anda harus memasukkan Tahun Akademik, Angkatan dan Program Studi terlebih dulu.<br />
      Setelah itu Anda dapat mengganti status pembayarannya.
      <hr size=1 color=silver />
      Hubungi Perlengkapan untuk informasi lebih lanjut.");
  else TampilkanDaftarMhsw();
}
function TampilkanDaftarMhsw() {
	$_SESSION[dari]=$_SESSION[dari]+0;
	$_SESSION[sampai]=$_SESSION[sampai]+0;
  $limit = ($_SESSION['dari']>0 || $_SESSION['sampai']>0)?
  			"limit $_SESSION[dari],$_SESSION[sampai]":"";
  $s = "select m.MhswID, m.Nama, k.Biaya, k.Bayar
    from mhsw m
      left outer join khs k on m.MhswID = k.MhswID
    where m.KodeID = '".KodeID."'
      and m.ProdiID = '$_SESSION[prodi]'
      and m.TahunID like '$_SESSION[angk]%'
	    and k.TahunID='$_SESSION[TahunID]'
    order by m.MhswID $limit";
  $r = _query($s);
  $n=_num_rows($r);
  echo <<<ESD
  <form name='frmBeasiswa' action='?' method=POST>
    <table class=box cellspacing=1 align=center width=100%>
    <input type=hidden name='mnux' value='$_SESSION[mnux]' />
    <input type=hidden name='gos' value='SimpanBeasiswa' />
    <input type=hidden name='angk' value='$_SESSION[angk]' />
    <input type=hidden name='prodi' value='$_SESSION[prodi]' />
    <input type=hidden name='TahunID' value='$_SESSION[TahunID]' />
    <input type=hidden name='JML' value='$n' />
    <tr>
        <td class=ul colspan=5 align=right>
          <input type=submit name='btnSimpan' value='Simpan Status' />
        </td>
    </tr>
    
    <tr><th class=ttl width=30>Nmr</th>
        <th class=ttl width=100>NPM</th>
        <th class=ttl>Mahasiswa</th>
        <th class=ttl>Status Bayar</th>
        <th class=ttl>Pilih</th>
    </tr>
ESD;
  $n = 0;
  while ($w = _fetch_array($r)) {
   $n++;
    
      $selY = ($w['Bayar']>0)? "selected":"";
      $selN = ($w['Bayar']==0)? "selected":"";
      $pilihan = "<option value='Y' $selY>Sudah Bayar</option>";
      $pilihan .= "<option value='' $selN>(Belum Bayar)</option>";
    $status = ($w['Bayar']>0)? 
              "<font color=green>Sudah</font>":
              "<font color=red>Belum</font>";
    echo <<<ESD
    <tr>
        <td class=inp>$n</td>
        <td class=ul>$w[MhswID]</td>
        <td class=ul>$w[Nama]</td>
        <td class=ul align=center>$status</td>
        <td class=ul align=center>
          <input type='hidden' name='MhswID_$n' value='$w[MhswID]' />
          <SELECT name='StatusBayar_$n' class='nones'/>$pilihan</SELECT>
          </td>
        </tr>
ESD;
  }
  RandomStringScript();
  echo <<<ESD
    
    </table>
    </form>
    <p>
    <div class='box0' id='caridosen'></div>
ESD;
}
function SimpanBeasiswa() {
  $angk = sqling($_REQUEST['angk']);
  $prodi = sqling($_REQUEST['prodi']);
  $TahunID = sqling($_REQUEST['TahunID']);
  $JML = $_REQUEST['JML']+0;
  //echo $JML;
  if ($JML <= 0) {
    echo ErrorMsg("Error",
      "Tidak ada mahasiswa yang perlu diset ($JML).<br />
      Pilih Angkatan dan Program Studi yang tepat.<br />
      Hubungi Sysadmin untuk informasi lebih lanjut.
      <hr size=1 color=silver />
      <input type=button name='btnKembali' value='Kembali'
      onClick=\"location='?mnux=$_SESSION[mnux]&gos='\" />");
  }
  else {
    $hitung = 0;
    for ($i = 1; $i <= $JML; $i++) {
      $MhswID = $_REQUEST['MhswID_'.$i];
      $StatusBayar = $_REQUEST['StatusBayar_'.$i];
        $hitung++;
        if ($StatusBayar=='Y'){
          $s = "update khs
            set Biaya = '1',Bayar='1'
            where 
              MhswID = '$MhswID' and TahunID='$TahunID' ";
          $r = _query($s);
        }else{
          $s = "update khs
            set Biaya = '1',Bayar='0'
            where 
              MhswID = '$MhswID' and TahunID='$TahunID' ";
          $r = _query($s);
        }
        //die($s);
    }
    if ($hitung == 0) {
      echo ErrorMsg("Error",
      "Anda belum memilih seorang pun mahasiswa yg akan diset.<br />
      Pilih mahasiwa2 yang akan diganti status bayarnya terlebih dahulu.<br />
      Hubungi Sysadmin untuk informasi lebih lanjut.
      <hr size=1 color=silver />
      <input type=button name='btnKembali' value='Kembali'
      onClick=\"location='?mnux=$_SESSION[mnux]&gos='\" />");
    }
    else BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=", 10);
  }
}
?>
