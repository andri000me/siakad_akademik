<?php
// Arisal Yanuarafi 17 September 2014
session_start();
include_once "../dwo.lib.php";
include_once "../db.mysql.php";
include_once "../connectdb.php";
include_once "../parameter.php";
include_once "../cekparam.php";

header("Content-type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename=Rekap");
header("Expires:0");
header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
header("Pragma: public");
echo "<style>td {mso-number-format:'\@';}.alterCellPrinted{background-color:#999;}.alterCellPrinted2{background-color:#FFF;}</style>";
BuatIsinya($_SESSION['_pustakaProdiID'], $xls,$_REQUEST['md']);


function BuatIsinya($ProdiID, $p, $md) {
    echo "<h3>Rekap Koleksi Berdasarkan ".($md==0? "GMD":"Bahasa")."</h3>";
    echo "<p>Tanggal ".TanggalFormat(date('Y-m-d'))."</p>";
  // Buat header tabel
  echo '<table border=1>
      <td width=50><strong style="font-size: 1.5em;">Nmr</td>
    <td><strong style="font-size: 1.5em;">Koleksi</td>
    <td><strong style="font-size: 1.5em;">Judul</td>
    <td><strong style="font-size: 1.5em;">Eksemplar</td>
    </tr>'; $no=0;
  if ($md==0){
            $gmd_q = _query("SELECT DISTINCT gmd_id, gmd_name FROM app_pustaka1.mst_gmd");
            while ($gmd_d = _fetch_array($gmd_q)) {
              $no++;
                $row_class = ($row_class == 'alterCellPrinted')?'alterCellPrinted2':'alterCellPrinted';
                $output .= '<tr><td class="'.$row_class.'"><strong style="font-size: 1.3em;">'.$no.'</td><td class="'.$row_class.'"><strong style="font-size: 1.5em;">'.$gmd_d['gmd_name'].'</strong></td>';
                // count by title
                $bytitle_d = GetaField('app_pustaka1.biblio',"gmd_id", $gmd_d['gmd_id'],"COUNT(biblio_id)");
                $output .= '<td class="'.$row_class.'"><strong style="font-size: 1.3em;">'.$bytitle_d.'</strong></td>';
                // count by item
                $byitem_d = GetaField('app_pustaka1.item AS i INNER JOIN app_pustaka1.biblio AS b ON i.biblio_id=b.biblio_id',"b.gmd_id", $gmd_d['gmd_id'],"COUNT(item_id)");
                $output .= '<td class="'.$row_class.'"><strong style="font-size: 1.3em;">'.$byitem_d.'</strong></td>';
                $output .= '</tr>';
      }
    }else{
            /* LANGUAGE */
            $lang_q = _query("SELECT DISTINCT language_id, language_name FROM app_pustaka1.mst_language");
            while ($gmd_d = _fetch_array($lang_q)) {
              $no++;
                $row_class = ($row_class == 'alterCellPrinted')?'alterCellPrinted2':'alterCellPrinted';
                $output .= '<tr><td class="'.$row_class.'"><strong style="font-size: 1.5em;">'.$no.'</strong></td><td class="'.$row_class.'"><strong style="font-size: 1.5em;">'.$gmd_d['language_name'].'</strong></td>';
                // count by title
                $bytitle_d = GetaField('app_pustaka1.biblio',"language_id", $gmd_d['language_id'],"COUNT(biblio_id)");
                $output .= '<td class="'.$row_class.'"><strong style="font-size: 1.3em;">'.$bytitle_d.'</strong></td>';
                // count by item
                $byitem_d = GetaField('app_pustaka1.item AS i INNER JOIN app_pustaka1.biblio AS b ON i.biblio_id=b.biblio_id',"b.language_id", $gmd_d['language_id'],"COUNT(item_id)");
                $output .= '<td class="'.$row_class.'"><strong style="font-size: 1.3em;">'.$byitem_d.'</strong></td>';
                $output .= '</tr>';
            }
    }
    echo $output;
}
?>
