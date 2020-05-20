<?php
// Arisal Yanuarafi 28 September 2015
session_start();
include_once "../dwo.lib.php";
include_once "../db.mysql.php";
include_once "../connectdb.php";
include_once "../parameter.php";
include_once "../cekparam.php";

// *** Main ***
$gos = (empty($_REQUEST['gos']))? 'Cetak' : 'Cetak';
$gos();

function Cetak(){
    header("Content-type:application/vnd.ms-excel");
    header("Content-Disposition:attachment;filename=Daftar-Buku-Pustaka");
    header("Expires:0");
    header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
    header("Pragma: public");
    echo "<style>td {mso-number-format:'\@';}.alterCellPrinted{background-color:#999;}.alterCellPrinted2{background-color:#FFF;}</style>";
    BuatIsinya($_SESSION['_pustakaProdiID'], $xls,$_REQUEST['md']);
}

function BuatIsinya($ProdiID, $p, $md) {
    echo "<h3>Daftar Buku</h3>";
    echo "<p>Tanggal ".TanggalFormat(date('Y-m-d'))."</p>";
  // Buat header tabel
  echo '<table border=1>
      <td width=50><strong style="font-size: 1.5em;">Nmr</td>
    <td><strong style="font-size: 1.5em;">ID Biblio</td>
    <td><strong style="font-size: 1.5em;">Judul</td>
    <td><strong style="font-size: 1.5em;">Penerbit</td>
    <td><strong style="font-size: 1.5em;">Kota Terbit</td>
    <td><strong style="font-size: 1.5em;">ISBN/ISSN</td>
    <td><strong style="font-size: 1.5em;">Nomor Panggil</td>
    <td><strong style="font-size: 1.5em;">Eksemplar</td>
    </tr>'; $no=0;

        $gmd_q = _query("SELECT b.biblio_id, b.title , COUNT(item_id) AS 'Copies'
        , pl.place_name AS 'Kota Terbit'
        , pb.publisher_name AS 'Penerbit'
        ,  b.isbn_issn AS 'ISBN', b.call_number AS 'Nomor Panggil' FROM 
        app_pustaka1.biblio AS b
        LEFT JOIN app_pustaka1.item AS i ON b.biblio_id=i.biblio_id
        LEFT JOIN app_pustaka1.mst_place AS pl ON b.publish_place_id=pl.place_id
        LEFT JOIN app_pustaka1.mst_publisher AS pb ON b.publisher_id=pb.publisher_id
        group by b.biblio_id");
            while ($gmd_d = _fetch_array($gmd_q)) {
              $no++;
                $row_class = ($row_class == 'alterCellPrinted')?'alterCellPrinted2':'alterCellPrinted';
                $output .= '<tr><td class="'.$row_class.'"><strong style="font-size: 1.3em;">'.$no.'</strong></td>
                            <td class="'.$row_class.'"><strong style="font-size: 1.3em;">'.$gmd_d['biblio_id'].'</strong></td>';
                $output .= '<td class="'.$row_class.'"><strong style="font-size: 1.3em;">'.$gmd_d['title'].'</strong></td>';
                $output .= '<td class="'.$row_class.'"><strong style="font-size: 1.3em;">'.$gmd_d['Penerbit'].'</strong></td>';
                $output .= '<td class="'.$row_class.'"><strong style="font-size: 1.3em;">'.$gmd_d['Kota Terbit'].'</strong></td>';
                $output .= '<td class="'.$row_class.'"><strong style="font-size: 1.3em;">'.$gmd_d['ISBN'].'</strong></td>';
                $output .= '<td class="'.$row_class.'"><strong style="font-size: 1.3em;">'.$gmd_d['Nomor Panggil'].'</strong></td>';
                $output .= '<td class="'.$row_class.'"><strong style="font-size: 1.3em;">'.$gmd_d['Copies'].'</strong></td>';
                $output .= '</tr>';
            }
    echo $output;
}
?>
