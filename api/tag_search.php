<?php
require_once("../db_open.php");
require_once("../util.php");
require_once("../models/tags.php");

$search_value = get_if_set("search", $_GET);

$tags = search_tags($dbh, $search_value);

header("Content-Type: text/json", true, 200);
echo json_encode($tags);

