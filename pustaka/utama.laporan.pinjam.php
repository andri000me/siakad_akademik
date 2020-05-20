<?php
// Arisal Yanuarafi 17 September 2014
session_start();
include_once "../dwo.lib.php";
include_once "../db.mysql.php";
include_once "../connectdb.php";
include_once "../parameter.php";
include_once "../cekparam.php";

// Tgl Mulai
$TglMulai_y = GetSetVar('TglMulai_y', date('Y'));
$TglMulai_m = GetSetVar('TglMulai_m', date('m'));
$TglMulai_d = GetSetVar('TglMulai_d', '01');
$_SESSION['TglMulai'] = "$TglMulai_y-$TglMulai_m-$TglMulai_d";
// Tgl Selesai
$TglSelesai_y = GetSetVar('TglSelesai_y', date('Y'));
$TglSelesai_m = GetSetVar('TglSelesai_m', date('m'));
$TglSelesai_d = GetSetVar('TglSelesai_d', date('d'));
$_SESSION['TglSelesai'] = "$TglSelesai_y-$TglSelesai_m-$TglSelesai_d";

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'KonfirmasiTgl' : $_REQUEST['gos'];
$gos();

// *** Functions ***
function KonfirmasiTgl() {
  KonfirmasiTanggal("../$_SESSION[mnux].laporan.pinjam.php", "Cetak");
}

function Cetak(){
    header("Content-type:application/vnd.ms-excel");
    header("Content-Disposition:attachment;filename=Laporan-Peminjaman-".$_SESSION['TglMulai']."-".$_SESSION['TglSelesai']);
    header("Expires:0");
    header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
    header("Pragma: public");
    echo "<style>td {mso-number-format:'\@';}.alterCellPrinted{background-color:#999;}.alterCellPrinted2{background-color:#FFF;}</style>";
    BuatIsinya($_SESSION['_pustakaProdiID'], $xls,$_REQUEST['md']);
}

function BuatIsinya($ProdiID, $p, $md) {
    echo "<h3>Laporan Peminjaman</h3>";
    echo "<p>Tanggal ".TanggalFormat($_SESSION['TglMulai'])." s/d ".TanggalFormat($_SESSION['TglSelesai'])."</p>";
  // Buat header tabel
  echo '<table border=1>
      <td width=50><strong style="font-size: 1.5em;">Nmr</td>
    <td><strong style="font-size: 1.5em;">ID Anggota</td>
    <td><strong style="font-size: 1.5em;">Nama</td>
    <td><strong style="font-size: 1.5em;">Kode</td>
    <td><strong style="font-size: 1.5em;">Judul</td>
    <td><strong style="font-size: 1.5em;">Tanggal Pinjam</td>
    <td><strong style="font-size: 1.5em;">Harus Kembali</td>
    </tr>'; $no=0;
    $date_criteria = ' AND (TO_DAYS(l.loan_date) BETWEEN TO_DAYS(\''.$_SESSION['TglMulai'].'\') AND
            TO_DAYS(\''.$_SESSION['TglSelesai'].'\'))';

              $gmd_q = _query("SELECT l.item_code,
            b.title, l.loan_date, l.due_date,l.member_id,m.member_name
            FROM app_pustaka1.loan AS l
                LEFT JOIN app_pustaka1.item AS i ON l.item_code=i.item_code
                LEFT JOIN app_pustaka1.biblio AS b ON i.biblio_id=b.biblio_id
                LEFT JOIN app_pustaka1.member AS m ON l.member_id=m.member_id
            WHERE (l.is_lent=1 AND l.is_return=0) ".( !empty($date_criteria)?$date_criteria:'' ));
            while ($gmd_d = _fetch_array($gmd_q)) {
              $no++;
                $row_class = ($row_class == 'alterCellPrinted')?'alterCellPrinted2':'alterCellPrinted';
                $output .= '<tr><td class="'.$row_class.'"><strong style="font-size: 1.3em;">'.$no.'</strong></td><td class="'.$row_class.'"><strong style="font-size: 1.3em;">'.$gmd_d['member_id'].'</strong></td>';
                $output .= '<td class="'.$row_class.'"><strong style="font-size: 1.3em;">'.$gmd_d['member_name'].'</strong></td>';
                $output .= '<td class="'.$row_class.'"><strong style="font-size: 1.3em;">'.$gmd_d['item_code'].'</strong></td>';
                $output .= '<td class="'.$row_class.'"><strong style="font-size: 1.3em;">'.$gmd_d['title'].'</strong></td>';
                $output .= '<td class="'.$row_class.'"><strong style="font-size: 1.3em;">'.$gmd_d['loan_date'].'</strong></td>';
                $output .= '<td class="'.$row_class.'"><strong style="font-size: 1.3em;">'.$gmd_d['due_date'].'</strong></td>';
                $output .= '</tr>';
            }
    echo $output;
}
?>
