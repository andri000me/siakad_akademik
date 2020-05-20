<?php
// *** infrastruktur **
echo <<<SCR
  <script src="jur/dosen.penasehat.script.js"></script>
SCR;

// *** Parameters ***
$angk = GetSetVar('angk');
$prodi = GetSetVar('prodi');
$program = GetSetVar('program');

// *** Main ***
TampilkanJudul("Set Dosen Penasehat Akademik");
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
    <td class=inp>Tahun</td>
    <td class=ul>
      <input type=text name='angk' value='$_SESSION[angk]' size=10 maxlength=20>
      <input type=submit name='Tampilkan' value='Tampilkan'>
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
    </td>
  </tr>
  </form></table></p>";

$gos = (empty($_REQUEST['gos']))? "DaftarMhsw" : $_REQUEST['gos'];
$gos();

// *** Functions ***
function DaftarMhsw() {
  if ($_SESSION['angk'] == '' || $_SESSION['prodi'] == '')
    echo Konfirmasi("Masukkan Parameter",
      "Anda harus memasukkan Angkatan & Program Studi terlebih dulu.<br />
      Setelah itu Anda dapat mengeset Dosen Penasehatnya.
      <hr size=1 color=silver />
      Hubungi Sysadmin untuk informasi lebih lanjut.");
  else TampilkanDaftarMhsw();
}
function TampilkanDaftarMhsw() {
  
  $kls =($_SESSION['kelas']=='All')? '' : "and m.KelasID = '$_SESSION[kelas]'";
  
  $s = "select m.MhswID, m.Nama, m.PenasehatAkademik,k.Nama as Kls,
      d.Nama as NamaDosen, d.Gelar as GelarDosen,d.Gelar1 as GelarDosen1,d.Login
    from mhsw m
      left outer join dosen d on m.PenasehatAkademik = d.Login and d.KodeID = '".KodeID."'
	  left outer join kelas k on m.KelasID = k.KelasID
    where m.KodeID = '".KodeID."'
      and m.ProdiID = '$_SESSION[prodi]'
      and m.TahunID like '$_SESSION[angk]%'
	    
    order by m.MhswID";
  $r = _query($s);
  $n = 0;
  
  echo <<<ESD
    <table class=box cellspacing=1 align=center width=100%>
    
    <form name='frmPA' action='?' method=POST>
    <input type=hidden name='mnux' value='$_SESSION[mnux]' />
    <input type=hidden name='gos' value='SimpanPA' />
    <input type=hidden name='angk' value='$_SESSION[angk]' />
    <input type=hidden name='prodi' value='$_SESSION[prodi]' />
    <input type=hidden name='kelas' value='$_SESSION[kelas]' />    
    <tr>
        <td class=ul colspan=10>
          <input type=button name='btnRekapPA' value='Rekap PA'
            onClick="javascript:CetakRekapPA('$_SESSION[prodi]','$_SESSION[program]')" />
          <input type=button name='btnDaftarPA' value='Daftar PA-Mhsw'
            onClick="javascript:CetakDaftarPA('$_SESSION[prodi]','$_SESSION[angk]')" />
        <!-- </td>
        
        <td class=ul colspan=5 align=right> -->
        <div align=right>
        <input type=text name='DosenID' value='$w[DosenID]' size=10 maxlength=50 />
        <input type=text name='Dosen' value='$w[Dosen]' size=30 maxlength=50 onKeyUp="javascript:CariDosen('$_SESSION[prodi]', 'frmPA')" />
        &raquo;
        <a href='#'
          onClick="javascript:CariDosen('$_SESSION[prodi]', 'frmPA')" />Cari...</a> |
        <a href='#' onClick="javascript:frmPA.DosenID.value='';frmPA.Dosen.value=''">Reset</a>
        <input type=submit name='btnSimpan' value='Set PA' />
        </div>
        </td>
    </tr>
    
    <tr><th class=ttl width=30>Nmr</th>
        <th class=ttl width=100>NPM</th>
        <th class=ttl>Mahasiswa</th>
        <th class=ttl>Kelas</th>
        <th class=ttl>PA</th>
        <th class=ttl>Cek</th>
    </tr>
ESD;
  while ($w = _fetch_array($r)) {
    $n++;
    $dsn = (empty($w['PenasehatAkademik']))? "<abbr title='Belum diset'>&minus;</abbr>" : "<sub>#$w[Login]</sub> $w[GelarDosen1] $w[NamaDosen] <sup>$w[GelarDosen]</sup>";
    echo <<<ESD
    <tr>
        <td class=inp>$n</td>
        <td class=ul>$w[MhswID]</td>
        <td class=ul>$w[Nama]</td>
        <td class=ul>$w[Kls]&nbsp;</td>
        <td class=ul width=200>$dsn</td>
        <td class=ul width=5>
          <input type=checkbox name='MhswID_$n' value='$w[MhswID]' />
          </td>
        </tr>
ESD;
  }
  RandomStringScript();
  echo <<<ESD
    <input type=hidden name='JML' value='$n' />
    </form>
    <tr><td class=ul colspan=5 align=right>
        <input type=button name='btnCheckAll' value='Centang Semua' onClick="javascript:CentangSemua($n)" />
        </td></tr>
    </table>
    
    <p>
    <div class='box0' id='caridosen'></div>
    
  <script>
  function toggleBox(szDivID, iState) // 1 visible, 0 hidden
  {
    if(document.layers)	   //NN4+
    {
       document.layers[szDivID].visibility = iState ? "show" : "hide";
    }
    else if(document.getElementById)	  //gecko(NN6) + IE 5+
    {
        var obj = document.getElementById(szDivID);
        obj.style.visibility = iState ? "visible" : "hidden";
    }
    else if(document.all)	// IE 4
    {
        document.all[szDivID].style.visibility = iState ? "visible" : "hidden";
    }
  }
  function CariDosen(ProdiID, frm) {
    if (eval(frm + ".Dosen.value != ''")) {
      eval(frm + ".Dosen.focus()");
      showDosen(ProdiID, frm, eval(frm +".Dosen.value"), 'caridosen');
      toggleBox('caridosen', 1);
    }
  }
  function CentangSemua(n) {
    for (i = 1; i <= n; i++) {
      eval("frmPA.MhswID_" + i + ".checked = true");
    }
  }
  function CetakRekapPA(prd,prg) {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].rekap.php?ProdiID="+prd+"&ProgramID="+prg+"&_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=700, height=500, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function CetakDaftarPA(prd,thn) {
    lnk = "$_SESSION[mnux].daftar.php?ProdiID="+prd+"&Tahun="+thn+"&_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=700, height=500, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  </script>
ESD;
}
function SimpanPA() {
  $angk = sqling($_REQUEST['angk']);
  $prodi = sqling($_REQUEST['prodi']);
  $DosenID = sqling($_REQUEST['DosenID']);
  $JML = $_REQUEST['JML']+0;
  
  if ($JML <= 0) {
    echo ErrorMsg("Error",
      "Tidak ada mahasiswa yang perlu diset.<br />
      Pilih Angkatan dan Program Studi yang tepat.<br />
      Hubungi Sysadmin untuk informasi lebih lanjut.
      <hr size=1 color=silver />
      <input type=button name='btnKembali' value='Kembali'
      onClick=\"location='?mnux=$_SESSION[mnux]&gos='\" />");
  }
  elseif (empty($DosenID)) {
    echo ErrorMsg("Error",
      "Anda belum memilih dosen.<br />
      Pilih dosen yang akan dijadikan Penasehat Akademik terlebih dahulu.<br />
      Hubungi Sysadmin untuk informasi lebih lanjut.
      <hr size=1 color=silver />
      <input type=button name='btnKembali' value='Kembali'
      onClick=\"location='?mnux=$_SESSION[mnux]&gos='\" />");
  }
  else {
    $hitung = 0;
    for ($i = 1; $i <= $JML; $i++) {
      $MhswID = $_REQUEST['MhswID_'.$i];
      if ($MhswID != '') {
        $hitung++;
        $s = "update mhsw
          set PenasehatAkademik = '$DosenID'
          where KodeID = '".KodeID."'
            and MhswID = '$MhswID' ";
        $r = _query($s);
      }
    }
    if ($hitung == 0) {
      echo ErrorMsg("Error",
      "Anda belum memilih seorang pun mahasiswa yg akan diset.<br />
      Pilih mahasiwa2 yang akan diset Penasehat Akademiknya terlebih dahulu.<br />
      Hubungi Sysadmin untuk informasi lebih lanjut.
      <hr size=1 color=silver />
      <input type=button name='btnKembali' value='Kembali'
      onClick=\"location='?mnux=$_SESSION[mnux]&gos='\" />");
    }
    else BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=", 10);
  }
}
?>
