<?php
ini_set('display_errors', true);
error_reporting(E_ALL);

if (!isset($_SESSION["id_setor"]) || $_SESSION["id_setor"] != 2) {
	header("Location: ../");
}
// Get src.
$src = $_POST["src"];

// Check if file exists.
if (file_exists("../uploads/" . $src)) {
	// Delete file.
	unlink("../uploads/" . $src);
}
?>