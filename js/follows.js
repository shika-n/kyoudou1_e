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
        if (data.success ) {
            buttonElement.classList.remove("bg-blue-200", "bg-red-400");
			buttonElement.classList.add("bg-gray-300");
			if (data.message.startsWith("フレンド申請")) {
				buttonElement.textContent = "申請済み";
			} else if (data.message.startsWith("フレンド削除")) {
				buttonElement.textContent = "削除済み";
			}
			buttonElement.setAttribute("disabled", true);
        } else {
            console.error("エラー:", data.message);
    }
})
        });
    });
});
