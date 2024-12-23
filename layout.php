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
		<header class="bg-slate-300">
			<h1 class="text-center py-8 text-xl">掲示板</h1>
		</header>
		<main class="mt-8">
			<div class="flex gap-8 w-4/5 m-auto">
				<div class="my-10 mx-9 flex flex-col">
					<a class="w-40 py-2 px-4 bg-slate-300 rounded-md my-5">TOP</a>
					<a class="w-40 py-2 px-4 bg-slate-300 rounded-md my-5">プロファイル</a>
					<a class="w-40 py-2 px-4 bg-slate-300 rounded-md my-5 ">ユーザー一覧</a>
				</div>
				<div class="p-8 border border-2 border-black rounded-xl flex-grow">
					<!-- CONTENT -->
				</div>
			</div>
		</main>
	</body>
</html>
___EOF___;
