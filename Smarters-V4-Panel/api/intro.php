<?php


error_reporting(0);
$db = new SQLite3('./.db.db');
$intro = $db->querySingle('SELECT url FROM intro WHERE id=1');

if ($intro != '') {
	header('Location: ' . $intro);
	exit();
}
else {
	header('Location: ../img/blank.mp4');
	exit();
}

?>