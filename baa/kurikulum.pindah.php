<?php
// by Arisal Yanuarafi 16 April 2017

// == Parameter ==
$TahunID = GetSetVar('_KurTahunID');
$ProdiID = GetSetVar('_KurProdiID');
$Prg = GetSetVar('_KurProgramID');
$MhswID = GetSetVar('_KurMhswID');
$KurID = GetSetVar('_KurKurikulumID');
TampilkanJudul("Pindah Kurikulum");

function Tampilkan(){
	  $s = "select TahunID from mhsw where KodeID='".KodeID."' group by TahunID order by TahunID DESC";
	  $r = _query($s);
	  $opttahun = "<option value=''></option>";
	  while($w = _fetch_array($r))
		{  $ck = ($w['TahunID'] == $_SESSION['_KurTahunID'])? "selected" : '';
		   $opttahun .=  "<option value='$w[TahunID]' $ck>$w[TahunID]</option>";
		}
	$optprog  = GetOption2('program', "concat(ProgramID, ' - ', Nama)", 'ProgramID', $_SESSION['_KurProgramID'], "KodeID='".KodeID."'", 'ProgramID');
	$optkur  = GetOption2('kurikulum', "concat(KurikulumKode, ' - ', Nama)", 'KurikulumID', $_SESSION['_KurKurikulumID'], "ProdiID='$_SESSION[_KurProdiID]' and KodeID='".KodeID."'", 'KurikulumID');
	$optprd = GetProdiUser($_SESSION['_Login'], $_SESSION['_KurProdiID']);
	
	if (!empty($_SESSION['_KurProdiID'])){
		$kur = (!empty($optkur) ? "<tr><td class=inp>Pilih Kurikulum</td><td class=ul1><select name='_KurKurikulumID'>$optkur</select></td></tr><tr><td class=inp>NPM/NIM</td><td class=ul1><input type=text name='_KurMhswID' value='$_SESSION[_KurMhswID]' size=20 maxlength=20></td></tr>":"Kurikulum tidak ditemukan.");
		$btn = "<input type=submit name='Proses' value='Pindahkan' onClick=\"return confirm('Anda yakin memindahkan kurikulum mahasiswa ke Kurikulum ini?')\"> <input type='hidden' name='gox' value='ProsesThem'>";
	}else {$kur='';$btn=''; }
	?>
    
    <form name='rapikan' action=? method="post">
    <table class="box">
    <tr><td class="inp">Angkatan:</td><td class="ul1"><select name="_KurTahunID"><?php echo $opttahun; ?></select></td></tr>
    <tr><td class="inp">Prodi:</td><td class="ul1"><select name="_KurProdiID"><?php echo $optprd; ?></select></td></tr>
    <tr><td class="inp">Prg. Pendidikan:</td><td class="ul1"><select name="_KurProgramID"><?php echo $optprog; ?></select></td></tr>
    <?php echo $kur;?>
    <tr><td class="ul1" colspan=2><input type="submit" name="Set" value="Cek Kurikulum"> <?php echo $btn;?></td></tr>
    </table>
    </form>
   <?php 
   echo "<center>Anda akan memproses pemindahan kurikulum mahasiswa berdasarkan Angkatan, Program Studi, dan Program Pendidikan tertentu.<br>
		Hanya mahasiswa yang statusnya (Aktif/Pasif/Cuti) saja yang akan diproses.</center>"; ?>
    <?php
}
// Eksekusi
$gos = (empty($_REQUEST['gox'])? "Tampilkan":$_REQUEST['gox']);
$gos();

function ProsesThem(){
	$MhswID = (!empty($_SESSION['_KurMhswID']) ? "and m.MhswID = '$_SESSION[_KurMhswID]' ":"");
	$s = "select m.MhswID,m.Nama
    from mhsw m
    where m.KodeID = '".KodeID."'
      and m.TahunID = '$_SESSION[_KurTahunID]'
	  and m.ProdiID = '$_SESSION[_KurProdiID]'
	  and m.ProgramID = '$_SESSION[_KurProgramID]'
	  and m.StatusMhswID in ('A','P','C')
	  $MhswID
    order by m.MhswID";
  $r = _query($s);
  $n = 0;
  while ($w = _fetch_array($r)) {
  	$_SESSION['PRC_BIPOT_MhswID_'.$n] = $w['MhswID'];
  	$_SESSION['PRC_BIPOT_Nama_'.$n] = $w['Nama'];
	$n++;
  }
  $_SESSION['PRC_BIPOT_TahunID'] = $_SESSION['_KurTahunID'];
  $_SESSION['PRC_BIPOT_ProdiID'] = $_SESSION['_KurProdiID'];
  $_SESSION['PRC_BIPOT_ProgramID'] = $_SESSION['_KurProgramID'];
  $_SESSION['PRC_BIPOT_JML'] = $n;
  $_SESSION['PRC_BIPOT_PRC'] = 0;
  // Tampilkan konfirmasi
  echo Konfirmasi("Konfirmasi Proses",
    "Anda akan memproses Pindah Kurikulum dari prodi: <b>$_SESSION[_KurProdiID]</b> Angkatan: <b>$_SESSION[_KurTahunID]</b>.<br />
    Jumlah yg akan diproses: <b>$_SESSION[PRC_BIPOT_JML]</b>.<br />
    Anda yakin akan memprosesnya?
    <hr size=1 color=silver />
    Opsi: <input type=button name='Proses' value='Proses Sekarang'
      onClick=\"window.location='?mnux=$_SESSION[mnux]&gox=Proses'\" />
      <input type=button name='Batal' value='Batal' 
      onClick=\"window.location='?mnux=$_SESSION[mnux]'\" />"); 
}
function Proses() {
  $jml = $_SESSION['PRC_BIPOT_JML']+0;
  $prc = $_SESSION['PRC_BIPOT_PRC']+0;
  
  $TahunID = $_SESSION['PRC_BIPOT_TahunID'];
  $ProdiID = $_SESSION['PRC_BIPOT_ProdiID'];
  if ($prc < $jml) {
  	// Parameter
  	$MhswID = $_SESSION['PRC_BIPOT_MhswID_'.$prc];
  	$Nama = $_SESSION['PRC_BIPOT_Nama_'.$prc];
    
    $update = "UPDATE mhsw set KurikulumID='$_SESSION[_KurKurikulumID]' where MhswID = '$MhswID'";
    $run = _query($update);
	
    // Tampilkan
    $persen = ($jml > 0)? $prc/$jml*100 : 0;
    $sisa = ($jml > 0)? 100-$persen : 0;
    $persen = number_format($persen);
    echo "<p align=center>
    <font size=+1>$persen %</font><br />
    <img src='img/B1.jpg' width=1 height=20 /><img src='img/B2.jpg' width=$persen height=20 /><img src='img/B3.jpg' width=$sisa height=20 /><img src='img/B1.jpg' width=1 height=20 /><br />
    Memproses: #$prc<br />
    <sup>$MhswID</sup><br />
    <b>$Nama</b><br />
    </p>
    <hr size=1 color=silver />
    <p align=center>
      <input type=button name='Batal' value='Batalkan' 
      onClick=\"window.location='?mnux=$_SESSION[mnux]'\" />
    </p>";

    // Next
    $_SESSION['PRC_BIPOT_PRC']++;
    // Reload
    $tmr = 10;
  echo <<<SCR
    <script>
    window.onload=setTimeout("window.location='?mnux=$_SESSION[mnux]&gox=Proses'", $tmr);
    </script>
SCR;
  }
  else echo Konfirmasi("Proses Selesai",
    "Proses telah selesai.<br />
    Data yang berhasil diproses: <b>$_SESSION[PRC_BIPOT_PRC]</b>.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Kembali' 
    onClick=\"window.location='?mnux=$_SESSION[mnux]'\" />");
}
?>