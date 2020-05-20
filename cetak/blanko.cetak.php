<?php

// Created By Sugeng S
// Juli 2006
  
session_start();
include "../sisfokampus.php";
include "db.mysql.php";
include_once "connectdb.php";
include_once "dwo.lib.php";
include_once "parameter.php";
Cetak();
include_once "disconnectdb.php";

function GetaField2($_tbl,$_key,$_value,$order,$_result) {
  global $strCantQuery;
	$_sql = "select $_result from $_tbl where $_key='$_value' $order limit 1";
	$_res = _query($_sql);
	//echo $_sql;
	if (_num_rows($_res) == 0) return '';
	else {
	  $w = _fetch_array($_res);
	  return $w[$_result];
	}
}

function BuatNilai($w,$mhsw){
  $kd = "Select distinct mk.MKKode,mk.MKID 
    from mkpra 
    left outer join mk on mkpra.PraID = mk.MKID 
    where mkpra.MKKode = '$w[MKKode]' or mkpra.MKID = $w[MKID]";
  $rkd = _query($kd);
  $retkd = '';
  while($wkd = _fetch_array($rkd)){
  	$nl = GetaField2("krs","Mhswid = '$mhsw[MhswID]' and MKKode",$wkd['MKKode'],'order by BobotNilai DESC','GradeNilai');
	  $nnl = (!empty($nl)) ? "(".$nl.")" : '';
	  $retkd .= $wkd['MKKode'] . $nnl . ", ";
  }
  return TRIM($retkd, ", ");
}

function GetArrayTable3($sql, $key, $label, $separator=', ') {
  // Digunakan untuk menerjemahkan array dalam string
  $r = _query($sql);
  $ret = '';
  while ($w = _fetch_array($r)) {
    $ret .= "'".$w[$label]."'".$separator;
  }
  return TRIM($ret, $separator);
}


function KecualiMK($khs,$mhsw){
  $nilai = GetArrayTable3("select Nama from nilai where Lulus = 'N' and ProdiID = '$mhsw[ProdiID]'",'Nama' ,'Nama');
  $nilai = (empty($nilai)) ? 0 : $nilai;
  $s0 = "select krsprc.MKKode
         from krsprc
           where krsprc.MhswID='$khs[MhswID]'
         and not (krsprc.GradeNilai in ($nilai,'-'))";
  $r = _query($s0);
  $hasil = array();
  while ($w = _fetch_array($r)) $hasil[] = "'$w[MKKode]'";
  $kecuali = (empty($hasil))? '' : implode(', ', $hasil);
  return $kecuali;
}

function GetSerial($w){
  global $_lf;
  $s0 = "select distinct j.*,h.Nama as HR,
        time_format(j.JamMulai, '%H:%i') as JM, time_format(j.JamSelesai, '%H:%i') as JS from
        jadwal j left outer join hari h on j.HariID=h.HariID where JadwalSer = '$w[JadwalID]' and 
        NamaKelas = '$w[NamaKelas]'";
  $r0 = _query($s0);
        while($ksl = _fetch_array($r0)){
          $serial .=  str_pad("    + SERIAL", 1) . 
                     str_pad($ksl['NamaKelas'], 50, ' ', STR_PAD_LEFT) .
                     str_pad($ksl['HR'], 22, ' ',STR_PAD_LEFT).
                     str_pad($ksl['JM']."-".$ksl['JS'],13,' ',STR_PAD_LEFT) . $_lf;
        }
  return $serial;
}

function GetIPSLalu($mhsw){
  $s = "select IPS from khs 
          where 
        MhswID = '$mhsw[MhswID]' 
          and
        IPS <> 0
          order By TahunID DESC";
  $r = _query($s);
  $w = _fetch_array($r);
  return $w['IPS']+0;
}

function Cetak() {
  global $_lf;
  echo "<body bgcolor=#EEFFFF>";
   // Parameters
  $pos = $_SESSION['BLANKO-POS'];
  $max = $_SESSION['BLANKO-MAX'];
  $nmf = $_SESSION['BLANKO-FILE'];
  $_khsid = $_SESSION['khsid'];
  $khsid = $_khsid[$pos];
  if ($pos < $max) {
  	$grs = str_pad("-",150 ,'-').$_lf;
    // Buat file
    $f = fopen($nmf, 'a');
	  $brs = 0; $maxbrs= 40; $hal=1;
	  fwrite($f, chr(27).chr(77).chr(15).chr(27).chr(108).chr(8). $_lf);
	  $namathn = NamaTahun($_SESSION['tahun']);
    //$khsid = $_REQUEST['khsid'];
    $khs = GetFields('khs', 'KHSID', $khsid, '*');
    $mhsw = GetFields("mhsw m
      left outer join program prg on m.ProgramID=prg.ProgramID
      left outer join prodi prd on m.ProdiID=prd.ProdiID", 
      'MhswID', $khs['MhswID'], 
      "m.*, prg.Nama as PRG, prd.Nama as PRD");
    $tahun = GetFields('tahun', "KodeID='$khs[KodeID]' and TahunID='$khs[TahunID]' and ProdiID",
      $khs['ProdiID'], '*');
    $PA = GetaField('dosen', 'Login', $mhsw['PenasehatAkademik'], "concat(Nama, ', ', Gelar)");
    
    if ($khs['Sesi'] > 1) {
      $ipslalu = GetIPSLalu($mhsw);
    } else {
      $ipslalu = 0;
    }
    
    $kecuali = KecualiMK($khs,$mhsw);
	  $sqlkecuali = (empty($kecuali))? '' : "and not (j.MKKode in ($kecuali))";
	  $maxsks = GetaField("maxsks","INSTR(ProdiID, '.$mhsw[ProdiID].') > 0 and DariIP <= $ipslalu or SampaiIP >" ,$ipslalu ,'SKS' );
    $peringatan = "    Anda masih memiliki utang semester lalu + denda 5% sebesar Rp.$_bal. $_lf 
    Anda tidak akan mendapat KHS dan juga tidak dapat mendaftar KRS $_lf
    sebelum utang ini anda lunasi. $_lf
    Lakukan pembayaran di Bank sesuai dengan prosedur yang berlaku.";
    $hdr  = str_pad("FORM LEMBAR RENCANA STUDI",150,' ',STR_PAD_BOTH).$_lf.$_lf;
	  $hdr .= str_pad($namathn,150,' ',STR_PAD_BOTH).$_lf.$_lf;
    $hdr .= str_pad("NPM           : $mhsw[MhswID]", 115,' ').$_lf;
	
	  $hdr .= str_pad("Mahasiswa     : $mhsw[Nama]", 115, ' ').
            str_pad("SKS Maksimal  : $maxsks", 18, ' ').$_lf;
			
	  $hdr .= str_pad("Program       : $mhsw[PRG]", 115, ' ').
	        str_pad("IPS           : $ipslalu", 18, ' ').$_lf;
	
	  $hdr .= str_pad("Prodi         : $mhsw[PRD]", 115, ' '). 
            str_pad("Total SKS     : $mhsw[TotalSKS]", 18, ' ').$_lf;
	
	  $hdr .= str_pad("P.A           : $PA", 115, ' ').
            str_pad("IPK           : $mhsw[IPK]", 18, ' ').$_lf;
		  
    $hdr .= $grs."NO. KODE         MATA KULIAH                         SKS JEN KEL TAR ISI AMBIL HARI      JAM      PRASYARAT                            PARAF PA  " .$_lf.$grs.$_lf;
    fwrite($f, $hdr);
  	//$isi = GetIsiBlanko($khsid, $khs, $mhsw);
	  $s = "select j.*, h.Nama as HR, time_format(j.JamMulai, '%H:%i') as JM,
    time_format(j.JamSelesai, '%H:%i') as JS, LEFT(j.Nama,45) as JNama, mod(mk.Sesi,2) as Sesi
      from jadwal j
    left outer join hari h on j.HariID=h.HariID
	  left outer join mk on mk.MKID = j.MKID
      where j.KodeID='$khs[KodeID]'
    and j.TahunID='$_SESSION[tahun]'
    and INSTR(j.ProdiID, '.$mhsw[ProdiID].')>0
	  $sqlkecuali
    and j.JadwalSer=0
    order by j.MKKode";
  //echo "<pre>$s</pre>";
  //exit;
    $r = _query($s);
    $jumlahrec = _num_rows($r);
    $jumhal = ceil($jumlahrec/$maxbrs);
    $n = 0;
  //$isi = array();
  //if (empty($bal)) {
    while($w = _fetch_array($r)){
	    $brs++;
	    if($brs > $maxbrs){
		    $hal++; $brs = 1;
        fwrite($f, $grs);
        fwrite($f, str_pad("Hal. : ".$hal.'/'.$jumhal, $maxcol, ' ', STR_PAD_LEFT).$_lf);
		    fwrite($f,chr(12));
		    fwrite($f, $hdr);
	    }
      if ($w['JumlahKelasSerial'] > 0) {
        $serial = GetSerial($w);
      }  else {$serial = '';}
	    if ($kdmk != $w['MKKode']) {
        $kdmk = $w['MKKode'];
        $_kdmk = $kdmk;
	      $n++;
      } else $_kdmk = '';
	    if ($n_ != $n) {
	      $n_ = $n;
	      $_n_ = $n_.".";
	      $titik = "......";
	    } else {
	      $_n_ = '';
	      $titik = '';
	    }	
	    $pra = BuatNilai($w,$mhsw);
	    $arrPra = Array();
	    $apa = Array();
	    $apa2 = Array();
	    $arrPra = explode(', ',$pra);
	    if (Sizeof($arrPra) > 3){
        for ($i=0; $i < 3; $i++) {
          $apa[] = $arrPra[$i];
        }
        for ($j=3; $j < sizeof($arrPra);$j++){
          $apa2[] = $arrPra[$j];
        }
      }
      else {
        $__pra = $pra;
      }
      $__pra = (empty($apa)) ? $__pra : implode(", ", $apa);
      $_pra_ = (empty($apa2)) ? '' : implode(", ", $apa2);
  
    if ($mk != $w['JNama']) {
	  $mk = $w['JNama'];
	  $_mk = $mk;
	  $_sks = $w['SKS'];
	  $_pra = $__pra; 
	  $_pra2 = $_pra_; 
	} else {
	  $_mk = '';
	  $_sks = '';
	  $_pra ='';
	  $_pra2 = '';
	}
	  $_pra2_ = (empty($_pra2)) ? '' : str_pad(" ",98,' ').str_pad($_pra2, 30, ' ').$_lf;
		$as = ($_n_ % 2 == 0) ? 11 : 18;
		$isi =
      //str_pad($w['MKID'], 4). 
      str_pad($_n_, 4, ' ').
      	str_pad($_kdmk, 7).
      	str_pad($_mk, 41).
		str_pad($_sks, 4, ' ',STR_PAD_Left) .
		str_pad($w['JenisJadwalID'],2,' ',STR_PAD_LEFT).
      	str_pad($w['NamaKelas'], 4, ' ',STR_PAD_LEFT).
		str_pad($w['Kapasitas'], 6,' ',STR_PAD_LEFT).' '.
      	str_pad($w['JumlahKRSMhsw'], 3, ' ',STR_PAD_LEFT). ' '.
		str_pad(".....", 4,' ').' '.
		str_pad($w['HR'], 7, ' ').
		str_pad($w['JM']."-".$w['JS'],11,' ').' '.
		str_pad($_pra, 28,' ').
		str_pad($titik, $as, ' ',STR_PAD_LEFT) . $_lf .
		$_pra2_;
	  fwrite($f, $isi);
    fwrite($f, $serial);
	//}
	//} else {
    //fwrite($f, $peringatan . $_lf);
  }
  $tgl = date('d-m-Y H:i');
	$isi_ .= $grs;
	$isi_ .= str_pad("Jumlah",139,' ',STR_PAD_LEFT). str_pad(":", 4,' '). str_pad(".......",5, ' ').$_lf.$grs;
	$isi_ .= str_pad("Hal. : ".$hal.'/'.$jumhal, $maxcol, ' ', STR_PAD_LEFT).$_lf;
  $isi_ .= str_pad("Dicetak oleh : ",4,' '). $_SESSION['_Login']. ', '. $tgl. $_lf;
	fwrite($f, $isi_);
	fwrite($f,chr(12).$_lf);
	fclose($f);
    // refresh page
    echo "<p>Proses Lembar Rencana Studi: <font size=+2>$pos/$max</font><br />
	$khsid &raquo; $khs[NamaMhsw]</p>";
    echo "<script type='text/javascript'>window.onload=setTimeout('window.location.reload()', 2);</script>";
  	}
  	else {
      echo "<p>Pembuatan file Cetak Lembar Rencana Studi telah selesai.<br />
	  Untuk memulai mencetak klik: <a href='$nmf'><img src='img/printer.gif' border=0></a></p>";
	  echo "<p>Untuk melihat preview klik <a href=blanko.preview.php?nmf=$nmf target=_blank><img src='img/view.png' border=0></a></p>";
  	  }
	  $_SESSION['BLANKO-POS']++;
}
