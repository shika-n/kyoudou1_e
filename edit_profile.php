<?php
require_once("layout.php"); // レイアウトテンプレート
include "db_open.php";  // データベース接続
include("models/users.php"); // ユーザー情報

if (!is_authenticated()) {
	redirect_to(Pages::k_login);
}

// ** ユーザー情報取得 **
$target_user_id = $_SESSION['user_id']; // 現在のユーザーID（セッションから取得）
$user = get_user_by_id($dbh, $target_user_id);

// ユーザー情報を初期化
$name = "";
$nickname = "";
$email = "";
$current_password = "";
$icon = "";

if ($user) {
    $icon = htmlspecialchars($user['icon'], ENT_QUOTES, 'UTF-8');
    $name = htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8');
    $nickname = htmlspecialchars($user['nickname'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8');
    $current_password = $user['password'];
}

// ** プロフィール更新処理 **
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // フォームからデータを取得
	$name = trim(get_if_set("name", $_POST, ""));
	$nickname = trim(get_if_set("nickname", $_POST, ""));
	$password = get_if_set("password", $_POST);
	$new_email = trim(get_if_set("email", $_POST, ""));
	$new_password = get_if_set("new-password", $_POST);
	$re_new_password = get_if_set("re-new-password", $_POST);

	if (!$name || mb_strlen($name) < 1 || mb_strlen($name) > 20) {
		$_SESSION["error"] = "名前は1~20文字で入力してください";
		redirect_back();
	}

	if (!$nickname || mb_strlen($nickname) < 1 || mb_strlen($nickname) > 20) {
		$_SESSION["error"] = "ニックネームは1~20文字で入力してください";
		redirect_back();
	}

	if (!$password) {
		$_SESSION["error"] = "現在パスワードを入力してください";
		redirect_back();
	}

	if ($new_password && mb_strlen($new_password) < 6) {
		$_SESSION["error"] = "新しいパスワードは6文字以上で入力してください";
		redirect_back();
	}

	if (!$new_email || mb_strlen($new_email) < 1 || mb_strlen($new_email) > 120) {
		$_SESSION["error"] = "メールは1~120文字で入力してください";
		redirect_back();
	}

	if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
		$_SESSION["error"] = "メールに間違いがあります";
		redirect_back();
	}

	if ($new_email != $email && user_exists($dbh, $new_email)) {
		$_SESSION["error"] = "メールは既に存在しています";
		redirect_back();
	}
	
	if ($new_password && $new_password !== $re_new_password) {
		$_SESSION["error"] = "新しいパスワードは一致していません";
		redirect_back();
	}
	
	if (!password_verify($password, $current_password)) {
		$_SESSION["error"] = "現在のパスワードは間違います";
		redirect_back();
	}

    // パスワードを暗号化
	$hashed_password = null;
	if ($new_password) {
		$hashed_password = password_hash($new_password, PASSWORD_ARGON2I);
	}

    // アップロードされたアイコン処理
	$icon_file = get_if_set("icon", $_FILES);
    if ($icon_file && !empty($icon_file['name'])) {
		$is_uploadable = check_uploadable_image($icon_file);
		if ($is_uploadable !== true) {
			$_SESSION["error"] = $is_uploadable;
			redirect_back();
		}
        $upload_dir = "profile_pictures/";
        $uploaded_file = get_unique_image_name($icon_file);
        
        // ファイルの安全性確認と保存
        if (move_uploaded_file($_FILES['icon']['tmp_name'], $upload_dir . $uploaded_file)) {
            $icon = $uploaded_file;
        }
    }

    // データベースを更新
    if (edit_profile($dbh, $target_user_id, $name, $nickname, $new_email, $hashed_password, $icon)) {
        $_SESSION["info"] = "ユーザー情報の変更が完了しました！";
		$_SESSION["name"] = $name;
		redirect_to(Pages::k_profile);
    } else {
        $_SESSION["error"] = "更新に失敗しました。";
		redirect_back();
    }
	return;
}

$error = get_if_set("error", $_SESSION);
$_SESSION["error"] = null;

$content = <<<___EOF___
    <div class="container mx-auto p-4 text-center">
        <h1 class="text-xl font-bold mb-4">ユーザー情報の編集</h1>
    	<div class="border-2 border-gray-300 rounded-lg p-4 flex flex-col gap-2">
			<p class="text-red-500">変更したい項目を入力してください<p>
			<p class="mb-2 text-red-600 font-bold underline decoration-wavy">{$error}</p>

			<!-- プロフィール編集フォーム -->
			<form method="post" action="" enctype="multipart/form-data">
				<!-- アイコン変更 -->
				<div class="mb-4">
					<label for="icon" class="block font-bold mb-1">アイコン画像:</label>
					<div class="mt-2 text-center">
						<img src="profile_pictures/$icon" alt="現在のアイコン" id="preview" class="w-24 h-24 rounded-full block m-auto object-cover object-center mb-4">
					</div>
					<input type="file" id="icon" name="icon" class="border-2 rounded-lg p-2 w-full" accept="image/png, image/jpeg, image/gif" class="flex-grow">
					   
					<!-- 名前変更 -->
					<div class="mb-4">
						<label for="name" class="block font-bold mb-1">名前:</label>
						<input type="text" id="name" name="name" value="$name" required class="border-2 rounded-lg p-2 w-full">
					</div>
					<!-- ニックネーム変更 -->
					<div class="mb-4">
						<label for="nickname" class="block font-bold mb-1">ニックネーム:</label>
						<input type="text" id="nickname" name="nickname" value="$nickname" required class="border-2 rounded-lg p-2 w-full">
					</div>
					<!-- メールアドレス変更 -->
					<div class="mb-4">
						<label for="email" class="block font-bold mb-1">メールアドレス:</label>
						<input type="email" id="email" name="email" value="$email" required class="border-2 rounded-lg p-2 w-full">
					</div>
					<!-- パスワード変更 -->
					<div class="mb-4">
						<label for="new-password" class="block font-bold mb-1">新しいパスワード:</label>
						<input type="password" id="new-password" name="new-password" class="border-2 rounded-lg p-2 w-full" placeholder="変更なし">
					</div>
					<div class="mb-4">
						<label for="re-new-password" class="block font-bold mb-1">新しいパスワードの再確認:</label>
						<input type="password" id="re-new-password" name="re-new-password" class="border-2 rounded-lg p-2 w-full" placeholder="変更なし">
					</div>
					<div class="mb-4">
						<label for="password" class="block font-bold mb-1">現在のパスワード:</label>
						<input type="password" id="password" name="password" required class="border-2 rounded-lg p-2 w-full">
					</div>
				</div>
				<button type="button" onclick="showDialog()" class="bg-blue-500 hover:bg-blue-400 active:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg mt-4 flex-1 w-1/2 transition-all">更新</button>
				<div id="dialog-panel" class="hidden fixed top-0 left-0 w-screen h-screen flex items-center justify-center bg-black/50 z-50 backdrop-blur-md">
					<div class="bg-white w-fit h-fit p-4 rounded-xl">
						<p>本当に更新しますか？</p>
						<div class="flex gap-2">
							<button type="submit" onclick="hideDialog()" class="bg-blue-500 hover:bg-blue-400 active:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg mt-4 min-w-32 transition-all">更新</button>
							<button type="button" onclick="hideDialog()" class="bg-gray-500 hover:bg-gray-400 active:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg mt-4 min-w-32 transition-all">キャンセル</button>
						</div>
					</div>
					<script src="js/dialog.js"></script>
				</div>
			</form>
		</div>
	</div>
	<script src="js/icon_preview.js"></script>
___EOF___;
$html = str_replace("<!-- CONTENT -->", $content, $html);
echo $html;
?>
