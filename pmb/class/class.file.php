<?php
/** 
	Description: This class handles all actions that could be made on a file (upload, download, write, read...)
	and all its characteristics (size, type, extension, owner...)
	@author Antoine BOUËT <antoinebouet@free.fr>
	@copyright GPL 25/03/03
	@package file
	@version 1.0
**/
class file{
	/**
		used as the dentifier.
		@access public
		@var string
	*/
	var $File_Name;
	/**
		Directory where to find/put the file. Will be appended to File_Name
		@access public
		@var string
	*/
	var $File_Path;
	/**
		File size in Bytes
		@access public
		@var numeric
	*/	
	var $File_Size;
	/**
		@access public
		@var string
	*/	
	var $File_Owner;
	/**
		@access public
		@var string
	*/	
	var $File_Grp;
	/**
		@access public
		@var numeric
	*/	
	var $File_Perm = 0777;
	/**
		@access public
		@var string
	*/	
	var $Extension;
	/**
		Mime type ( for this version are based on Apache 1.3.27 )
		@access public
		@var string
	*/	
	var $File_Type;
	/**
		@access public
		@var numeric
	*/	
	var $Folder_Perm 	= 0777;
	/**
		array of extensions allowed for upload
		@access public
		@var array
	*/		
	var $Allowed_Files = array(".doc", ".xls",".txt",".pdf",".gif",".bmp",".jpg",".jpeg",".zip",".rar",".ppt",".mp3");
	/**
		array of extensions not allowed for upload.
		@access public
		@var array
	*/	
	var $Disallowed_Files = array(".exe",".bat",".msi",".sh","");
	/**
		@access private
		@var string
	*/	
	var $_Tmp_Name;	
	/**
		@access private
		@var array
	*/	
	var $_Mime_Array;
	/**
		@access private
		@var string
	*/	
	var $_ErrCode;
	######################################################
	# Constructor
	######################################################
	/**
		
		@access Public
		@param string [$_file] File Name
	*/		
	function file( $_file ){
		$this->File_Name = $_file;
		$this->_Get_Extension();
		$this->Get_File_Type();
	}
	######################################################
	# ACTION METHODS
	######################################################
	/**
		Opens, reads the file and return the content
		@access Public
		@return string File Content
	*/
	function read(){
		$_filename = $this->File_Path.$this->File_Name;
		$_file = fread($fp = fopen($_filename, 'r'), filesize($_filename));
		fclose($fp);
		return $_file;
	}
	/**
		Delete the file
		inline {@internal checks the OS php is running on, and execute appropriate command}}
		@access Public
		@return string File Content
	*/
	function delete(){
		//if Windows
		if (substr(php_uname(), 0, 7) == "Windows") {
			$_filename  = str_replace( '/', '\\', $this->File_Path.$this->File_Name);
			system( 'del /F "'.$_filename.'"', $_result );
			if( $_result == 0 ){
				return true;
			} else {
				$this->_ErrCode = 'FILE_DEL'.$_result;
				return false;
			}
		//else unix assumed
		} else {
			chmod( $this->File_Path.$this->File_Name, 0775 );
			return unlink( $this->File_Path.$this->File_Name );
		}
	}
	/**
		Create a directory.
		@access Public
		@param string [$_path] path to locate the directory
		@param string [$_DirName] name of the directory to create		
		@return boolean
	*/	
	function Make_Dir($_path, $_DirName){
		if(!file_exists($_path."/".$_DirName)){
			$_oldumask = @umask($this->umask); 
			$_action = @mkdir($_path."/".$_DirName, $this->Folder_Perm);
			@umask($_oldumask);
			if($_action == true){
				return true;
			} else {
				$this->_ErrCode = 'DIR03';
				return false;
			}
		} else{
			$this->_ErrCode = 'DIR04';
			return false;
		}
	}
	/**
		write data to a file
		@access Public
		@param string [$_content] data to write into the file
		@return boolean
	*/	
	function write( $_content ){
		$_filename = $this->File_Path.$this->File_Name;
		if(!empty($_filename) && !empty($_content)){
			$_fp = fopen($_filename,"w");
			$_action = fwrite($_fp,$_content);
			fclose($_fp);
			@chmod( $_filename, $this->File_Perm);
			@chown( $_filename, $this->File_Owner);
			if($_action != -1){
				$this->Get_Size();
				return true;
			} else {
				$this->_ErrCode = 'DIR01';
				return false;
			}
		} else {
			$this->_ErrCode = 'DIR02';
			return false;
		}
	}
	/**
		download a file
		@access Public
		@param string [$_content] data to write into the file
		@return boolean
	*/	
	function Download(){
		header( "Content-type: ".$this->File_Type );
		header("Content-Length: ".FileSize($this->File_Path.$this->$File_Name));
		//header( "Content-Length: ".$this->File_Size );
		header( "Content-Disposition: filename=".$this->File_Path.$this->File_Name );
		header( "Content-Description: Download Data" );
		echo $this->Content;
	}
	/**
		upload a file
		@access Public
		@param string [$_handler] html file field name
		@param string [$_rename] new name for the uploaded file. Keep same name if empty (optional)
		@param boolean [$_OverWrite] Overwrite existing file (Yes/No)
		@return boolean
	*/			
	function Upload($_handler, $_rename='', $_OverWrite=false){
		$this->_ErrCode = 0;
		$this->File_Name = $_FILES[$_handler]['name'];
		$this->File_Size = $_FILES[$_handler]['size'];
		$this->File_Type = $_FILES[$_handler]['type'];
		$this->_Tmp_Name = $_FILES[$_handler]['tmp_name'];
		$this->_Get_Extension();
		// Check if extension is allowed
		if ( !$this->type_check() ){
			$this->_ErrCode = 1;
			return false;
		}
		//set the name for the uploaded file
		if($_rename){
			$_filename = $_rename;
		}else{
			$_filename = $this->File_Name;
		}
		// if file exists and no overwrite, then error
		if ( file_exists( $this->File_Path.$_filename ) && !$_OverWrite ){
			$this->_ErrCode = 4;
			return false;
		}
		//copy the uploaded file to specified location
		$_status = move_uploaded_file ( $this->_Tmp_Name, $this->File_Path.$_filename);
		if( !$_status ){
			$this->_ErrCode = 6;
			return false;
		}
		//if rename = true, then update property
		if($_rename){ $this->File_Name = $_rename ;}
		return $_status;
	}
	/**
		File type check
		@access Public
		@return boolean
	*/
	function Type_Check(){
		# check against disallowed files
		foreach ( $this->Disallowed_Files as $_idx=>$_val ) {
		    if ( $_val == $this->Extension ) {return false;}
		}
		# check against allowed files
		# if the allowed list is populated then the file must be in the list.
		if ( empty( $this->Allowed_Files ) ) { return true; }
		foreach ( $this->Allowed_Files as $_idx=>$_val ) {
		    if ( $_val == $this->Extension ) { return true; }
		}
		return false;
	}
	######################################################
	# ACCESSOR - SET PROPERTIES METHODS
	######################################################
	/**
		Set file owner
		@access Public
		@param string [$_owner] file owner
		@return boolean
	*/	
	function Set_Owner($_owner){
		$_filename = $this->File_Path.$this->File_Name;
		if(chown($_filename, $_owner)){
			$this->File_Owner = $_owner;
		}else{
			$this->File_Owner = false;
		}
	}
	/**
		Set file group
		@access Public
		@param string [$_grp] file group
		@return boolean
	*/	
	function Set_Grp($_grp){
		$_filename = $this->File_Path.$this->File_Name;
		if(chgrp($_filename, $_grp)){
			$this->File_Grp = $_grp;
		}else{
			$this->File_Grp = false;		}
	}
	/**
		set the directory in which the file is
		@access Public		
		@param String [$_dir] Name of directory we upload to
	*/
	function Set_Dir( $_dir ){
		$this->File_Path = $_dir;
    }
	/**
		Add an addtional extension to the disallowed file array
		@access Public		
		@param mixed [$_Extension] string or array of extensions to be added
	*/	
	function Set_Disallowed_Files($_Extension){
		if( is_array($_Extension) ){
			$this->Disallowed_Files .= $_Extension;
		}else{
			$this->Disallowed_Files[] = $_Extension;
		}
		array_unique ( $this->Disallowed_Files );
	}
	/**
		Add an addtional extension to the allowed file array
		@access Public		
		@param mixed [$_Extension] string or array of extensions to be added
	*/
	function Set_Allowed_Files($_Extension){
		if( is_array( $_Extension)){
			$this->Allowed_Files .= $_Extension;
		}else{
			$this->Allowed_Files[] = $_Extension;
		}
		array_unique ( $this->Allowed_Files );
	}
	/**
		reset the array to blank
		@access Public		
	*/	
	function Reset_Disallowed_Files(){
		unset($this->Disallowed_Files);
	}
	/**
		reset the array to blank
		@access Public		
	*/	
	function Reset_Allowed_Files(){
		unset($this->Allowed_Files);
	}		
	######################################################
	# GET PROPERTIES METHODS
	######################################################
	/**
		Get the mime type of a file
		@access Public		
	*/		
	function Get_File_Type(){
		$_mimetypes = array(
		 ".dwoprn" => "application/dwoprn",
         ".ez" => "application/andrew-inset",
         ".hqx" => "application/mac-binhex40",
         ".cpt" => "application/mac-compactpro",
         ".doc" => "application/msword",
         ".bin" => "application/octet-stream",
         ".dms" => "application/octet-stream",
         ".lha" => "application/octet-stream",
         ".lzh" => "application/octet-stream",
         ".exe" => "application/octet-stream",
         ".class" => "application/octet-stream",
         ".so" => "application/octet-stream",
         ".dll" => "application/octet-stream",
         ".oda" => "application/oda",
         ".pdf" => "application/pdf",
         ".ai" => "application/postscript",
         ".eps" => "application/postscript",
         ".ps" => "application/postscript",
         ".smi" => "application/smil",
         ".smil" => "application/smil",
         ".wbxml" => "application/vnd.wap.wbxml",
         ".wmlc" => "application/vnd.wap.wmlc",
         ".wmlsc" => "application/vnd.wap.wmlscriptc",
         ".bcpio" => "application/x-bcpio",
         ".vcd" => "application/x-cdlink",
         ".pgn" => "application/x-chess-pgn",
         ".cpio" => "application/x-cpio",
         ".csh" => "application/x-csh",
         ".dcr" => "application/x-director",
         ".dir" => "application/x-director",
         ".dxr" => "application/x-director",
         ".dvi" => "application/x-dvi",
         ".spl" => "application/x-futuresplash",
         ".gtar" => "application/x-gtar",
         ".hdf" => "application/x-hdf",
         ".js" => "application/x-javascript",
         ".skp" => "application/x-koan",
         ".skd" => "application/x-koan",
         ".skt" => "application/x-koan",
         ".skm" => "application/x-koan",
         ".latex" => "application/x-latex",
         ".nc" => "application/x-netcdf",
         ".cdf" => "application/x-netcdf",
         ".sh" => "application/x-sh",
         ".shar" => "application/x-shar",
         ".swf" => "application/x-shockwave-flash",
         ".sit" => "application/x-stuffit",
         ".sv4cpio" => "application/x-sv4cpio",
         ".sv4crc" => "application/x-sv4crc",
         ".tar" => "application/x-tar",
         ".tcl" => "application/x-tcl",
         ".tex" => "application/x-tex",
         ".texinfo" => "application/x-texinfo",
         ".texi" => "application/x-texinfo",
         ".t" => "application/x-troff",
         ".tr" => "application/x-troff",
         ".roff" => "application/x-troff",
         ".man" => "application/x-troff-man",
         ".me" => "application/x-troff-me",
         ".ms" => "application/x-troff-ms",
         ".ustar" => "application/x-ustar",
         ".src" => "application/x-wais-source",
         ".xhtml" => "application/xhtml+xml",
         ".xht" => "application/xhtml+xml",
         ".zip" => "application/zip",
         ".au" => "audio/basic",
         ".snd" => "audio/basic",
         ".mid" => "audio/midi",
         ".midi" => "audio/midi",
         ".kar" => "audio/midi",
         ".mpga" => "audio/mpeg",
         ".mp2" => "audio/mpeg",
         ".mp3" => "audio/mpeg",
         ".aif" => "audio/x-aiff",
         ".aiff" => "audio/x-aiff",
         ".aifc" => "audio/x-aiff",
         ".m3u" => "audio/x-mpegurl",
         ".ram" => "audio/x-pn-realaudio",
         ".rm" => "audio/x-pn-realaudio",
         ".rpm" => "audio/x-pn-realaudio-plugin",
         ".ra" => "audio/x-realaudio",
         ".wav" => "audio/x-wav",
         ".pdb" => "chemical/x-pdb",
         ".xyz" => "chemical/x-xyz",
         ".bmp" => "image/bmp",
         ".gif" => "image/gif",
         ".ief" => "image/ief",
         ".jpeg" => "image/jpeg",
         ".jpg" => "image/jpeg",
         ".jpe" => "image/jpeg",
         ".png" => "image/png",
         ".tiff" => "image/tiff",
         ".tif" => "image/tif",
         ".djvu" => "image/vnd.djvu",
         ".djv" => "image/vnd.djvu",
         ".wbmp" => "image/vnd.wap.wbmp",
         ".ras" => "image/x-cmu-raster",
         ".pnm" => "image/x-portable-anymap",
         ".pbm" => "image/x-portable-bitmap",
         ".pgm" => "image/x-portable-graymap",
         ".ppm" => "image/x-portable-pixmap",
         ".rgb" => "image/x-rgb",
         ".xbm" => "image/x-xbitmap",
         ".xpm" => "image/x-xpixmap",
         ".xwd" => "image/x-windowdump",
         ".igs" => "model/iges",
         ".iges" => "model/iges",
         ".msh" => "model/mesh",
         ".mesh" => "model/mesh",
         ".silo" => "model/mesh",
         ".wrl" => "model/vrml",
         ".vrml" => "model/vrml",
         ".css" => "text/css",
         ".html" => "text/html",
         ".htm" => "text/html",
         ".asc" => "text/plain",
         ".txt" => "text/plain",
         ".rtx" => "text/richtext",
         ".rtf" => "text/rtf",
         ".sgml" => "text/sgml",
         ".sgm" => "text/sgml",
         ".tsv" => "text/tab-seperated-values",
         ".wml" => "text/vnd.wap.wml",
         ".wmls" => "text/vnd.wap.wmlscript",
         ".etx" => "text/x-setext",
         ".xml" => "text/xml",
         ".xsl" => "text/xml",
         ".mpeg" => "video/mpeg",
         ".mpg" => "video/mpeg",
         ".mpe" => "video/mpeg",
         ".qt" => "video/quicktime",
         ".mov" => "video/quicktime",
         ".mxu" => "video/vnd.mpegurl",
         ".avi" => "video/x-msvideo",
         ".movie" => "video/x-sgi-movie",
         ".ice" => "x-conference-xcooltalk"
		);
		// return mime type for extension
		if (isset( $_mimetypes[$this->Extension] ) ) {
			$this->File_Type = $_mimetypes[$this->Extension];
		// if the extension wasn't found return octet-stream         
		} else {
			$this->File_Type = 'application/octet-stream';
		}	
	}
	/**
		Get the owner of a file
		@access Public		
	*/	
	function Get_Owner(){
		$_filename = $this->File_Path.$this->File_Name;
		$this->File_Owner = fileowner( $_filename );
	}
	/**
		Get the group of the file owner
		@access Public		
	*/	
	function Get_Grp(){
		$_filename = $this->File_Path.$this->File_Name;
		$this->File_Grp = filegroup( $_filename);
	}
	/**
		Get the file size
		@access Public		
	*/	
	function Get_Size(){
		if( !$this->File_Size ){
			$this->File_Size = @filesize( $this->File_Path.$this->File_Name );
		}
	}
	/**
		Return everything after the . of the file name (including the .)
		@access Public		
	*/	
    function _Get_Extension(){
		$this->Extension = strrchr( $this->File_Name, "." );
    }	
	/**
		Return the error message of an action made on a file (upload, delete, write...)
		@return string error message		
	*/	
	function Status_Message(){
		switch( $this->_ErrCode ){
			case 0:
					$_msg = "The file <b>".$this->File_Name."</b> was succesfully uploaded.\n";
					break;
			case 1:
					$_msg = "<b>".$this->File_Name."</b> was not uploaded. <b>".$this->Extension."</b> Extension is not accepted!\n";
					break;
			case 2:
					$_msg = "The file <b>$this->cls_filename</b> is too big or does not exists!";
					break;
			case 3:
			        $_msg = "Remote file could not be deleted!\n";
					break;
			case 4:
					$_msg = "The file <b>".$this->File_Name."</b> exists and overwrite is not set in class!\n";
					break;
			case 5:
					$_msg = "Copy successful, but renaming the file failed!\n";
					break;
			case 6:
					$_msg = "Unable to copy file :(\n";
					break;
			case 7:
			        $_msg = "You don't have permission to use this script!\n";
                    break;
			case 8:
			        $_msg = ""; // if user does not select a file
					break;
			case "DIR01":
					$_msg = "Can't write File [no fwrite]";
					break;
			case "DIR02":
					$_msg = "Can't write File [no filename | no content]";
					break;
			case "DIR02":
					$_msg = "Can't create Folder [mkdir failed]";
					break;
			case "DIR04":
					$_msg = "Folder exists";
					break;
			case "FILE_DEL1":
					$_msg = "File deletion impossible";
					break;
			default:
					$_msg = "Unknown error!";
		}
		return $_msg ;
	}
}
?>