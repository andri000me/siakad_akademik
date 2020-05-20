<?php

// *** Parameters ***
$_Prodi = GetSetVar('_Prodi');
$_MahasiswaID = GetSetVar('_MahasiswaID');
$_MK = GetSetVar('_MK');
$_Prog  = GetSetVar('_Prog');
$nilaipage = GetSetVar('nilaipage', 1);

// *** Main ***
TampilkanJudul("Perubahan Nilai");
TampilkanHeader();
RandomStringScript();
// validasi
if (!empty($_Prog) && !empty($_Prodi)) {
  $gos = (empty($_REQUEST['gos']))? 'DftrPerubahan' : $_REQUEST['gos'];
  $gos();
}

// *** Functions ***
function TampilkanHeader() {
  $optprodi = GetProdiUser($_SESSION['_Login'], $_SESSION['_Prodi']);
  $optprog  = GetOption2('program', "concat(ProgramID, ' - ', Nama)", 'ProgramID', $_SESSION['_Prog'], "KodeID='".KodeID."'", 'ProgramID');
 
    
 $URL = $_SERVER['REQUEST_URI'];
  
  
  echo <<<SCR
  <table class=box cellspacing=1 align=center width=900>
  <form name='frmHeader' action='?' method=POST>
  <input type=hidden name='mnux' value='$_SESSION[mnux]' />
  <input type=hidden name='gos' value='' />
  
  <tr><td class=wrn width=2></td>
      <td class=inp>Prg. Pendidikan:</td>
      <td class=ul1><select name='_Prog'>$optprog</select></td>
      <td class=inp>Program Studi:</td>
      <td class=ul1><select name='_Prodi'>$optprodi</select></td>
      </tr>
  <tr><td class=wrn width=2></td>
  	<td class=inp nowrap>
      Saring berdasarkan NPM:</td> 
	  <td class=ul1 colspan=3><input type=text name='_MahasiswaID' placeholder='NPM' value='$_SESSION[_MahasiswaID]' >
      </td></tr>
  <tr><td class=wrn width=2></td>
    <td class=inp nowrap>
      Saring berdasarkan Nama MK:</td> 
    <td class=ul1 colspan=3><input type=text name='_MK' placeholder='Bahasa Indonesia' value='$_SESSION[_MK]' > <input type=submit name='btnKirim' value='Kirim Parameter' />
      </td></tr>
  </form>
  </table>
SCR;
}
function DftrPerubahan() {
	$whr = (!empty($_SESSION['_MahasiswaID']) ? " and k.MhswID = '$_SESSION[_MahasiswaID]' ":"");
  $whr .= (!empty($_SESSION['_MK']) ? " and mk.Nama like '%$_SESSION[_MK]%' ":"");

  include_once "class/dwolister.class.php";
  $lst = new dwolister;
  $lst->maxrow = 10;
  $lst->page = $_SESSION['nilaipage']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=$_SESSION[mnux]&nilaipage==PAGE='>=PAGE=</a>";
  $lst->tables = "koreksinilai k left outer join mhsw m on m.MhswID=k.MhswID
  			left outer join mk mk on mk.MKID=k.MKID
			left outer join karyawan w on w.Login=k.LoginBuat
    	where m.ProgramID='$_SESSION[_Prog]' and m.ProdiID='$_SESSION[_Prodi]' 
      $whr
      order by k.TglBuat DESC";
  $lst->fields = "k.*, m.ProdiID,m.ProgramID,mk.Nama as _Matakuliah, w.Nama as _Operator";
  $lst->headerfmt = "
	<table class=box cellspacing=1 cellpadding=4 width=900>
    <tr><th class=ttl width=10>#</th>
      <th class=ttl width=120>Tanggal</th>
      <th class=ttl width=60>TahunAkd</th>
      <th class=ttl>Matakuliah</th>
      <th class=ttl width=100>Pejabat</th>
	  <th class=ttl width=100>Jabatan</th>
      <th class=ttl width=40>Nilai Awal</th>
	  <th class=ttl width=40>Nilai Akhir</th>
      <th class=ttl width=100>Operator</th>
      </tr>";
  $lst->footerfmt = "</table></p>";
  $lst->detailfmt = "<tr>
  	  <td width=10>=NOMER=</td>
      <td width=120>=TglBuat=</td>
      <td width=60>=TahunID=</td>
      <td>=_Matakuliah=<hr /><sup>=MhswID=<br />NB: =Keterangan=</sup></td>
      <td width=100>=Pejabat=</td>
	  <td width=100>=Jabatan=</td>
      <td width=30>=GradeLama=</td>
	  <td width=30>=GradeNilai=</td>
      <td width=100>=LoginBuat= =_Operator=<hr /><sup>Modul: =Modul=</sup></td>
    </tr>";
  echo $lst->TampilkanData();
  echo $ttl;
  echo "<p>Hal.: ". $lst->TampilkanHalaman() . "<br />".
    "Total: " . number_format($lst->MaxRowCount). "</p>";
}

