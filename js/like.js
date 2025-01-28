function like(post_id) {
    console.log(post_id);

    // いいねを送信
    fetch(`api/like.php?action=like&post_id=${post_id}`, {
        method: "POST", // POSTメソッドで送信
        credentials: "include", // セッション情報を送信
        headers: {
            "Content-Type": "application/json",
        },
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error(`HTTPエラー！ステータス: ${response.status}`);
            }
            return response.json(); // JSON形式でを取得
        })
        .then((data) => {
            if (data.message === "OK") {
                console.log("いいね成功！");
                // UIの更新
                const likeButton = document.getElementById(`like-button-${post_id}`);
				const likeCount = document.getElementById(`like-count-${post_id}`);
                if (likeButton && likeCount){
					likeCount.textContent = data.like_count;
                }else{
                    console.error(`ID "like-button-${post_id}"や"like-button-${post_id}"に対応する要素が見つかりません`);
                }

            } else if (data.message === "Already liked") {
                console.log("すでにいいねされています！");
            } else {
                console.error("いいねに失敗しました:", data.message);
            }
        })
        .catch((error) => {
            console.error("Fetch API エラー:", error);
        });
}
