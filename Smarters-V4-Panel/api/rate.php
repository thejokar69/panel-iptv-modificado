<?php


error_reporting(0);
$db = new SQLite3('./.db.db');
$rate = $db->querySingle('SELECT url FROM rate WHERE id=1');

if ($rate != '') {
	header('Location: ' . $rate);
	exit();
}
else {
	header('Location: https://google.com');
	exit();
}

?>