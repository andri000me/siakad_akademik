<?php
error_reporting(E_ALL);
session_start();
	include_once "../dwo.lib.php";
  	include_once "../db.mysql.php";
  	include_once "../connectdb.php";
  	include_once "../parameter.php";
  	include_once "../cekparam.php";



 $getAntrian = explode("~",$_SESSION['_antrianKatalogID']);
	 foreach ($getAntrian as $n){
		 $item_ids .= $n.',';
	 }
	 //echo $item_ids;
	 // strip the last comma
		$item_ids = substr_replace($item_ids, '', -1);
	
		$criteria = "b.biblio_id IN($item_ids)";
		
		$biblio_q = _query('SELECT b.biblio_id, b.title as title, b.call_number, b.sor,
		CONCAT(\'[\', g.gmd_name, \'].\') as gmd,
		CONCAT(b.edition, \'.\') as edition, b.isbn_issn,
		CONCAT(pp.place_name, \' : \', p.publisher_name, \', \', b.publish_year, \'.\') as publisher,
		CONCAT(b.collation, \'.\') as physic,
		CONCAT(b.series_title, \'.\') as series
			FROM app_pustaka1.biblio as b
			LEFT JOIN app_pustaka1.mst_gmd as g on b.gmd_id = g.gmd_id
			LEFT JOIN app_pustaka1.mst_publisher as p on b.publisher_id = p.publisher_id
			LEFT JOIN app_pustaka1.mst_place as pp on b.publish_place_id = pp.place_id
			WHERE '.$criteria);

	$katalog = "";
	$item = 0;
    while ($biblio_d = _fetch_array($biblio_q)) {
		//echo $biblio_d['title'];
		$tajuk[] = "&nbsp;";
		$tajuk[] = $biblio_d['title'];
		// author
		$author_q = _query('SELECT a.author_name
		   FROM app_pustaka1.biblio_author as ba
		   LEFT JOIN app_pustaka1.mst_author as a on ba.author_id = a.author_id
		   WHERE ba.biblio_id = '. $biblio_d['biblio_id']);
		$biblio_d['author'] = "";
		$i = 0;
		while ($author_d = _fetch_row($author_q)) {
			//echo reverseAuthor($author_d[0]) . ', ';
			$biblio_d['author'] .= reverseAuthor($author_d[0]) . ', ';
			$i += 1;
			if ($i == 1) { $mainauthor = $author_d[0]; }
			if ($i > 1) { $tajuk[] = $author_d[0]; }
			if ($i >= 3) { break; }
		}
		// strip the last comma
		if ($biblio_d['sor'] <> "") {
			$biblio_d['author'] = $biblio_d['sor'];
		} else {
			$biblio_d['author'] = substr_replace($biblio_d['author'], '', -2);
		}

        // subject
		$subject_q = _query('SELECT t.topic
		   FROM app_pustaka1.biblio_topic as bt
		   LEFT JOIN app_pustaka1.mst_topic as t on bt.topic_id = t.topic_id
		   WHERE bt.biblio_id = '. $biblio_d['biblio_id']);
		$biblio_d['subject'] = "";
		$i = 0;
		while ($subject_d = _fetch_row($subject_q)) {
			$biblio_d['subject'] .= $subject_d[0]. '; ';
			$tajuk[] = $subject_d[0];
			$i += 1;
			if ($i >= 3) { break; }
		}
		$biblio_d['subject'] = substr_replace($biblio_d['subject'], '', -2);

		// explode label data by space
		$sliced_label = explode(' ', $biblio_d['call_number'], 5);
		if (count($sliced_label) < 3) {
			for ($i=count($sliced_label); $i<3; ++$i) {
				$sliced_label[$i]= "&nbsp";
			}
		}
		// number of copy
		$number_q = _query('SELECT count(item_id)
		   FROM app_pustaka1.item
		   WHERE biblio_id = '. $biblio_d['biblio_id']. ' GROUP BY biblio_id');
		$biblio_d['copies'] = "&nbsp;";
		while ($number_d = _fetch_row($number_q)) {
			$biblio_d['copies'] = $number_d[0] ." salin";
		}

		for ($i=0; $i < count($tajuk); $i++)
		{
		/* check for break page */
		if($item % 3 == 0 AND $item != 0)
		{
			$set_break = ' style="page-break-before:always;" ';
		} else {
			$set_break = '';
		}
		$katalog .= "<tr ".$set_break."><td class=kotak>
			<table border=0 width=470 height=270 cellpadding=0 cellspacing=0>
			<tr><td class=data>&nbsp;</td><td align=center colspan=2 rowspan=2>";
		if (strlen($tajuk[$i]) > 60) {
			$katalog .= substr($tajuk[$i], 0,60)."...";
		} else {
			$katalog .= $tajuk[$i];
		}
		$katalog .="</td></tr>
			<tr><td class=data>".$sliced_label[0]."</td></tr>
			<tr><td class=callno nowrap>".$sliced_label[1]."</td><td align=left class=data colspan=2>".$mainauthor."</td></tr>
			<tr><td class=callno>".$sliced_label[2]."</td><td align=left class=data colspan=2>&nbsp;&nbsp;&nbsp;".$biblio_d['title']." / ".$biblio_d['author'].". --  ".$biblio_d['edition']."</td></tr>
			<tr><td></td><td align=left class=data colspan=2>&nbsp;&nbsp;&nbsp;".$biblio_d['publisher']."</td></tr>
			<tr><td></td><td colspan=2 class=data>&nbsp;</td></tr>
			<tr><td></td><td align=left class=data colspan=2>&nbsp;&nbsp;&nbsp;".$biblio_d['physic']."-- ".$biblio_d['series'].".</td></tr>
			<tr><td></td><td align=left colspan=2 class=data>&nbsp;&nbsp;&nbsp;ISBN ".$biblio_d['isbn_issn'].".</td></tr>
			<tr><td></td><td colspan=2 class=data>&nbsp;&nbsp;&nbsp;</td></tr>
			<tr><td></td><td align=left class=data colspan=2>&nbsp;&nbsp;&nbsp;".$biblio_d['subject'].".</td></tr>
			<tr><td></td><td class=data colspan=2>&nbsp;</td></tr>
			<tr><td></td><td align=left class=data>&nbsp;&nbsp;&nbsp;".$biblio_d['copies']."</td><td class=data align=right>&nbsp;</td></tr>
			</table>
			</td></tr>\n";
		$item++;
		}

		unset($tajuk);
		unset($sliced_label);
    }
	// create html ouput of images
    $html_str = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'."\n";
    $html_str .= '<html xmlns="http://www.w3.org/1999/xhtml"><head><title>Document Label Print Result</title>'."\n";
    $html_str .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    $html_str .= '<meta http-equiv="Pragma" content="no-cache" /><meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, post-check=0, pre-check=0" /><meta http-equiv="Expires" content="Sat, 26 Jul 1997 05:00:00 GMT" />';
    $html_str .= '<style type="text/css">'."\n";
    $html_str .= '@media print {'."\n";
    $html_str .= '.doNotPrint { display: none; }'."\n";
    $html_str .= '}'."\n";
    $html_str .= '.data {FONT-FAMILY: verdana; FONT-SIZE: 10px; HEIGHT: 20px; PADDING-LEFT: 5px; PADDING-TOP: 0px; text-valign: bottom;  background:#ffffff}'."\n";
    $html_str .= '.callno {FONT-FAMILY: verdana; FONT-SIZE: 10px; HEIGHT: 20px; PADDING-LEFT: 5px; PADDING-TOP: 0px; vertical-align: top;  background:#ffffff}'."\n";
    $html_str .= '.kata {FONT-FAMILY: verdana; FONT-SIZE: 11px;}'."\n";
    $html_str .= '.kotak {FONT-FAMILY: verdana; FONT-SIZE: 11px; HEIGHT: 20px; FONT-STYLE: bold; PADDING-LEFT: 5px; PADDING-RIGHT: 5px; text-valign: bottom;background:#ffffff;border-bottom:solid 1px #000000;border-top:solid 1px #000000;border-left:solid 1px #000000;border-right:solid 1px #000000;text-align:center}'."\n";
    $html_str .= '</style>'."\n";
    $html_str .= '</head>'."\n";
    $html_str .= '<body>'."\n";
    $html_str .= '<a href="#" class="doNotPrint" onclick="window.print()">Print Again</a>'."\n";
    $html_str .= '<table border=0 cellpadding=0 cellspacing=5>'."\n";
	  $html_str .= $katalog;
    $html_str .= '</table>'."\n";
    $html_str .= '<script type="text/javascript">self.print();</script>'."\n";
    $html_str .= '</body></html>'."\n";
	print $html_str;
$_SESSION['_antrianKatalogID']='';
function reverseAuthor($lastfirst) {
	if ($lastfirst == "") {
		return "";
	} else {
		list($last, $first) = explode(', ', $lastfirst);
		if ($first <>"") {
			return $first . " " . $last;
		} else {
			return $last;
		}
	}
}
 

?>
