<?php
// *** Parameters ***
$TahunID = GetSetVar('TahunID');
$FilterMhswID = GetSetVar('FilterMhswID');
$FilterNamaMhsw = GetSetVar('FilterNamaMhsw');
$FilterProdiID = GetSetVar('FilterProdiID');


// *** Main ***
CekBolehAksesModul();
TampilkanJudul("Administrasi Tugas Akhir");
TampilkanFilter();
$gos = (empty($_REQUEST['gos']))? 'DftrMhswTA' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function TampilkanFilter() {
  $optprodi = GetProdiUser($_SESSION['_Login'], $_SESSION['FilterProdiID']);
  echo "<table class=box cellspacing=1 align=center width=940>
  <form name='frmFilterTA' action='?' method=POST>
  <input type=hidden name='gos' value='' />
  <input type=hidden name='tapage' value='1' />
  <tr>
      <td class=inp>Tahun Akd:</td>
      <td class=ul><input type=text name='TahunID' value='$_SESSION[TahunID]' size=5 maxlength=5 /></td>
      <td class=inp>Filter Prodi:</td>
      <td class=ul><select name='FilterProdiID' onChange='this.form.submit()'>$optprodi</select></td>
      </tr>
  <tr><td class=inp>Cari NPM:</td>
      <td class=ul><input type=text name='FilterMhswID' value='$_SESSION[FilterMhswID]' size=20 maxlength=20 /></td>
      <td class=inp>Cari Nama:</td>
      <td class=ul><input type=text name='FilterNamaMhsw' value='$_SESSION[FilterNamaMhsw]' size=20 maxlength=20 /></td>
      </tr>
  <tr>
      <td class=ul colspan=4 align=center>
        <input type=submit name='Cari' value='Cari Data' />
        <input type=button name='ResetFilter' value='Reset Filter'
          onClick=\"location='?mnux=$_SESSION[mnux]&gos=&TahunID=&FilterProdiID=&FilterMhswID=&FilterNamaMhsw='\" />
        ". ($_SESSION['_LevelID']!=100 ? "&#9655;&#9654;
        <input type=button name='DaftarkanMhswTA' value='Daftarkan Tugas Akhir Mhsw'
          onClick=\"javascript:TAEdit(1,0)\" />
        <input type=button name='CetakDaftarTA' value='Cetak Daftar'
          onClick=\"javascript:CetakTA()\" />" : ""). "
      </td>
      </tr>
  </form>
  </table>";
  RandomStringScript();
echo <<<SCR
  <script>
  <!--
  function TAEdit(md,id) {
    if (frmFilterTA.FilterProdiID.value == '') alert("Pilihan Program Studi terlebih dahulu");
    else {
      _rnd = randomString();
      lnk = "$_SESSION[mnux].edit.php?md="+md+"&TAID="+id+"&ProdiID="+frmFilterTA.FilterProdiID.value+"&_rnd="+_rnd;
      win2 = window.open(lnk, "", "width=700, height=500, scrollbars, status");
      if (win2.opener == null) childWindow.opener = self;
    }
  }
  function TAUjian(id) {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].ujian.php?TAID="+id+"&_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=700, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function TAUjianProposal(id) {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].ujian.proposal.php?TAID="+id+"&_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=700, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function CetakTA() {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].cetak.php?TahunID=$_SESSION[TahunID]&_rnd="+_rnd;
    win2 = window.open(lnk, "", "width=700, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }function CetakSKPembimbing(id) {
    _rnd = randomString();
    lnk = "jur/tesis.form.SK.php?_rnd="+_rnd+"&MhswID="+id;
    win2 = window.open(lnk, "", "width=700, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }function CetakSKPengujiProposal(id) {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].SK.penguji.proposal.php?_rnd="+_rnd+"&MhswID="+id;
    win2 = window.open(lnk, "", "width=700, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }function CetakUndanganProposal(id) {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].undang.seminar.proposal.php?_rnd="+_rnd+"&MhswID="+id;
    win2 = window.open(lnk, "", "width=700, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }function CetakUndanganHasil(id) {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].undang.seminar.hasil.php?_rnd="+_rnd+"&MhswID="+id;
    win2 = window.open(lnk, "", "width=700, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }function CetakSKPengujiHasil(id) {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].SK.penguji.hasil.php?_rnd="+_rnd+"&MhswID="+id;
    win2 = window.open(lnk, "", "width=700, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }function EditBimbingan(id) {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].bimbingan.php?_rnd="+_rnd+"&TAID="+id;
    win2 = window.open(lnk, "", "width=700, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }function EditPembimbing(id) {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].pembimbing.php?_rnd="+_rnd+"&TAID="+id;
    win2 = window.open(lnk, "", "width=700, height=600, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function fnKelulusan(id) {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].lulus.php?_rnd="+_rnd+"&TAID="+id;
    win2 = window.open(lnk, "", "width=700, height=400, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  function fnDelete(id) {
    _rnd = randomString();
    lnk = "$_SESSION[mnux].hapus.php?_rnd="+_rnd+"&TAID="+id;
    win2 = window.open(lnk, "", "width=700, height=400, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  //-->
  </script>
SCR;
}
function TampilkanFotoScript() {
  echo <<<SCR
  <script>
  function TampilkanFoto(MhswID, Nama, Foto) {
    jQuery.facebox("<font size=+1>"+Nama+"</font> <sup>(" + MhswID + ")</sup><hr size=1 color=silver /><img src='"+Foto+"' />");
  }
  </script>
SCR;
}
function DftrMhswTA() {
  TampilkanFotoScript();
  // setup where-statement
  $whr_prodi = (empty($_SESSION['FilterProdiID']))? '' : "and m.ProdiID='$_SESSION[FilterProdiID]'";
  $whr_nama = (empty($_SESSION['FilterNamaMhsw']))? '' : "and m.Nama like '$_SESSION[FilterNamaMhsw]%'";
  $whr_nim  = (empty($_SESSION['FilterMhswID']))?   '' : "and m.MhswID like '$_SESSION[FilterMhswID]%'";
  $whr_tahun = (empty($_SESSION['TahunID']))? '' : "and t.TahunID = '$_SESSION[TahunID]' ";
  $whr_pbb = ($_SESSION['_LevelID']!=100)? '' : "and (t.Pembimbing = '$_SESSION[_Login]' or t.Pembimbing2 = '$_SESSION[_Login]') ";
  // Tampilkan
  $tapage = GetSetVar('tapage', 1);
  include_once "class/dwolister.class.php";
  $lst = new dwolister;
  $lst->maxrow = 10;
  $lst->page = $_SESSION['tapage']+0;
  $lst->pageactive = "=PAGE=";
  $lst->pages = "<a href='?mnux=$_SESSION[mnux]&tapage==PAGE='>=PAGE=</a>";
  $lst->tables = "ta t
    left outer join mhsw m on t.MhswID = m.MhswID and m.KodeID = '".KodeID."'
    left outer join dosen d on d.Login = t.Pembimbing and d.KodeID = '".KodeID."'
    left outer join dosen d1 on d1.Login = t.Penguji and d1.KodeID = '".KodeID."'
    left outer join dosen d2 on d2.Login = t.PengujiProposal and d2.KodeID = '".KodeID."'
    left outer join prodi prd on prd.ProdiID = m.ProdiID
    where t.NA = 'N'
    $whr_prodi
    $whr_nama
    $whr_nim
    $whr_tahun
    $whr_pbb
    ";
  $lst->fields = "t.*, m.Nama as NamaMhsw,
    date_format(TglMulai, '%d-%m-%Y') as _TglMulai,
    date_format(TglSelesai, '%d-%m-%Y') as _TglSelesai,
    date_format(TglUjian, '%d-%m-%Y') as _TglUjian,
    date_format(TglUjianProposal, '%d-%m-%Y') as _TglUjianProposal,
    m.PenasehatAkademik, 
    d.Nama as NamaDosen, d.Gelar,
    d1.Nama as NamaPenguji, concat(d1.Gelar1,', ',d1.Gelar) as GelarPenguji,
    d2.Nama as NamaPengujiProposal, concat(d2.Gelar1,', ',d2.Gelar) as GelarPengujiProposal,
    
    replace((select group_concat(concat('&rsaquo; ', td_d.Nama, ' <sup>', td_d.Gelar, '</sup>')) 
    from tadosen td
      left outer join dosen td_d on td_d.Login = td.DosenID and td_d.KodeID = '".KodeID."' 
    where td.TAID = t.TAID
      and td.Tipe=0), ',', '<br />') as _DP,
    
	(select count(tb.TAID) from tabimbingan tb where tb.TAID = t.TAID) as Bimbingan,
	
    replace((select group_concat(concat('&rsaquo; ', td_d.Nama, ' <sup>', td_d.Gelar, '</sup>')) 
    from tadosen td
      left outer join dosen td_d on td_d.Login = td.DosenID and td_d.KodeID = '".KodeID."' 
    where td.TAID = t.TAID
      and td.Tipe=1), ',', ', ') as _DU,
(select count(tb.TAID) from tabimbingan tb where tb.TAID = t.TAID) as Bimbingan,
  
    replace((select group_concat(concat('&rsaquo; ', td_d.Nama, ' <sup>', td_d.Gelar, '</sup>')) 
    from tadosen td
      left outer join dosen td_d on td_d.Login = td.DosenID and td_d.KodeID = '".KodeID."' 
    where td.TAID = t.TAID
      and td.Tipe=2), ',', ', ') as _DUProposal

    ";
  $lst->headerfmt = "<table class=box cellspacing=1 cellpadding=4 width=100%>
    <tr><th class=ttl width=10>Edit</th>
        <th class=ttl width=80>NPM</th>
        <th class=ttl>Nama</th>
        <th class=ttl>Judul</th>
        <th class=ttl width=70>Tgl Mulai<hr size=1 color=white />Selesai</th>
        <th class=ttl width=180>Pembimbing</th>
        <th class=ttl width=70>Bimbingan</th>
		<th class=ttl width=180>Seminar Proposal/Hasil<hr size=1 color=white />Penguji</th>
        <th class=ttl width=10>Lulus</th>
        <th class=ttl width=10>Hapus</th>
        </tr>";
  $lst->footerfmt = "</table></p>";
  $lst->detailfmt = ($_SESSION['_LevelID']!=100)? "<tr>
    <td class=cna=Lulus= align=center>
      <a href='#' onClick=\"javascript:TAEdit(0,=TAID=)\"><img src='img/edit.png' title='Edit Data TA' /></a>
      </td>
    <td class=cna=Lulus= align=center>
      =MhswID=
      <hr size=1 color=silver />
      <sup>=TahunID=</sup>
      </td>
    <td class=cna=Lulus=>=NamaMhsw=</td>
    <td class=cna=Lulus=>=Judul=</td>
    <td class=cna=Lulus= align=center>
      <sup>=_TglMulai=
      <hr size=1 color=silver />
      =_TglSelesai=</sup>
      </td>
	<td class=cna=Lulus=>
      &bull; =NamaDosen= <sup>=Gelar=</sup><br />
      =_DP=
      <div align=right>
      <a href='#' onClick=\"javscript:EditPembimbing(=TAID=)\" title='Edit Dosen Pembimbing'><img src='img/edit.png' /></a> 
      <a href='#' onClick=\"javscript:CetakSKPembimbing(=MhswID=)\" title='Cetak SK Pembimbing'><img src='img/printer.gif' /></a>
      </div>
      </td>
    <td class=cna=Lulus= align=center>
	  =Bimbingan=&times;
	  <div align=right>
      <a href='#' onClick=\"javscript:EditBimbingan(=TAID=)\" title='Edit Bimbingan'><img src='img/edit.png' /></a></div>
	  </td>
    <td class=cna=Lulus= align=center>
    <h4>Seminar Proposal
    <a href='#' onClick=\"javascript:TAUjianProposal(=TAID=)\" title='Edit Dosen Penguji'><img src='img/edit.png' /></a></h4>
      <sup>=_TglUjianProposal= Pukul =JamUjianProposal=</sup>
      <div align=left>
      &bull; =NamaPengujiProposal= <sup>=GelarPengujiProposal=</sup><br />
      =_DUProposal=
      </div>
      <div align=right>
      &laquo; <a href='#' onClick=\"javscript:CetakSKPengujiProposal(=MhswID=)\" title='Cetak SK Penguji Seminar Proposal'>SK Penguji</a>
      &laquo; <a href='#' onClick=\"javscript:CetakUndanganProposal(=MhswID=)\" title='Undangan Seminar Proposal'>Undangan</a>
      </div>
      <hr size=1 color=silver />
      <h4>Seminar Hasil <a href='#' onClick=\"javascript:TAUjian(=TAID=)\" title='Edit Dosen Penguji'><img src='img/edit.png' /></a></h4>
      <sup>=_TglUjian= Pukul =JamUjian=</sup>
      <div align=left>
      &bull; =NamaPenguji= <sup>=GelarPenguji=</sup><br />
      =_DU=
      </div>
      <div align=right>
      &laquo; <a href='#' onClick=\"javscript:CetakSKPengujiHasil(=MhswID=)\" title='Cetak SK Penguji Seminar Hasil'>SK Penguji</a>
      &laquo; <a href='#' onClick=\"javscript:CetakUndanganHasil(=MhswID=)\" title='Undangan Seminar Hasil'>Undangan</a>
      </div>

      </td>
    <td class=cna=Lulus= align=center>
      <a href='#' onClick=\"javascript:fnKelulusan(=TAID=)\"><img src='img/=Lulus=.gif' /></a>
      </td>
      <td class=cna=Lulus= align=center>
      <a href='#' onClick=\"javascript:fnDelete(=TAID=)\"><img src='img/del.gif' /></a>
      </td>
    </tr>
    <tr><td bgcolor=silver colspan=9 height=1></td></tr>" : 
    "<tr>
    <td class=cna=Lulus= align=center>
      #
      </td>
    <td class=cna=Lulus= align=center>
      =MhswID=
      <hr size=1 color=silver />
      <sup>=TahunID=</sup>
      </td>
    <td class=cna=Lulus=>=NamaMhsw=</td>
    <td class=cna=Lulus=>=Judul=</td>
    <td class=cna=Lulus= align=center>
      <sup>=_TglMulai=
      <hr size=1 color=silver />
      =_TglSelesai=</sup>
      </td>
	<td class=cna=Lulus=>
      &bull; =NamaDosen= <sup>=Gelar=</sup><br />
      =_DP=
      </td>
    <td class=cna=Lulus= align=center>
	  =Bimbingan=&times;
	  <div align=right>
      <a href='#' onClick=\"javscript:EditBimbingan(=TAID=)\" title='Edit Bimbingan'><img src='img/edit.png' /></a>
	  </td>
    <td class=cna=Lulus= align=center>
      <sup>=_TglUjian=</sup>
      <hr size=1 color=silver />
      <div align=left>
      &bull; =NamaPenguji= <sup>=GelarPenguji=</sup><br />
      =_DU=
      </div>
      </td>
    <td class=cna=Lulus= align=center>
      <img src='img/=Lulus=.gif' />
      </td>
      <td class=cna=Lulus= align=center>
      -
      </td>
    </tr>
    <tr><td bgcolor=silver colspan=10 height=1></td></tr>";
  echo $lst->TampilkanData();
  echo $ttl;
  echo "<p>Hal.: ". $lst->TampilkanHalaman() . "<br />".
    "Total: " . number_format($lst->MaxRowCount). "</p>";
}
function CekBolehAksesModul() {
  $arrAkses = array(1, 20, 40, 42, 43, 100, 56, 66, 440, 51);
  $key = array_search($_SESSION['_LevelID'], $arrAkses);
  if ($key === false)
    die(ErrorMsg('Error',
      "Anda tidak berhak mengakses modul ini.<br />
      Hubungi SysAdmin untuk informasi lebih lanjut."));
}
?>