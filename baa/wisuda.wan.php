<?php

//Kostumisasi oleh Arisal Yanuarafi Mulai 7 Maret 2012

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Daftarkan Wisudawan");
echo <<<SCR
  <script src="../$_SESSION[mnux].wan.script.js"></script>
SCR;

// *** Parameters ***
$TahunID = GetSetVar('TahunID');
$ProdiID = GetSetVar('ProdiID');
$md = $_REQUEST['md']+0;
$id = $_REQUEST['id']+0;

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'Edit' : $_REQUEST['gos'];
$gos($md, $id);

// *** Functions ***
function arrayPrasyaratWisuda($w) {
  $def = explode(',', $w['Prasyarat']);
  $hsl = array();
  $s = "select PrasyaratID, Nama
    from wisudaprasyarat
    where KodeID = '".KodeID."' and NA='N'
    order by PrasyaratID"; 
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    $ada = array_search($w['PrasyaratID'], $def);
    $ck = ($ada === false)? '' : 'checked';
    $hsl[] = "<input type=checkbox name='$w[PrasyaratID]' value='Y' $ck /> $w[PrasyaratID] &#8594; $w[Nama]";
  }
  $_hsl = implode("<br />", $hsl);
  return $_hsl;
}

function Edit($md, $id) {
  if ($md == 0) {
    $jdl = "Edit Wisudawan";
    $w = GetFields('wisudawan w,mhsw m', "w.MhswID=m.MhswID and WisudawanID", $id, 'w.*,m.TanggalMasuk,m.IPK');
    $w['Nama'] = GetaField('mhsw', "KodeID='".KodeID."' and MhswID", $w['MhswID'], 'Nama');
    $prs = arrayPrasyaratWisuda($w);
    $ro = "readonly=true";
    $btn = "";
  }
  elseif ($md == 1) {
    $jdl = "Tambah Wisudawan";
    $w = array();
    $w['PrasyaratLengkap'] = 'N';
    $prs = arrayPrasyaratWisuda($w);
    $ro = '';
    $btn = "&raquo;
        <a href='#'
          onClick=\"javascript:CariMhsw('$_SESSION[ProdiID]', 'frmWisuda')\" />Cari...</a> |
        <a href='#' onClick=\"javascript:frmWisuda.MhswID.value='';frmWisuda.NamaMhsw.value=''\">Reset</a>";
  }
  else die(ErrorMsg('Error',
    "Mode edit <b>$md</b> tidak dikenali.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Tutup' onClick='window.close()' />"));
  // tampilkan
  TampilkanJudul($jdl);
  	$Lengkap = ($w['PrasyaratLengkap'] == 'Y')? 'checked' : '';
  	$optpred = "<option></option>";
    $optpred .= ($w['Predikat']=='Dengan Pujian')? "<option value='Dengan Pujian' selected>Dengan Pujian</option>" : "<option value='Dengan Pujian'>Dengan Pujian</option>";
    $optpred .= ($w['Predikat']=='Sangat Memuaskan')? "<option value='Sangat Memuaskan' selected>Sangat Memuaskan</option>" : "<option value='Sangat Memuaskan'>Sangat Memuaskan</option>";
    $optpred .= ($w['Predikat']=='Memuaskan')? "<option value='Memuaskan' selected>Memuaskan</option>" : "<option value='Memuaskan'>Memuaskan</option>";

  echo <<<ESD
  <table class=bsc cellspacing=1 width=100%>
  <form name='frmWisuda' action='../$_SESSION[mnux].wan.php' method=POST>
  <input type=hidden name='md' value='$md' />
  <input type=hidden name='id' value='$id' />
  <input type=hidden name='TahunID' value='$_SESSION[TahunID]' />
  <input type=hidden name='ProdiID' value='$_SESSION[ProdiID]' />
  <input type=hidden name='gos' value='Simpan' />
  
  <tr><td class=inp>Mahasiswa:</td>
      <td class=ul>
      <input type=text name='MhswID' value='$w[MhswID]' size=10 maxlength=30 $ro />
        <input type=text name='NamaMhsw' value='$w[Nama]' size=30 maxlength=50 $ro
          onKeyUp="javascript:CariMhsw('$_SESSION[ProdiID]', 'frmWisuda')"/>
        $btn
      </td></tr>
  <tr><td class=inp>Judul Skripsi/<br />Tugas Akhir:</td>
      <td class=ul><textarea name='Judul' cols=60 rows=2>$w[Judul]</textarea></td></tr>
      <tr><td class=inp>Tanggal Daftar:</td>
      <td class=ul>
    <input type='text' name='TglDaftar' size='12' maxlength='12' value='$w[TglDaftar]' /> <sup> * Format: 2012-01-30
      </td></tr>
      <tr><td class=inp>Tanggal Mulai:</td>
      <td class=ul>
    <input type='text' name='TglMulai' size='12' maxlength='12' value='$w[TglMulai]' /> <sup> * Format: 2012-01-30
      </td></tr>
      <tr><td class=inp>Tanggal Selesai:</td>
      <td class=ul>
    <input type='text' name='TglSelesai' size='12' maxlength='12' value='$w[TglSelesai]' /> <sup> * Format: 2012-01-30
      </td></tr>
	  <tr><td class=inp>Tanggal Sidang:</td>
      <td class=ul>
    <input type='text' name='TglSidang' size='12' maxlength='12' value='$w[TglSidang]' /> <sup> * Format: 2012-01-30
      </td></tr>
<tr><td class=inp>Nama Pembimbing:</td>
      <td class=ul>1. <input type='text' name='pembimbing' size='40' maxlength='50' value='$w[Pembimbing]' /><sup> * Mohon diperiksa nama/gelar pembimbing.</sup><br />
      2. <input type='text' name='pembimbing2' size='40' maxlength='50' value='$w[Pembimbing2]' /><sup> * Mohon diperiksa nama/gelar pembimbing.</sup></td></tr>
	    <tr><td class=inp>Predikat:</td>
      <td class=ul>
  <select name='Predikat'>$optpred</select> <== <sup>Harap diperiksa kembali</sup>
      </td></tr>
  
  <tr><td class=inp>Prasyarat:</td>
      <td class=ul>
      $prs<hr>
      Harap diceklis semua prasyarat (termasuk labor walaupun tidak ada adm. Labor di Fakultas) bila mahasiswa benar-benar telah menyelesaikan keseluruhan administrasi di Fakultas.
      </td></tr>
  <tr><td class=inp>Nilai IPK:</td>
      <td class=ul>
      <input type='text' name='IPK' size='5' maxlength='4' value='$w[IPK]' />
      </td></tr>
  <tr><td class=inp>Tanggal Masuk:</td>
      <td class=ul>
      <input type='text' name='TanggalMasuk' size='20' maxlength='10' value='$w[TanggalMasuk]' />
      </td></tr>
  <tr><td class=inp>Nilai Toefl:</td>
      <td class=ul>
      <input type='text' name='Toefl' size='5' maxlength='4' value='$w[Toefl]' />
      </td></tr>
  <tr><td class=ul colspan=2 align=center>
      <input type=submit name='Simpan' value='Simpan' />
      <input type=button name='Batal' value='Batal' onClick='window.close()' />
      </td></tr>
  </form>
  </table>
  
  <div class='box0' id='carimhsw'></div>
  
  <script>
  <!--
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
  function CariMhsw(ProdiID, frm) {
    if (eval(frm + ".NamaMhsw.value != ''")) {
      eval(frm + ".NamaMhsw.focus()");
      showMhsw(ProdiID, frm, eval(frm +".NamaMhsw.value"), 'carimhsw');
      toggleBox('carimhsw', 1);
    }
  }
  //-->
  </script>
ESD;
}
function CekPrasyarat() {
  $hsl = array();
  $s = "select PrasyaratID, Nama
    from wisudaprasyarat
    where KodeID='".KodeID."' and NA='N'
    order by PrasyaratID";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    if ($_REQUEST[$w['PrasyaratID']] == 'Y') $hsl[] = $w['PrasyaratID'];
  }
  $_hsl = implode(',', $hsl);
  return $_hsl;
}
function CekPrasyaratlengkap() {
  $hsl = 0;
  $s = "select PrasyaratID, Nama
    from wisudaprasyarat
    where KodeID='".KodeID."' and NA='N'
    order by PrasyaratID";
  $r = _query($s);
  $n=0;
  while ($w = _fetch_array($r)) {
    $n++;
    if ($_REQUEST[$w['PrasyaratID']] == 'Y') $hsl++;
  }
  $ck = ($n==$hsl)? 'Y' : 'N';
  return $ck;
}
function Simpan($md, $id) {
  $Judul = sqling($_REQUEST['Judul']);
  $Predikat = sqling($_REQUEST['Predikat']);
  $TglSidang = sqling($_REQUEST['TglSidang']);
  $TglDaftar = sqling($_REQUEST['TglDaftar']);
  $TglMulai = sqling($_REQUEST['TglMulai']);
  $TglSelesai = sqling($_REQUEST['TglSelesai']);
  $Pembimbing = sqling($_REQUEST['pembimbing']);
  $Pembimbing2 = sqling($_REQUEST['pembimbing2']);
  $Toefl = sqling($_REQUEST['Toefl']);
  $TanggalMasuk = sqling($_REQUEST['TanggalMasuk']);
  $IPK = sqling($_REQUEST['IPK']);
  $wisuda = GetFields('wisuda',"NA='N' and KodeID",KodeID,'*');
  $QryPrasyarat = CekPrasyarat();  
  $_QryPrasyarat = (empty($QryPrasyarat))? '' : ", Prasyarat = '$QryPrasyarat' ";
  $PrasyaratLengkap = CekPrasyaratlengkap();
  $SKSLulus = GetaField("krs k left outer join khs h on k.KHSID=h.KHSID and h.KodeID='".KodeID."'", "k.MhswID='$id' AND k.GradeNilai is not Null  AND k.GradeNilai != '' and not k.GradeNilai='T' AND k.GradeNilai !='-' and not k.GradeNilai='E' and k.Tinggi='*'  and k.KodeID",
    KodeID, "sum(k.SKS)");
  if ($md == 0) {
    $s = "update wisudawan
      set PrasyaratLengkap = '$PrasyaratLengkap',
          Judul = '$Judul',
		  Pembimbing = '$Pembimbing',
          Pembimbing2 = '$Pembimbing2',
          Toefl = '$Toefl',
		  Predikat = '$Predikat',
		  TglSidang = '$TglSidang',
          TglDaftar = '$TglDaftar',
          TglMulai = '$TglMulai',
          TglSelesai = '$TglSelesai',
          IPK = '$IPK',
          LoginEdit = '$_SESSION[_Login]', TanggalEdit = now()
          $_QryPrasyarat
      where WisudawanID = '$id' ";
    $r = _query($s);
	
	      // Update Tanggal Lulus dan IPK Mahasiswa bersangkutan
	  $mh = GetaField('wisudawan', "WisudawanID",$id, 'MhswID');
      $maxSesi = GetaField('khs', "MhswID='$mh' and KodeID", KodeID, 'max(Sesi)+0');
    //$IPK = HitungIPK2($mh);
	$s1 = "update mhsw set Predikat='$Predikat',IPK='$IPK',TanggalMasuk='$TanggalMasuk' where MhswID='$mh'";
      $r1 = _query($s1);
       
	if (empty($Predikat)){ echo "<script>alert('Predikat yudisium masih kosong! Harap diisi!!');</script>"; }
    TutupScript();
  
  }
  elseif ($md == 1) {
    $MhswID = sqling($_REQUEST['MhswID']);
    $ada = GetFields("wisudawan", "KodeID='".KodeID."' and MhswID", $MhswID, '*');
	 if (empty($ada)) {
	 	//Proses No. Ijazah by Arisal Yanuarafi
	 	$prodi=GetaField('mhsw', "MhswID", $MhswID, 'ProdiID');
		$p=GetaField('prodi', "ProdiID", $prodi, 'SUBSTR( FormatNIM, 7, LENGTH( FormatNIM ) -14 )');
		$periodeID=GetaField('wisuda', "NA",N, 'PeriodeID');
	  	$prog = GetFields('prodi p, jenjang j', "j.JenjangID=p.JenjangID and ProdiID", $prodi, 'j.Nama as NMProg');
		$u=GetFields('wisudawan', "KodeID='".KodeID."' and TahunID", $_SESSION['TahunID'], 'count(MhswID) as Urut');
		$urutan=($u[Urut]+1)+0;
		if (($urutan < 100) && ($urutan >= 10)) { $_urutan='0'.$urutan; 	}
		elseif ($urutan < 10) { $_urutan='00'.$urutan; 	}
		else {$_urutan=$urutan; }
		$nomerijazah = $_urutan.'--'.$prog[NMProg].'.'.$p.'/'.$periodeID;
		$nomertranskrip = $_urutan.'--'.$prog[NMProg].'.'.$p.'/'.$periodeID;
		
      	$gel = GetFields('wisuda', "KodeID='".KodeID."' and TahunID", $_SESSION['TahunID'], '*');
     	$s = "insert into wisudawan
        (KodeID, TahunID, WisudaID, Toefl,
        MhswID, Judul, PrasyaratLengkap, Prasyarat,
        LoginBuat, TanggalBuat,NomerIjazah,NomerTranskrip,Pembimbing,Pembimbing2, Predikat,TglSidang,TglDaftar,TglMulai,TglSelesai)
        values
        ('".KodeID."', '$_SESSION[TahunID]', '$gel[WisudaID]', '$Toefl',
        '$MhswID', '$Judul', '$PrasyaratLengkap', '$QryPrasyarat',
        '$_SESSION[_Login]', now(), '$nomerijazah','$nomertranskrip','$Pembimbing', '$Pembimbing2','$Predikat', '$TglSidang'
        , '$TglDaftar', '$TglMulai', '$TglSelesai')";
      $r = _query($s);
	  
	  $jumlah=GetFields('wisuda',"NA",N,'Jumlah');
	  $_jumlah=$jumlah[Jumlah]+1;
	  $s="update wisuda set jumlah=$_jumlah where NA='N'";
      $r = _query($s);
           

   // Update Tanggal Lulus dan IPK Mahasiswa bersangkutan
    $IPK = GetaField('krs left outer join khs on krs.KHSID=khs.KHSID', "krs.KodeID='".KodeID."' and GradeNilai !='' And GradeNilai is not Null And not GradeNilai='-' and not GradeNilai='T' and krs.Tinggi='*' and krs.NA='N' and krs.MhswID",$MhswID,'sum(krs.BobotNilai * krs.SKS)/sum(krs.SKS)');
	$s1 = "update mhsw set IPK='$IPK', 
    						Predikat='$Predikat', 
                           	TotalSKS='$SKSLulus', 
                            NoIjazah='$nomerijazah',
                            TglIjazah = '$wisuda[TglWisuda]',
                            WisudaID= '$wisuda[WisudaID]'
                            where MhswID='$MhswID'";
      $r1 = _query($s1);
      TutupScript();     
    }
    else die(ErrorMsg('Error',
      "Mahasiswa dengan NIM: <b>$MhswID</b> telah pernah terdaftar di wisuda.<br />
      Berikut adalah datanya:<br />
      NIM: <b>$ada[MhswID]</b><br />
      Gelombang: <b>$ada[TahunID]</b>
      <hr size=1 color=silver />
      <input type=button name='Tutup' value='Tutup' onClick='window.close()' />"));
  }
  else die(ErrorMsg('Error',
    "Mode edit <b>$md</b> tidak dikenali.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Tutup' onClick='window.close()' />"));
}
function TutupScript() {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    opener.location='../index.php?mnux=$_SESSION[mnux]&_tabWisuda=$_SESSION[_tabWisuda]&gos=Daftar';
    self.close();
    return false;
  }
  ttutup();
</SCRIPT>
SCR;
}
?>
