<?php
/*
Basic Backdoor Backdoor Script Finder
Copyright 2011-2012 White Fir Design (http://www.whitefirdesign)

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; only version 2 of the License is applicable.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (PHP_SAPI == "cli") 
	echo "\nBasic Backdoor Script Finder by White Fir Design (www.whitefirdesign.com)";
else
	echo "<html>\n<head>\n<title>Basic Backdoor Script Finder by White Fir Design (www.whitefirdesign.com)</title>\n<meta name=\"robots\" content=\"noindex\" />\n</head>\n<body>\n\n<p><b>Basic Backdoor Script Finder</b> by <a href=\"http://www.whitefirdesign.com\">White Fir Design</a></p>\n\n";

//checks if PHP version is 5.1.2 or above
if (phpversion() < "5.1.2") {
	echo "You are running version ".phpversion()." of PHP, you need PHP 5.1.2 or above to use this.";
	exit();
}

if (((isset($_GET['directory'])) && $_GET['directory']!="") || ( isset($argc) && $argc == 2 && (!in_array($argv[1], array('--help', '-help', '-h', '-?'))) )) {
	if (isset($_GET['directory']))
		$basepath = $_GET['directory'];
	else
		$basepath = $argv[1];
	if (is_dir($basepath)) {		
		if (isset($_GET['directory']))
			echo "<br /><b>Scanning from ".htmlspecialchars($basepath).":</b><br /><br />";
		else
			echo "\n\n\nScanning from ".htmlspecialchars($basepath).":\n\n";
		$filecount = 0;
		//Creates signatures arrays
		$hashes = array("31341dcede73378804a3625dfe02cd1c164712c8483e30da8818e1989214220af1bfa773bb26ce1f52bfeb80609bd077c7fa8c4c080b2fc8618e0846b9063c46","d0b3fb5868a28d7421f474b977ff9492d44140db29366e52980c588ef348abb48266cecdaef20754f93f668ba2b92a80689f17e58598370e27804bc0427d2c86","32c5ec613585952721a8aad4ae896605524f3a589c8da2d99af0c47173b06f2be3e66b4374b3a12d348b255d9f99322cb60b07b7eea0a9b211e1ad84227e2084","6a96577dbcb990f636608243b1cb9cf15e935f8b8332ad4d1c6bf1ff4cf39c877eb30cc6d42956afdaaea2fa3136b2e5cba32e27c1ade5a85b8ba08e32801566","a5713cc069a56742cb6f9f7c6d39229ae2672e8f5668f51f91f9bd75bba2696f20c81874da3d78519960ce5d15a90c3cf370d17fd5f3666f5f3fbff36dc94b3e");
		$strings = array("Web Shell by boff", "Web Shell by oRb", "devilzShell", "Shell by Mawar_Hitam", "N3tshell", "Storm7Shell", "Locus7Shell", "private Shell by m4rco", "w4ck1ng shell", "blackhat Shell", "FaTaLisTiCz_Fx Fx29Sh", "th3w1tch Shell", "r57shell.php", "default_action = 'FilesMan'", 'default_action = "FilesMan"', "if (isset(\$_REQUEST['asc'])) eval(stripslashes(\$_REQUEST['asc']));","Goog1e_analist","if (isset(\$_GET[\"cookie\"])) { echo 'cookie=");



		$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($basepath), RecursiveIteratorIterator::SELF_FIRST);
		foreach($objects as $object){

			if ($object->isFile() && stripos($object->getFilename(),".php") && $object->getPathname() != __FILE__) {
				$filecount++;
				$path = $object->getPathname();

				//Checks file hash against SHA-512 hash signatures
				$filehash = hash_file('sha512',$path);
				if (in_array($filehash,$hashes)) {
					if (PHP_SAPI == "cli") {
						echo "File: $path\n";
						echo "SHA-512 Hash: ";
						echo $filehash;
						echo "\n\n";}
					else {
						echo "<b>File:</b> $path<br />\n";
						echo "<b>SHA-512 Hash:</b>: ";
						echo $filehash;
						echo "<br /><br />\n\n";
					}
				}

				//Checks file against text signatures		
				$file = file_get_contents($path);
				foreach ($strings as $value) {
					$pos = strpos($file,$value);
					if(!($pos === false)) {
						if (PHP_SAPI == "cli") {
							echo "File: $path\n";
							echo "String: ";
							echo $value;
							echo "\n\n";}
						else {
							echo "<b>File:</b> $path<br />\n";
							echo "<b>String:</b>: ";
							echo $value;
							echo "<br /><br />\n\n";
						}
					}
				}
			}
		}
		
		if (PHP_SAPI == "cli") 
			echo $filecount." Files Scanned\n";
		else
			echo $filecount." Files Scanned<br /><br />";
	}
	else {
		if (PHP_SAPI == "cli")
			echo "\n\n\nDirectory ".htmlspecialchars($basepath)." does not exist.\n";
		else	
			echo "<br /><b>Directory ".htmlspecialchars($basepath)." does not exist.</b><br /><br />";
	}
}
if (PHP_SAPI == "cli") {
	echo "\n\nUsage: ".$argv[0]." <directory to scan>";
	echo "\nThe current directory is ".getcwd()."\n";
}
else {
	echo "<br /><b>Run scan on:</b><br />";
	echo "<form method=\"get\" action=\"".basename(__FILE__)."\">";
	echo "<input type=\"text\" name=\"directory\" size=\"50\" value=\"".getcwd()."\" />";
	echo "<input type=\"submit\" value=\"Scan\" />";
	echo "<br />Current Directory: ".getcwd()."<br />";
	echo "\n</form>";
	echo "\n</body>";
	echo "\n</html>";
}

?>