<?php

//created By Sugeng

class DBFConnection{
	var $DBFCon;
	var $DBFFile;
	
	function DBFConnection($file){
		$this->DBFFile = $file;
	}
	
	function DBFCreate($arrDef){
		if (is_array($arrDef)){
			if (dbase_create($this->DBFFile, $arrDef)) {
				$ret = TRUE;
			} else {
				$ret = FALSE;
			}
		} else die("Tipe data bukan Array nih ye");
		return $ret;
	}
	
	function DBFOpen($flag=2){
		$this->DBFCon = dbase_open($this->DBFFile, $flag);
		if (!$this->DBFCon) die("Gagal Membuka File DBF");
		else return $this->DBFCon;
	}
	
	function DBFAddRecord($add){
		if (!is_array($add)) die("Tipe data bukan Array");
		else {
			$ret = dbase_add_record($this->DBFCon, $add); //or die ("Gagal Menyimpan Data");
		}
		return $ret;
	}
	
	function DBFClose(){
		return dbase_close($this->DBFCon);
	}
	
	function NumRec(){
		$ret = dbase_numrecords($this->DBFCon) or die("Gagal mengambil data");
		return $ret;
	}
	
	function GetDBFRecByName($i){
		$ret = dbase_get_record_with_names($this->DBFCon, $i) or die ("Gagal Mengambil Data");
		return $ret;
	}
}

?>