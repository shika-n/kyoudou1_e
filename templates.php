<?php
require_once("util.php");

function sort_order_select() {
	$sort_order = get_if_set("sort_order", $_SESSION, "newest");

	$selected_newest = $sort_order === "newest" ? "selected" : "";
	$selected_likes = $sort_order === "likes" ? "selected" : "";

	return <<< ___EOF___
		<div class="text-right text-xs">
			<label for="post-sort-order">並び順</label>
			<select id="post-sort-order" onchange="changeSortOrder(this)" class="w-fit px-1 border border-gray-400 rounded-md">
				<option value="newest" $selected_newest>最新順</option>
				<option value="likes" $selected_likes>いいねが多い</option>
			</select>
		</div>
	___EOF___;
}

function like_svg($row) {
	if ($row["liked_by_user"]) {
		return <<< ___EOF___
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-4 fill-rose-500 stroke-[1.5px] stroke-black">
				<path d="M7.493 18.5c-.425 0-.82-.236-.975-.632A7.48 7.48 0 0 1 6 15.125c0-1.75.599-3.358 1.602-4.634.151-.192.373-.309.6-.397.473-.183.89-.514 1.212-.924a9.042 9.042 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75A.75.75 0 0 1 15 2a2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 0 1-2.649 7.521c-.388.482-.987.729-1.605.729H14.23c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 0 0-1.423-.23h-.777ZM2.331 10.727a11.969 11.969 0 0 0-.831 4.398 12 12 0 0 0 .52 3.507C2.28 19.482 3.105 20 3.994 20H4.9c.445 0 .72-.498.523-.898a8.963 8.963 0 0 1-.924-3.977c0-1.708.476-3.305 1.302-4.666.245-.403-.028-.959-.5-.959H4.25c-.832 0-1.612.453-1.918 1.227Z" />
			</svg>
		___EOF___;
	} else {
		return <<< ___EOF___
			<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
				<path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282m0 0h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 0 1-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 0 0-1.423-.23H5.904m10.598-9.75H14.25M5.904 18.5c.083.205.173.405.27.602.197.4-.078.898-.523.898h-.908c-.889 0-1.713-.518-1.972-1.368a12 12 0 0 1-.521-3.507c0-1.553.295-3.036.831-4.398C3.387 9.953 4.167 9.5 5 9.5h1.053c.472 0 .745.556.5.96a8.958 8.958 0 0 0-1.302 4.665c0 1.194.232 2.333.654 3.375Z" />
			</svg>
		___EOF___;
	}
}

function post_actions_comp($post) {
	return <<< ___EOF___
			<div class="flex items-center">
				<a href="edit_post.php?post_id={$post['post_id']}" class="p-2 rounded-full hover:bg-slate-50 active:bg-slate-200"><img src="images/edit.png" class="w-3 aspect-square"></a>
				<a href="delete_post.php?post_id={$post['post_id']}" class="p-2 rounded-full hover:bg-slate-50 active:bg-slate-200"><img src="images/trash.png" class="w-3 aspect-square"></a>
			</div>
	___EOF___;
}

function post_owner_comp($id, $icon, $nickname, $created_at) {
	return <<< ___EOF___
		<div class="flex flex-row flex-wrap items-center">
			<div class="rounded-full">
				<img src="profile_pictures/{$icon}" class="w-8 rounded-full aspect-square object-cover object-center">
			</div>
			<div class="flex flex-col flex-wrap ml-5 text-sm px-2 divide-y divide-black">
				<div class="font-semibold">
					<a href="profile.php?id=$id">{$nickname}</a>
				</div>
				<div>
					<p>{$created_at}</p>
				</div>
			</div>
		</div>
	___EOF___;
}

function comment_panel($comment, $target_timezone, $hidden = false) {
	$id = htmlspecialchars($comment["user_id"], ENT_QUOTES, "UTF-8");
	$comment['icon'] = htmlspecialchars($comment['icon'], ENT_QUOTES, 'UTF-8');
	$comment['nickname'] = htmlspecialchars($comment['nickname'], ENT_QUOTES, 'UTF-8');
	$comment['created_at'] = htmlspecialchars($comment['created_at'], ENT_QUOTES, 'UTF-8');
	$comment['title'] = htmlspecialchars($comment['title'], ENT_QUOTES, 'UTF-8');
	$comment['content'] = htmlspecialchars($comment['content'], ENT_QUOTES, 'UTF-8');
	$created_at = (new DateTime($comment["created_at"]))->setTimezone($target_timezone)->format("Y-m-d H:i:s");
	
	$actions = "";
	if ($comment["user_id"] === $_SESSION["user_id"]) {
		$actions = post_actions_comp($comment);
	}
	
	$like_icon = like_svg($comment);

	$hidden_class = "";
	if ($hidden) {
		$hidden_class = "hidden";
	}

	return <<< ___EOF___
		<div class="p-2 border-l-2 border-slate-500 bg-slate-200 $hidden_class">
			<div class="flex justify-between">
				<div class="flex flex-row flex-wrap items-center">
					<div class="rounded-full">
						<img src="profile_pictures/{$comment['icon']}" class="w-6 rounded-full aspect-square object-cover object-center">
					</div>
					<div class="flex flex-col flex-wrap ml-3 text-xs px-2 divide-y divide-black">
						<div class="font-semibold">
							<a href="profile.php?id=$id">{$comment['nickname']}</a>
						</div>
						<div>
							<p>{$created_at}</p>
						</div>
					</div>
				</div>
				$actions
			</div>
			<div class="leading-4">
				<p class="text-wrap text-xs break-all hover:line-clamp-none text-ellipsis overflow-hidden line-clamp-3">{$comment['content']}</p>
			</div>
			<div class="mt-2 flex gap-2 items-center">
				<div class="flex gap-1 items-center text-xs">
					<button onclick="like({$comment['post_id']})" id="like-button-{$comment['post_id']}" class="p-1 bg-slate-300 hover:bg-slate-200 active:bg-slate-400 rounded-full ring-0 hover:ring-2 hover:ring-rose-400 transition-all">
						$like_icon
					</button>
					<span id="like-count-{$comment['post_id']}">{$comment["like_count"]}</span>
				</div>
			</div>
		</div>
	___EOF___;
}

function post_panel($row, $target_timezone, $comments) {
	$id = htmlspecialchars($row["user_id"], ENT_QUOTES, "UTF-8");
	$row['icon'] = htmlspecialchars($row['icon'], ENT_QUOTES, 'UTF-8');
	$row['nickname'] = htmlspecialchars($row['nickname'], ENT_QUOTES, 'UTF-8');
	$row['created_at'] = htmlspecialchars($row['created_at'], ENT_QUOTES, 'UTF-8');
	$row['title'] = htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8');
	$row['content'] = htmlspecialchars($row['content'], ENT_QUOTES, 'UTF-8');
	$row['image'] = htmlspecialchars($row['image'], ENT_QUOTES, 'UTF-8');
	$created_at = (new DateTime($row["created_at"]))->setTimezone($target_timezone)->format("Y-m-d H:i:s");
	
	$actions = "";
	if ($row["user_id"] === $_SESSION["user_id"]) {
		$actions = post_actions_comp($row);
	}

	$like_icon = like_svg($row);
	$post_owner = post_owner_comp($id, $row["icon"], $row["nickname"], $created_at);

	$comments_html = "";
	$show_more_comments_button = "";
	if ($comments) {
		$comments_count = count($comments);
		for ($i = 0; $i < $comments_count; ++$i) {
			$comments_html .= comment_panel($comments[$i], $target_timezone, $i < $comments_count - 3);
		}
		
		if ($comments_count > 3) {
			$show_more_comments_button = <<< ___EOF___
				<div class="flex gap-2 p-1 border-l-2 border-slate-500 bg-slate-200 text-xs">
					<button onclick="showAllComments({$row['post_id']})" class="px-1 w-fit rounded-md text-blue-700 font-bold text-left hover:underline transition-all">コメントをすべて表示する</button>
				</div>
			___EOF___;
		}
	}
	
	$image = "";
	if ($row["image"]) {
		$image = <<< ___EOF___
				<img class="mx-auto max-h-60" src="post_images/{$row['image']}">
		___EOF___;
	}

	return <<< ___EOF___
		<div class="flex flex-col gap-1 border-2 rounded-lg border-black p-2 bg-slate-100">
			<div class="flex justify-between">
				$post_owner
				$actions
			</div>
			<div class="font-semibold">
				<p>{$row['title']}</p>
			</div>
			<div class="leading-4">
				{$image}
				<p class="text-wrap break-all hover:line-clamp-none text-ellipsis overflow-hidden line-clamp-3">{$row['content']}</p>
			</div>
			<div class="mt-2 flex gap-2 items-center">
				<div class="flex gap-1 items-center text-xs">
					<button onclick="like({$row['post_id']})" id="like-button-{$row['post_id']}" class="p-1 bg-slate-300 hover:bg-slate-200 active:bg-slate-400 rounded-full ring-0 hover:ring-2 hover:ring-rose-400 transition-all">
						$like_icon
					</button>
					<span id="like-count-{$row['post_id']}">{$row["like_count"]}</span>
				</div>
				<div class="px-2 py-1 bg-slate-300 rounded-lg text-xs">
					コメント：{$row["comment_count"]}
				</div>
			</div>
			<div id="comment-region-{$row["post_id"]}" class="flex flex-col gap-1" data-is-comments-hidden>
				$show_more_comments_button
				$comments_html
				<div class="flex gap-2 p-1 border-l-2 border-slate-500 bg-slate-200 text-xs">
					<input type="text" id="comment-content-{$row["post_id"]}" name="comment" class="px-2 py-1 flex-grow border rounded-md">
					<button onclick="comment({$row["post_id"]})" class="px-4 bg-blue-300 hover:bg-blue-200 active:bg-blue-400 rounded-md font-bold transition-all">送信</button>
				</div>
			</div>
		</div>
	___EOF___;
}

