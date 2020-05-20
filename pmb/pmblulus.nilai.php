<?php

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Nilai USM");

// *** Parameters ***
$PMBID = sqling($_REQUEST['PMBID']);
$pmb = GetFields('pmb', "KodeID='".KodeID."' and PMBID", $PMBID, '*');

// Cek, apakah data valid atau tidak
if (empty($pmb)) {
  die(ErrorMsg('Error',
    "Data PMB dengan nomer: <b>$PMBID</b> tidak ditemukan.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" />"));
}

// Cek apakah sudah diproses menjadi mahasiswa atau belum
if (!empty($pmb['MhswID']))
  die(ErrorMsg('Error',
    "<img src='../img/lock.jpg' align=right />
    Anda sudah tidak dapat mengubah data ini karena Cama sudah diproses menjadi mahasiswa.<br />
    Nomer registrasi mahasiswa (NIM): <b>$pmb[MhswID]</b>.<br />
    Hubungi Sysadmin untuk informasi lebih lanjut.
    <hr size=1 color=silver />
    <input type=button name='Tutup' value='Tutup' onClick=\"window.close()\" />"));

// *** Main ***
TampilkanJudul("Nilai USM");
$gos = (empty($_REQUEST['gos']))? 'Edit' : $_REQUEST['gos'];
$gos($PMBID, $pmb);

// *** Functions ***
function TampilkanHeader($pmb) {
  $STA = GetaField('statusawal', "StatusAwalID", $pmb['StatusAwalID'], 'Nama');
  $FRM = GetaField('pmbformulir', "KodeID='".KodeID."' and PMBFormulirID", $pmb['PMBFormulirID'], 'Nama');
  $p1 = GetaField('prodi', "KodeID='".KodeID."' and ProdiID", $pmb['Pilihan1'], 'Nama');
  $p2 = GetaField('prodi', "KodeID='".KodeID."' and ProdiID", $pmb['Pilihan2'], 'Nama');
  $p3 = GetaField('prodi', "KodeID='".KodeID."' and ProdiID", $pmb['Pilihan3'], 'Nama');
  echo "<table class=bsc cellspacing=1 width=100%>
  <tr><td class=inp width=100>No. PMB:</td>
      <td class=ul1>$pmb[PMBID]&nbsp;</td>
      <td class=inp>Nama Cama:</td>
      <td class=ul1>$pmb[Nama]&nbsp;</td>
      </tr>
  <tr><td class=inp>Status Masuk:</td>
      <td class=ul1>$STA&nbsp;</td>
      <td class=inp>Formulir:</td>
      <td class=ul1>$FRM&nbsp;</td>
      </tr>
  <tr><td class=inp>Pilihan 1:</td>
      <td class=ul1 colspan=3>$pmb[Pilihan1] - $p1</td>
      </tr>
  <tr><td class=inp>Pilihan 2:</td>
      <td class=ul1 colspan=3>$pmb[Pilihan2] - $p2</td>
      </tr>
  <tr><td class=inp>Pilihan 3:</td>
      <td class=ul1 colspan=3>$pmb[Pilihan3] - $p3</td>
      </tr>
  </table>";
}

function Edit($PMBID, $pmb) {
  TampilkanHeader($pmb);
  
  $optprodi = AmbilPilihanFinal($pmb);
  $getnilaisekolah = (empty($pmb['NilaiSekolah']))? 'N/A' : $pmb['NilaiSekolah'];
  $gel = GetaField('pmbperiod', "KodeID='".KodeID."' and NA", 'N', "PMBPeriodID");
  $getnilaiujian = (empty($pmb['NilaiUjianTotal']))? GetaField('ruangusm', "PMBID='$pmb[PMBID]' and PMBPeriodID='$gel' and KodeID", KodeID, "sum(NilaiUSM)")+0: $pmb['NilaiUjianTotal']+0;
  $xx = GetFields('ruangusm', "PMBID='$pmb[PMBID]' and PMBPeriodID='$gel' and KodeID", KodeID, "sum(NilaiUSM) as N, count(ProdiUSMID) as J");
  $rata = ($xx['J']>0)? number_format($xx['N']/$xx['J'],2) : 0; 
  $getnilaiujianrata = (empty($pmb['NilaiUjian']))? $rata : $pmb['NilaiUjian']+0;
  $getgrade = (empty($pmb['GradeNilai']))? 
		GetaField('pmbgrade', "NilaiUjianMin <= $getnilaiujianrata and $getnilaiujianrata <= NilaiUjianMax and KodeID", KodeID, 'GradeNilai') : 
		$pmb['GradeNilai'];
  $optgrd = GetOption2('pmbgrade', "concat(GradeNilai, ' (', if (Keterangan is NULL, '', Keterangan), ')')", 'GradeNilai', $getgrade, "KodeID='".KodeID."'", 'GradeNilai');
  $arrPT = explode('~', $pmb['PrestasiTambahan']);
  foreach($arrPT as $Prestasi) 
  {	if(!empty($Prestasi)) $PrestasiTambahan .= (empty($PrestasiTambahan))? $Prestasi : "<br>".$Prestasi;
  }
  $ck = ($pmb['LulusUjian'] == 'Y')? 'checked' : '';
  echo '
  		<script>
			function cekThisForm(){
				var errmsg = "";
				var cek = document.getElementById("LulusUjian").checked;
				var nilai = document.getElementById("NilaiUjian").value;
				if (cek == true){
					if (nilai == 0){
						errmsg += "Nilai ujian masih bernilai 0 \\n";
					}
				}
				if (errmsg != ""){
					alert (errmsg);
					return false;
				} else {
					return true;
				}
			}
			
		</script>
  		';
  echo "<table class=bsc cellspacing=1 width=100%>
  
  <form action='../$_SESSION[mnux].nilai.php' method=POST onsubmit='return cekThisForm()'>
  <input type=hidden name='PMBID' value='$PMBID' />
  <input type=hidden name='gos' value='Simpan' />
  ";
  
  $pmbformulir = GetFields('pmbformulir', "KodeID='".KodeID."' and PMBFormulirID", $pmb['PMBFormulirID'], 'USM, Wawancara');
  // Bila PMB Formulir memiliki komponen ujian, ambil kolom dan isi detail USM nya
  if($pmbformulir['USM'] == 'Y')
  {
	  $DetailUSM = AmbilDetailUSM($pmb);
	  echo "
	  <tr><th class=ttl colspan=2>Detail Penilaian</th></tr>
	  $DetailUSM
	  <tr><th class=ttl colspan=2>Penilaian Akhir</th></tr>";
	  /*
    <tr><td class=inp>Nilai Rata-rata USM:</td>
		  <td class=ul1><input type=text id='NilaiUjian' name='NilaiUjian' 
      value='$getnilaiujianrata' size=4 maxlength=4 style='text-align:right' readonly=true></td>
		  </tr>
      */
  }
  
  if($pmbformulir['Wawancara'] == 'Y')
  {	$HasilWawancara = GetaField('wawancara w', "w.Tanggal = (select max(w2.Tanggal) from wawancara w2 where w2.PMBID='$PMBID' group by w2.PMBID) and w.PMBID='$PMBID' and w.KodeID", KodeID, "HasilWawancara");
	$_HasilWawancara = (!empty($HasilWawancara))? "<td class=ul1><input type=text name='HasilWawancara' value='$HasilWawancara' disabled></td>" : "<td><b>Belum Wawancara</b></td>";
	echo "<tr><td class=inp>Hasil Wawancara:</td>
				$_HasilWawancara
				</tr>";
  }
  
  echo "
  <tr><td class=inp>Nilai Sekolah Terakhir:</td>
	  <td class=ul1><input type=text name='NilaiSekolah' value='$getnilaisekolah' size=4 maxlength=4 style='text-align:right' disabled></td></tr>
  <tr><td class=inp>Prestasi Tambahan:</td>
	  <td class=ul1>$PrestasiTambahan</td>
	  </tr>";
	  
  echo "
  <tr><td class=inp>Catatan Lainnya:</td>
	  <td class=ul1><textarea name='Catatan' cols=30 row=2>$pmb[Catatan]</textarea></td>
	  </tr>
  <tr><td class=inp width=100>Pilihan Final:</td>
      <td class=ul1><select name='ProdiID'>$optprodi</select></td>
      </tr>
  <tr><td class=inp>Lulus?</td>
      <td class=ul1><input type=checkbox id='LulusUjian' name='LulusUjian' value='Y' $ck />
        Beri tanda centang jika lulus
      </td></tr>
  <tr><td class=inp>Grade:</td>
      <td class=ul1><input type=text name='GradeNilai' value='$getgrade' size=5 readonly></td>
      </tr>
  <tr><td class=ul1 colspan=2 align=center>
      <input type=submit name='Simpan' value='Simpan' />
      <input type=button name='Batal' value='Batal' onClick=\"window.close()\" />
      </td></tr>
  </form>
  </table>";
}

function AmbilPilihanFinal($w) {
  $a = '';
  for ($i = 1; $i <= 3; $i++) {
    if (!empty($w['Pilihan'.$i])) {
      $_p = GetaField('prodi', "KodeID='".KodeID."' and ProdiID", $w['Pilihan'.$i], 'Nama');
      $sel = ($w['Pilihan'.$i] == $w['ProdiID'])? 'selected' : '';
      $a .= "<option value='".$w['Pilihan'.$i]."' $sel>".$w['Pilihan'.$i].' - '.$_p."</option>";
    }
  }
  return $a;
}
function Simpan($PMBID, $pmb) {
  $ProdiID = sqling($_REQUEST['ProdiID']);
  $Catatan = sqling($_REQUEST['Catatan']);
  $NilaiUjianTotal = $_REQUEST['NilaiUjianTotal'];
  $rat1 = $_REQUEST['rat_'.$pmb['Pilihan1']]+0;
  $rat2 = $_REQUEST['rat_'.$pmb['Pilihan2']]+0;
  $rat3 = $_REQUEST['rat_'.$pmb['Pilihan3']]+0;
  $LulusUjian = (empty($_REQUEST['LulusUjian']))? 'N' : sqling($_REQUEST['LulusUjian']);
  // echo "#$rat1~$rat2~$rat3#";
  //
  $GradeNilai1 = GetaField('pmbgrade', "NilaiUjianMin <= $rat1
              and $rat1 <= NilaiUjianMax and KodeID", KodeID, 'GradeNilai');
  $GradeNilai2 = GetaField('pmbgrade', "NilaiUjianMin <= $rat2
              and $rat2 <= NilaiUjianMax and KodeID", KodeID, 'GradeNilai');
  $GradeNilai3 = GetaField('pmbgrade', "NilaiUjianMin <= $rat3
              and $rat3 <= NilaiUjianMax and KodeID", KodeID, 'GradeNilai');
  //jika lulusnya di centang
  if($LulusUjian=='Y'){
    if($ProdiID==$pmb['Pilihan1']){
      $grd = $GradeNilai1;
      $NilaiUjian = $rat1;
    }elseif($ProdiID==$pmb['Pilihan2']){
      $grd = $GradeNilai2;
      $NilaiUjian = $rat2;
    }else{
      $grd = $GradeNilai3;
      $NilaiUjian = $rat3;
    }  
  }
  //jika tidak
  else{
     $grd = $GradeNilai1;
     $NilaiUjian = $rat1;  
  }
  //echo "#$rat1~$rat2~$rat3~$grd~$NilaiUjian#";
  //exit;
  // Simpan
  $s = "update pmb
    set ProdiID = '$ProdiID',
		Catatan = '$Catatan',
        LulusUjian = '$LulusUjian',
        NilaiUjian = '$NilaiUjian',
        NilaiUjianTotal = '$NilaiUjianTotal',
		GradeNilai = '$grd',
        LoginEdit = '$_SESSION[_Login]',
        TanggalEdit = now()
    where KodeID = '".KodeID."' and PMBID = '$PMBID' ";
  $r = _query($s);
  TutupScript();
  
  include_once "statusaplikan.lib.php";
  SetStatusAplikan('LLS', GetaField('pmb', "PMBID='$PMBID' and KodeID", KodeID, 'AplikanID'), GetaField('pmbperiod', "KodeID='".KodeID."' and NA", 'N', "PMBPeriodID"));
}

function AmbilDetailUSM($pmb) {
  //if(!empty($pmb['Pilihan2'])){
    $s = "select pu.ProdiID, ru.NilaiUSM, pu.PMBUSMID, pu2.Nama, ru.Kehadiran
  			from ruangusm ru 
  				left outer join prodiusm pu on ru.ProdiUSMID=pu.ProdiUSMID 
  				left outer join pmbusm pu2 on pu.PMBUSMID=pu2.PMBUSMID and pu2.KodeID='".KodeID."'
  			where ru.KodeID='".KodeID."' 
				and pu.NA='N' 
				and PMBID='$pmb[PMBID]' 
				and (pu.ProdiID='$pmb[Pilihan1]' or pu.ProdiID='$pmb[Pilihan2]' 
        or pu.ProdiID='$pmb[Pilihan3]')
				and ru.PMBPeriodID='$pmb[PMBPeriodID]' order by pu.ProdiID, PMBUSMID";
    $r = _query($s);
    $prodi = 'skdashkjd';
    $a = ''; $n =0; $tot = 0; $x = 0;
    while ($w = _fetch_array($r)) {
  	$ro = 'readonly=true';
    	if($w['ProdiID']!=$prodi){    	  
        $NM = GetaField('prodi', "KodeID='".KodeID."' and ProdiID", $w['ProdiID'], 'Nama'); 
        if($n==0){
          $a .= "<tr>
              <td class=wrn colspan=2>$NM</td></tr>";
        }else{
          $rat =($tot>0)? number_format($tot/$x,2) : number_format(0,2);
          
          $a .= "<tr>
              <td class=wrn>Rata-rata</td>
              <td class=ul1><input type=text name='rat_$prodi' value='$rat' $ro 
              size=7 ></td></tr>
              <tr>
              <td class=wrn colspan=2>$NM</td></tr>";
          $tot=0;
        }
        $prodi = $w['ProdiID'];        
        $x = 0;              
      }
      
      $x++;
      $tot += $w['NilaiUSM'];
      $n++;
    	
      $a .= "<tr>
          <td class=inp>$w[Nama]</td>
          <td class=ul1>
            <input type=text name='USM_$n' value='$w[NilaiUSM]' size=7 $ro>
            </td>
          </tr>";
    }
    
    $rat =($tot>0)? number_format($tot/$x,2) : number_format(0,2);
          
          $a .= "<tr>
              <td class=wrn>Rata-rata</td>
              <td class=ul1><input type=text name='rat_$prodi' value='$rat' $ro 
              size=7></td></tr>";   
    return $a; 
 /* 
  }
  else{
    $s = "select ru.NilaiUSM, pu.PMBUSMID, pu2.Nama, ru.Kehadiran
  			from ruangusm ru 
  				left outer join prodiusm pu on ru.ProdiUSMID=pu.ProdiUSMID 
  				left outer join pmbusm pu2 on pu.PMBUSMID=pu2.PMBUSMID and pu2.KodeID='".KodeID."'
  			where ru.KodeID='".KodeID."' 
  				and pu.NA='N' 
  				and PMBID='$pmb[PMBID]' 
  				and INSTR(concat('|', pu.ProdiID, '|'), concat('|','$pmb[Pilihan1]','|')) != 0 
  				and ru.PMBPeriodID='$pmb[PMBPeriodID]' order by PMBUSMID";
    $r = _query($s);
    
    HitungUlangUSM($n);
    $a = '';
    $count = 1;
    $gel = GetaField('pmbperiod', "KodeID='".KodeID."' and NA", 'N', "PMBPeriodID");
    while ($w = _fetch_array($r)) {
  	$ro = 'readonly=true';
  	
  	$nilai = $w['NilaiUSM'];
      $a .= "<tr>
        <td class=inp>$w[PMBUSMID]</td>
        <td class=ul1>
          <input type=text name='USM_$count' value='$nilai' $ro 
          size=4 maxlength=4 style='text-align: right'
  			onChange=\"HitungUlangUSM()\"/>
          $w[Nama]</td>
          </tr>";
  	$count++;
    }
    
    return $a; 
  }
  */
}

function AmbilTotalNilaiUSM($w) {
  /*$s = "select pu.PMBUSMID, p.Nama from prodiusm pu 
			left outer join pmbusm p on pu.PMBUSMID=p.PMBUSMID  
			where pu.KodeID='".KodeID."' and p.KodeID='".KodeID."' and pu.NA='N' and pu.ProdiID='$w[Pilihan1]' and pu.PMBPeriodID='$w[PMBPeriodID]' order by PMBUSMID";
  */
  $s = "select ru.NilaiUSM from ruangusm ru left outer join prodiusm pu on ru.ProdiUSMID=pu.ProdiUSMID 
			where ru.KodeID='".KodeID."' and pu.NA='N' and pu.ProdiID='$w[Pilihan1]' and ru.PMBPeriodID='$w[PMBPeriodID]' order by PMBUSMID";
  $r = _query($s);
  
  $total = 0;
  while ($w = _fetch_array($r)) {
    $nilai = $w['NilaiUSM'];
    
	$total += $nilai;
  }
  return $total;
}

function HitungUlangUSM($n) {
  echo <<<SCR
  <script>
  function HitungUlangUSM() {
    var i = 0;
    var ttl = 0;
SCR;
  for ($i = 1; $i<= $n; $i++) {
    echo "ttl = ttl + Number(datalulus.USM_" . $i . ".value);\n";
  }
  echo <<<SCR
    datalulus.NilaiUjian.value = ttl;
  }
  </script>

SCR;
}

function TutupScript() {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    opener.location='../index.php?mnux=$_SESSION[mnux]';
    self.close();
    return false;
  }
  ttutup();
</SCRIPT>
SCR;
}
?>
