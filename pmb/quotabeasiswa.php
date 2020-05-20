<?php
// Author : Emanuel Setio Dewo
// Email  : setio.dewo@gmail.com
// Start  : 03 Sept 2008

// *** Parameters ***
$ProdiID=GetSetVar('ProdiID');
RandomStringScript();

// *** Main ***
TampilkanJudul("Proses Quota Beasiswa");
$gos = (empty($_REQUEST['gos']))? 'DataQuota' : $_REQUEST['gos'];
$gel = GetaField('pmbperiod', "KodeID='".KodeID."' and NA", 'N', 'PMBPeriodID');
$gos($gel);

// *** Functions ***
function DataQuota($gel) {
  $optprodi = GetProdiUser($_SESSION['_Login'], $_SESSION['ProdiID']);
  $whr_prodi = (empty($_SESSION['ProdiID']))? '' : "and ProdiID='$_SESSION[ProdiID]'";
  EditQuotaScript();
  CheckFormScript('TahunID,ProdiID');
  echo "<p>
  <table class=box cellspacing=1 align=center>
  <form action='?' method=POST onSubmit='return CheckForm(this)'>
  <input type=hidden name='mnux' value='$_SESSION[mnux]' />
  <input type=hidden name='gos' value='' />
  <tr><td class=wrn width=2 rowspan=4></td>
      <td class=inp>Gelombang:</td>
      <td class=ul>$gel</td>
      </tr>
  <tr>
      <td class=inp>Program Studi:</td>
      <td class=ul><select name='ProdiID'>$optprodi</select></td>
      </tr>
  <tr><td></td>
	  </tr>
  <tr><td class=ul colspan=2>
      <input type=submit name='AmbilData' value='Ambil Data' />
      </td></tr>
  </form>
  </table>
  </p>";
  
  $maxLast = 100;
  echo "<p>
  <table class=box cellspacing=1 align=center>
  <form action='?' method=POST>
  <input type=hidden name='gos' value='ProsesQuota' />
  <input type=hidden name='gel' value='$gel' />
  <input type=hidden name='prodi' value='$_SESSION[ProdiID]' />
  <input type=hidden name='BypassMenu' value=1 />
  <tr>
	<th class=ttl>100.00</th>
	<th class=ttl colspan=3></th>
	<th class=ttl><input type=button name='Tambah' value='+' onClick='EditQuota(1, 0, 100)'></th>
  </tr>";
  
  $whr_prodi = (empty($_SESSION['ProdiID']))? '' : "and ProdiID='$_SESSION[ProdiID]'";
  $s = "select * from quotabeasiswa where PMBPeriodID='$gel' $whr_prodi and KodeID='".KodeID."' order by DariNilai DESC";
  $r = _query($s);
  while($w = _fetch_array($r))
  {	
	$s1 = "select PMBID, Nama, NilaiSekolah from pmb where PMBPeriodID='$gel' $whr_prodi 
				and $w[DariNilai] <= NilaiSekolah and NilaiSekolah < $maxLast and LulusUjian='Y' and KodeID='".KodeID."' order by NilaiSekolah DESC";
	$r1 = _query($s1);
	$optpeserta = "<option value=''>-- Lihat List Calon Mahasiswa di Sini --</option>"; $n1 = 0;
	while($w1 = _fetch_array($r1))
	{	$n1++;
		$optpeserta .= "<option value='$n1'>$w1[PMBID] - $w1[Nama] ( $w1[NilaiSekolah] )</option>";
	}
	echo "
	  <tr>
		<td class=ul rowspan=2></td>
		<td class=ul rowspan=2 align=center><font size=3><b>$w[Diskon] %</b></font></td>
		<td class=inp>Calon<sup>2</sup> Mahasiswa:</td>
		<td class=ul><select name='Nama$w[MaxQuotaID]'>$optpeserta</select></td>
		<td class=ul rowspan=2 align=center><a href='#' onClick=\"EditQuota(0, $w[MaxQuotaID], '$maxLast')\"><img src='img/edit.png'></a></td>
	  </tr>
	  <tr>
		<td class=inp>Jumlah:</td>
		<td class=ul><b>$n1</b></td>
	  </tr>
	  <tr>
		<th class=ttl>$w[DariNilai]</th>
		<th class=ttl colspan=3></th>
		<th class=ttl><input type=button name='Tambah' value='+' onClick=\"EditQuota(1, 0, '$w[DariNilai]')\"></th>
	  </tr>";
	  $maxLast = $w['DariNilai'];
  }
  
  $s1 = "select PMBID, Nama, NilaiSekolah from pmb where PMBPeriodID='$gel' $whr_prodi 
				and NilaiSekolah < $maxLast and LulusUjian='Y' and KodeID='".KodeID."' order by NilaiSekolah DESC";
  $r1 = _query($s1);
  $optpeserta = "<option value=''>-- Lihat List Calon Mahasiswa di Sini --</option>"; $n1 = 0;
  while($w1 = _fetch_array($r1))
  {	$n1++;
	$optpeserta .= "<option value='$n1'>$w1[PMBID] - $w1[Nama] ( $w1[NilaiSekolah] )</option>";
  }
  
  echo "
  <tr>
	<td class=ul rowspan=2></td>
	<td class=ul rowspan=2 align=center><font size=3><b>0.00 %</b></font></td>
	<td class=inp>Calon<sup>2</sup> Mahasiswa:</td>
	<td class=ul><select name='NamaLast'>$optpeserta</select></td>
  </tr>
  <tr>
	<td class=inp>Jumlah:</td>
	<td class=ul><b>$n1</b></td>
  </tr>
  <tr>
	<th class=ttl>0.00</th>
	<th class=ttl colspan=3></th>
	<th class=ttl></th>
  </tr>
  <tr>
	<td colspan=8><input type=submit name='Proses' value='Proses Quota Beasiswa'></td>
  </tr>
  </form>  
  </table>
  </p>";
}
function ProsesQuota($gel)
{	$prodi = $_REQUEST['prodi'];

	$s = "select PMBID, NilaiSekolah from pmb where PMBPeriodID='$gel' and ProdiID='$prodi' and LulusUjian='Y' and KodeID='".KodeID."'";
	$r = _query($s);
	
	while($w = _fetch_array($r))
	{	$quota = GetFields('quotabeasiswa', "PMBPeriodID='$gel' and ProdiID='$prodi' and DariNilai <= $w[NilaiSekolah] and $w[NilaiSekolah] < SampaiNilai and KodeID", KodeID, 'MaxQuotaID, Diskon, DariNilai, SampaiNilai');
		//echo "$quota[DariNilai] < $w[NilaiSekolah] < $quota[SampaiNilai] : $quota[Diskon]<br>"; 
		
		$s1 = "update pmb set MaxQuotaID='$quota[MaxQuotaID]', Diskon='$quota[Diskon]' where PMBID='$w[PMBID]'";
		$r1 = _query($s1);
	}
	
	echo Konfirmasi('Update Data',
      "Updating Data...<br />
      Please wait a second.");
    echo "<script>window.location='?mnux=$_SESSION[mnux]&gos=';</script>";
}

function EditQuotaScript() {
  echo <<<SCR
  <script>
  function EditQuota(md, id, max) {
    lnk = "$_SESSION[mnux].edt.php?md="+md+"&id="+id+"&max="+max;
    win2 = window.open(lnk, "", "width=500, height=400, scrollbars, status");
    if (win2.opener == null) childWindow.opener = self;
  }
  </script>
SCR;
}
?>
