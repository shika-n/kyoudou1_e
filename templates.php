<?php
require_once("util.php");
require_once("externals/php-markdown/Michelf/Markdown.inc.php");
use Michelf\Markdown;

function chip($value) {
	return <<< ___EOF___
		<div>
			<input type="hidden" name="tags[]" value="{$value}">
			<span class="chips flex items-center gap-1 py-1 pl-2 pr-1 bg-gray-300 rounded-full min-w-0">
				{$value}
				<button type="button" onclick="deleteChip(this)" class="chips p-1 rounded-full hover:bg-gray-200 w-5 h-5">
					<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3">
						<path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
					</svg>
				</button>
			</span>
		</div>
	___EOF___;
}

function tag_chips($tags) {
	if (count($tags) === 0) {
		return "";
	}

	$chips_html = "<div class='flex flex-wrap gap-1'>";
	$search_page = Pages::k_kensaku;
	foreach ($tags as $tag) {
		$chips_html .= <<< ___EOF___
			<a href="{$search_page->get_url()}?query=$tag" class="chips flex items-center gap-1 py-[0.2em] px-2 bg-gray-300 hover:bg-gray-200 active:bg-gray-400 rounded-full min-w-0 text-xs transition-all">
				$tag
			</a>
		___EOF___;
	}
	return $chips_html . "</div>";
}

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
				<a href="edit_post.php?post_id={$post['post_id']}" class="p-2 rounded-full hover:bg-slate-50 active:bg-slate-200"><img src="images/edit.png" class="min-w-3 w-3 aspect-square"></a>
				<a href="delete_post.php?post_id={$post['post_id']}" class="p-2 rounded-full hover:bg-slate-50 active:bg-slate-200"><img src="images/trash.png" class="min-w-3 w-3 aspect-square"></a>
			</div>
	___EOF___;
}

function post_owner_comp($id, $icon, $nickname, $created_at, $updated_at) {
	$showed_datetime = $created_at;
	$hover = "";
	if ($created_at != $updated_at) {
		$showed_datetime = $created_at . "*";
		$hover = "title='{$updated_at}に編集された'";
	}
	$post_owner_comp_layout = <<< ___EOF___
		<div class="flex flex-row items-center">
			<div class="rounded-full">
				<img src="profile_pictures/{$icon}" class="min-w-8 w-8 rounded-full aspect-square object-cover object-center">
			</div>
			<div class="flex flex-col flex-wrap ml-5 text-sm px-2 divide-y divide-black">
				<div class="font-semibold">
					<a href="profile.php?id=$id" class="break-all line-clamp-1">{$nickname}</a>
				</div>
				<div>
					<p <!-- HOVER -->>{$showed_datetime}</p>
				</div>
			</div>
		</div>
	___EOF___;
	$post_owner_comp_layout = str_replace("<!-- HOVER -->", $hover, $post_owner_comp_layout);
	return $post_owner_comp_layout;
}

function comment_panel($comment, $target_timezone, $hidden = false) {
	$id = htmlspecialchars($comment["user_id"], ENT_QUOTES, "UTF-8");
	$comment['icon'] = htmlspecialchars($comment['icon'], ENT_QUOTES, 'UTF-8');
	$comment['nickname'] = htmlspecialchars($comment['nickname'], ENT_QUOTES, 'UTF-8');
	$comment['created_at'] = htmlspecialchars($comment['created_at'], ENT_QUOTES, 'UTF-8');
	$comment['updated_at'] = htmlspecialchars($comment['updated_at'], ENT_QUOTES, 'UTF-8');
	$comment['title'] = htmlspecialchars($comment['title'], ENT_QUOTES, 'UTF-8');
	$comment['content'] = htmlspecialchars($comment['content'], ENT_QUOTES, 'UTF-8');
	$created_at = (new DateTime($comment["created_at"]))->setTimezone($target_timezone)->format("Y-m-d H:i:s");
	$updated_at = (new DateTime($comment["updated_at"]))->setTimezone($target_timezone)->format("Y-m-d H:i:s");
	
	$actions = "";
	if ($comment["user_id"] === $_SESSION["user_id"]) {
		$actions = post_actions_comp($comment);
	}
	
	$like_icon = like_svg($comment);

	$hidden_class = "";
	if ($hidden) {
		$hidden_class = "hidden";
	}

	$showed_datetime = $created_at;
	$hover = "";
	if ($created_at != $updated_at) {
		$showed_datetime = $created_at . "*";
		$hover = "title='{$updated_at}に編集された'";
	}
	$comment_panel_layout = <<< ___EOF___
		<div class="p-2 border-l-2 border-slate-500 bg-slate-200 $hidden_class">
			<div class="flex justify-between">
				<div class="flex flex-row items-center truncate">
					<div class="rounded-full">
						<img src="profile_pictures/{$comment['icon']}" class="min-w-6 w-6 rounded-full aspect-square object-cover object-center">
					</div>
					<div class="flex flex-col flex-wrap ml-3 text-xs px-2 divide-y divide-black">
						<div class="font-semibold">
							<a href="profile.php?id=$id">{$comment['nickname']}</a>
						</div>
						<div>
							<p <!-- HOVER -->>{$showed_datetime}</p>
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
	$comment_panel_layout = str_replace("<!-- HOVER -->", $hover, $comment_panel_layout);
	return $comment_panel_layout;
}

function comment_section($post_id, $comments, $target_timezone) {
	if ($comments) {
		$comments_count = count($comments);
	} else {
		$comments_count = 0;
	}
	$comments_html = "";
	for ($i = 0; $i < $comments_count; ++$i) {
		$comments_html .= comment_panel($comments[$i], $target_timezone, $i < $comments_count - 3);
	}
	
	$show_more_comments_button = "";
	if ($comments_count > 3) {
		$show_more_comments_button = <<< ___EOF___
			<div class="flex gap-2 p-1 border-l-2 border-slate-500 bg-slate-200 text-xs">
				<button onclick="showAllComments({$post_id})" class="px-1 w-fit rounded-md text-blue-700 font-bold text-left hover:underline transition-all">コメントをすべて表示する</button>
			</div>
		___EOF___;
	}

	return <<< ___EOF___
		<div id="comment-region-{$post_id}" class="flex flex-col gap-1" data-is-comments-hidden>
			$show_more_comments_button
			$comments_html
			<div class="flex flex-col md:flex-row gap-1 md:gap-2 p-1 border-l-2 border-slate-500 bg-slate-200 text-xs">
				<input type="text" id="comment-content-{$post_id}" name="comment" class="px-2 py-2 md:py-1 flex-grow border rounded-md">
				<button onclick="comment({$post_id})" class="px-4 py-2 md:py-1 bg-blue-300 hover:bg-blue-200 active:bg-blue-400 rounded-md font-bold transition-all">送信</button>
			</div>
		</div>
	___EOF___;
}

function post_panel($row, $target_timezone, $comments = null, $enable_comments = false, $collapse_post = true) {
	$id = htmlspecialchars($row["user_id"], ENT_QUOTES, "UTF-8");
	$row['icon'] = htmlspecialchars($row['icon'], ENT_QUOTES, 'UTF-8');
	$row['nickname'] = htmlspecialchars($row['nickname'], ENT_QUOTES, 'UTF-8');
	$row['created_at'] = htmlspecialchars($row['created_at'], ENT_QUOTES, 'UTF-8');
	$row['updated_at'] = htmlspecialchars($row['updated_at'], ENT_QUOTES, 'UTF-8');
	$row['title'] = htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8');
	$row['content'] = htmlspecialchars($row['content'], ENT_QUOTES, 'UTF-8');
	$row['image'] = htmlspecialchars($row['image'], ENT_QUOTES, 'UTF-8');
	$row['categories'] = htmlspecialchars($row['categories'], ENT_QUOTES, 'UTF-8');
	$created_at = (new DateTime($row["created_at"]))->setTimezone($target_timezone)->format("Y-m-d H:i:s");
	$updated_at = (new DateTime($row["updated_at"]))->setTimezone($target_timezone)->format("Y-m-d H:i:s");

	if (mb_strlen(trim($row["tags"])) > 0) {
		$tags = explode(",", $row["tags"]);
		foreach ($tags as $key => $val) {
			$tags[$key] = htmlspecialchars($val, ENT_QUOTES, "UTF-8");
		}
		$tags_html = tag_chips($tags);
	} else {
		$tags_html = "";
	}
	
	$actions = "";
	if ($row["user_id"] === $_SESSION["user_id"]) {
		$actions = post_actions_comp($row);
	}

	$like_icon = like_svg($row);
	$post_owner = post_owner_comp($id, $row["icon"], $row["nickname"], $created_at , $updated_at);

	$row["content"] = Markdown::defaultTransform($row["content"]); 

	$comment_section_html = "";
	$image = "";
	if ($enable_comments) {
		$comment_section_html = comment_section($row["post_id"], $comments, $target_timezone);
		if ($row["image"]) {
			$image = <<< ___EOF___
					<img class="mx-auto my-1 max-h-60 rounded-md" src="post_images/{$row['image']}">
			___EOF___;
		}
	}

	$like_button = "";
	if ($row["user_id"] !== $_SESSION["user_id"]) {
		$like_button = <<<__EOF__

				<div class="flex gap-1 items-center text-xs">
					<button onclick="like({$row['post_id']})" id="like-button-{$row['post_id']}" class="p-1 bg-slate-300 hover:bg-slate-200 active:bg-slate-400 rounded-full ring-0 hover:ring-2 hover:ring-rose-400 transition-all">
						$like_icon
					</button>
					<span id="like-count-{$row['post_id']}">{$row["like_count"]}</span>
				</div>
		__EOF__;
	}else {
		$like_button = "";
		$like_button = <<<__EOF__

				<div class="flex gap-1 items-center text-xs">
					<button class="p-1 rounded-full">
						$like_icon
					</button>
					<span id="like-count-{$row['post_id']}">{$row["like_count"]}</span>
				</div>
		__EOF__;
	}
	

	$line_clamp = "";
	if ($collapse_post) {
		$line_clamp = "line-clamp-3";
	}

	$pages = Pages::k_base_url;

	$image_layout = "";
	if (get_if_set("image_position", $row) == 0 ){
		$image_layout = <<<__EOF__
			<div class="leading-none">
				{$image}
				<div class="markdown-content text-wrap break-all text-ellipsis overflow-hidden $line_clamp">{$row['content']}</div>
			</div>
		__EOF__;
	}elseif ($row["image_position"] == 1 ){
		$image_layout = <<<__EOF__
		<div class="leading-none">
			<div class="markdown-content text-wrap break-all text-ellipsis overflow-hidden $line_clamp">{$row['content']}</div>
			{$image}
		</div>
	__EOF__;
	}

	return <<< ___EOF___
		<div class="post-panel flex flex-col gap-1 border-2 rounded-lg border-black p-2 bg-slate-100">
			<div class="flex justify-between">
				$post_owner
				$actions
			</div>
			<div class="font-semibold">
				<a href="{$pages::k_post_detail->get_url()}?id={$row['post_id']}">{$row['title']}</a>
			</div>
			<div class="font-semibold px-2 py-1 bg-slate-300 rounded-lg">
				<p>{$row['categories']}</p>
			</div>
			$image_layout
			$tags_html
			<div class="mt-2 flex gap-2 items-center">
				$like_button 
				<a id="comment-count-{$row['post_id']}" href="{$pages::k_post_detail->get_url()}?id={$row['post_id']}" class="px-2 py-1 bg-slate-300 rounded-lg text-xs">
					コメント：{$row["comment_count"]}
				</a>
			</div>
			$comment_section_html
		</div>
	___EOF___;
}
