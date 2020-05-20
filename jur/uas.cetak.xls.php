<?php
$_uasProdi = GetSetVar('_uasProdi2');
$_uasProg  = GetSetVar('_uasProg2');
$_uasTahun = GetSetVar('_uasTahun2');
$namafile = "tes.xls";
header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename='tes.xls'");
header("Expires:0");
header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
header("Pragma: public");


// Buat Header
  echo "<table class=box cellspacing=1 align=center width=860>";
  $hdr = "
  <tr><th class=ttl width=50>#</th>
      <th class=ttl>Matakuliah</th>
	  <th class=ttl width=80>Kelas</th>
      <th class=ttl width=20>Print</th>
	  <th class=ttl width=75>Ujian</th>
      <th class=ttl width=75>Jam</th>
	  <th class=ttl width=40 title='Pembagian Kursi'>Kursi</th>
      <th class=ttl width=20 title='Hapus Jadwal'>Del</th>
	  <th class=ttl width=30 title='Hapus Jadwal'>Edit</th>
      </tr>";

  $whr_prg = (empty($_SESSION['_uasProg']))? '' : "and j.ProgramID = '$_SESSION[_uasProg]'";
  
  $s = "select kl.Nama as NamaKelas, j.JadwalID, j.ProdiID, j.ProgramID, j.HariID,
      j.RuangID, j.MKKode, j.Nama, j.DosenID, j.SKS,
      concat(d.Nama, ' <sup>', d.Gelar, '</sup>') as DSN,
      LEFT(j.JamMulai, 5) as _JM, LEFT(j.JamSelesai, 5) as _JS,
      h.Nama as HR, mk.Sesi, j.Final, 
      j.JumlahMhsw, j.Kapasitas, 
      j.BiayaKhusus, j.Biaya, format(j.Biaya, 0) as _Biaya
    from kelas kl, jadwal j
      left outer join hari h on j.HariID = h.HariID
      left outer join dosen d on d.Login = j.DosenID and d.KodeID = '".KodeID."'
      left outer join mk mk on mk.MKID = j.MKID
	  left outer join jenisjadwal jj on jj.JenisJadwalID = j.JenisJadwalID
	where j.KodeID = '".KodeID."'
      and j.TahunID = '$_SESSION[_uasTahun]'
      and j.ProdiID = '$_SESSION[_uasProdi]'
      $whr_prg $whr_hr $whr_smt $whr_kls $whr_kd
	  and kl.KelasID=j.NamaKelas
      and j.NA = 'N'
	  and jj.Tambahan = 'N'
    order by j.UASTanggal, j.UASJamMulai, j.UASJamSelesai, j.Nama";
  $r = _query($s); $n = 0;
  $HariID = -320;
  $kanan = "<img src='img/kanan.gif' />";
  echo $hdr;
  while ($w = _fetch_array($r)) {
    $n++;
    if ($w['Final'] == 'Y') $c = "class=nac";
    else $c = "class=ul";
    
    // Ambil dosen2
    $dsn = AmbilDosen2($w['JadwalID']);
    
    // Tampilkan data
	$JumlahJadwalUAS = GetaField('jadwaluas', "JadwalID='$w[JadwalID]' and KodeID", KodeID, "count(JadwalUASID)");
	$rowspan = "rowspan=". (($JumlahJadwalUAS == 0)? 1 : $JumlahJadwalUAS);
	
    echo "<tr>
      <td class=inpx align=center width=20 $rowspan>$n</font></br>
        <div align=center><sub title='ID Jadwal'>#$w[JadwalID]</div></sub>
        </td>
      <td $c $rowspan><b>$w[Nama]</b> <sup>$w[MKKode]</sup><br>
		<div align=left>
			(<b>$w[HR]</b>, $w[_JM]&#8594;$w[_JS])
			</div>
		<div align=right>
			<b>Dosen:</b><i> $w[DSN]
			$dsn</i></div>
        </td>
      <td $c align=center $rowspan>
        &nbsp;<br>
		<font color=darkred><b>$w[NamaKelas]</b></font>&nbsp;<br>
		&nbsp;</br>
		<div valign=bottom># Mhsw: <b>$w[JumlahMhsw]</b></div>
        </td>";
	
	if($JumlahJadwalUAS == 0)
	{ if($w['Final'] == 'Y')
		echo "
		<td $c colspan=6 align=center><b>Belum terjadwal.</b></td>";
		else
		echo "
	  <td $c colspan=6 align=center><b>Belum terjadwal.</b> <a href='#' onClick=\"javascript:JdwlEdt(1, $w[JadwalID])\">>> Tambah <<</a></td>";
	}
	else
	{ 
	  $s1 = "select  ju.RuangID,ju.JadwalUASID,
	            date_format(ju.Tanggal, '%d-%m-%y') as _UASTanggal,
			    huas.Nama as _UASHari, ju.JumlahMhsw as _JumlahMhswUAS,
			    LEFT(ju.JamMulai, 5) as _UASJamMulai, LEFT(ju.JamSelesai, 5) as _UASJamSelesai
				from jadwaluas ju left outer join hari huas on huas.HariID = date_format(ju.Tanggal, '%w')
				where ju.JadwalID='$w[JadwalID]' and ju.KodeID='".KodeID."'";
	  $r1 = _query($s1);
	  $s8 = "select  ju.JadwalUASID,
	            date_format(ju.Tanggal, '%d-%m-%y') as _UASTanggal,
			    huas.Nama as _UASHari, ju.JumlahMhsw as _JumlahMhswUAS,
			    LEFT(ju.JamMulai, 5) as _UASJamMulai, LEFT(ju.JamSelesai, 5) as _UASJamSelesai
				from jadwaluas ju left outer join hari huas on huas.HariID = date_format(ju.Tanggal, '%w')
				where ju.JadwalID='$w[JadwalID]' and ju.KodeID='".KodeID."'";
	  $r8 = _query($s8);
	   $JadwalUASID2='-';
	   	  while($w1 = _fetch_array($r1))
	  {	  
	  if ($w['Final'] == 'Y')
	      {  $edt = "<img src='img/lock.jpg' width=26 title='Sudah difinalisasi. Sudah tidak dapat diedit.' />";
			 $del = "&times;";
			 $editkursi = "<a href='#' onClick=\"alert('Penempatan kursi mahasiswa sudah tidak dapat dilakukan.')\"><img src='img/kursi.jpg'></a>";
          }
		  else 
		  {$nn=0;
		 
		  while($w8 = _fetch_array($r8))
		  { 
		  if ($nn==0) {
		  $JadwalID=$w[JadwalID];
		  $JadwalUASID1=$w8[JadwalUASID];
		   $edt = "<a href='#' onClick=\"javascript:JdwlEdt1(0, $JadwalID, $JadwalUASID1 )\" title='Edit jadwal'><img src='img/edit.jpg' width=20 /></a>";
		  }
		  else {
		  $JadwalUASID2=$w8[JadwalUASID];
		  $JadwalID=$w[JadwalID];
		  $edt = "<a href='#' onClick=\"javascript:JdwlEdt(0, $JadwalID, $JadwalUASID1, $JadwalUASID2 )\" title='Edit jadwal'><img src='img/edit.jpg' width=20 /></a>";
		  }
		  $nn++;
		  }
		  
		   
		  }
		 
		  $del = ($w1['JumlahMhsw'] > 0)? "<abbr title='Tidak dapat dihapus karena sudah ada Mhsw yang mendaftar'>&times;</abbr>" : "<a href='#' onClick=\"javascript:JdwlDel($w[JadwalID],$w1[JadwalUASID])\" title='Hapus jadwal'><img src='img/del.gif' /></a>";
			 $editkursi = "<a href='#' onClick=\"EdtKursi('$w1[JadwalUASID]')\"><img src='img/kursi.jpg'></a>";
		  echo "
		  <td $c align=center>
			<a href='#' onClick=\"PrintUAS('$w1[JadwalUASID]')\">Cetak</a>
			</td>
		  <td $c align=center>
		  	<sup>$w1[_UASHari]</sup><br />
			$w1[_UASTanggal]
			</td>
		  <td $c align=center>
		  <b>$w1[RuangID]</b><br />
			<sup>$w1[_UASJamMulai]</sup>&#8594;<sub>$w1[_UASJamSelesai]</sub>
			</td>
		  
		  <td $c align=center valign=center nowrap>
			$editkursi
			<div valign=bottom># Mhsw: <b>$w1[_JumlahMhswUAS]</b></div>
			</td>
		  <td $c align=center>
			$del
		  </td>";
		 	 			echo "<td class=ul1 align=center >$edt</td></tr>";		

	   }
	    
	  
	}
	
  }
  echo "</table></p>";
?>