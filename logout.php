<?php
session_start();

$_SESSION["user_id"] = null;
header("Location: login_page.php", true, 303);
return;
