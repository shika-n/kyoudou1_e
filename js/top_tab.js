function changeTopTab(type) {
	const contentPanel = document.getElementById("content");
	const postPanels = contentPanel.querySelectorAll(".post-panel");
	postPanels.forEach(e => e.remove());
	
	reachBottomActionQuery.set("type", type);
	const params = new URLSearchParams({
		"type": type
	});
	fetch("api/get_posts.php?" + params)
		.then((response) => response.text())
		.then((text) => {
			contentPanel.innerHTML += text;
			resetCurrentPage(); // reach_bottom_action.js
		});
}
