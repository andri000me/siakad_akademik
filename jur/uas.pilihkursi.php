<?php

session_start();
include_once "../sisfokampus1.php";

HeaderSisfoKampus("Pilih Kursi");

// *** Parameters ***
$id = sqling($_REQUEST['id']);

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'PilihKursi' : $_REQUEST['gos'];
$gos($id);

// *** Functions ***
function PilihKursi($id)
{	$arrProdi = array();
	$jadwaluas = GetFields("jadwaluas ju left outer join jadwal j on j.JadwalID=ju.JadwalID and j.KodeID='".KodeID."'
										 left outer join hari h on h.HariID=j.HariID
										 left outer join prodi prd on j.ProdiID=prd.ProdiID
										 left outer join program prg on j.ProgramID=prg.ProgramID", 
								"ju.JadwalUASID='$id' and ju.KodeID", KodeID, 
								"ju.*, LEFT(ju.JamMulai, 5) as _JM, LEFT(ju.JamSelesai, 5) as _JS,  
								    j.MKKode, j.Nama, LEFT(j.JamMulai, 5) as _JamMulaiKuliah, 
									h.Nama as _HariKuliah,
									LEFT(j.JamSelesai, 5) as _JamSelesaiKuliah,
									j.ProdiID, j.ProgramID,
									prd.Nama as _PRD, prg.Nama as _PRG");
	echo "<p><table class=box cellspacing=2 cellpadding=4 width=600 align=center>
			  <tr><td class=inp width=100>Tahun Akademik:</td>
				 <td class=ul><b>$jadwaluas[TahunID]</b></td></tr>
			  <tr><td class=inp>Program Studi :</td>
				  <td class=ul><b>$jadwaluas[ProdiID] - $jadwaluas[_PRD]</b></td>
				  <td class=inp width=150>Program Pendidikan :</td>
				  <td class=ul><b>$jadwaluas[ProgramID] - $jadwaluas[_PRG]</b></td></tr>
			  <tr><td class=inp>Mata Kuliah:</td>
			     <td class=ul1 colspan=3><b>$jadwaluas[MKKode] - $jadwaluas[Nama]</b></td></tr>
			  <tr>
				 <td class=inp>Hari Kuliah:</td>
				 <td class=ul1><b>$jadwaluas[_HariKuliah]</b></td>
				 <td class=inp>Waktu Kuliah:</td>
				 <td class=ul1><b>$jadwaluas[_JamMulaiKuliah]</b> &#8594; <b>$jadwaluas[_JamSelesaiKuliah]</b></td></tr>  
			  <tr><td class=inp>Tanggal Ujian:</td>
				  <td class=ul><b>$jadwaluas[Tanggal]</b></td>
				  <td class=inp>Waktu Ujian:</td>
				  <td class=ul><b>$jadwaluas[_JM]</b> &#8594; <b>$jadwaluas[_JS]</b></td></tr>
		  </table></p>";
	
	$ruang = GetFields('ruang', "RuangID='$jadwaluas[RuangID]' and KodeID", KodeID, 'KapasitasUjian, KolomUjian');
	
	
	
	$arrSiswa = array();
	$JmlBaris = ceil($ruang['KapasitasUjian']/$ruang['KolomUjian']);
	$JmlKolom = $ruang['KolomUjian'];
	
	$s = "select * from uasmhsw where JadwalUASID='$JadwalUASID' and KodeID='".KodeID."'";
	$r = _query($s);
	while($w = _fetch_array($r))
	{	$arrSiswa[$w['UrutanDiRuang']] = $w['MhswID'];
	}
	//Demo();
	
	echo "<form name='formuas' action='?mnux=$_SESSION[mnux]&gos=SavData' method=POST >
			<input type=hidden name='id' value='$id'>";
	echo "<div id='drag' align=centre>";
	
	echo GetStudentList($jadwaluas['JadwalUASID']);
			
	//echo GetHelperButtons();
	
	echo GetMapKursi($jadwaluas['JadwalUASID']);
	
	echo "</div>";
	echo "</form>";
}		

function GetStudentList($juasid)
{	$a = "		<table id='table1' border=1 width=20%>
					<colgroup><col width=25><col width=175></colgroup>
					";
	$a.= "<tr><td class='forbid' colspan=2>Siswa Yang Belum Mendapatkan Kursi UAS</td></tr>";
	
	$s = "select m.MhswID, m.Nama, m.ProdiID 
			from uasmhsw um left outer join mhsw m on um.MhswID=m.MhswID
			where um.UrutanDiRuang=0 and um.JadwalUASID='$juasid' and um.KodeID='".KodeID."'";
	$r = _query($s);
	$KapasitasRuang = GetaField('jadwaluas', "JadwalUASID='$juasid' and KodeID", KodeID, "Kapasitas")+0;
	$n = 0;
	while($w = _fetch_array($r))
	{	$n++;
		$a .= "<tr><td class='forbid'>$n</td><td><input type=hidden name='Kosong$n' id='Kosong$n' value='$w[MhswID]'><div class='drag t1'>$w[MhswID]<br>$w[Nama]<br>$w[ProdiID]</div></td></tr>";
	}
	while($n < $KapasitasRuang)
	{	$n++;
		$a .= "<tr><td class='forbid'>$n</td><td><input type=hidden name='Kosong$n' id='Kosong$n' value=''></td></tr>";
	}
	$a .= "	</table>
			<input type=hidden id='TotalKosong' name='TotalKosong' value='$n'>";
	return $a;
}

function GetMapKursi($juasid)
{	
	$jadwaluas = GetFields('jadwaluas', "JadwalUASID='$juasid' and KodeID", KodeID, "*");
	$jadwaluas['BarisUjian'] = ($jadwaluas['KolomUjian'] == 0)? $jadwaluas['Kapasitas'] : ceil($jadwaluas['Kapasitas']/$jadwaluas['KolomUjian']);
	
	// Buat array yang berisi siswa2 yang telah memiliki tempat
	$arrSiswa = array();
	$s = "select um.MhswID, um.UrutanDiRuang
			from uasmhsw um
			where um.JadwalUASID='$juasid' and um.UrutanDiRuang > 0 and um.KodeID='".KodeID."' order by um.UrutanDiRuang";
	$r = _query($s);
	while($w = _fetch_array($r))
	{	$arrSiswa[$w['UrutanDiRuang']] = $w['MhswID'];
	}
	echo "<input type=hidden id='JumlahKolom' value='$jadwaluas[KolomUjian]'>";
	/*echo "<script>
			function RandomAllocation()
			{	alert('Haha');
			}
		  </script>";
	*/
	echo "		<table id='table2' border=1 width=75%>";
	// Buat header Barisnya
	echo "<tr><td class='forbid' colspan=".($jadwaluas['KolomUjian']+1)." align=center>
			<input type=submit name='submit' value='Simpan Data'>
			<input type=button name='Randomize' value='Alokasi Acak' onClick=\"RandomAllocation()\">
			<input type=button name='Deallocate' value='Dealokasi Semua' onClick=\"DeallocateAll()\">
      <input type=button name='Tutup' value='tutup' onClick=\"window.close()\" ></td></tr>";
	echo "<tr><td class='forbid'>Ruang $jadwaluas[RuangID]</td>";
	for($j = 1; $j <= $jadwaluas['KolomUjian']; $j++)
	{	echo "<td class='forbid'>Kolom $j</td>"; 
	}
	echo "</tr>";
	
	// Buat isinya
	$n = 0;
	for($i = 1; $i <= $jadwaluas['BarisUjian']; $i++)
	{	echo "<tr><td class='forbid'>Baris $i</td>";
		for($j = 1; $j <= $jadwaluas['KolomUjian']; $j++)
		{	$n++;
			if(!empty($arrSiswa[$n]))
			{	$mhsw = GetFields('mhsw', "MhswID='$arrSiswa[$n]' and KodeID", KodeID, "Nama");
				echo "<td><input type=hidden name='Urutan$n' id='Urutan$n' value='$arrSiswa[$n]'>
						  <span id='UrutanGambar$n'></span>
						  <div class='drag t1'>$arrSiswa[$n]<br>$mhsw[Nama]</div></td>";	
			}
			else echo "<td><input type=hidden name='Urutan$n' id='Urutan$n' value=''>
			              <span id='UrutanGambar$n'><img src='../img/kursi.jpg'></span></td>";
		}
		echo "</tr>";
	}
	echo "</table>
		<input type=hidden id='TotalKursi' name='TotalKursi' value='$n'>";
}

function SavData($id)
{	if(empty($id)) die(ErrorMsg('Error', "Jadwal yang dicari tidak ditemukan. <br>Harap hubungi administrator."));
	
	$jdwluas = GetFields('jadwaluas', "JadwalUASID='$id' and KodeID", KodeID, '*');
	$TotalKursi = $_REQUEST['TotalKursi']+0;
	$TotalKosong = $_REQUEST['TotalKosong']+0;
	
	if($TotalKursi > 0)
	{	for($i=1; $i <=$TotalKursi; $i++)
		{	$MhswID = $_REQUEST['Urutan'.$i];
			
			$cek = GetaField('uasmhsw', "MhswID='$MhswID' and JadwalUASID='$id' and KodeID", KodeID, 'UASMhswID');
			if(!empty($cek))
			{	$s = "update uasmhsw 
						set UrutanDiRuang='$i'
						where MhswID='$MhswID' and JadwalUASID='$id' and KodeID='".KodeID."'";
				$r = _query($s);
			}
		}	
	}
	if($TotalKosong > 0)
	{	for($i=1; $i <=$TotalKosong; $i++)
		{	$MhswID = $_REQUEST['Kosong'.$i];
			
			echo "SAVKOSONG $i : $MhswID<br>";
			$cek = GetaField('uasmhsw', "MhswID='$MhswID' and JadwalUASID='$id' and KodeID", KodeID, 'UASMhswID');
			
			if(!empty($cek))
			{	$s = "update uasmhsw 
						set UrutanDiRuang=0
						where MhswID='$MhswID' and JadwalUASID='$id' and KodeID='".KodeID."'";
				$r = _query($s);
			}
		}
	}
	
	$JumlahMhsw = GetaField('uasmhsw', "JadwalUASID='$id' and UrutanDiRuang>0 and KodeID", KodeID, 'count(UASMhswID)'); 
	$s = "update jadwaluas set JumlahMhsw = '$JumlahMhsw' where JadwalUASID='$id'";
	$r = _query($s);
	
	TutupScript();
	echo "<script>ttutup()</script>";	
}

function TutupScript() {
echo <<<SCR
<SCRIPT>
  function ttutup() {
    opener.location='../index.php?mnux=$_SESSION[mnux]&gos=';
    self.close();
    return false;
  }
</SCRIPT>
SCR;
}
	
function Demo()
{	
echo <<<ESD
	<div id="drag" align=center>
			<table id="table1">
				<colgroup><col width="100"/><col width="100"/><col width="100"/><col width="100"/><col width="100"/></colgroup>
				<tr>
					<td class="forbid">You</td>
					<td class="forbid">can</td>
					<td class="forbid">not</td>
					<td class="forbid">drop</td>
					<td class="forbid">here</td>
				</tr>
				<tr style="background-color: #eee">
					<td valign="middle">
						<div class="drag t1">Drag</div>
					</td>
					<td></td>
					<td><div class="drag t1">and</div></td>
					<td><div class="drag t1">drop</div></td>
					<td></td>
				</tr>
				<tr>
					<td><div class="drag t1">content</div></td>
					<td></td>
					<td></td>
					<td></td>
					<td><div class="drag t1"><select style="width: 60px"><option>table</option><option>drop</option><option>down</option><option>menu</option></select></div></td>
				</tr>
				<tr style="background-color: #eee">
					<td></td>
					<td><div class="drag t1">with</div></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td><div class="drag t1">JavaScript</div></td>
					<td></td>
					<td></td>
				</tr>
				<tr style="background-color: #eee">
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
			</table>
			<table id="table2">
				<colgroup><col width="100"/><col width="100"/><col width="100"/><col width="100"/><col width="100"/></colgroup>
				<tr>
					<td class="forbid" title="You can not drop here">Table2</td>
					<td style="background-color: #eee"><div class="drag t2">and</div></td>
					<td rowspan="3" style="background-color: #C6C8CB" title="rowspan 3"></td>
					<td style="background-color: #eee"></td>
					<td></td>
				</tr>
				<tr>
					<td><div class="drag t2">Drag</div></td>
					<td style="background-color: #eee"></td>
					<td style="background-color: #eee"><div class="drag t2">drop</div></td>
					<td><div class="drag t2">table</div></td>
				</tr>
				<tr>
					<td colspan="2" style="background-color: #C6C8CB" title="colspan 2"></td>
					<td colspan="2" style="background-color: #C6C8CB" title="colspan 2"></td>
				</tr>
				<tr>
					<td colspan="2" style="background-color: #C6C8CB" title="colspan 2"></td>
					<td rowspan="3" style="background-color: #C6C8CB" title="rowspan 3"></td>
					<td colspan="2" style="background-color: #C6C8CB" title="colspan 2"></td>
				</tr>
				<tr>
					<td><div class="drag t2"><input type="text" style="width: 60px" value="content"/></div></td>
					<td style="background-color: #eee"></td>
					<td style="background-color: #eee"></td>
					<td></td>
				</tr>
				<tr>
					<td></td>
					<td style="background-color: #eee"><div class="drag t2">with</div></td>
					<td style="background-color: #eee"><div class="drag t2">JavaScript</div></td>
					<td class="forbid" title="You can not drop here">Table2</td>
				</tr>
			</table>
			<table id="table3">
				<colgroup><col width="100"/><col width="100"/><col width="100"/><col width="100"/><col width="100"/></colgroup>
				<tr style="background-color: #eee">
					<td class="forbid" title="You can not drop here">Table3</td>
					<td></td>
					<td></td>
					<td></td>
					<td><div class="drag t3"><input type="checkbox" name="cb1"/><input type="checkbox" name="cb2"/><input type="checkbox" name="cb3"/></div></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td><div class="drag t3 clone">Clone</div></td>
					<td></td>
					<td></td>
				</tr>
				<tr style="background-color: #eee">
					<td><div class="drag t3"><input type="radio" name="radio1"/><input type="radio" name="radio1"/><input type="radio" name="radio1"/></div></td>
					<td></td>
					<td></td>
					<td></td>
					<td class="trash" title="Trash">Trash</td>
				</tr>
			</table><div id="obj_new"></div>
			<div><input type="button" value="Click" class="button" onclick="table_content('table1')" title="Show content of the first table"/><span class="message_line">Show content of the first table</span></div>
			<div><input type="checkbox" class="checkbox" onclick="toggle_dropping(this)" title="Enable dropping to already taken table cells" checked="true"/><span class="message_line">Enable dropping to already taken table cells</span></div>
			<div><input type="checkbox" class="checkbox" onclick="toggle_confirm(this)" title="Confirm before delete object" checked="true"/><span class="message_line">Confirm before delete object</span></div>
		</div>
ESD;
}

?>
