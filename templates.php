<?php

function post_panel($row, $target_timezone) {
	$id = htmlspecialchars($row["user_id"], ENT_QUOTES, "UTF-8");
	$row['icon'] = htmlspecialchars($row['icon'], ENT_QUOTES, 'UTF-8');
	$row['nickname'] = htmlspecialchars($row['nickname'], ENT_QUOTES, 'UTF-8');
	$row['created_at'] = htmlspecialchars($row['created_at'], ENT_QUOTES, 'UTF-8');
	$row['title'] = htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8');
	$row['content'] = htmlspecialchars($row['content'], ENT_QUOTES, 'UTF-8');
	$created_at = (new DateTime($row["created_at"]))->setTimezone($target_timezone)->format("Y-m-d H:i:s");
	
	$actions = "";
	if ($row["user_id"] === $_SESSION["user_id"]) {
		$actions = <<< ___EOF___
				<div class="flex items-center gap-4">
					<a href="edit_post.php/{$row['post_id']}" class="p-2 rounded-full hover:bg-slate-50 active:bg-slate-200"><img src="images/edit.png" class="w-5 aspect-square"></a>
					<a href="delete_post.php/{$row['post_id']}" class="p-2 rounded-full hover:bg-slate-50 active:bg-slate-200"><img src="images/trash.png" class="w-5 aspect-square"></a>
				</div>
		___EOF___;
	}
	
	return <<< ___EOF___
		<div class="border-2 rounded-lg border-black p-2 bg-slate-100">
			<div class="flex justify-between">
				<div class="flex flex-row flex-wrap items-center">
					<div class="rounded-full">
						<img src="profile_pictures/{$row['icon']}" class="w-8 rounded-full aspect-square object-cover object-center">
					</div>
					<div class="flex flex-col flex-wrap ml-5 text-sm p-2 divide-y divide-black">
						<div class="font-semibold">
							<a href="profile.php?id=$id">{$row['nickname']}</a>
						</div>
						<div>
							<p>{$created_at}</p>
						</div>
					</div>
				</div>
				$actions
			</div>
			<div class="font-semibold">
				<p>{$row['title']}</p>
			</div>
			<div class="leading-4">
				<p class="text-wrap break-all hover:line-clamp-none text-ellipsis overflow-hidden line-clamp-3">{$row['content']}</p>
			</div>
		</div>
	___EOF___;
}

