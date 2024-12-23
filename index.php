<?php
require_once("layout.php");

$content = <<< ___EOF___
こんにちは！
___EOF___;

$html = str_replace("<!-- CONTENT -->", $content, $html);
echo $html;
