<?php
$db = new SQLite3('./api/database.db');
function initializeDatabase($db) {
	$tables = [
		"users" => "CREATE TABLE IF NOT EXISTS users(id INTEGER PRIMARY KEY,username TEXT ,password TEXT)",
		"dns" => "CREATE TABLE IF NOT EXISTS dns(id INTEGER PRIMARY KEY  AUTOINCREMENT  NOT NULL, title TEXT, url TEXT)",
		"maintenance" => "CREATE TABLE IF NOT EXISTS maintenance (id INTEGER PRIMARY KEY, title TEXT, body TEXT, mode TEXT)",
		"advertisement" => "CREATE TABLE IF NOT EXISTS advertisement (id INTEGER PRIMARY KEY, status TEXT, viewable_rate TEXT, message TEXT)",
		"devices" => "CREATE TABLE IF NOT EXISTS devices (id INTEGER PRIMARY KEY, deviceid TEXT, deviceusername TEXT, added_on TEXT)",
		"reports" => "CREATE TABLE IF NOT EXISTS reports (id INTEGER PRIMARY KEY, username TEXT, macaddress TEXT, section TEXT, section_category TEXT, report_title TEXT, report_sub_title TEXT, report_cases TEXT, report_custom_message TEXT, stream_name TEXT, stream_id INTEGER)",
		"feedback" => "CREATE TABLE IF NOT EXISTS feedback (id INTEGER PRIMARY KEY, username TEXT, macaddress TEXT, feedback_content TEXT)",
		"announcements" => "CREATE TABLE IF NOT EXISTS announcements (id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT NOT NULL, message TEXT NOT NULL, created_on TEXT NOT NULL)",
		"announcement_views" => "CREATE TABLE IF NOT EXISTS announcement_views (announcement_id INTEGER NOT NULL, deviceid TEXT NOT NULL, FOREIGN KEY (announcement_id) REFERENCES announcements(id))",
		"vpn" => "CREATE TABLE IF NOT EXISTS vpn(id INTEGER PRIMARY KEY	AUTOINCREMENT  NOT NULL, vpn_country TEXT ,vpn_file_name TEXT, username TEXT, password TEXT, embed TEXT)",
		"maint" => "INSERT OR REPLACE INTO maintenance (title, body, mode) VALUES('','','no')"
	];

	foreach ($tables as $tableName => $createStmt) {
		$db->exec($createStmt);
	}
}


//sanitize strings
function sanitize($data) {
	$data = trim($data);
	$data = htmlspecialchars($data, ENT_QUOTES );
	$data = SQLite3::escapeString($data);
	return $data;
}//"vpn" => "CREATE TABLE IF NOT EXISTS vpn(id INTEGER PRIMARY KEY	AUTOINCREMENT  NOT NULL, vpn_country TEXT ,vpn_config TEXT,vpn_file_name TEXT)"