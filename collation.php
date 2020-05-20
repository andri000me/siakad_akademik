<?php
// original script (v1.0) by/from: http://www.phpwact.org/php/i18n/utf-8/mysql
// improved/modified (v1.03) by Bogdan http://bogdan.org.ua/
// this script will output all queries needed to change all fields/tables to a different collation
// it is HIGHLY suggested you take a MySQL dump/backup prior to running any of the generated queries
// this code is provided AS IS and without any warranty
//die("Make a backup of your MySQL database, then remove this line from the code!");
set_time_limit(0);
// collation you want to change to:
$convert_to   = 'latin1_general_ci';
// character set of new collation:
$character_set= 'latin1';
// DB login information - *modify before use*
$username = 'root';
$password = '';
$database = 'binainsani';
$host	 = 'localhost';
//-- usually, there is nothing to modify below this line --//
// show TABLE alteration queries?
$show_alter_table = true;
// show FIELD alteration queries?
$show_alter_field = true;
mysql_connect($host, $username, $password);
mysql_select_db($database);
$rs_tables = mysql_query(" SHOW TABLES ") or die(mysql_error());
print '<pre>';
while ($row_tables = mysql_fetch_row($rs_tables)) {
	$table = mysql_real_escape_string($row_tables[0]);
	// Alter table collation
	// ALTER TABLE `account` DEFAULT CHARACTER SET utf8
	if ($show_alter_table)
		echo("ALTER TABLE `$table` DEFAULT CHARACTER SET $character_set;\r\n");
	$rs = mysql_query(" SHOW FULL FIELDS FROM `$table` ") or die(mysql_error());
	while ( $row = mysql_fetch_assoc($rs) ) {
		if ( $row['Collation'] == '' || $row['Collation'] == $convert_to )
			continue;
		// Is the field allowed to be null?
		if ( $row['Null'] == 'YES' )
			$nullable = ' NULL ';
		else
			$nullable = ' NOT NULL';
		// Does the field default to null, a string, or nothing?
		if ( $row['Default'] === NULL )
			$default = " DEFAULT NULL";
		elseif ( $row['Default'] != '' )
			$default = " DEFAULT '".mysql_real_escape_string($row['Default'])."'";
		else
			$default = '';
		// Alter field collation:
		// ALTER TABLE `tab` CHANGE `fiel` `fiel` CHAR( 5 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL
		if ($show_alter_field) {
			$field = mysql_real_escape_string($row['Field']);
			echo "ALTER TABLE `$table` CHANGE `$field` `$field` $row[Type] CHARACTER SET $character_set COLLATE $convert_to $nullable $default; \r\n";
		}
	}
}
?>