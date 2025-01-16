<?php
require_once("util.php");
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
			<button class="md:hidden p-2 rounded-full hover:bg-slate-200" onclick="toggleNavMenu()">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
					<path fill-rule="evenodd" d="M2 4.75A.75.75 0 0 1 2.75 4h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 4.75ZM2 10a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 10Zm0 5.25a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 0 1.5H2.75a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd" />
				</svg>
			</button>
			<h1 class="text-center py-4 text-xl">掲示板</h1>
			<div class="hidden md:flex flex-col">
				<div class="border-b-2 border-black">ユーザー名</div>
				<a href="logout.php">ログアウト</a>
			</div>
		</header>
		<main>
			<div class="flex py-4 gap-8 w-full md:w-4/5 m-auto">
				<div id="navMenu" class="hidden md:flex flex-col fixed md:sticky md:gap-4 divide-y md:divide-none divide-gray-500 bg-slate-300 md:bg-transparent mt-0 md:mt-4 top-0 md:top-4 h-full md:h-min shadow-[0px_0px_32px_16px_rgba(0,0,0,0.3)] md:shadow-none">
					<a class="w-40 py-2 px-4 bg-slate-300 hover:bg-slate-200 md:rounded-md md:hidden" href="#">ユーザー名</a>
					<a class="w-40 py-2 px-4 bg-slate-300 hover:bg-slate-200 md:rounded-md md:hidden" href="logout.php">ログアウト</a>
					<a class="w-40 py-2 px-4 bg-slate-300 hover:bg-slate-200 md:rounded-md" href=".">TOP</a>
					<a class="w-40 py-2 px-4 bg-slate-300 hover:bg-slate-200 md:rounded-md" href="#">プロファイル</a>
					<a class="w-40 py-2 px-4 bg-slate-300 hover:bg-slate-200 md:rounded-md" href="user_list.php">ユーザー一覧</a>
				</div>
				<div class="px-4 mt-16 md:mt-4 flex-grow flex flex-col gap-2">
					<!-- CONTENT -->
				</div>
			</div>
			<a class="fixed flex w-12 aspect-square right-8 bottom-8 items-center justify-center bg-slate-300 rounded-full shadow-xl shadow-gray-800/30" href="post.php">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-6">
					<path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
				</svg>
			</a>
		</main>
		<script src="js/toggle.js"></script>
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
session_start();
$loggedinusername = get_if_set("name", $_SESSION) ;
if ($loggedinusername) {
	$html = str_replace("ユーザー名", $loggedinusername, $html);
}