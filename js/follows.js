document.addEventListener("DOMContentLoaded", function () {
document.querySelectorAll(".follow-btn").forEach(button => {
button.addEventListener("click", function () {
const targetId = this.getAttribute("data-user-id");
const buttonElement = this; 
    fetch("api/follows.php", {
        method: "POST",
        headers: {
        "Content-Type": "application/x-www-form-urlencoded",
    },
        body: `user_id_target=${encodeURIComponent(targetId)}`
})

.then(response => response.json())
    .then(data => {
        console.log("サーバーからのレスポンス:", data);
        if (data.success) {
            console.log("フォロー成功");
            buttonElement.classList.remove("bg-blue-200", "bg-red-400");
        if (data.following) {
            buttonElement.textContent = "フォロー解除しました";
            buttonElement.classList.add("bg-red-400");
        } else {
            buttonElement.textContent = "フォローしました";
            buttonElement.classList.add("bg-blue-200");
    }
        } else {
            console.error("エラー:", data.message);
    }
})
        });
    });
});
