<?php
$html = <<< ___EOF___
<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Eチームの掲示板</title>
		<script src="https://cdn.tailwindcss.com"></script>
	</head>
	<body>
		<header class="px-4 flex justify-between items-center bg-slate-300">
			<h1 class="text-center py-8 text-xl">掲示板</h1>
			<div class="flex flex-col">
				<div class="border-b-2 border-black">ユーザー名</div>
				<div>ログアウト</div>
			</div>
		</header>
		<main class="mt-8">
			<div class="flex gap-8 w-4/5 m-auto">
				<div class="my-10 flex flex-col">
					<a class="w-40 py-2 px-4 bg-slate-300 rounded-md mb-5">TOP</a>
					<a class="w-40 py-2 px-4 bg-slate-300 rounded-md mb-5">プロファイル</a>
					<a class="w-40 py-2 px-4 bg-slate-300 rounded-md mb-5 ">ユーザー一覧</a>
				</div>
				<div class="p-8 border border-2 border-black rounded-xl flex-grow">
					<!-- CONTENT -->
				</div>
			</div>
		</main>
	</body>
</html>
___EOF___;
