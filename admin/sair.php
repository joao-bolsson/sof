<?php
ini_set('display_erros', true);
error_reporting(E_ALL);

session_start();
// remove all session variables
session_unset();
// destroy the session
session_destroy();

header("Location: ../");
?>
