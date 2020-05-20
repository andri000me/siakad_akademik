<?php

session_start();
// *** Parameters ***
$TahunID = GetSetvar('TahunID');
$ProdiID = GetSetVar('ProdiID');
$_SESSION['parsial'] = 200;
$_SESSION['KodePTI'] = GetaField('identitas', 'Kode', KodeID, 'KodeHukum');
$_SESSION['Timer'] = 1;

$thnprcArr = (empty($_SESSION['_DiktiTahunProses']))? array() : explode('~', $_SESSION['_DiktiTahunProses']);
if(!empty($_REQUEST['DiktiTahunProses'])) 
{	if(!(in_array($_REQUEST['DiktiTahunProses'], $thnprcArr))) $thnprcArr[] = str_replace('~', ' ', $_REQUEST['DiktiTahunProses']);
	asort($thnprcArr);
}
$_SESSION['_DiktiTahunProses'] = implode('~', $thnprcArr);

// *** Main ***
TampilkanJudul("Export Data ke DIKTI");
TampilkanHeaderExportDikti();

if (!empty($TahunID)) {
  $gos = (empty($_REQUEST['gos']))? 'ExportDikti' : $_REQUEST['gos'];
  $gos();
}

// *** Functions ***
function TampilkanHeaderExportDikti() {
  $optprodi = GetOption2('prodi', "concat(ProdiID, ' - ', Nama)", 'ProdiID',
    $_SESSION['ProdiID'], "KodeID='".KodeID."'", 'ProdiID');
  CheckFormScript('TahunID');
  echo <<<ESD
  <p>
  <table class=box cellspacing=1 align=center width=800>
  <form name='frmHeader' action='?' method=POST onSubmit='return CheckForm(this)'>
  <tr><td class=wrn width=2></td>
      <td class=inp width=80>Tahun Akd di Laporan:</td>
      <td class=ul>
        <input type=text name='TahunID' value='$_SESSION[TahunID]' size=6 maxlength=6 />
        <input type=submit name='SetParam' value='Set' />
        </td>
      <td class=inp width=80>Program Studi:</td>
      <td class=ul nowrap>
        <select name='ProdiID' onChange='this.form.submit()'>$optprodi</select>
        <font color=red>*) Kosongkan untuk proses semua
        </td>
      </tr>
  </form>
  </table>
ESD;
}

function TampilkanHeaderExportDikti2() {
  if(!empty($_SESSION['_DiktiTahunProses']))
  {
    $tahunArr = explode('~', $_SESSION['_DiktiTahunProses']);
    $tahunstring = ''; 
  
	foreach($tahunArr as $tahun) $tahunstring .= (empty($tahunstring))? 
								"$tahun<a href='?mnux=$_SESSION[mnux]&gos=HapusTahunProses&thn=$tahun'><sup>&times;</sup></a>" : 
								" &bull; $tahun<a href='?mnux=$_SESSION[mnux]&gos=HapusTahunProses&thn=$tahun'><sup>&times;</sup></a>"; 
  }	
  echo <<<ESD
  <table class=box cellspacing=1 align=center width=800>
  <form name='frmHeader2' action='?' method=POST onSubmit='return CheckForm(this)'>
  <tr><td class=wrn width=2></td>
	  <td class=inp>Tahun<sup>2</sup> untuk diproses:</td>
	  <td class=ul1 colspan=3>$tahunstring <input type=text name='DiktiTahunProses' size=4 maxlength=10><input type=submit name='Tambah' value='Tambah' />
											<input type=button name='HapusSemua' value='Hapus Semua' onClick="location='?mnux=$_SESSION[mnux]&gos=HapusSemuaTahun'"></td>
  </tr>
  </form>
  </table>
  </p>
ESD;
}

function HapusTahunProses()
{	$thn = $_REQUEST['thn'];
	if(!empty($thn))
	{	$tahunArr = explode('~', $_SESSION['_DiktiTahunProses']);
		$tahunArr2 = array();
		
		if(in_array($thn, $tahunArr))
		{	foreach($tahunArr as $tahun)
			{	if($tahun != $thn) $tahunArr2[] = $tahun;
			}
		}
	}
	if(!empty($tahunArr2)) $_SESSION['_DiktiTahunProses'] = implode('~', $tahunArr2);
	else $_SESSION['_DiktiTahunProses'] = '';
	ExportDikti();
}	

function HapusSemuaTahun()
{	$_SESSION['_DiktiTahunProses'] = '';
	ExportDikti();
}

function ExportDikti() {
  TampilkanHeaderExportDikti2();
  
  $arrDikti = array(
    "Aktivitas Dosen~dosen",
    "Aktivitas Mahasiswa~mhsw",
    "Master Dosen~masterdosen",
    "Master Mahasiswa~mastermhsw",
    "Nilai Mahasiswa~nilaimhsw",
    "Kelulusan Mahasiswa~lulusmhsw",
    "Kurikulum-Matakuliah~kmk",
	"Nilai Mahasiswa Pindahan~nilaitransfer"
	
  );
  //var iframeids=["FRAMEMSG","FRAMEDETAIL","FRAMEDETAIL1"]

  $_frm = array();
  for ($i = 0; $i < sizeof($arrDikti); $i++) {
    $_frm[] = "\"FRM_$i\"";
  }
  $__frm = implode(',', $_frm);

  echo <<<ESD
  <script>var iframeids=[$__frm];</script>
  <script src='putiframe.js' language='javascript' type='text/javascript'></script>
ESD;

  for ($i = 0; $i < sizeof($arrDikti); $i++) {
    $_a = explode('~', $arrDikti[$i]);
    $judul = $_a[0];
    $modul = $_a[1];
    echo <<<ESD
    <iframe id="FRM_$i" 
      src="$_SESSION[mnux].$modul.php?TahunID=$_SESSION[TahunID]&ProdiID=$_SESSION[ProdiID]"
      width=800 height=1 frameborder=0 align=center>
    Browser Anda tidak mendukung frame.
    </iframe>
ESD;
  }
}

?>

<table class=box cellspacing=1 width=800>
<tr>
    <td class=ul1><b><u>Catatan:</u></b></td>
    </tr>
<tr>
    <td>
  <ol align=left>
  <li>Proses akan menghasilkan file DBF yang kemudian disatukan dengan program Evaluasi.</li>
  <li>Lakukan reindex dari program Evaluasi terhadap file DBF hasil proses.</li>
  <li>Setelah itu Anda dapat menggunakan program Evaluasi seperti biasa.</li>
  </ol>
    </td>
    </tr>
</table>
<p></p>
