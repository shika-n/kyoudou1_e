<?php
require_once("db_open.php");
require_once("models/posts.php");
require_once("layout.php");

function post_panel($row, $target_timezone) {
	$id = htmlspecialchars($row["user_id"], ENT_QUOTES, "UTF-8");
	$row['icon'] = htmlspecialchars($row['icon'], ENT_QUOTES, 'UTF-8');
	$row['nickname'] = htmlspecialchars($row['nickname'], ENT_QUOTES, 'UTF-8');
	$row['created_at'] = htmlspecialchars($row['created_at'], ENT_QUOTES, 'UTF-8');
	$row['title'] = htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8');
	$row['content'] = htmlspecialchars($row['content'], ENT_QUOTES, 'UTF-8');
	$created_at = (new DateTime($row["created_at"]))->setTimezone($target_timezone)->format("Y-m-d H:i:s");
	$image = "";
	if ($row["image"]) {
		$image = <<< ___EOF___
				<img class="mx-auto max-h-60" src="post_images/{$row['image']}">
		___EOF___;
	}
	
	return <<< ___EOF___
		<style>
			.massage{
				font-size:16px;
				font-wight:70;
				color:rgb(218, 16, 16);
				background:rgb(251, 212, 205);
				border:1px solid black ;
				border-radius: 8px;
				border-color:rgb(208, 66, 58);
				padding:15px 12px 15px 100px;
				width: 390px;
			}
			button{
				display: inline-block;
				transition: .0.5s;
			}
			.deletebutton{
			
				font-size: 13px;
				font-weight:bold;
				border:2px solid black ;
				border-radius: 8px;
				padding:11px 15px 13px 15px;
				margin:0px 50px 0px 0px; 
				width: 170px;
			}
			.deletebutton:hover{
				background: #c94036;
				color: #FFF;
			}
			.deletebutton:active{
				background: #efb8b8;
				color: #520707;
				border-color: #000000;
			}
			.cancelbutton{
				font-size: 13px;
				font-weight:bold;
				border:2px solid black ;
				border-radius: 8px;
				padding:11px 15px 13px 15px;
				width: 170px;
			}
			.cancelbutton:hover{
				background: #b9b9b9;
				color: #000000;
			}
			.cancelbutton:active{
				background: #555555;
				color: white;
				border-color: #000000;
			}
		</style>

	
		<p class="massage">次のメッセージを削除します:</p>
		
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
			</div>
			<div class="font-semibold">
				<p>{$row['title']}</p>
			</div>
			<div class="leading-4">
				{$image}
				<p class="text-wrap break-all hover:line-clamp-none text-ellipsis overflow-hidden line-clamp-3">{$row['content']}</p>
			</div>
		</div>


		
			<form method="POST" class="button">
				<input type="hidden" name="post_id" value="{$row['post_id']}">
				<button class="deletebutton" type="submit">削除</button>
				<a class="cancelbutton" href="profile.php">キャンセル</a>
			</form>
		
	___EOF___;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if (delete_post($dbh, $_POST["post_id"])) {
		redirect_to(Pages::k_profile);
	} else {
		redirect_back();
	}
}

$post_id = $_GET["post_id"];

$dummy = get_post_by_id($dbh, $_SESSION["user_id"], $post_id);

// ** レイアウトに組み込み＆出力 **
$html = str_replace("<!-- CONTENT -->", post_panel($dummy, new DateTimeZone("Asia/Tokyo")), $html);
echo $html;

?>


