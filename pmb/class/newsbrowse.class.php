<?php
  // Pencipta: E. Setio Dewo
  // 2002
  // version 0.2 27-06-2003

class newsbrowser {
  var $headerfmt = "";
  var $footerfmt = "";
  var $detailfmt = "";
  var $query = "";
  var $sqlres;

  function BrowseNews() {
	$resstr = "";
	Global $strCantQuery, $strNoContent, $strFatalFailure;

    if (!isset($this->query)) die($strFatalFailure);
    if (isset($this->headerfmt)) { $strhead = $this->headerfmt; }
    $this->sqlres = mysql_query ($this->query) or 
      die ("$strCantQuery: ".$this->query."<br>".mysql_error());

    if (mysql_num_rows ($this->sqlres) == 0) { $resstr = $strhead . $this->footerfmt; }
    else {
      if (isset($this->headerfmt)) { $resstr = $this->headerfmt; }
      // ambil nama2 field
      $numfields = mysql_num_fields($this->sqlres);
	  for ($cl=0; $cl < $numfields; $cl++) { $arrfields [$cl] = mysql_field_name ($this->sqlres, $cl); }
	  $nomer = 0;
      while ($row = mysql_fetch_array($this->sqlres)) {
	    $tmp = $this->detailfmt;
		$nomer++;
	    for ($cl=0; $cl < $numfields; $cl++) {
		  settype($nomer, "string");
		  //echo $nomer;
		  $tmp = str_replace("=NOMER=", $nomer, $tmp);
          $nmf = $arrfields [$cl];
          $tmp = str_replace ("=".$nmf."=", StripEmpty($row[$nmf]), $tmp);
          $tmp = str_replace ("=!".$nmf."=", StripEmpty(urlencode($row[$nmf])), $tmp);
	      $tmp = str_replace ("=:".$nmf."=", StripEmpty(stripslashes($row[$nmf])), $tmp);	    
		}
	    $resstr = $resstr . $tmp;
	  }
	  $resstr = $resstr . $this->footerfmt;
	}
    
    return $resstr;
  }  // end function
  
}  // end class

?>
