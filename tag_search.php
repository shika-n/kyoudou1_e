<?php
require_once("layout.php");

$content = <<< ___EOF___
	<div class="relative flex gap-2">
		<input type="search" id="search-field" class="flex-grow px-2 py-1 border border-gray-400 outline-none rounded-md">
		<button type="button" onclick="searchTags()" class="px-4 py-1 rounded-md bg-slate-300 hover:bg-slate-200 active:bg-slate-400">検索</button>
		<ol id="suggestion-list" class="hidden absolute top-8 p-1 bg-white/30 rounded-md border border-gray-400 shadow-xl backdrop-blur-md text-sm"></ol>
	</div>
	<div id="searchResult" class="flex flex-col gap-2">
	</div>
___EOF___;;

$scripts = <<< ___EOF___
	<script src='js/reach_bottom_action.js'></script>
	<script src="js/tag_search_complete.js"></script>
___EOF___;

echo str_replace("<!-- CONTENT -->", $content . $scripts, $html);
