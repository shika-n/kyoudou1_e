<?php
require_once("layout.php"); // レイアウトテンプレート
include "db_open.php";  // データベース接続
include("models/users.php"); // ユーザー情報

// ** ユーザー情報取得 **
$target_user_id = $_SESSION['user_id']; // 現在のユーザーID（セッションから取得）
$sql = "SELECT * FROM users WHERE user_id = :user_id";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':user_id', $target_user_id, PDO::PARAM_INT);
$stmt->execute();

// ユーザー情報を初期化
$name = "";
$nickname = "";
$email = "";
$password = "";
$icon = "";

if ($record = $stmt->fetch()) {
    $icon = htmlspecialchars($record['icon'], ENT_QUOTES, 'UTF-8');
    $name = htmlspecialchars($record['name'], ENT_QUOTES, 'UTF-8');
    $nickname = htmlspecialchars($record['nickname'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($record['email'], ENT_QUOTES, 'UTF-8');
    $password = htmlspecialchars($record['password'], ENT_QUOTES, 'UTF-8');
}

// ** プロフィール更新処理 **
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // フォームからデータを取得
    $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
    $nickname = htmlspecialchars($_POST['nickname'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
    $password = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');
    
    // パスワードを暗号化
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // アップロードされたアイコン処理
    if (!empty($_FILES['icon']['name'])) {
        $upload_dir = "profile_pictures/";
        $uploaded_file = $upload_dir . basename($_FILES['icon']['name']);
        
        // ファイルの安全性確認と保存
        if (move_uploaded_file($_FILES['icon']['tmp_name'], $uploaded_file)) {
            $icon = htmlspecialchars($uploaded_file, ENT_QUOTES, 'UTF-8');
        }
    }

    // データベースを更新
    $update_sql = "UPDATE users SET name = :name, nickname = :nickname, email = :email, password = :password, icon = :icon WHERE user_id = :user_id";
    $update_stmt = $dbh->prepare($update_sql);
    $update_stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $update_stmt->bindParam(':nickname', $nickname, PDO::PARAM_STR);
    $update_stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $update_stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR); // 暗号化されたパスワードを保存
    $update_stmt->bindParam(':icon', $icon, PDO::PARAM_STR);
    $update_stmt->bindParam(':user_id', $target_user_id, PDO::PARAM_INT);

    if ($update_stmt->execute()) {
        echo "<p>ユーザー情報の変更が完了しました！</p>";
    } else {
        echo "<p>更新に失敗しました。</p>";
    }
}


$content = <<<___EOF___
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザー情報の編集</title>
</head>
<body>
    <div class="container mx-auto p-4 text-center">
        <h1 class="text-xl font-bold mb-4">ユーザー情報の編集</h1>
    <div class="border-2 border-gray-300 rounded-lg p-4 flex flex-col gap-2">
        <p class="text-red-500">変更したい項目を入力してください<p>

        <!-- プロフィール編集フォーム -->
        <form method="post" action="" enctype="multipart/form-data">
        <!-- アイコン変更 -->
        <div class="mb-4">
                <label for="icon" class="block font-bold mb-1">アイコン画像:</label>
                 <div class="mt-2 text-center">
                        <img src="$icon" alt="現在のアイコン" class="w-24 h-24 rounded-full block m-auto">
                    </div>
                <input type="file" id="icon" name="icon" class="border-2 rounded-lg p-2 w-full">
                <?php if ($icon): ?>
                   
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
                <label for="password" class="block font-bold mb-1">パスワード:</label>
                <input type="password" id="password" name="password" required class="border-2 rounded-lg p-2 w-full">
            </div>
            </div>
                <?php endif; ?>
            </div>
            <button type="submit" class="bg-blue-500 text-white font-bold py-2 px-4 rounded-lg mt-4 w-1/2">更新</button>
        </form>
    </div>
</body>
</html>
___EOF___;
$html = str_replace("<!-- CONTENT -->", $content, $html);
echo $html;
?>
