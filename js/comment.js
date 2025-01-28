function comment(reply_to) {
	if (!Number.isInteger(reply_to)) {
		return;
	}
	const commentContentElement = document.getElementById("comment-content-" + reply_to);
	const commentContent = commentContentElement?.value;

	if (commentContent) {
		fetch("api/reply.php", {
			method: "POST",
			body: JSON.stringify({
				reply_to: reply_to,
				content: commentContent
			})
		}).then((response) => response.json())
		.then((body) => {
			const commentsElem = document.getElementById("comment-region-" + reply_to);
			commentsElem.children[commentsElem.childElementCount - 1].insertAdjacentHTML("beforebegin", body.html);
			commentContentElement.value = "";
		});
	}
}

function showAllComments(post_id) {
	const commentRegion = document.getElementById("comment-region-" + post_id);
	const showAllCommentsButton = commentRegion.querySelector("button");
	const children = commentRegion.children;
	console.log(commentRegion.dataset.isCommentsHidden);
	if (commentRegion.dataset.isCommentsHidden != undefined) {
		for (let i = 1; i < children.length - 1; ++i) {
			children[i].classList.remove("hidden");
		}
		showAllCommentsButton.textContent = "コメントを隠す";
		delete commentRegion.dataset.isCommentsHidden;
	} else {
		for (let i = 1; i < children.length - 1 - 3; ++i) {
			children[i].classList.add("hidden");
		}
		showAllCommentsButton.textContent = "コメントをすべて表示する";
		commentRegion.dataset.isCommentsHidden = "";
	}
}
