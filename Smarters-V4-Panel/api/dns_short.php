<?php
error_reporting(0);
$db = new SQLite3('./database.db');
$res = $db->query('SELECT * FROM dns'); 
$rows = array();
while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
	$rows[] = $row['url'];
}
$dns = rtrim(implode(',', $rows), ',');

echo "{\"status\":true,\"su\":\"$dns\",\"sc\":\"\",\"ndd\":\"\"}";
?>