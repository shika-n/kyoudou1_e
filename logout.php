<?php
require_once("util.php");

session_start();
session_destroy();
redirect_to(Pages::k_login);
