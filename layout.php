<?php
require_once("util.php");

session_start();

$info_message = get_if_set("info", $_SESSION);
$_SESSION["info"] = null;
$info_message_comp = "";
if ($info_message) {
	$info_message_comp = <<< ___EOF___
		<div class="flex px-2 py-1 bg-blue-100 rounded-lg border-2 border-black">
			<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mr-1 text-blue-500">
				<path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
			</svg>
			$info_message
		</div>
	___EOF___;
}

$pages = Pages::k_base_url;

$html = <<< ___EOF___
<!DOCTYPE html>
<html lang="ja" class="scroll-smooth">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Eチームの掲示板</title>
		<script src="https://cdn.tailwindcss.com"></script>
		<!-- HEAD -->
	</head>
	<body>
		<header class="fixed md:static px-4 w-full flex gap-4 md:justify-between items-center bg-slate-300">
			<button class="md:hidden p-2 rounded-full hover:bg-slate-200 active:bg-slate-400 transition-all" onclick="toggleNavMenu()">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
					<path fill-rule="evenodd" d="M2 4.75A.75.75 0 0 1 2.75 4h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 4.75ZM2 10a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 10Zm0 5.25a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 0 1.5H2.75a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd" />
				</svg>
			</button>
			<h1 class="text-center py-4 text-xl">掲示板</h1>
			<div class="hidden md:flex flex-col">
				<a href="{$pages::k_profile->get_url()}" class="border-b-2 border-black">ユーザー名</a>
				<a href="{$pages::k_logout->get_url()}">ログアウト</a>
			</div>
		</header>
		<main>
			<div class="flex py-4 gap-8 w-full md:w-4/5 m-auto">
				<div id="navMenu" class="hidden md:flex flex-col fixed md:sticky md:gap-4 divide-y md:divide-none divide-gray-500 bg-slate-300 md:bg-transparent mt-0 md:mt-4 top-0 md:top-4 h-full md:h-min shadow-[0px_0px_32px_16px_rgba(0,0,0,0.3)] md:shadow-none z-50">
					<a class="w-40 py-2 px-4 bg-slate-300 hover:bg-slate-200 active:bg-slate-400 md:rounded-md md:hidden truncate transition-all" href="{$pages::k_profile->get_url()}">ユーザー名</a>
					<a class="w-40 py-2 px-4 bg-slate-300 hover:bg-slate-200 active:bg-slate-400 md:rounded-md md:hidden transition-all" href="{$pages::k_logout->get_url()}">ログアウト</a>
					<a class="w-40 py-2 px-4 bg-slate-300 hover:bg-slate-200 active:bg-slate-400 md:rounded-md transition-all" href="{$pages::k_index->get_url()}">TOP</a>
					<a class="w-40 py-2 px-4 bg-slate-300 hover:bg-slate-200 active:bg-slate-400 md:rounded-md transition-all" href="{$pages::k_profile->get_url()}">プロファイル</a>
					<a class="w-40 py-2 px-4 bg-slate-300 hover:bg-slate-200 active:bg-slate-400 md:rounded-md transition-all" href="{$pages::k_user_list->get_url()}">ユーザー一覧</a>
				</div>
				<div id="content" class="px-4 mt-16 md:mt-4 flex-grow flex flex-col gap-2 overflow-hidden">
					$info_message_comp
					<!-- CONTENT -->
				</div>
			</div>
			<a class="fixed flex w-12 aspect-square right-8 bottom-8 items-center justify-center bg-slate-300 hover:bg-slate-200 active:bg-slate-400 rounded-full shadow-xl shadow-gray-800/30 transition-all" href="{$pages::k_post->get_url()}">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-6">
					<path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
				</svg>
			</a>
			<a class="fixed flex w-12 aspect-square right-8 bottom-24 items-center justify-center bg-slate-300 hover:bg-slate-200 active:bg-slate-400 rounded-full shadow-xl shadow-gray-800/30 transition-all" href="#top" id="scrollToTopButton">
				<svg xmlns="http://www.w3.org/2000/svg" class="size-6" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<path d="M12 19V6M5 12l7-7 7 7"/>
				</svg>
			</a>
		</main>
		<script>
			// トップに戻るボタンの表示制御
			document.addEventListener("DOMContentLoaded", function() {
				const scrollToTopButton = document.getElementById("scrollToTopButton");

				// 初期状態で非表示に設定
    				scrollToTopButton.style.display = "none";

				// スクロールイベントを監視
				window.addEventListener("scroll", function() {
					if (window.scrollY > 200) { // スクロール量が200pxを超えた場合
						scrollToTopButton.style.display = "flex"; // ボタンを表示
					} else {
						scrollToTopButton.style.display = "none"; // ボタンを非表示
					}
				});
			});
		</script>
		</main>
		<script src="js/toggle.js"></script>
		<script src="js/reach_bottom_action.js"></script>
		<script src="js/comment.js"></script>
		<script src="js/like.js"></script>
	</body>
</html>
___EOF___;

$guest_html = <<< ___EOF___
<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Eチームの掲示板</title>
		<script src="https://cdn.tailwindcss.com"></script>
		<link rel="stylesheet" href="sinup.css">
	</head>
	<body>
		<header class="px-4 w-full justify-center items-center bg-slate-300">
			<h1 class="text-center py-4 font-bold text-3xl">掲示板</h1>
		</header>
		<main>
			<!-- CONTENT -->
		</main>
	</body>
</html>
___EOF___;
$loggedinusername = get_if_set("name", $_SESSION) ;
if ($loggedinusername) {
	$html = str_replace("ユーザー名", $loggedinusername, $html);
}
