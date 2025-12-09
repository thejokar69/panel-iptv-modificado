<?php
error_reporting(0);
$db = new SQLite3('./apimain.db');
$note = $db->query('SELECT * FROM announcements');

while ($announcements = $announcements->fetchArray(SQLITE3_ASSOC)) {
	$data[] = ['title' => $announcements['title'], 'message' => $announcements['message'], 'created_on' => $announcements['created_on']];
}

$jdata = json_encode($data);
echo '{"status":true,' . "\r\n\t\t" . '"response":' . "\r\n\t\t" . $jdata . "\r\n\t\t" . '}';

?>