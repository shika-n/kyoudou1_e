<?php
require_once("../db_open.php");
require_once("../models/posts.php");

header("Content-Type: text/json", true, 200);
echo json_encode(get_posts($dbh, 1));
