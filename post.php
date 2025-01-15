<?php
require_once("layout.php");


$content = <<< ___EOF___
<!DOCTYPE html>
<html lang="ja">
<head>
    <link rel="stylesheet" href="post.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>掲示板</title>
    <style>
        body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f9f9f9;
  }

  header {
    background-color: #fff;
    padding: 10px 20px;
    border-bottom: 1px solid #ccc;
    text-align: center;
  }

  header h1 {
    margin: 0;
    font-size: 24px;
    text-decoration: underline;
  }

  .menu-icon {
    position: absolute;
    left: 10px;
    top: 10px;
    font-size: 20px;
    cursor: pointer;
  }

  main {
    padding: 20px;
  }

  .form-container {
    max-width: 800px;
    margin: 0 auto;
    background: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  }

  .form-container label {
    display: block;
    margin-bottom: 10px;
    font-weight: bold;
  }

  .form-container input[type="text"],
  .form-container textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
  }

  .form-container button {
    width: 100%;
    padding: 10px;
    background-color: #007BFF;
    color: #fff;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
  }

  .form-container button:hover {
    background-color: #0056b3;
  }
    </style>
</head>
<body>

    <main>
        <div class="form-container flex flex-col">
          <label for="title">投稿内容</label>
          <input type="text" id="title" name="title" placeholder="タイトル">
          <textarea id="content" name="content" rows="5" placeholder="コンテンツ"></textarea>
          <button type="submit">投稿</button>
        </div>
      </main>

    
</body>
</html>
___EOF___;

$html = str_replace("<!-- CONTENT -->", $content, $html);
echo $html;