<?php
// Pencipta: E. Setio Dewo (setio_dewo@telkom.net)
// 2002
// version 0.2 27-06-2003

// Pembaharuan class lister
// Dibuat tanggal 28 Feb 2006
// Versi 1.0

class dwolister {
  // Yang harus diset:
  var $headerfmt = "";
  var $footerfmt = "";
  var $detailfmt = "";
  var $tables = "";
  var $fields = "";
  var $pageactive = "=PAGE=";
  var $pages = "=PAGE=";
  // Optional
  var $pageseparator = ", ";
  var $MaxRowCount = 0;
  var $startrow = 0;
  var $maxrow = 20;
  var $page = 1;
  var $pagecount = 0;
  var $maxpages = 10;

  function BuatHalaman($mulai, $selesai) {
    for ($i = $mulai; $i <= $selesai; $i++) {
      $tmp = ($i == $this->page)? $this->pageactive : $this->pages;
      $tmp = $this->BuatHalaman1($i, $tmp);
      $a .= $tmp . $this->pageseparator;
    }
    return $a;
  }
  function BuatHalaman1($pg, $template) {
    return str_replace('=PAGE=', $pg, $template);
  }
  function TampilkanHalaman() {
    // Hitung jumlah row
    $s_jml = "select count(0) as RW from ".$this->tables;
    $r_jml = _query($s_jml);
    $this->MaxRowCount = _result($r_jml, 0, 'RW');
    $this->maxrow = ($this->maxrow == 0)? 20 : $this->maxrow;
    $this->pagecount = ceil($this->MaxRowCount / $this->maxrow);

    $a = '';
    if ($this->page <= 0) $this->page = 1;
    if ($this->page > $maxpages) {
      // Awal
      if ($this->page-3 > 1) $a .= $this->BuatHalaman1(1, $this->pages) . $this->pageseparator;
      if ($this->page-3 > 3) $a .= "...". $this->pageseparator;
      // Tengah
      $a .= $this->BuatHalaman(($this->page-3 < 1)? 1 : $this->page-3,
        ($this->page+3 > $this->pagecount)? $this->pagecount : $this->page+3);
      // Akhir
      if ($this->page+3 < $this->pagecount-1) $a .= "..." . $this->pageseparator;
      if ($this->page+3 < $this->pagecount)   $a .= $this->BuatHalaman1($this->pagecount, $this->pages);
    }
    else $a .= $this->BuatHalaman(1, $this->pagecount);
    $a = TRIM($a, $this->pageseparator);
    return $a;
  }

  function TampilkanData() {
    // Tampilkan
    $halaman = ($this->page > 0)? $this->page-1 : 0;
    $startrow = $halaman * $this->maxrow;
    $s = "select ". $this->fields . " from ". $this->tables . " limit " . $startrow . "," . $this->maxrow;
    //echo $s;
    $r = _query($s);
    $a = ''; $nomer = 0;
    // Ambil nama2 fields
    $numfields = _num_fields($r);
    $arrfields = array();
    for ($cl=0; $cl < $numfields; $cl++) {
      $arrfields[$cl] = _field_name ($r, $cl);
    }
    // Parsing
    while ($w = _fetch_array($r)) {
      $nomer++;
      $det = $this->detailfmt;
      $det = str_replace("=NOMER=", $nomer, $det);
      for ($i=0; $i < $numfields; $i++) {
        $det = str_replace("=".$arrfields[$i]."=", $w[$arrfields[$i]], $det);
      }
      $a .= $det;
    }
    return $this->headerfmt . $a . $this->footerfmt;
  }
}


?>
