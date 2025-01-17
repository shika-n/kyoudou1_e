<?php
$dbserver = "localhost";
$dbname = "";
$dbuser = "";
$dbpass = "";

$opt = [
	PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
	PDO::ATTR_EMULATE_PREPARES => false,
	PDO::MYSQL_ATTR_MULTI_STATEMENTS => false
];

$dbh = new PDO("mysql:host=" . $dbserver . ";dbname=" . $dbname, $dbuser, $dbpass, $opt);
