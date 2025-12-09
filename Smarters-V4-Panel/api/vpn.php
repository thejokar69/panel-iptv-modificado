<?php


// // header('Location: ' . $base_file
$rootPath = realpath('../vpn');
$zip = new ZipArchive();
$zip->open('ovpn.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootPath), RecursiveIteratorIterator::LEAVES_ONLY);

foreach ($files as $name => $file) {
	if (!$file->isDir()) {
		$filePath = $file->getRealPath();
		$relativePath = substr($filePath, strlen($rootPath) + 1);

		if ($file->getFilename() != '.htaccess') {
			$zip->addFile($filePath, $relativePath);
		}
	}
}

$zip->close();
$filename = 'ovpn.zip';

if (file_exists($filename)) {
	header('Content-Type: application/zip');
	header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
	header('Content-Length: ' . filesize($filename));
	flush();
	readfile($filename);
	unlink($filename);
}
else {
	exit();
}

?>