<?php
// Author : Arisal Yanuarafi	
// Email  : arisal.yanuarafi@yahoo.com	
// Start  : 04 Mei 2012

// *** Parameters ***
$_TahunID = GetSetVar('TahunID');
$_ProgramID = GetSetVar('_ProgramID');
$_TProdiID = GetSetVar('_TProdiID');

// *** Main ***
TampilkanJudul("Hapus Transaksi KHS/KRS");
DftrMK();
// *** Functions ***
function DftrMK() {
   $sM = "select distinct(TahunID) from tahun m where TahunID not like 'Tra%'
   	order by m.TahunID DESC";
	
	$rM = _query($sM);
	$optprog  = GetOption2('program', "concat(ProgramID, ' - ', Nama)", 'ProgramID', $_SESSION['_ProgramID'], "KodeID='".KodeID."'", 'ProgramID');
	$optprodi = GetProdiUser($_SESSION['_Login'], $_SESSION['_TProdiID']);
	$optTMasuk = "<option value=''></option>";
  while($w = _fetch_array($rM))
  {  $ck = ($w['TahunID'] == $_SESSION['TahunID'])? "selected" : '';
     $optTMasuk .=  "<option value='$w[TahunID]' $ck>$w[TahunID]</option>";
  }
	
 ?><Table class=box align=center cellspacing=1 width=800> 
  <form action='?' method=POST>
  <input type=hidden name='mnux' value='<?php echo "$_SESSION[mnux]"; ?>' />
  <input type=hidden name='gos' value='' /><tr> 
  <td class=inp width=150>Tahun Akademik: </td>
   <td class=ul><select name='TahunID' onChange='this.form.submit()' /><?php echo $optTMasuk; ?></select></td><td class=inp>Prodi:</td><td><select name='_TProdiID' onChange='this.form.submit()'><?php echo $optprodi; ?></select></td><td class=inp>Program:</td><td><select name='_ProgramID' onChange='this.form.submit()'><?php echo $optprog; ?></select></td><td class=ul><input type=button onclick="location.href='baa/trans.nol.bayar.pdf.php?TahunID=<?php echo $_SESSION['TahunID']; ?>&_ProgramID=<?php echo $_SESSION['_ProgramID']; ?>&_TProdiID=<?php echo $_SESSION['_TProdiID']; ?>'" value='Cetak'></td>

  <?php 
  // Cari Kelas:
 
?></form></tr></table> <?php
$whrProdi=(empty($_SESSION['_TProdiID']))? '' : "and m.ProdiID = '$_SESSION[_TProdiID]' ";
  $s = "SELECT h.KHSID,h.MhswID,m.Nama,m.TahunID,h.Biaya,h.Potongan,h.Bayar
FROM khs h, mhsw m
WHERE m.MhswID = h.MhswID
AND h.TahunID = '$_SESSION[TahunID]'
AND h.Sesi > 2
AND h.Bayar = 0
AND h.StatusMhswID='A'
$whrProdi
AND m.ProgramID = '$_SESSION[_ProgramID]'
AND m.StatusAwalID not in  ('S','M')";
	
	
  $r = _query($s); $n=0;
  $dsn = 'laskdjfoaiurhfasdlasdkjf';
  $jmlrow= _num_rows($r);
  
if ($jmlrow>0) {	  
  echo "<table class=box cellspacing=1 align=center width=800>
  
  <form action='$_SESSION[mnux].hapus.php' method=POST target='_blank'>";
while ($w = _fetch_array($r)) {
    $n++;
	$id=$w[MhswID]+0;
	
	//$Status = GetaField("prosesstatusmhsw", "TahunID='$_SESSION[TahunID]' AND MhswID='$w[MhswID]' AND StatusMhswID='A' AND KodeID", KodeID, "MhswID");
 	$tr = "<tr".(empty($Status)? '':" style=\"color:#F00;font-weight:bold\"").">";
	
?><tr><td colspan=7 align=center> <input type=submit value='Hapus Semua yg ditandai (X)'> <input type=reset value='Reset'></td></tr>
<tr class=ttl><th>No</th><th>No. BP</th><th>Nama</th><th>Thn Masuk</th><th>Total Bayar<br />Semua Sesi</th><th>Biaya<br />Sesi ini</th><th>Bayar<br />Sesi ini</th><th>Hapus (X)</th></tr><?php
	echo $tr;
	echo "	<input type=hidden name='mhswid[]' value='$n' />
      	<input type=hidden name='Mhsw_$n' value='$w[MhswID]' />
		<input type=hidden name='khsid[]' value='$n' />
		<input type=hidden name='KHSID_$n' value='$w[KHSID]' />
		<input type=hidden name='tahunid[]' value='$n' />
		<input type=hidden name='TahunID_$n' value='$_SESSION[TahunID]' />
		";
$totBayar= GetaField("khs","MhswID",$w['MhswID'],'sum(Bayar)');
$_totBayar = number_format($totBayar, 0, ',', '.');
$biaya = GetaField('bipotmhsw',"TahunID='$_SESSION[TahunID]' and MhswID", $w['MhswID'],"Sum(Besar*Jumlah*TrxID)");
$_biaya = number_format($biaya);
$bayar = GetaField('bipotmhsw',"TahunID='$_SESSION[TahunID]' and MhswID", $w['MhswID'],"Sum(Dibayar)");
$_bayar = number_format($bayar);
$inpHapus = "<input align=middle type='text'  name='hapus$n' size=1 maxlength=1>";


      echo "<td class=inp width=25>$n</td>";
echo " <td  class=ul>$w[MhswID]
        </td><td  class=ul>$w[Nama]</td><td  class=ul align=center>$w[TahunID]</td><td  class=ul align=right>$_totBayar</td><td  class=ul align=right>$_biaya</td><td  class=ul align=right>$_bayar</td><td class=ul align=center>$inpHapus</td>";
echo "</tr>";
$s2 = "Select k.MKKode,k.JadwalID,m.Nama,m.SKS,GradeNilai,k.TanggalEdit from krs k,mk m where k.MhswID='$w[MhswID]' and k.TahunID='$_SESSION[TahunID]' and m.MKID=k.MKID";
$r2=_query($s2);
$jrow=_num_rows($r2);
$_jrow=$jrow+1;
if (!empty($jrow)){
echo "<tr><td colspan=8><table align=center class=box >
		<tr><td rowspan=$_jrow valign=middle class=ul><b><u>KRS yang diambil :</u></b></td><th>MKKode</th><th>Nama MK</th><th>SKS</th><th>Nilai</th><th>Nama Dosen</th></tr>
		";
	while ($w2= _fetch_array($r2)) {	
	$jdwlDosenID = GetaField ('jadwal',"JadwalID",$w2['JadwalID'],'DosenID');
	$nmDosen=GetFields("dosen","NIDN",$jdwlDosenID,'Nama,Gelar');
	echo "<tr title='Tanggal Terakhir Edit KRS: $w2[TanggalEdit]'><td class=ul>$w2[MKKode]</td><td class=ul>$w2[Nama]</td><td class=ul align=center>$w2[SKS]</td><td class=ul align=center>$w2[GradeNilai]</td><td class=ul align=right><i>$nmDosen[Nama]</i>  <sup>$nmDosen[Gelar] </sup></td></tr>";
	  }
  echo "</table>";
  }
  else { echo "<tr><td colspan=9 align=center>
		<font color=red>tidak ada data KRS </font></td></tr>"; }
  }

// DELETE LANGSUNG
  $s4="Select * from krs k where k.MhswID='$id' and k.TahunID='$_SESSION[TahunID]'";
              $r4=_query($s4);
              $nr=_num_rows($r4);
while ($w4 = _fetch_array($r4)) {
$s8="insert into krsdump(KRSDumpID, KHSID,KRSID, MhswID, TahunID, JadwalID, MKID, MKKode, Nama, SKS, DosenID, UTS, UAS, NilaiAkhir,GradeNilai,BobotNilai,Tinggi,Final,RuangID,LoginBuat,TanggalBuat) values ('','$w4[KHSID]','$w4[KRSID]', '$w4[MhswID]', '$w4[TahunID]','$w4[JadwalID]','$w4[MKID]','$w4[MKKode]','$w4[Nama]','$w4[SKS]','$w4[DosenID]', '$w4[UTS]', '$w4[UAS]', '$w4[NilaiAkhir]','$w4[GradeNilai]','$w4[BobotNilai]','$w4[Tinggi]','$w4[Final]','$w4[RuangID]','$_SESSION[_Login]',now())";
// proses query insert
$r8=_query($s8);
}
            $s_1="update khs set StatusMhswID='P' where MhswID='$id' and TahunID='$_SESSION[TahunID]'";
            $r8=_query($s_1);
            $s_2="update mhsw set StatusMhswID='P' where MhswID='$id'";
            $r8=_query($s_2);
            $s7="delete from krs where MhswID='$id' AND TahunID='$_SESSION[TahunID]'";
            $r7=_query($s7);

// --------------------
  echo "</td></tr></table>";
  echo "</form>";
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
function Ambil() {
  $jid = array();
  $jid = $_REQUEST['mhswid'];


				foreach($jid as $j) {
				$MhswID=$_REQUEST['Mhsw_'.$j];
				$cetak=$_REQUEST['cetak'.$j];
				if (!empty($cetak)) {
				echo "$MhswID<br />";
				}
				
			}
			

}
?>
