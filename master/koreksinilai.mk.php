<?php
// Author : Arisal Yanuarafi	
// Email  : arisal.yanuarafi@yahoo.com	
// Start  : 02 Oktober 2011

// *** Parameters ***
$_SESSION[tabNilai]="NilaiMhsw";
$TahunID = GetSetVar('TahunID');
$ProdiID = GetSetVar('ProdiID');
$_nilaiJadwalID = GetSetVar('_nilaiJadwalID');
$tabNilai = GetSetVar('tabNilai');
$TMasuk = GetSetVar('TMasuk');
$_KurikulumID = GetSetVar('_KurikulumID');
$_KelasID = GetSetVar('_KelasID');
$arrNilai = array(  "Nilai Mahasiswa~NilaiMhsw~Nilai2"
  );


// *** Main ***
TampilkanJudul("Koreksi Nilai Kolektif");
$gos = (empty($_REQUEST['gos']))? 'DftrMK' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function TampilkanHeaderPenilaian() {
  $s = "select DISTINCT(TahunID) from tahun where KodeID='".KodeID."' order by TahunID DESC";
  $r = _query($s);
  $opttahun = "<option value=''></option>";
  while($w = _fetch_array($r))
  {  $ck = ($w['TahunID'] == $_SESSION['TahunID'])? "selected" : '';
     $opttahun .=  "<option value='$w[TahunID]' $ck>$w[TahunID]</option>";
  }

  $optprodi = GetProdiUser($_SESSION['_Login'], $_SESSION['ProdiID']);
    $frmProdi = "
      <td class=inp width=180>Program Studi:</td>
      <td class=ul nowrap>
        <select name='ProdiID' onChange='this.form.submit()'>$optprodi</select>
      </td>";
  echo "
  <table class=box cellspacing=1 align=center width=800>
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$_SESSION[mnux]' />
  <input type=hidden name='gos' value='' />
  <input type=hidden name='_KelasID' value='' />
  
  <tr>
         <td class=inp width=180>Tahun Akd:</td>
      <td class=ul width=90><select name='TahunID' onChange='this.form.submit()' />$opttahun</td>
      $frmProdi
      <td class=ul>
        <input type=submit name='Tampilkan' value='Tampilkan' />
        </td>
      </tr>
  
  </form>
  </table>";
}
function DftrMK() {
  TampilkanHeaderPenilaian();

    $whr_dsn = '';
    $whr_prd = "and m.ProdiID = '$_SESSION[ProdiID]'";
	if (!empty($_SESSION['_KurikulumID'])) {
	$whr_kuri = "and m.KurikulumID = '$_SESSION[_KurikulumID]'";
	}
	
/*
  $s = "select j.*, h.Nama as HR, p.Nama as _PRD,
      concat(d.Nama, ' <sup>', d.Gelar, '</sup>') as DSN,
      left(j.JamMulai, 5) as _JM, left(j.JamSelesai, 5) as _JS,
      if (j.Final = 'Y', 'Final', 'Draft') as STT,
	  jj.Nama as _NamaJenisJadwal, jj.Tambahan
    from jadwal j
      left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
      left outer join hari h on j.HariID = h.HariID
      left outer join prodi p on p.ProdiID = j.ProdiID and p.KodeID = '".KodeID."'
	  left outer join jenisjadwal jj on jj.JenisJadwalID=j.JenisJadwalID
    where j.KodeID = '".KodeID."'
      and j.TahunID = '$_SESSION[TahunID]'
      $whr_prd
      $whr_dsn
	  and jj.Tambahan = 'N'
    order by d.Nama, j.HariID, j.JamMulai, j.JamSelesai";
*/  
  $s5 = "select distinct(Sesi) from mk m
    where m.KodeID = '".KodeID."'
	  and m.NA = 'N'
      $whr_prd
      $whr_kuri
	order by m.Sesi";
	$r5 = _query($s5);

  	
  echo "<Table class=box align=center cellspacing=1 width=800> 
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='$_SESSION[mnux]' />
  <input type=hidden name='gos' value='' />";
  
   
  echo "<tr>
   
  <td class=inp >Kurikulum: </td><td>";
  
  // Cari Kurikulum:
  $s6 = "select KurikulumID,KurikulumKode,Nama
    from kurikulum
    where ProdiID = '$_SESSION[ProdiID]' order by Nama";
	$r6 = _query($s6);
	  $optkurikulum = "<option value=''></option>";
	  while($w6 = _fetch_array($r6))
		{  $ck = ($w6['KurikulumID'] == $_SESSION['_KurikulumID'])? "selected" : '';
		   $optkurikulum .=  "<option value='$w6[KurikulumID]' $ck>$w6[Nama]</option>";
		}
	  $_inputKurikulum = "<select name='_KurikulumID' onChange='this.form.submit()'>$optkurikulum</select>";    
   echo"$_inputKurikulum</td> </tr></form></table>";
  		if (!empty($_SESSION[_KelasID])) {
  			$whr_kelas="And mh.KelasID='$_SESSION[_KelasID]'";
  		}
  while($w5 = _fetch_array($r5))
{
  $s = "select distinct(k.MKID), m.* from mhsw mh, krs k, mk m
    where m.KodeID = '".KodeID."'
      and m.Sesi='$w5[Sesi]'
      $whr_prd
      $whr_kuri
	  and m.MKKode = k.MKKode
	and k.TahunID ='$_SESSION[TahunID]'
	and mh.ProdiID='$_SESSION[ProdiID]'
	and mh.MhswID=k.MhswID
	$whr_kelas
	order by m.Sesi";
	
	
  $r = _query($s); $n=0;
  $dsn = 'laskdjfoaiurhfasdlasdkjf';
  $jmlrow= _num_rows($r);
  
if ($jmlrow>0) {	  
  echo "<table class=box cellspacing=1 align=center width=800>";
  echo "<tr><td class=ul colspan=5><b>Semester $w5[Sesi]</b></td></tr>
  <tr>
    <th class=ttl width=20>No</th>
    <th class=ttl width=80>Kode MK</th>
    <th class=ttl width=240>Mata Kuliah <sub>SKS</sub></th>
    <th class=ttl width=70>Kelas <sub>Prg</sub></th>
       <th class=ttl width=40>Koreksi Nilai</th>
    </tr>";
  $kanan = "<img src='img/kanan.gif' />";
  while ($w = _fetch_array($r)) {
    $n++;
    
    $c = ($w['Final'] == 'Y')? 'class=nac' : 'class=ul';
    $TagTambahan = ($w['Tambahan'] == 'Y')? "<b>( $w[_NamaJenisJadwal] )</b>" : "";
	$gos2 = ($w['Tambahan'] == 'Y')? "Nilai2" : "Nilai2";
	echo "<tr>
      <td class=inp width=25>$n</td>
        <td $c>$w[MKKode]</td>";

$NamaKelas = GetaField("kelas", "KelasID",
    $_SESSION[_KelasID], "Nama");	
	
echo " <td $c>$w[Nama] $TagTambahan
        <div align=right>
        <sup>$w[SKS] sks</sup>
        </div>
        </td>
      <td $c>$NamaKelas
        <div align=right>
        <sup>$w[ProgramID]
        - <abbr title='$w[_PRD]'>$w[ProdiID]</abbr></sup>
        </div>
        </td>
        <form action=?mnux=$_SESSION[mnux]&gos=$gos2 method=POST><td $c align=center><input type=hidden name='_MKID' value='$w[MKID]'><input type=submit name='Isi' title='Koreksi Nilai Kolektif' value='Koreksi Nilai' /></td>
	      </form> </tr>";
	  }
  }
  echo "</table></p>";
  }

  echo <<<SCR
  
  <script>
  <!--
 function CetakNilai(id) {
      lnk = "$_SESSION[mnux].pdf.php?JadwalID="+id;
      win2 = window.open(lnk, "", "width=600, height=400, scrollbars, status");
      if (win2.opener == null) childWindow.opener = self;
  }
  //-->
  </script>
SCR;
}
function Nilai2() {
  
  	if (!empty($_SESSION[_KelasID])) {
	$whr_kelas="And m.KelasID='$_SESSION[_KelasID]'";
	}
  		$jdwl = GetFields("mk", "MKID",$_REQUEST[_MKID],"*");
    
	//CekHakAksesJadwal($_SESSION['_nilaiJadwalID']);
    TampilkanTabNilai();
    TampilkanHeaderMK($jdwl);
    TampilkanPenilaian($jdwl);
  
}
function TampilkanTabNilai() {
  global $arrNilai;
  echo "<table class=bsc cellspacing=1 align=center>";
  echo "<tr>";
  foreach ($arrNilai as $a) {
    $isi = explode('~', $a);
    $c = ($_SESSION['tabNilai'] == $isi[1])? 'class=menuaktif' : 'class=menuitem';
    echo "<td $c id='tab_$isi[1]'>
      <a href='?mnux=$_SESSION[mnux]&tabNilai=$isi[1]&gos=$isi[2]'>$isi[0]</a>
      </td>";
  }
  echo "<td class=menuitem>
    <input type=button name='Kembali' value='Kembali' onClick=\"location='?mnux=$_SESSION[mnux]&gos='\" /></td>";
  echo "</tr>";
  echo "</table>";
}
function tampilkanHeaderMK($jdwl) {
  
  $param = GetFields("jadwal","JadwalID",$_SESSION['_nilaiJadwalID'],"*");
  $tglAkhir = GetaField("tahun", "TahunID = '$param[TahunID]' and ProdiID = '$param[ProdiID]' and ProgramID", $param[ProgramID],"TglNilai");
   $PRD = GetFields("prodi","ProdiID",$_SESSION['ProdiID'],"Nama");
  $now = date('Y-m-d');
   $KLS = GetFields("kelas","KelasID",$_SESSION['_KelasID'],"Nama,ProgramID");

  	$TIMEOUT = "";
	$_SESSION[_timeout] = false;

  
  echo "<table class=box cellspacing=0 align=center width=840>
  <tr><td class=inp width=100>Thn Akademik:</td>
      <td class=ul>$_SESSION[TahunID]</td>
      <td class=inp width=100>Program Studi:</td>
      <td class=ul>$PRD[Nama] <sup>$_SESSION[ProdiID]</sup></td>
      </tr>
  <tr><td class=inp>Matakuliah:</td>
      <td class=ul>$jdwl[Nama]<sup>$jdwl[MKKode]</sup>  &#8594; $jdwl[SKS] <sup>SKS</sup></td>
	        <td class=inp>Kelas:</td>
      <td class=ul>$KLS[Nama] <sup>$KLS[ProgramID]</sup></td>
      </tr>
  </table>";
}
function TampilkanPenilaian($jdwl) {
  if (!empty($_SESSION['tabNilai']))
    $_SESSION['tabNilai']($jdwl);
}
function CheckPersentaseScript() {
  echo <<<SCR
  <script>
  <!--
  function HitungBobot(frm) {
    var tm = parseFloat(frm.TugasMandiri.value);
    if (tm == 0) {
      var tot = parseFloat(frm.Tugas1.value) +
        parseFloat(frm.Tugas2.value) +
        parseFloat(frm.Tugas3.value) +
        parseFloat(frm.Tugas4.value) +
        parseFloat(frm.Tugas5.value) +
        parseFloat(frm.Presensi.value) +
        parseFloat(frm.UTS.value) +
        parseFloat(frm.UAS.value);
    }
    else {
      var tot = parseFloat(frm.TugasMandiri.value) +
        parseFloat(frm.Presensi.value) +
        parseFloat(frm.UTS.value) +
        parseFloat(frm.UAS.value);
    }
    frm.TOT.value = tot;
  }
  function CheckBobot(frm) {
    var tm = parseFloat(frm.TugasMandiri.value);
    if (tm == 0) {
      var tot = parseFloat(frm.Tugas1.value) +
        parseFloat(frm.Tugas2.value) +
        parseFloat(frm.Tugas3.value) +
        parseFloat(frm.Tugas4.value) +
        parseFloat(frm.Tugas5.value) +
        parseFloat(frm.Presensi.value) +
        parseFloat(frm.UTS.value) +
        parseFloat(frm.UAS.value);
    }
    else {
      var tot = parseFloat(frm.TugasMandiri.value) +
        parseFloat(frm.Presensi.value) +
        parseFloat(frm.UTS.value) +
        parseFloat(frm.UAS.value);
    }
    if (tot != 100) alert('Tidak dapat disimpan karena jumlah bobot tidak 100%');
    return tot == 100;
  }
  //-->  </script>
SCR;
}
function NilaiMhsw($jdwl) {
if (!empty($_SESSION[_KelasID])) {
$whr_kelas = "And m.KelasID='$_SESSION[_KelasID]' ";
}
if (!empty($_SESSION[TMasuk])) {
$whr_tmasuk = "And m.TahunID like '$_SESSION[TMasuk]%' ";
}
  $s = "select k.*, UPPER(m.Nama) as NamaMhsw
    from krs k
      left outer join mhsw m on k.MhswID = m.MhswID and m.KodeID = '".KodeID."'
    where k.MKKode = '$jdwl[MKKode]'
	and k.TahunID ='$_SESSION[TahunID]'
	and m.ProdiID='$_SESSION[ProdiID]'
	$whr_tmasuk
	$whr_kelas
	      and k.NA = 'N'
    order by k.MhswID";
  $r = _query($s); $n = 0;
  echo "<table class=box cellpadding=0 cellspacing=1 align=center width=840>";


    $frm = "<form action='?' method=POST>";
    $ro = '';
    $btnSimpan = "<input type=submit name='SimpanSemua' value='Simpan Semua' />";
        //$btnGagal = "<input type=button name='Gagal' value='Gagal Penilaian' onClick=\"javascript:Gagalkan($jdwl[JadwalID])\" />";
    // Javascript
    echo <<<SCR
    <script>
    <!--
    function Finalisasikan(id) {
      lnk = "$_SESSION[mnux].final.php?id="+id;
      win2 = window.open(lnk, "", "width=400, height=400, scrollbars, status");
    }
    function Gagalkan(id) {
      lnk = "$_SESSION[mnux].gagal.php?id="+id;
      win2 = window.open(lnk, "", "width=400, height=440, scrollbars, status");
    }
    //-->
    </script>
SCR;
  
  echo "$frm
    <input type=hidden name='gos' value='NilaiMhswSimpan' />
    <input type=hidden name='BypassMenu' value=1 />
    <input type=hidden name='_nilaiJadwalID' value='$jdwl[JadwalID]' />";
  echo "<tr>
    <td class=ul colspan=15 align='center'>
    $btnSimpan
    </td></tr>";
  echo "<tr>
    <th class=ttl>NO</th>
    <th class=ttl>NIM</th>
    <th class=ttl>Mahasiswa</th>
    <th class=ttl title='Presensi Mahasiswa'>&sum;<br />PRS</th>
  	 <th class=ttl>Nilai</th>
	 <th class=ttl>Bobot</th>
	 <th class=ttl>Koreksi Nilai <br />Huruf | Angka</th>
	</tr>
    ";
  $wd = "width=30"; $nomer = 0;
  $jml = _num_rows($r);
  while ($w = _fetch_array($r)) {
    $nomer++;
    $_pr = $nomer;
    $_t1 = $nomer + $jml;
    $_t2 = $nomer + $jml *2;
    $_t3 = $nomer + $jml *3;
    $_t4 = $nomer + $jml *4;
    $_t5 = $nomer + $jml *5;
    $_ut = $nomer + $jml *6;
    $_ua = $nomer + $jml *7;
    $n = $w['KRSID'];

	$countPresensi = GetaField('presensi', 'JadwalID', $w['JadwalID'], 'count(PresensiID)');
	$Presensi = ($countPresensi == 0)? 0 : number_format($w['_Presensi']/$countPresensi*100, 0);
    echo "<tr>
      <input type=hidden name='krsid[]' value='$w[KRSID]' />
      <input type=hidden name='KRS_$n' value='$w[KRSID]' />
      <td class=inp>$nomer</td>
      <td class=ul width=70>$w[MhswID]</td>
      <td class=ul>$w[NamaMhsw]</td>
      <td class=ul align=right>$w[_Presensi]<sup>&times;</sup></td>
      <td class=ul align=center><b>$w[GradeNilai] <sup></sup></b></td>
      <td class=ul align=center><b>$w[BobotNilai]</b></td>
	     <td class=ul align=center><select name='Nilai_$n'>
		 <option value=''>---</option>";
		 $s8 = "select Nama, Bobot, NilaiID from nilai Group by Nama";
		 $r8 = _query($s8);
		 while ($w8 = _fetch_array($r8)) {
		 echo "<option value='$w8[NilaiID]'>$w8[Nama] - $w8[Bobot]</option>";
		 }
		 echo "</select>&nbsp;|&nbsp;<input type=text name='NilAngka_$n' value='' maxlength=4 size=3 />
		 </td>
      </tr>";
  }
  echo "<input type=hidden name='JumlahMhsw' value='$jml' />";
  echo "</form></table>";
}
function NilaiMhswSimpan() {
    $_nilaiJadwalID = $_REQUEST['_nilaiJadwalID'];
  $krsid = array();
  $krsid = $_REQUEST['krsid'];
  foreach ($krsid as $id) {
   $Nilai9 = $_REQUEST['Nilai_'.$id]+0;
   $NilAngka = $_REQUEST['NilAngka_'.$id]+0;
   if ($NilAngka !='') {
   	  $arrgrade = GetFields('nilai', 
      		"KodeID='$_SESSION[KodeID]' and NilaiMin <= $NilAngka and $NilAngka <= NilaiMax and ProdiID",
      		$_SESSION[ProdiID], "Nama, Bobot");
    // Simpan
    $s1 = "update krs set NilaiAkhir='$NilAngka', GradeNilai='$arrgrade[Nama]', BobotNilai='$arrgrade[Bobot]', LoginEdit='$_SESSION[_Login]-KnK',TanggalEdit=now()
      where KRSID='$id' ";
    $r1 = _query($s1);
    echo "<pre>$s1 ..1</pre>";
    $s = "INSERT INTO koreksinilai (Tanggal,TahunID,SK,Perihal,KRSID,MhswID,MKID,GradeLama,GradeNilai,Pejabat,Modul,LoginBuat,TglBuat)
									values
									(now(),
									'Transfer',
									'ybs',
									'Input MK dan Nilai menggunakan Modul Koreksi Kolektif',
									'$id',
									'',
									'',
									'-',
									'$arrgrade[Nama]',
									'$_SESSION[_Nama]',
									'KoreksiNilaiKolektif',
									'$_SESSION[_Login]',
									now())";
		$r = _query($s);
	}
	else {
   if ($Nilai9>0){
  $nil2 = GetFields('nilai', 'NilaiID', $Nilai9, '*');
    // Simpan
    $s7 = "update krs
    set GradeNilai = '$nil2[Nama]',
        BobotNilai = '$nil2[Bobot]',
        EvaluasiDosen = 'Y',
        Final = 'Y',
        LoginEdit = '$_SESSION[_Login]-KNK',
        TanggalEdit = now()
    where KRSID = '$id' ";
    $r7 = _query($s7);
    echo "<pre>$s7 ..7</pre>";
    $s = "INSERT INTO koreksinilai (Tanggal,TahunID,SK,Perihal,KRSID,MhswID,MKID,GradeLama,GradeNilai,Pejabat,Modul,LoginBuat,TglBuat)
									values
									(now(),
									'Transfer',
									'ybs',
									'Input MK dan Nilai menggunakan Modul Koreksi Kolektif',
									'$id',
									'',
									'',
									'-',
									'$nil2[Nama]',
									'$_SESSION[_Nama]',
									'KoreksiNilaiKolektif',
									'$_SESSION[_Login]',
									now())";
		$r = _query($s);
	}
  }
  }
  BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=", 1);
}
function HitungNilai() {
//function HitungNilai1($jadwalid, $jdwl) {
  $jadwalid = $_REQUEST['_nilaiJadwalID'];
  $jdwl = GetFields('jadwal', 'JadwalID', $jadwalid, '*');
  // lihat persentase Tugas Mandiri
  if ($jdwl['TugasMandiri'] > 0) {
    // Ambil jumlah tugas2 utk distribusi nilai tugas
    $TGS = GetFields('krs', 'JadwalID', $jadwalid,
      "sum(Tugas1) as T1, sum(Tugas2) as T2, sum(Tugas3) as T3, sum(Tugas4) as T4, sum(Tugas5) as T5");
    $_T1 = ($TGS['T1'] > 0)? 1 : 0;
    $_T2 = ($TGS['T2'] > 0)? 1 : 0;
    $_T3 = ($TGS['T3'] > 0)? 1 : 0;
    $_T4 = ($TGS['T4'] > 0)? 1 : 0;
    $_T5 = ($TGS['T5'] > 0)? 1 : 0;
    $JumlahTugas = $_T1 + $_T2 + $_T3 + $_T4 + $_T5;
    // Distribusikan persentase tugas
    $PersenTugas = $jdwl['TugasMandiri'] / $JumlahTugas;
    $SisaTugas = $jdwl['TugasMandiri'] % $JumlahTugas;
    $_fld = array();
    for ($i = 1; $i <= 5; $i++) {
      $fld = "_T$i";
      $_PersenTugas = ($$fld == 1)? $PersenTugas : 0;
      $jdwl["Tugas$i"] = $_PersenTugas;
      //$persen = ($i == 1)? $PersenTugas + $SisaTugas : $PersenTugas;
      $_fld[] = "Tugas$i=$_PersenTugas";
    }
    $fld = implode(', ', $_fld);
    $s0 = "update jadwal set $fld where JadwalID=$jadwalid";
    $r0 = _query($s0);
  }
  // Proses
  $s = "select * from krs where JadwalID=$jadwalid and NA='N' order by MhswID";
  $r = _query($s);
  while ($w = _fetch_array($r)) {
    $nilai = ($w['Tugas1'] * $jdwl['Tugas1']) +
      ($w['Tugas2'] * $jdwl['Tugas2']) +
      ($w['Tugas3'] * $jdwl['Tugas3']) +
      ($w['Tugas4'] * $jdwl['Tugas4']) +
      ($w['Tugas5'] * $jdwl['Tugas5']) +
      ($w['Presensi'] * $jdwl['Presensi']) +
      ($w['UTS'] * $jdwl['UTS']) +
      ($w['UAS'] * $jdwl['UAS'])
      ;
    $nilai = ($nilai / 100) +0;
    if ($jdwl['Responsi'] > 0) {
      $nilai = ($nilai * (100 - $jdwl['Responsi'])/100) +
        ($w['Responsi'] * ($jdwl['Responsi'])/100);
    }
    $ProdiID = GetaField('mhsw', "MhswID", $w['MhswID'], "ProdiID");
    $arrgrade = GetFields('nilai', 
      "KodeID='$_SESSION[KodeID]' and NilaiMin <= $nilai and $nilai <= NilaiMax and ProdiID",
      $ProdiID, "Nama, Bobot");
    // Simpan
    $s1 = "update krs set EvaluasiDosen = 'Y',Final = 'Y',NilaiAkhir='$nilai', GradeNilai='$arrgrade[Nama]', BobotNilai='$arrgrade[Bobot]'
      where KRSID=$w[KRSID] ";
    $r1 = _query($s1);
  }
  BerhasilSimpan("?mnux=$_SESSION[mnux]&gos=Nilai2&_nilaiJadwalID=$jadwalid", 100);
}
?>
